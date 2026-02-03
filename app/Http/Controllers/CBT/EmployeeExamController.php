<?php

namespace App\Http\Controllers\CBT;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\ExamAnswer;
use App\Models\ExamSession;
use App\Models\Question;
use App\Models\Skill;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 * EmployeeExamController
 * 
 * Controller for employee-facing CBT exam features.
 * Handles: taking exams, viewing results, my competencies.
 */
class EmployeeExamController extends Controller
{
    /**
     * Dashboard - Show employee's assigned exams and competencies.
     */
    public function dashboard()
    {
        $user = Auth::user();
        $employee = $user->employee;

        if (!$employee) {
            return redirect()->route('dashboard')
                ->with('error', 'Akun Anda tidak terhubung dengan data karyawan.');
        }

        // Get assigned exams (pending)
        $pendingExams = ExamSession::with(['exam.skill'])
            ->where('employee_nik', $employee->nik)
            ->whereIn('status', [
                ExamSession::STATUS_ASSIGNED,
                ExamSession::STATUS_STARTED,
            ])
            ->latest()
            ->get();

        // Get completed exams
        $completedExams = ExamSession::with(['exam.skill', 'verifier'])
            ->where('employee_nik', $employee->nik)
            ->whereIn('status', [
                ExamSession::STATUS_SUBMITTED,
                ExamSession::STATUS_VERIFIED_PASS,
                ExamSession::STATUS_VERIFIED_FAIL,
            ])
            ->latest()
            ->take(10)
            ->get();

        // Get current competencies
        $competencies = $employee->competencies()
            ->with(['skill', 'verifier'])
            ->get();

        return view('cbt.employee.dashboard', compact(
            'employee',
            'pendingExams',
            'completedExams',
            'competencies'
        ));
    }

    /**
     * Show exam details before starting.
     */
    public function showExam(ExamSession $session)
    {
        $this->authorizeSession($session);

        if (!in_array($session->status, [
            ExamSession::STATUS_ASSIGNED,
            ExamSession::STATUS_STARTED
        ])) {
            return redirect()->route('cbt.employee.dashboard')
                ->with('error', 'Ujian ini tidak tersedia untuk dikerjakan.');
        }

        $session->load(['exam.skill', 'exam.questions']);

        return view('cbt.employee.exam-info', compact('session'));
    }

    /**
     * Show exam info page (for employees to view and register for an exam).
     */
    public function examInfo(Exam $exam)
    {
        $user = Auth::user();
        $employee = $user->employee;

        if (!$employee) {
            return redirect()->route('dashboard')
                ->with('error', 'Akun Anda tidak terhubung dengan data karyawan.');
        }

        // Check if employee already has a pending session for this exam
        $existingSession = ExamSession::where('employee_nik', $employee->nik)
            ->where('exam_id', $exam->id)
            ->whereIn('status', [ExamSession::STATUS_ASSIGNED, ExamSession::STATUS_STARTED])
            ->first();

        if ($existingSession) {
            return redirect()->route('cbt.employee.show', $existingSession);
        }

        $exam->load(['skill', 'questions']);

        return view('cbt.employee.exam-preview', compact('exam', 'employee'));
    }

    /**
     * Register employee for an exam (create a new session).
     */
    public function registerForExam(Exam $exam)
    {
        $user = Auth::user();
        $employee = $user->employee;

        if (!$employee) {
            return redirect()->route('dashboard')
                ->with('error', 'Akun Anda tidak terhubung dengan data karyawan.');
        }

        // Check if employee already has a pending session for this exam
        $existingSession = ExamSession::where('employee_nik', $employee->nik)
            ->where('exam_id', $exam->id)
            ->whereIn('status', [ExamSession::STATUS_ASSIGNED, ExamSession::STATUS_STARTED])
            ->first();

        if ($existingSession) {
            return redirect()->route('cbt.employee.show', $existingSession);
        }

        // Check if exam is published
        if (!$exam->is_published) {
            return redirect()->route('cbt.employee.dashboard')
                ->with('error', 'Ujian ini tidak tersedia.');
        }

        // Check employee's current skill level
        $competency = $employee->competencies()
            ->where('skill_id', $exam->skill_id)
            ->first();
        
        $currentLevel = $competency ? $competency->level : 0;
        
        // Employee must be exactly one level below target to take exam
        $requiredLevel = $exam->target_level - 1;
        if ($currentLevel != $requiredLevel) {
            return redirect()->route('cbt.employee.dashboard')
                ->with('error', "Anda belum memenuhi syarat untuk ujian ini. Level Anda saat ini: {$currentLevel}, diperlukan: Level {$requiredLevel}.");
        }

        // Create new exam session
        $session = ExamSession::create([
            'exam_id' => $exam->id,
            'employee_nik' => $employee->nik,
            'status' => ExamSession::STATUS_ASSIGNED,
        ]);

        return redirect()->route('cbt.employee.show', $session)
            ->with('success', 'Anda berhasil mendaftar untuk ujian ini.');
    }

    /**
     * Start exam - mark session as started and redirect to questions.
     */
    public function startExam(ExamSession $session)
    {
        $this->authorizeSession($session);

        if ($session->status === ExamSession::STATUS_ASSIGNED) {
            $session->update([
                'status' => ExamSession::STATUS_STARTED,
                'started_at' => now(),
            ]);
        }

        return redirect()->route('cbt.employee.take', $session);
    }

    /**
     * Take exam - show questions and answer form.
     */
    public function takeExam(ExamSession $session)
    {
        $this->authorizeSession($session);

        if ($session->status !== ExamSession::STATUS_STARTED) {
            return redirect()->route('cbt.employee.dashboard')
                ->with('error', 'Ujian belum dimulai atau sudah selesai.');
        }

        // Check if time is up
        if ($session->isTimeUp()) {
            return $this->autoSubmit($session);
        }

        $session->load(['exam.skill', 'exam.questions' => function($q) {
            $q->orderBy('exam_question.order');
        }]);

        // Get existing answers
        $answers = $session->answers->keyBy('question_id');

        return view('cbt.employee.take-exam', compact('session', 'answers'));
    }

    /**
     * Save answer (AJAX) - auto-save during exam.
     */
    public function saveAnswer(Request $request, ExamSession $session)
    {
        $this->authorizeSession($session);

        if ($session->status !== ExamSession::STATUS_STARTED) {
            return response()->json(['error' => 'Ujian tidak aktif'], 403);
        }

        $validated = $request->validate([
            'question_id' => 'required|exists:questions,id',
            'selected_answer' => 'nullable|string',
        ]);

        // Update or create answer
        $answer = ExamAnswer::updateOrCreate(
            [
                'exam_session_id' => $session->id,
                'question_id' => $validated['question_id'],
            ],
            [
                'selected_answer' => $validated['selected_answer'],
            ]
        );

        // Auto-grade if multiple choice
        $question = Question::find($validated['question_id']);
        if ($question && $question->type !== 'essay') {
            $answer->grade();
        }

        return response()->json([
            'success' => true,
            'saved_at' => now()->format('H:i:s'),
        ]);
    }

    /**
     * Submit exam - finalize all answers with simple percentage-based scoring.
     * 
     * Process:
     * 1. Validate and save all answers from request
     * 2. Auto-grade each answer (multiple choice & true/false only)
     * 3. Calculate score: (Correct Answers / Total Questions) × 100
     * 4. Update session status to 'submitted' with calculated score
     * 5. Compare against passing_score threshold (KKM)
     */
    public function submitExam(Request $request, ExamSession $session)
    {
        $this->authorizeSession($session);

        if ($session->status !== ExamSession::STATUS_STARTED) {
            return redirect()->route('cbt.employee.dashboard')
                ->with('error', 'Ujian tidak dalam status yang dapat disubmit.');
        }

        $validated = $request->validate([
            'answers' => 'nullable|array',
            'answers.*' => 'nullable|string',
        ]);

        DB::beginTransaction();

        try {
            // Load exam with questions
            $session->load(['exam.questions']);
            
            // Get total number of questions in this exam
            $totalQuestions = $session->exam->questions()->count();
            
            // Edge case: No questions
            if ($totalQuestions === 0) {
                DB::rollBack();
                return back()->with('error', 'Ujian tidak memiliki soal.');
            }
            
            $correctAnswersCount = 0;
            
            // Process and save all answers
            if ($request->has('answers')) {
                foreach ($request->answers as $questionId => $selectedAnswer) {
                    // Fetch the question to get correct answer
                    $question = Question::find($questionId);
                    
                    if (!$question) {
                        continue; // Skip if question not found
                    }

                    // Determine if answer is correct (for non-essay questions only)
                    $isCorrect = false;

                    if ($question->type !== 'essay') {
                        // Auto-grade: Compare selected answer with correct answer
                        $isCorrect = $question->isCorrectAnswer($selectedAnswer ?? '');
                        
                        // Count correct answers
                        if ($isCorrect) {
                            $correctAnswersCount++;
                        }
                    }

                    // Save answer to database
                    ExamAnswer::updateOrCreate(
                        [
                            'exam_session_id' => $session->id,
                            'question_id' => $questionId,
                        ],
                        [
                            'selected_answer' => $selectedAnswer,
                            'is_correct' => $isCorrect,
                            'score_earned' => $isCorrect ? 1 : 0, // Just for tracking
                        ]
                    );
                }
            }

            // Calculate final score based on percentage
            // Formula: (Correct Answers / Total Questions) × 100
            $finalScore = round(($correctAnswersCount / $totalQuestions) * 100, 2);

            // Check if exam contains only non-essay questions (auto-verifiable)
            $hasEssayQuestions = $session->exam->questions()->where('type', 'essay')->exists();
            
            // Determine status and auto-verify if no essay questions
            if ($hasEssayQuestions) {
                // Has essay questions - need admin verification
                $status = ExamSession::STATUS_SUBMITTED;
                $verifiedAt = null;
                $verifiedBy = null;
                $resultMessage = "Ujian berhasil diselesaikan! Nilai Anda: {$finalScore} ({$correctAnswersCount}/{$totalQuestions} benar). Menunggu verifikasi admin.";
            } else {
                // Only multiple choice / true-false - auto-verify
                $passingScore = $session->exam->passing_score;
                $isPassed = $finalScore >= $passingScore;
                
                $status = $isPassed ? ExamSession::STATUS_VERIFIED_PASS : ExamSession::STATUS_VERIFIED_FAIL;
                $verifiedAt = now();
                $verifiedBy = null; // System auto-verify
                
                // Update competency level if passed
                if ($isPassed) {
                    $this->updateEmployeeSkillLevel(
                        $session->employee_nik,
                        $session->exam->skill_id,
                        $session->exam->target_level
                    );
                    $resultMessage = "Selamat! Ujian berhasil diselesaikan dengan nilai {$finalScore}. Anda LULUS dan level kompetensi Anda telah diperbarui!";
                } else {
                    $resultMessage = "Ujian berhasil diselesaikan dengan nilai {$finalScore}. Maaf, Anda TIDAK LULUS (KKM: {$passingScore}). Silakan coba lagi.";
                }
            }

            // Update session with final results
            $session->update([
                'status' => $status,
                'submitted_at' => now(),
                'finished_at' => now(),
                'score' => $finalScore,
                'verified_at' => $verifiedAt,
                'verified_by' => $verifiedBy,
            ]);

            DB::commit();

            // Redirect to result page with success message
            return redirect()
                ->route('cbt.employee.result', $session)
                ->with('success', $resultMessage);

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal submit ujian: ' . $e->getMessage());
        }
    }

    /**
     * Auto-submit when time is up.
     */
    private function autoSubmit(ExamSession $session)
    {
        // Grade all unanswered questions as 0
        $session->load('answers');
        $answeredQuestionIds = $session->answers->pluck('question_id')->toArray();

        // Get all exam questions
        $examQuestionIds = $session->exam->questions->pluck('id')->toArray();
        $unansweredIds = array_diff($examQuestionIds, $answeredQuestionIds);

        // Create empty answers for unanswered questions
        foreach ($unansweredIds as $questionId) {
            ExamAnswer::create([
                'exam_session_id' => $session->id,
                'question_id' => $questionId,
                'selected_answer' => null,
                'is_correct' => false,
                'score_earned' => 0,
            ]);
        }

        // Calculate and save score
        $score = $session->calculateScore();
        
        // Check if exam contains only non-essay questions (auto-verifiable)
        $hasEssayQuestions = $session->exam->questions()->where('type', 'essay')->exists();
        
        if ($hasEssayQuestions) {
            // Has essay questions - need admin verification
            $status = ExamSession::STATUS_SUBMITTED;
            $verifiedAt = null;
            $verifiedBy = null;
        } else {
            // Only multiple choice / true-false - auto-verify
            $passingScore = $session->exam->passing_score;
            $isPassed = $score >= $passingScore;
            
            $status = $isPassed ? ExamSession::STATUS_VERIFIED_PASS : ExamSession::STATUS_VERIFIED_FAIL;
            $verifiedAt = now();
            $verifiedBy = null;
            
            // Update competency level if passed
            if ($isPassed) {
                $this->updateEmployeeSkillLevel(
                    $session->employee_nik,
                    $session->exam->skill_id,
                    $session->exam->target_level
                );
            }
        }

        $session->update([
            'status' => $status,
            'finished_at' => now(),
            'submitted_at' => now(),
            'score' => $score,
            'verified_at' => $verifiedAt,
            'verified_by' => $verifiedBy,
        ]);

        return redirect()
            ->route('cbt.employee.result', $session)
            ->with('warning', 'Waktu ujian habis. Jawaban Anda telah disimpan otomatis.');
    }

    /**
     * Show exam result.
     */
    public function showResult(ExamSession $session)
    {
        $this->authorizeSession($session);

        if (!in_array($session->status, [
            ExamSession::STATUS_SUBMITTED,
            ExamSession::STATUS_VERIFIED_PASS,
            ExamSession::STATUS_VERIFIED_FAIL,
        ])) {
            return redirect()->route('cbt.employee.dashboard')
                ->with('error', 'Hasil ujian belum tersedia.');
        }

        $session->load([
            'exam.skill',
            'exam.questions' => fn($q) => $q->orderBy('exam_question.order'),
            'answers.question',
            'verifier',
        ]);

        return view('cbt.employee.result', compact('session'));
    }

    /**
     * My competencies page.
     */
    public function myCompetencies()
    {
        $user = Auth::user();
        $employee = $user->employee;

        if (!$employee) {
            return redirect()->route('dashboard')
                ->with('error', 'Akun Anda tidak terhubung dengan data karyawan.');
        }

        $competencies = $employee->competencies()
            ->with(['skill', 'verifiedBy'])
            ->get();

        // Get skills that employee doesn't have competency for yet
        $existingSkillIds = $competencies->pluck('skill_id')->toArray();
        $availableSkills = Skill::whereNotIn('id', $existingSkillIds)
            ->where('is_active', true)
            ->get();

        return view('cbt.employee.competencies', compact('employee', 'competencies', 'availableSkills'));
    }

    /**
     * Exam history.
     */
    public function history(Request $request)
    {
        $user = Auth::user();
        $employee = $user->employee;

        if (!$employee) {
            return redirect()->route('dashboard')
                ->with('error', 'Akun Anda tidak terhubung dengan data karyawan.');
        }

        $query = ExamSession::with(['exam.skill', 'verifiedBy'])
            ->where('employee_nik', $employee->nik);

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $sessions = $query->latest()->paginate(20);

        // Calculate statistics
        $stats = [
            'total' => ExamSession::where('employee_nik', $employee->nik)->count(),
            'passed' => ExamSession::where('employee_nik', $employee->nik)
                ->where('status', ExamSession::STATUS_VERIFIED_PASS)->count(),
            'failed' => ExamSession::where('employee_nik', $employee->nik)
                ->where('status', ExamSession::STATUS_VERIFIED_FAIL)->count(),
            'pending' => ExamSession::where('employee_nik', $employee->nik)
                ->where('status', ExamSession::STATUS_SUBMITTED)->count(),
        ];

        return view('cbt.employee.history', compact('sessions', 'stats'));
    }

    /**
     * Authorize that current user owns this session.
     */
    private function authorizeSession(ExamSession $session): void
    {
        $user = Auth::user();
        
        if (!$user->employee || $session->employee_nik !== $user->employee->nik) {
            abort(403, 'Anda tidak memiliki akses ke sesi ujian ini.');
        }
    }
}
