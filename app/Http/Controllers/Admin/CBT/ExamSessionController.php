<?php

namespace App\Http\Controllers\Admin\CBT;

use App\Events\DashboardStatsUpdated;
use App\Events\SessionStatusUpdated;
use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\EmployeeCompetency;
use App\Models\EmployeeCompetencyHistory;
use App\Models\Exam;
use App\Models\ExamAnswer;
use App\Models\ExamSession;
use App\Models\ManagerAssessment;
use App\Models\Question;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

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
        $ongoingStatuses = [
            ExamSession::STATUS_ASSIGNED,
            ExamSession::STATUS_STARTED,
        ];

        $completedStatuses = [
            ExamSession::STATUS_SUBMITTED,
            ExamSession::STATUS_VERIFIED_PASS,
            ExamSession::STATUS_VERIFIED_FAIL,
            ExamSession::STATUS_APPROVED,
            ExamSession::STATUS_REJECTED,
        ];

        $tab = $request->get('tab', 'berlangsung');
        $tabStatuses = $tab === 'selesai' ? $completedStatuses : $ongoingStatuses;

        $query = ExamSession::with(['exam.skill', 'employee', 'verifier'])
            ->whereIn('status', $tabStatuses);

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

        $ongoingCount = ExamSession::whereIn('status', $ongoingStatuses)->count();
        $completedCount = ExamSession::whereIn('status', $completedStatuses)->count();

        return view('admin.cbt.sessions.index', compact('sessions', 'tab', 'ongoingCount', 'completedCount'));
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
            ->map(function ($set) {
                // Load skill for division info
                $set->skill = \App\Models\Skill::with('division')->find($set->skill_id);

                return $set;
            });

        $employees = Employee::with(['division', 'position'])
            ->where('status', 'Aktif')
            ->orderBy('name')
            ->get();

        $divisions = \App\Models\Division::orderBy('name')->get();

        $exams = Exam::with('skill')
            ->where('status', 'active')
            ->where('is_published', true)
            ->orderBy('title')
            ->get();

        return view('admin.cbt.sessions.create', compact('questionSets', 'employees', 'divisions', 'exams'));
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

            // Ujian tidak boleh mencampur esai dengan PG / benar-salah
            $hasEssay = $questions->contains(fn ($q) => $q->type === 'essay');
            $hasAuto = $questions->contains(fn ($q) => in_array($q->type, ['multiple_choice', 'true_false']));
            if ($hasEssay && $hasAuto) {
                return back()
                    ->withInput()
                    ->with('error', 'Ujian tidak boleh mencampur soal esai dengan pilihan ganda / benar-salah. Pilih set dengan tipe yang sama.');
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

            // Reuse existing exam if the same composition already exists
            $questionIds = $questions->pluck('id')->sort()->values()->toArray();
            $existingExam = Exam::where('skill_id', $skillId)
                ->where('target_level', $targetLevel)
                ->where('title', $examTitle)
                ->where('status', 'active')
                ->where('is_published', true)
                ->with('examQuestions')
                ->get()
                ->first(fn ($exam) => $exam->examQuestions->pluck('question_id')->sort()->values()->toArray() === $questionIds);

            $reused = false;
            if ($existingExam) {
                $exam = $existingExam;
                $reused = true;
            } else {
                // Create exam automatically
                $exam = Exam::create([
                    'skill_id' => $skillId,
                    'title' => $examTitle,
                    'description' => 'Ujian dibuat otomatis dari Sesi Ujian ('.count($validated['question_set_ids']).' set soal)',
                    'target_level' => $targetLevel,
                    'passing_score' => $validated['passing_score'],
                    'duration_minutes' => $validated['duration_minutes'],
                    'is_published' => true,
                    'status' => 'active',
                ]);

                // Attach all questions from selected sets to exam
                $weights = $questions->pluck('default_weight', 'id')
                    ->map(fn ($weight) => $weight ?? 1)
                    ->all();

                $exam->attachQuestionsWithSnapshot($questions->pluck('id')->all(), $weights);
            }

            // Assign exam to employees
            $assigned = 0;
            $skipped = 0;
            $notEligibleList = [];
            $skippedList = [];

            foreach ($validated['employee_niks'] as $nik) {
                // Get employee with competencies
                $employee = Employee::with('competencies')->where('nik', $nik)->first();

                if (! $employee) {
                    $skippedList[] = [
                        'name' => 'NIK: '.$nik,
                        'nik' => $nik,
                        'reason' => 'Karyawan tidak ditemukan',
                    ];
                    $skipped++;

                    continue;
                }

                // Check employee's current level for this skill
                $competency = $employee->competencies->where('skill_id', $skillId)->first();
                $currentLevel = $competency ? $competency->level : 0;

                // Validation: Employee must be exactly one level below the target exam level.
                // Ladder: level 0 -> exam 1, level 1 -> exam 2, level 2 -> exam 3, level 3 -> exam 4.
                // Level 4 (Expert, max) is allowed to access all levels 1-4.
                $requiredLevel = $targetLevel - 1;
                $isEligible = $currentLevel === EmployeeCompetency::LEVEL_EXPERT || $currentLevel === $requiredLevel;
                if (! $isEligible) {
                    $notEligibleList[] = [
                        'name' => $employee->name,
                        'nik' => $employee->nik,
                        'current_level' => $currentLevel,
                        'required_level' => $requiredLevel,
                        'target_level' => $targetLevel,
                    ];

                    continue;
                }

                // Check if employee already has pending/started session for this exam
                $existingSession = ExamSession::where('exam_id', $exam->id)
                    ->where('employee_nik', $nik)
                    ->whereIn('status', [
                        ExamSession::STATUS_ASSIGNED,
                        ExamSession::STATUS_STARTED,
                    ])
                    ->exists();

                if ($existingSession) {
                    $skippedList[] = [
                        'name' => $employee->name,
                        'nik' => $employee->nik,
                        'reason' => 'Sudah memiliki sesi ujian aktif',
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

            $sessions = ExamSession::where('exam_id', $exam->id)
                ->whereIn('employee_nik', $validated['employee_niks'])
                ->get();
            foreach ($sessions as $s) {
                SessionStatusUpdated::dispatch($s, 'assigned');
            }
            DashboardStatsUpdated::dispatch();

            $totalSoal = $questions->count();
            $totalSets = count($validated['question_set_ids']);
            $message = $reused
                ? "Ujian '{$exam->title}' sudah pernah dibuat - {$assigned} karyawan ditambahkan ke ujian yang sama. KKM & durasi mengikuti pengaturan ujian yang sudah ada."
                : "Ujian '{$exam->title}' berhasil dibuat dengan {$totalSets} set soal ({$totalSoal} soal). {$assigned} karyawan ditugaskan.";
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
                ->with('error', 'Gagal membuat ujian: '.$e->getMessage());
        }
    }

    /**
     * Show edit form for a session (returns JSON for modal).
     */
    public function edit(ExamSession $session)
    {
        $session->load(['exam.skill', 'employee.division']);

        return response()->json([
            'id' => $session->id,
            'employee_name' => $session->employee->name ?? $session->employee_nik,
            'exam_title' => $session->exam->title ?? '-',
            'deadline_at' => $session->deadline_at?->format('Y-m-d\TH:i'),
            'scheduled_start_at' => $session->scheduled_start_at?->format('Y-m-d\TH:i'),
            'passing_score' => $session->exam->passing_score ?? 70,
            'duration_minutes' => $session->exam->duration_minutes ?? 60,
            'status' => $session->status,
        ]);
    }

    /**
     * Update session settings (deadline, KKM, duration).
     */
    public function update(Request $request, ExamSession $session)
    {
        if (! in_array($session->status, [ExamSession::STATUS_ASSIGNED, ExamSession::STATUS_STARTED])) {
            return back()->with('error', 'Sesi yang sudah selesai atau diverifikasi tidak dapat diedit!');
        }

        $validated = $request->validate([
            'deadline_at' => 'required|date',
            'passing_score' => 'required|integer|min:0|max:100',
            'duration_minutes' => 'required|integer|min:5|max:300',
        ]);

        $deadline = Carbon::parse($validated['deadline_at']);

        // Read directly from request to avoid nullable validator converting empty -> null
        $schedStartRaw = $request->input('scheduled_start_at');
        $scheduledStart = ! empty($schedStartRaw) ? Carbon::parse($schedStartRaw) : null;

        DB::table('exam_sessions')->where('id', $session->id)->update([
            'deadline_at' => $deadline->toDateTimeString(),
            'scheduled_start_at' => $scheduledStart?->toDateTimeString(),
            'updated_at' => now()->toDateTimeString(),
        ]);

        DB::table('exams')->where('id', $session->exam_id)->update([
            'passing_score' => $validated['passing_score'],
            'duration_minutes' => $validated['duration_minutes'],
            'updated_at' => now()->toDateTimeString(),
        ]);

        return back()->with('success', 'Pengaturan sesi ujian berhasil diperbarui!');
    }

    /**
     * Display the specified session with answers.
     */
    public function show(ExamSession $session)
    {
        $session->load([
            'exam.skill',
            'exam.examQuestions',
            'employee.division',
            'employee.position',
            'answers.question',
            'verifier',
            'manager',
        ]);

        return view('admin.cbt.sessions.show', compact('session'));
    }

    /**
     * Verify and approve/reject exam session.
     */
    public function verify(Request $request, ExamSession $session)
    {
        $validated = $request->validate([
            'action' => 'required|in:approve,reject',
            'notes' => 'nullable|string|max:500',
            'essay_score_*' => 'nullable|integer|min:0',
        ]);

        if (! in_array($session->status, [ExamSession::STATUS_SUBMITTED])) {
            return back()->with('error', 'Sesi ujian tidak dalam status yang dapat diverifikasi!');
        }

        DB::beginTransaction();

        try {
            // Process essay grading first
            foreach ($request->all() as $key => $value) {
                if (strpos($key, 'essay_score_') === 0) {
                    $questionId = str_replace('essay_score_', '', $key);
                    $answer = ExamAnswer::where('exam_session_id', $session->id)
                        ->where('question_id', $questionId)
                        ->first();

                    if ($answer && $value !== null) {
                        $question = $session->exam->examQuestions()->where('question_id', $questionId)->first();
                        $maxScore = (int) ($question?->weight ?? 0);

                        if ((int) $value > $maxScore) {
                            throw ValidationException::withMessages([
                                $key => "Nilai essay tidak boleh melebihi bobot soal ({$maxScore}).",
                            ]);
                        }

                        $answer->update([
                            'score_earned' => (int) $value,
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

            SessionStatusUpdated::dispatch($session->fresh(), 'verified');
            DashboardStatsUpdated::dispatch();

            $statusText = $passed ? 'LULUS' : 'TIDAK LULUS';

            return back()->with('success', "Sesi ujian berhasil diverifikasi: {$statusText}");

        } catch (ValidationException $e) {
            DB::rollBack();

            throw $e;
        } catch (\Exception $e) {
            DB::rollBack();

            return back()->with('error', 'Gagal memverifikasi: '.$e->getMessage());
        }
    }

    /**
     * Recalculate session score using weighted scoring (delegates to model)
     */
    private function recalculateScore(ExamSession $session): void
    {
        $score = $session->calculateScore();
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

        // Get existing level before update
        $existing = EmployeeCompetency::where('employee_nik', $employee->nik)
            ->where('skill_id', $skill->id)
            ->first();

        $previousLevel = $existing?->level;

        // Update or create employee competency
        $competency = EmployeeCompetency::updateOrCreate(
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

        // Save history record
        EmployeeCompetencyHistory::create([
            'employee_competency_id' => $competency->id,
            'previous_level' => $previousLevel,
            'new_level' => $targetLevel,
            'change_type' => $previousLevel === null ? 'initial' : 'up',
            'change_source' => 'exam_pass',
            'changed_by' => Auth::id(),
            'exam_session_id' => $session->id,
            'notes' => $competency->notes,
            'created_at' => now(),
        ]);
    }

    /**
     * Cancel/revoke exam session.
     */
    public function cancel(ExamSession $session)
    {
        if (! in_array($session->status, [ExamSession::STATUS_ASSIGNED])) {
            return back()->with('error', 'Hanya sesi dengan status "Ditugaskan" yang dapat dibatalkan!');
        }

        $session->delete();

        DashboardStatsUpdated::dispatch();

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

        return view('admin.cbt.sessions.pending', compact('sessions'));
    }

    /**
     * Bulk assign exam to employees by division.
     *
     * Creates sessions for an existing exam on all eligible (Aktif) employees
     * of a division. Eligibility: current competency level must be exactly
     * one level below the exam's target level, and no active session exists.
     */
    public function bulkAssignByDivision(Request $request)
    {
        $validated = $request->validate([
            'exam_id' => 'required|exists:exams,id',
            'division_id' => 'required|exists:divisions,id',
            'deadline_at' => 'required|date|after:now',
            'scheduled_start_at' => 'nullable|date|before:deadline_at',
        ]);

        $exam = Exam::with('examQuestions')->findOrFail($validated['exam_id']);

        $skillId = $exam->examQuestions->first()?->skill_id ?? $exam->skill_id;
        $targetLevel = $exam->target_level;
        $requiredLevel = $targetLevel - 1;

        $employees = Employee::with('competencies')
            ->where('division_id', $validated['division_id'])
            ->where('status', 'Aktif')
            ->get();

        if ($employees->isEmpty()) {
            return back()->with('error', 'Tidak ada karyawan aktif di divisi tersebut!');
        }

        $assigned = 0;
        $notEligibleList = [];
        $skippedList = [];
        $createdSessions = [];

        foreach ($employees as $employee) {
            $competency = $employee->competencies->where('skill_id', $skillId)->first();
            $currentLevel = $competency ? $competency->level : 0;

            if ($currentLevel != $requiredLevel) {
                $notEligibleList[] = [
                    'name' => $employee->name,
                    'nik' => $employee->nik,
                    'current_level' => $currentLevel,
                    'required_level' => $requiredLevel,
                    'target_level' => $targetLevel,
                ];

                continue;
            }

            $existingSession = ExamSession::where('exam_id', $exam->id)
                ->where('employee_nik', $employee->nik)
                ->whereIn('status', [
                    ExamSession::STATUS_ASSIGNED,
                    ExamSession::STATUS_STARTED,
                ])
                ->exists();

            if ($existingSession) {
                $skippedList[] = [
                    'name' => $employee->name,
                    'nik' => $employee->nik,
                    'reason' => 'Sudah memiliki sesi ujian aktif',
                ];

                continue;
            }

            $createdSessions[] = ExamSession::create([
                'exam_id' => $exam->id,
                'employee_nik' => $employee->nik,
                'status' => ExamSession::STATUS_ASSIGNED,
                'deadline_at' => Carbon::parse($validated['deadline_at']),
                'scheduled_start_at' => ! empty($validated['scheduled_start_at'])
                    ? Carbon::parse($validated['scheduled_start_at'])
                    : null,
            ]);
            $assigned++;
        }

        foreach ($createdSessions as $s) {
            SessionStatusUpdated::dispatch($s, 'assigned');
        }
        DashboardStatsUpdated::dispatch();

        $message = "{$assigned} karyawan ditugaskan untuk ujian '{$exam->title}'.";
        if (count($notEligibleList) > 0) {
            $message .= ' ('.count($notEligibleList).' karyawan tidak memenuhi syarat - harus Level '.$requiredLevel.')';
        }
        if (count($skippedList) > 0) {
            $message .= ' ('.count($skippedList).' dilewati karena sudah memiliki sesi aktif)';
        }

        return redirect()
            ->route('cbt.admin.sessions.index')
            ->with('success', $message)
            ->with('notEligibleList', $notEligibleList)
            ->with('skippedList', $skippedList)
            ->with('assignedCount', $assigned);
    }

    // ============================================
    // MANAGER APPROVAL METHODS
    // ============================================

    /**
     * Display sessions pending manager approval, or history (with tab param).
     */
    public function pendingApproval(Request $request)
    {
        $tab = $request->get('tab', 'pending');

        if ($tab === 'history') {
            $sessions = ExamSession::with([
                'exam.skill',
                'employee.division',
                'employee.position',
                'manager',
                'managerAssessment',
                'verifier',
            ])
                ->whereIn('status', [ExamSession::STATUS_APPROVED, ExamSession::STATUS_REJECTED])
                ->whereNotNull('manager_decision')
                ->whereIn('manager_decision', [ExamSession::DECISION_APPROVED, ExamSession::DECISION_REJECTED])
                ->latest('decided_at')
                ->paginate(20);
        } else {
            $sessions = ExamSession::with(['exam.skill', 'exam.questions', 'employee.division', 'employee.position', 'verifier', 'managerAssessment'])
                ->where('status', ExamSession::STATUS_VERIFIED_PASS)
                ->where(function ($q) {
                    $q->where('manager_decision', ExamSession::DECISION_PENDING)
                        ->orWhereNull('manager_decision');
                })
                ->latest('verified_at')
                ->paginate(20);
        }

        return view('admin.cbt.sessions.pending-approval', compact('sessions', 'tab'));
    }

    /**
     * Redirect to pending-approval with history tab.
     */
    public function approvalHistory(Request $request)
    {
        return redirect()->route('cbt.admin.sessions.pending-approval', array_merge($request->query(), ['tab' => 'history']));
    }

    /**
     * Display qualitative assessment form for manager.
     */
    public function qualitativeAssessment(ExamSession $session)
    {
        if (! $session->isPendingManagerApproval()) {
            return redirect()->route('cbt.admin.sessions.pending-approval')
                ->with('error', 'Sesi ini tidak dalam status menunggu persetujuan.');
        }

        $session->load([
            'exam.skill',
            'exam.examQuestions',
            'employee.division',
            'employee.position',
            'verifier',
            'manager',
            'managerAssessment',
        ]);

        return view('admin.cbt.sessions.qualitative-assessment', compact('session'));
    }

    /**
     * Manager approves level upgrade.
     */
    public function approveLevel(Request $request, ExamSession $session)
    {
        $tidakMemenuhiCount = $this->countTidakMemenuhi($request);

        $rules = [
            'sop_understanding' => 'required|in:memenuhi,perlu_perbaikan,tidak_memenuhi',
            'competency_application' => 'required|in:memenuhi,perlu_perbaikan,tidak_memenuhi',
            'independence' => 'required|in:memenuhi,perlu_perbaikan,tidak_memenuhi',
            'problem_solving' => 'required|in:memenuhi,perlu_perbaikan,tidak_memenuhi',
            'readiness' => 'required|in:memenuhi,perlu_perbaikan,tidak_memenuhi',
            'manager_notes' => $tidakMemenuhiCount > 0 ? 'required|string|max:1000' : 'nullable|string|max:1000',
        ];

        $validated = $request->validate($rules);

        if (! $session->isPendingManagerApproval()) {
            return back()->with('error', 'Sesi ini tidak dalam status menunggu persetujuan.');
        }

        $qualitativeScore = ManagerAssessment::scoreFrom($request->only(ManagerAssessment::CRITERIA_FIELDS));

        if ($qualitativeScore < ManagerAssessment::MIN_APPROVAL_SCORE) {
            throw ValidationException::withMessages([
                'kriteria' => 'Kualifikasi belum memenuhi ambang minimal untuk menyetujui. Kenaikan level harus DITOLAK.',
            ]);
        }

        // Save assessment data if any
        $this->saveAssessmentData($request, $session);

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
            SessionStatusUpdated::dispatch($session->fresh(), 'approved');
            DashboardStatsUpdated::dispatch();

            return redirect()->route('cbt.admin.sessions.pending-approval')
                ->with('success', "Kenaikan level karyawan {$session->employee->name} telah DISETUJUI.");
        } catch (\Exception $e) {
            DB::rollBack();

            return back()->with('error', 'Gagal menyetujui: '.$e->getMessage());
        }
    }

    /**
     * Count how many criteria have 'tidak_memenuhi' value.
     */
    private function countTidakMemenuhi(Request $request): int
    {
        $fields = ['sop_understanding', 'competency_application', 'independence', 'problem_solving', 'readiness'];
        $count = 0;
        foreach ($fields as $field) {
            if ($request->input($field) === 'tidak_memenuhi') {
                $count++;
            }
        }

        return $count;
    }

    /**
     * Manager rejects level upgrade.
     */
    public function rejectLevel(Request $request, ExamSession $session)
    {
        $validated = $request->validate([
            'manager_notes' => 'required|string|max:1000',
        ]);

        if (! $session->isPendingManagerApproval()) {
            return back()->with('error', 'Sesi ini tidak dalam status menunggu persetujuan.');
        }

        // Save assessment data if any
        $this->saveAssessmentData($request, $session);

        $session->update([
            'status' => ExamSession::STATUS_REJECTED,
            'manager_decision' => ExamSession::DECISION_REJECTED,
            'manager_notes' => $validated['manager_notes'],
            'decided_by' => Auth::id(),
            'decided_at' => now(),
        ]);

        SessionStatusUpdated::dispatch($session->fresh(), 'rejected');
        DashboardStatsUpdated::dispatch();

        return redirect()->route('cbt.admin.sessions.pending-approval')
            ->with('success', "Kenaikan level karyawan {$session->employee->name} telah DITOLAK.");
    }

    /**
     * Save assessment data from request.
     */
    private function saveAssessmentData(Request $request, ExamSession $session): ?ManagerAssessment
    {
        $assessmentRules = [
            'assessment_method' => 'nullable|in:interview,observation,both',
            'sop_understanding' => 'nullable|in:memenuhi,perlu_perbaikan,tidak_memenuhi',
            'competency_application' => 'nullable|in:memenuhi,perlu_perbaikan,tidak_memenuhi',
            'independence' => 'nullable|in:memenuhi,perlu_perbaikan,tidak_memenuhi',
            'problem_solving' => 'nullable|in:memenuhi,perlu_perbaikan,tidak_memenuhi',
            'readiness' => 'nullable|in:memenuhi,perlu_perbaikan,tidak_memenuhi',
            'verification_date' => 'nullable|date',
        ];

        $hasAssessmentData = $request->hasAny(array_keys($assessmentRules));
        if (! $hasAssessmentData) {
            return null;
        }

        $assessmentData = $request->validate($assessmentRules);
        $assessmentData['created_by'] = Auth::id();

        return ManagerAssessment::updateOrCreate(
            ['exam_session_id' => $session->id],
            $assessmentData
        );
    }
}
