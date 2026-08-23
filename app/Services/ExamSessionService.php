<?php

namespace App\Services;

use App\Events\DashboardStatsUpdated;
use App\Events\SessionStatusUpdated;
use App\Models\ExamAnswer;
use App\Models\ExamSession;
use App\Models\EmployeeCompetency;
use App\Models\EmployeeCompetencyHistory;
use App\Models\ManagerAssessment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

/**
 * ExamSessionService
 *
 * Business logic siklus verifikasi admin & approval manajer.
 * R3a: badan method dipindahkan VERBATIM dari ExamSessionController
 * agar perilaku 100% identik (gate: feature test suite CBT).
 */
class ExamSessionService
{
    /**
     * Verifikasi sesi oleh supervisor/admin: koreksi skor esai,
     * hitung ulang skor akhir, lalu set status verified_pass/fail.
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
    public function recalculateScore(ExamSession $session): void
    {
        $score = $session->calculateScore();
        $session->update(['score' => $score]);
    }

    /**
     * Update employee's skill level after passing exam.
     */
    public function updateEmployeeSkillLevel(ExamSession $session): void
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
    /**
     * Manager menyetujui kenaikan level (skor kualitatif >= 7).
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
     * Manager menolak kenaikan level.
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
}
