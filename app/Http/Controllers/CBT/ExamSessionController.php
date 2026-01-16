<?php

namespace App\Http\Controllers\CBT;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\EmployeeCompetency;
use App\Models\Exam;
use App\Models\ExamSession;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 * ExamSessionController
 * 
 * Admin controller for managing CBT Exam Sessions.
 * Handles: assigning exams to employees, verification, results.
 */
class ExamSessionController extends Controller
{
    /**
     * Display a listing of all exam sessions.
     */
    public function index(Request $request)
    {
        $query = ExamSession::with(['exam.skill', 'employee', 'verifier']);

        // Filter by status
        if ($request->status) {
            $query->where('status', $request->status);
        }

        // Filter by exam
        if ($request->exam_id) {
            $query->where('exam_id', $request->exam_id);
        }

        // Filter by date range
        if ($request->from_date) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }
        if ($request->to_date) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }

        $sessions = $query->latest()->paginate(20);
        $exams = Exam::where('is_published', true)->get();

        return view('cbt.admin.sessions.index', compact('sessions', 'exams'));
    }

    /**
     * Show form to assign exam to employee(s).
     */
    public function create()
    {
        $exams = Exam::where('is_published', true)
            ->with('skill')
            ->get();

        $employees = Employee::with(['division', 'position'])
            ->where('status', 'Aktif')
            ->orderBy('name')
            ->get();

        $divisions = \App\Models\Division::orderBy('name')->get();

        return view('cbt.admin.sessions.create', compact('exams', 'employees', 'divisions'));
    }

    /**
     * Assign exam to employee(s).
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'exam_id' => 'required|exists:exams,id',
            'employee_niks' => 'required|array|min:1',
            'employee_niks.*' => 'exists:employees,nik',
        ]);

        $exam = Exam::findOrFail($validated['exam_id']);
        $assigned = 0;
        $skipped = 0;

        foreach ($validated['employee_niks'] as $nik) {
            // Check if employee already has pending/started session for this exam
            $existingSession = ExamSession::where('exam_id', $exam->id)
                ->where('employee_nik', $nik)
                ->whereIn('status', [
                    ExamSession::STATUS_ASSIGNED,
                    ExamSession::STATUS_STARTED
                ])
                ->exists();

            if ($existingSession) {
                $skipped++;
                continue;
            }

            ExamSession::create([
                'exam_id' => $exam->id,
                'employee_nik' => $nik,
                'status' => ExamSession::STATUS_ASSIGNED,
            ]);
            $assigned++;
        }

        $message = "{$assigned} karyawan berhasil ditugaskan ujian.";
        if ($skipped > 0) {
            $message .= " ({$skipped} dilewati karena sudah memiliki sesi aktif)";
        }

        return redirect()
            ->route('cbt.admin.sessions.index')
            ->with('success', $message);
    }

    /**
     * Display the specified session with answers.
     */
    public function show(ExamSession $session)
    {
        $session->load([
            'exam.skill',
            'exam.questions' => fn($q) => $q->orderBy('exam_question.order'),
            'employee.division',
            'employee.position',
            'answers.question',
            'verifier',
        ]);

        return view('cbt.admin.sessions.show', compact('session'));
    }

    /**
     * Verify and approve/reject exam session.
     */
    public function verify(Request $request, ExamSession $session)
    {
        $validated = $request->validate([
            'action' => 'required|in:approve,reject',
            'notes' => 'nullable|string|max:500',
            'essay_grade_*' => 'nullable|in:0,1',
        ]);

        if (!in_array($session->status, [ExamSession::STATUS_SUBMITTED])) {
            return back()->with('error', 'Sesi ujian tidak dalam status yang dapat diverifikasi!');
        }

        DB::beginTransaction();

        try {
            // Process essay grading first
            foreach ($request->all() as $key => $value) {
                if (strpos($key, 'essay_grade_') === 0) {
                    $questionId = str_replace('essay_grade_', '', $key);
                    $answer = ExamAnswer::where('exam_session_id', $session->id)
                        ->where('question_id', $questionId)
                        ->first();
                    
                    if ($answer) {
                        $answer->update([
                            'is_correct' => (bool) $value,
                        ]);
                    }
                }
            }

            // Recalculate score after essay grading
            $this->recalculateScore($session);

            $passed = $session->isPassed();

            if ($validated['action'] === 'approve') {
                $session->update([
                    'status' => $passed 
                        ? ExamSession::STATUS_VERIFIED_PASS 
                        : ExamSession::STATUS_VERIFIED_FAIL,
                    'verified_by' => Auth::id(),
                    'verified_at' => now(),
                    'admin_notes' => $validated['notes'] ?? null,
                ]);

                // If passed, update employee's skill level
                if ($passed) {
                    $this->updateEmployeeSkillLevel($session);
                }
            } else {
                // Rejected - allow retake
                $session->update([
                    'status' => ExamSession::STATUS_VERIFIED_FAIL,
                    'verified_by' => Auth::id(),
                    'verified_at' => now(),
                    'admin_notes' => $validated['notes'] ?? null,
                ]);
            }

            DB::commit();

            $statusText = $passed ? 'LULUS' : 'TIDAK LULUS';
            return back()->with('success', "Sesi ujian berhasil diverifikasi: {$statusText}");

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal memverifikasi: ' . $e->getMessage());
        }
    }

    /**
     * Recalculate session score based on all answers (including essay)
     */
    private function recalculateScore(ExamSession $session): void
    {
        $totalQuestions = $session->exam->questions()->count();
        
        if ($totalQuestions === 0) {
            return;
        }

        $correctAnswers = $session->answers()->where('is_correct', true)->count();
        $score = round(($correctAnswers / $totalQuestions) * 100, 2);

        $session->update(['score' => $score]);
    }

    /**
     * Update employee's skill level after passing exam.
     */
    private function updateEmployeeSkillLevel(ExamSession $session): void
    {
        $employee = $session->employee;
        $skill = $session->exam->skill;
        $targetLevel = $session->exam->target_level;

        // Update or create employee competency
        EmployeeCompetency::updateOrCreate(
            [
                'employee_nik' => $employee->nik,
                'skill_id' => $skill->id,
            ],
            [
                'level' => $targetLevel,
                'verified_by' => Auth::id(),
                'verified_at' => now(),
                'notes' => "Naik ke Level {$targetLevel} setelah lulus ujian: {$session->exam->title}",
            ]
        );
    }

    /**
     * Cancel/revoke exam session.
     */
    public function cancel(ExamSession $session)
    {
        if (!in_array($session->status, [ExamSession::STATUS_ASSIGNED])) {
            return back()->with('error', 'Hanya sesi dengan status "Ditugaskan" yang dapat dibatalkan!');
        }

        $session->delete();

        return back()->with('success', 'Penugasan ujian berhasil dibatalkan!');
    }

    /**
     * Get sessions pending verification.
     */
    public function pending()
    {
        $sessions = ExamSession::with(['exam.skill', 'employee'])
            ->where('status', ExamSession::STATUS_SUBMITTED)
            ->latest()
            ->paginate(20);

        return view('cbt.admin.sessions.pending', compact('sessions'));
    }

    /**
     * Bulk assign exam to employees by division.
     */
    public function bulkAssignByDivision(Request $request)
    {
        $validated = $request->validate([
            'exam_id' => 'required|exists:exams,id',
            'division_id' => 'required|exists:divisions,id',
        ]);

        $employees = Employee::where('division_id', $validated['division_id'])
            ->where(function($q) {
                $q->where('employment_status', 'active')
                  ->orWhere('status', 'active');
            })
            ->pluck('nik')
            ->toArray();

        if (empty($employees)) {
            return back()->with('error', 'Tidak ada karyawan aktif di divisi tersebut!');
        }

        // Reuse store logic
        $request->merge(['employee_niks' => $employees]);
        return $this->store($request);
    }
}
