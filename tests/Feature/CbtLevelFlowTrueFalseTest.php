<?php

namespace Tests\Feature;

use App\Models\ExamSession;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\BuildsCbtScenario;
use Tests\Concerns\BuildsLevelFlow;
use Tests\TestCase;

/**
 * Skenario utama Benar/Salah (True/False) untuk Level 1 s/d Level 4.
 *
 * Nilai dihitung otomatis oleh sistem: (benar / total soal) x 100.
 * 12 skenario: 4 level x (di bawah Threshold, lulus-ditolak, lulus-disetujui).
 */
class CbtLevelFlowTrueFalseTest extends TestCase
{
    use BuildsCbtScenario, BuildsLevelFlow, RefreshDatabase;

    private const THRESHOLD = 70;

    private function tfExam(array $scenario, int $targetLevel, string $suffix): \App\Models\Exam
    {
        return $this->buildLevelExam($scenario, $targetLevel, 'tf', "QS-L{$targetLevel}-TF-{$suffix}", self::THRESHOLD);
    }

    private function assertBelowThresholdResult(array $scenario, ExamSession $session): void
    {
        $this->assertEquals(ExamSession::STATUS_VERIFIED_FAIL, $session->status);
        $this->assertEquals(50, $session->score);
        $this->assertInManagerQueue($scenario, $session, false);
        $this->assertDatabaseMissing('manager_assessments', ['exam_session_id' => $session->id]);
    }

    private function assertRejectedResult(array $scenario, ExamSession $session, int $targetLevel): void
    {
        $this->assertEquals(ExamSession::STATUS_REJECTED, $session->status);
        $this->assertEquals(ExamSession::DECISION_REJECTED, $session->manager_decision);
        $this->assertNotNull($session->manager_notes);
        $this->assertEmployeeCompetencyLevel($scenario, $targetLevel - 1);
        $this->assertInApprovalHistory($scenario, $session, true);
    }

    private function assertApprovedResult(array $scenario, ExamSession $session, int $targetLevel): void
    {
        $this->assertEquals(ExamSession::STATUS_APPROVED, $session->status);
        $this->assertEquals(ExamSession::DECISION_APPROVED, $session->manager_decision);
        $this->assertEmployeeCompetencyLevel($scenario, $targetLevel);
        $this->assertLevelUpHistory($scenario, $session, $targetLevel === 1 ? null : $targetLevel - 1, $targetLevel);
        $this->linkSkillToDivision($scenario);
        $this->assertMatrixShowsEmployee($scenario, $scenario['employee']->name);
        $this->assertInApprovalHistory($scenario, $session, true);
    }

    // ============================================
    // LEVEL 1
    // ============================================

    public function test_level1_tf_below_threshold_not_processed(): void
    {
        $scenario = $this->buildCbtScenario();
        $this->setEmployeeLevel($scenario, 0);
        $exam = $this->tfExam($scenario, 1, 'FAIL');

        $session = $this->registerStartSubmit($scenario, $exam, $this->autoAnswers($exam, 2));

        $this->assertBelowThresholdResult($scenario, $session);
        $this->assertEmployeeCompetencyLevel($scenario, null);
    }

    public function test_level1_tf_pass_rejected_level_unchanged(): void
    {
        $scenario = $this->buildCbtScenario();
        $this->setEmployeeLevel($scenario, 0);
        $exam = $this->tfExam($scenario, 1, 'REJ');

        $session = $this->registerStartSubmit($scenario, $exam, $this->autoAnswers($exam, 3));
        $this->assertEquals(ExamSession::STATUS_VERIFIED_PASS, $session->status);
        $this->assertEquals(75, $session->score);

        $this->managerReject($scenario, $session);

        $this->assertRejectedResult($scenario, $session->refresh(), 1);
        $this->assertEmployeeCompetencyLevel($scenario, null);
    }

    public function test_level1_tf_pass_approved_matrix_updates(): void
    {
        $scenario = $this->buildCbtScenario();
        $this->setEmployeeLevel($scenario, 0);
        $exam = $this->tfExam($scenario, 1, 'OK');

        $session = $this->registerStartSubmit($scenario, $exam, $this->autoAnswers($exam, 4));
        $this->assertEquals(ExamSession::STATUS_VERIFIED_PASS, $session->status);
        $this->assertEquals(100, $session->score);

        $this->managerApprove($scenario, $session)->assertSessionHas('success');

        $this->assertApprovedResult($scenario, $session->refresh(), 1);
    }

    // ============================================
    // LEVEL 2
    // ============================================

    public function test_level2_tf_below_threshold_not_processed(): void
    {
        $scenario = $this->buildCbtScenario();
        $this->setEmployeeLevel($scenario, 1);
        $exam = $this->tfExam($scenario, 2, 'FAIL');

        $session = $this->registerStartSubmit($scenario, $exam, $this->autoAnswers($exam, 2));

        $this->assertBelowThresholdResult($scenario, $session);
        $this->assertEmployeeCompetencyLevel($scenario, 1);
    }

    public function test_level2_tf_pass_rejected_level_unchanged(): void
    {
        $scenario = $this->buildCbtScenario();
        $this->setEmployeeLevel($scenario, 1);
        $exam = $this->tfExam($scenario, 2, 'REJ');

        $session = $this->registerStartSubmit($scenario, $exam, $this->autoAnswers($exam, 3));
        $this->assertEquals(ExamSession::STATUS_VERIFIED_PASS, $session->status);
        $this->assertEquals(75, $session->score);

        $this->managerReject($scenario, $session, ['sop_understanding' => 'perlu_perbaikan']);

        $this->assertRejectedResult($scenario, $session->refresh(), 2);
        $this->assertEmployeeCompetencyLevel($scenario, 1);
    }

    public function test_level2_tf_pass_approved_matrix_updates(): void
    {
        $scenario = $this->buildCbtScenario();
        $this->setEmployeeLevel($scenario, 1);
        $exam = $this->tfExam($scenario, 2, 'OK');

        $session = $this->registerStartSubmit($scenario, $exam, $this->autoAnswers($exam, 3));
        $this->assertEquals(ExamSession::STATUS_VERIFIED_PASS, $session->status);
        $this->assertEquals(75, $session->score);

        $this->managerApprove($scenario, $session)->assertSessionHas('success');

        $this->assertApprovedResult($scenario, $session->refresh(), 2);
    }

    // ============================================
    // LEVEL 3
    // ============================================

    public function test_level3_tf_below_threshold_not_processed(): void
    {
        $scenario = $this->buildCbtScenario();
        $this->setEmployeeLevel($scenario, 2);
        $exam = $this->tfExam($scenario, 3, 'FAIL');

        $session = $this->registerStartSubmit($scenario, $exam, $this->autoAnswers($exam, 2));

        $this->assertBelowThresholdResult($scenario, $session);
        $this->assertEmployeeCompetencyLevel($scenario, 2);
    }

    public function test_level3_tf_pass_rejected_level_unchanged(): void
    {
        $scenario = $this->buildCbtScenario();
        $this->setEmployeeLevel($scenario, 2);
        $exam = $this->tfExam($scenario, 3, 'REJ');

        $session = $this->registerStartSubmit($scenario, $exam, $this->autoAnswers($exam, 3));
        $this->assertEquals(ExamSession::STATUS_VERIFIED_PASS, $session->status);
        $this->assertEquals(75, $session->score);

        $this->managerReject($scenario, $session, ['competency_application' => 'tidak_memenuhi'], 'Penerapan kompetensi belum terlihat.');

        $this->assertRejectedResult($scenario, $session->refresh(), 3);
        $this->assertEmployeeCompetencyLevel($scenario, 2);
    }

    public function test_level3_tf_pass_approved_matrix_updates(): void
    {
        $scenario = $this->buildCbtScenario();
        $this->setEmployeeLevel($scenario, 2);
        $exam = $this->tfExam($scenario, 3, 'OK');

        $session = $this->registerStartSubmit($scenario, $exam, $this->autoAnswers($exam, 4));
        $this->assertEquals(ExamSession::STATUS_VERIFIED_PASS, $session->status);
        $this->assertEquals(100, $session->score);

        $this->managerApprove($scenario, $session)->assertSessionHas('success');

        $this->assertApprovedResult($scenario, $session->refresh(), 3);
    }

    // ============================================
    // LEVEL 4
    // ============================================

    public function test_level4_tf_below_threshold_not_processed(): void
    {
        $scenario = $this->buildCbtScenario();
        $this->setEmployeeLevel($scenario, 3);
        $exam = $this->tfExam($scenario, 4, 'FAIL');

        $session = $this->registerStartSubmit($scenario, $exam, $this->autoAnswers($exam, 2));

        $this->assertBelowThresholdResult($scenario, $session);
        $this->assertEmployeeCompetencyLevel($scenario, 3);
    }

    public function test_level4_tf_pass_rejected_level_unchanged(): void
    {
        $scenario = $this->buildCbtScenario();
        $this->setEmployeeLevel($scenario, 3);
        $exam = $this->tfExam($scenario, 4, 'REJ');

        $session = $this->registerStartSubmit($scenario, $exam, $this->autoAnswers($exam, 3));
        $this->assertEquals(ExamSession::STATUS_VERIFIED_PASS, $session->status);
        $this->assertEquals(75, $session->score);

        $this->managerReject($scenario, $session, ['independence' => 'tidak_memenuhi'], 'Kemandirian belum memenuhi standar.');

        $this->assertRejectedResult($scenario, $session->refresh(), 4);
        $this->assertEmployeeCompetencyLevel($scenario, 3);
    }

    public function test_level4_tf_pass_approved_matrix_updates(): void
    {
        $scenario = $this->buildCbtScenario();
        $this->setEmployeeLevel($scenario, 3);
        $exam = $this->tfExam($scenario, 4, 'OK');

        $session = $this->registerStartSubmit($scenario, $exam, $this->autoAnswers($exam, 4));
        $this->assertEquals(ExamSession::STATUS_VERIFIED_PASS, $session->status);
        $this->assertEquals(100, $session->score);

        $this->managerApprove($scenario, $session)->assertSessionHas('success');

        $this->assertApprovedResult($scenario, $session->refresh(), 4);
    }
}
