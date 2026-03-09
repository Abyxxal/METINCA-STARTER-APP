<?php

namespace App\Http\Controllers\CBT;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\EmployeeCompetency;
use App\Models\Exam;
use App\Models\ExamSession;
use App\Models\Question;
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

        // Filter by date range
        if ($request->from_date) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }
        if ($request->to_date) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }

        $sessions = $query->latest()->paginate(20);

        return view('cbt.admin.sessions.index', compact('sessions'));
    }

    /**
     * Show form to assign exam to employee(s).
     * Using Question Set (paket soal) selection
     */
    public function create()
    {
        // Get unique question sets with their details
        $questionSets = Question::with(['skill.division'])
            ->where('status', 'active')
            ->whereNotNull('question_set_id')
            ->whereNotNull('set_title')
            ->select('question_set_id', 'set_title', 'skill_id', 'for_level')
            ->selectRaw('MIN(type) as type')
            ->selectRaw('COUNT(*) as total_questions')
            ->groupBy('question_set_id', 'set_title', 'skill_id', 'for_level')
            ->get()
            ->map(function($set) {
                // Load skill for division info
                $set->skill = \App\Models\Skill::with('division')->find($set->skill_id);
                return $set;
            });

        $employees = Employee::with(['division', 'position'])
            ->where('status', 'Aktif')
            ->orderBy('name')
            ->get();

        $divisions = \App\Models\Division::orderBy('name')->get();

        return view('cbt.admin.sessions.create', compact('questionSets', 'employees', 'divisions'));
    }

    /**
     * Create exam from selected question sets and assign to employees.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'duration_minutes' => 'required|integer|min:5|max:300',
            'passing_score' => 'required|integer|min:0|max:100',
            'deadline_at' => 'required|date|after:now',
            'scheduled_start_at' => 'nullable|date|before:deadline_at',
            'question_set_ids' => 'required|array|min:1',
            'question_set_ids.*' => 'string',
            'employee_niks' => 'required|array|min:1',
            'employee_niks.*' => 'exists:employees,nik',
        ]);

        DB::beginTransaction();

        try {
            // Get all questions from selected sets
            $questions = Question::whereIn('question_set_id', $validated['question_set_ids'])
                ->where('status', 'active')
                ->with('skill')
                ->get();

            if ($questions->isEmpty()) {
                return back()
                    ->withInput()
                    ->with('error', 'Tidak ada soal ditemukan dalam set yang dipilih');
            }

            // Get set titles for exam title
            $setTitles = Question::whereIn('question_set_id', $validated['question_set_ids'])
                ->select('set_title')
                ->distinct()
                ->pluck('set_title')
                ->toArray();
            
            $examTitle = count($setTitles) === 1 
                ? $setTitles[0] 
                : implode(' + ', $setTitles);

            // Get target level from question level (for_level)
            $targetLevel = $questions->first()->for_level ?? 1;

            // Get the first question to determine skill_id
            $skillId = $questions->first()->skill_id ?? null;

            // Create exam automatically
            $exam = Exam::create([
                'skill_id' => $skillId,
                'title' => $examTitle,
                'description' => 'Ujian dibuat otomatis dari Sesi Ujian (' . count($validated['question_set_ids']) . ' set soal)',
                'target_level' => $targetLevel,
                'passing_score' => $validated['passing_score'],
                'duration_minutes' => $validated['duration_minutes'],
                'is_published' => true,
                'status' => 'active',
            ]);

            // Attach all questions from selected sets to exam
            $order = 1;
            foreach ($questions as $question) {
                $exam->questions()->attach($question->id, [
                    'weight' => 1,
                    'order' => $order++,
                ]);
            }

            // Assign exam to employees
            $assigned = 0;
            $skipped = 0;
            $notEligibleList = [];
            $skippedList = [];

            foreach ($validated['employee_niks'] as $nik) {
                // Get employee with competencies
                $employee = Employee::with('competencies')->where('nik', $nik)->first();

                if (!$employee) {
                    $skippedList[] = [
                        'name' => 'NIK: ' . $nik,
                        'nik' => $nik,
                        'reason' => 'Karyawan tidak ditemukan'
                    ];
                    $skipped++;
                    continue;
                }

                // Check employee's current level for this skill
                $competency = $employee->competencies->where('skill_id', $skillId)->first();
                $currentLevel = $competency ? $competency->level : 0;

                // Validation: Employee must be exactly one level below target
                // Level 0 can only take Level 1 exam
                // Level 1 can only take Level 2 exam, etc.
                $requiredLevel = $targetLevel - 1;
                if ($currentLevel != $requiredLevel) {
                    $notEligibleList[] = [
                        'name' => $employee->name,
                        'nik' => $employee->nik,
                        'current_level' => $currentLevel,
                        'required_level' => $requiredLevel,
                        'target_level' => $targetLevel
                    ];
                    continue;
                }

                // Check if employee already has pending/started session for this exam
                $existingSession = ExamSession::where('exam_id', $exam->id)
                    ->where('employee_nik', $nik)
                    ->whereIn('status', [
                        ExamSession::STATUS_ASSIGNED,
                        ExamSession::STATUS_STARTED
                    ])
                    ->exists();

                if ($existingSession) {
                    $skippedList[] = [
                        'name' => $employee->name,
                        'nik' => $employee->nik,
                        'reason' => 'Sudah memiliki sesi ujian aktif'
                    ];
                    $skipped++;
                    continue;
                }

                ExamSession::create([
                    'exam_id' => $exam->id,
                    'employee_nik' => $nik,
                    'status' => ExamSession::STATUS_ASSIGNED,
                    'deadline_at' => $validated['deadline_at'],
                    'scheduled_start_at' => $validated['scheduled_start_at'] ?? null,
                ]);
                $assigned++;
            }

            DB::commit();

            $totalSoal = $order - 1;
            $totalSets = count($validated['question_set_ids']);
            $message = "Ujian '{$exam->title}' berhasil dibuat dengan {$totalSets} set soal ({$totalSoal} soal). {$assigned} karyawan ditugaskan.";
            $notEligibleCount = count($notEligibleList);
            if ($notEligibleCount > 0) {
                $requiredLvl = $targetLevel - 1;
                $message .= " ({$notEligibleCount} karyawan tidak memenuhi syarat - harus Level {$requiredLvl})";
            }
            if ($skipped > 0) {
                $message .= " ({$skipped} dilewati karena sudah memiliki sesi aktif)";
            }

            return redirect()
                ->route('cbt.admin.sessions.index')
                ->with('success', $message)
                ->with('notEligibleList', $notEligibleList)
                ->with('skippedList', $skippedList)
                ->with('assignedCount', $assigned);

        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->withInput()
                ->with('error', 'Gagal membuat ujian: ' . $e->getMessage());
        }
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
            'manager',
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
                    'manager_decision' => $passed ? ExamSession::DECISION_PENDING : null,
                ]);

                // Tidak langsung update level - menunggu keputusan manager
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

    // ============================================
    // MANAGER APPROVAL METHODS
    // ============================================

    /**
     * Display sessions pending manager approval.
     */
    public function pendingApproval()
    {
        $sessions = ExamSession::with(['exam.skill', 'employee.division', 'employee.position', 'verifier'])
            ->where('status', ExamSession::STATUS_VERIFIED_PASS)
            ->where(function ($q) {
                $q->where('manager_decision', ExamSession::DECISION_PENDING)
                  ->orWhereNull('manager_decision');
            })
            ->latest('verified_at')
            ->paginate(20);

        return view('cbt.admin.sessions.pending-approval', compact('sessions'));
    }

    /**
     * Manager approves level upgrade.
     */
    public function approveLevel(Request $request, ExamSession $session)
    {
        $validated = $request->validate([
            'manager_notes' => 'nullable|string|max:500',
        ]);

        if (!$session->isPendingManagerApproval()) {
            return back()->with('error', 'Sesi ini tidak dalam status menunggu persetujuan.');
        }

        DB::beginTransaction();
        try {
            $session->update([
                'status' => ExamSession::STATUS_APPROVED,
                'manager_decision' => ExamSession::DECISION_APPROVED,
                'manager_notes' => $validated['manager_notes'] ?? null,
                'decided_by' => Auth::id(),
                'decided_at' => now(),
            ]);

            // Update employee skill level
            $this->updateEmployeeSkillLevel($session);

            DB::commit();
            return back()->with('success', "Kenaikan level karyawan {$session->employee->name} telah DISETUJUI.");
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menyetujui: ' . $e->getMessage());
        }
    }

    /**
     * Manager rejects level upgrade.
     */
    public function rejectLevel(Request $request, ExamSession $session)
    {
        $validated = $request->validate([
            'manager_notes' => 'required|string|max:500',
        ]);

        if (!$session->isPendingManagerApproval()) {
            return back()->with('error', 'Sesi ini tidak dalam status menunggu persetujuan.');
        }

        $session->update([
            'status' => ExamSession::STATUS_REJECTED,
            'manager_decision' => ExamSession::DECISION_REJECTED,
            'manager_notes' => $validated['manager_notes'],
            'decided_by' => Auth::id(),
            'decided_at' => now(),
        ]);

        return back()->with('success', "Kenaikan level karyawan {$session->employee->name} telah DITOLAK.");
    }
}
