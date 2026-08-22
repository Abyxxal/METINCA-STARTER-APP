<?php

namespace Tests\Feature;

use App\Models\ExamSession;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\BuildsCbtScenario;
use Tests\Concerns\BuildsLevelFlow;
use Tests\TestCase;

/**
 * Skenario utama Essay untuk Level 1 s/d Level 4.
 *
 * Essay TIDAK dinilai otomatis. Jawaban dinilai Supervisor (admin) melalui
 * verifikasi, dengan nilai 0 sampai bobot maksimal tiap soal.
 * Bobot soal: 20+30+25+20+5 = 100, KKM = 70.
 * 12 skenario: 4 level x (di bawah Threshold, lulus-ditolak, lulus-disetujui).
 */
class CbtLevelFlowEssayTest extends TestCase
{
    use BuildsCbtScenario, BuildsLevelFlow, RefreshDatabase;

    private const THRESHOLD = 70;

    /** @var int[] skor Supervisor contoh: 18/20, 25/30, 22/25, 17/20, 5/5 = 87 */
    private const PASS_SCORES = [18, 25, 22, 17, 5];

    /** @var int[] skor di bawah threshold: 50/100 */
    private const BELOW_SCORES = [10, 15, 12, 10, 3];

    private function essayExam(array $scenario, int $targetLevel, string $suffix): \App\Models\Exam
    {
        return $this->buildLevelExam($scenario, $targetLevel, 'essay', "QS-L{$targetLevel}-ESS-{$suffix}", self::THRESHOLD);
    }

    private function scoresFor(ExamSession $session, array $scores): array
    {
        return array_combine($session->exam->questions->pluck('id')->all(), $scores);
    }

    private function assertUnscoredBeforeSupervisor(array $scenario, ExamSession $session): void
    {
        $this->assertEquals(ExamSession::STATUS_SUBMITTED, $session->status);
        $this->assertEquals(0, $session->score);
        $this->assertInManagerQueue($scenario, $session, false);
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

    public function test_level1_essay_below_threshold_not_processed(): void
    {
        $scenario = $this->buildCbtScenario();
        $this->setEmployeeLevel($scenario, 0);
        $exam = $this->essayExam($scenario, 1, 'FAIL');

        $session = $this->registerStartSubmit($scenario, $exam, $this->essayTextAnswers($exam));
        $this->assertUnscoredBeforeSupervisor($scenario, $session);

        $this->gradeEssay($scenario, $session, $this->scoresFor($session, self::BELOW_SCORES))->assertSessionHas('success');

        $session->refresh();
        $this->assertEquals(ExamSession::STATUS_VERIFIED_FAIL, $session->status);
        $this->assertEquals(50, $session->score);
        $this->assertInManagerQueue($scenario, $session, false);
        $this->assertEmployeeCompetencyLevel($scenario, null);
    }

    public function test_level1_essay_pass_rejected_level_unchanged(): void
    {
        $scenario = $this->buildCbtScenario();
        $this->setEmployeeLevel($scenario, 0);
        $exam = $this->essayExam($scenario, 1, 'REJ');

        $session = $this->registerStartSubmit($scenario, $exam, $this->essayTextAnswers($exam));
        $this->gradeEssay($scenario, $session, $this->scoresFor($session, self::PASS_SCORES))->assertSessionHas('success');

        $session->refresh();
        $this->assertEquals(ExamSession::STATUS_VERIFIED_PASS, $session->status);
        $this->assertEquals(87, $session->score);
        $this->assertInManagerQueue($scenario, $session, true);

        $this->managerReject($scenario, $session, ['readiness' => 'tidak_memenuhi']);

        $this->assertRejectedResult($scenario, $session->refresh(), 1);
        $this->assertEmployeeCompetencyLevel($scenario, null);
    }

    public function test_level1_essay_pass_approved_matrix_updates(): void
    {
        $scenario = $this->buildCbtScenario();
        $this->setEmployeeLevel($scenario, 0);
        $exam = $this->essayExam($scenario, 1, 'OK');

        $session = $this->registerStartSubmit($scenario, $exam, $this->essayTextAnswers($exam));
        $this->gradeEssay($scenario, $session, $this->scoresFor($session, self::PASS_SCORES))->assertSessionHas('success');

        $session->refresh();
        $this->assertEquals(ExamSession::STATUS_VERIFIED_PASS, $session->status);
        $this->assertEquals(87, $session->score);

        $this->managerApprove($scenario, $session)->assertSessionHas('success');

        $this->assertApprovedResult($scenario, $session->refresh(), 1);
    }

    // ============================================
    // LEVEL 2
    // ============================================

    public function test_level2_essay_below_threshold_not_processed(): void
    {
        $scenario = $this->buildCbtScenario();
        $this->setEmployeeLevel($scenario, 1);
        $exam = $this->essayExam($scenario, 2, 'FAIL');

        $session = $this->registerStartSubmit($scenario, $exam, $this->essayTextAnswers($exam));
        $this->assertUnscoredBeforeSupervisor($scenario, $session);

        $this->gradeEssay($scenario, $session, $this->scoresFor($session, self::BELOW_SCORES))->assertSessionHas('success');

        $session->refresh();
        $this->assertEquals(ExamSession::STATUS_VERIFIED_FAIL, $session->status);
        $this->assertEquals(50, $session->score);
        $this->assertInManagerQueue($scenario, $session, false);
        $this->assertEmployeeCompetencyLevel($scenario, 1);
    }

    public function test_level2_essay_pass_rejected_level_unchanged(): void
    {
        $scenario = $this->buildCbtScenario();
        $this->setEmployeeLevel($scenario, 1);
        $exam = $this->essayExam($scenario, 2, 'REJ');

        $session = $this->registerStartSubmit($scenario, $exam, $this->essayTextAnswers($exam));
        $this->gradeEssay($scenario, $session, $this->scoresFor($session, self::PASS_SCORES))->assertSessionHas('success');

        $session->refresh();
        $this->assertEquals(ExamSession::STATUS_VERIFIED_PASS, $session->status);
        $this->assertEquals(87, $session->score);

        $this->managerReject($scenario, $session, ['sop_understanding' => 'perlu_perbaikan'], 'Pemahaman SOP perlu diperbaiki.');

        $this->assertRejectedResult($scenario, $session->refresh(), 2);
        $this->assertEmployeeCompetencyLevel($scenario, 1);
    }

    public function test_level2_essay_pass_approved_matrix_updates(): void
    {
        $scenario = $this->buildCbtScenario();
        $this->setEmployeeLevel($scenario, 1);
        $exam = $this->essayExam($scenario, 2, 'OK');

        $session = $this->registerStartSubmit($scenario, $exam, $this->essayTextAnswers($exam));
        $this->gradeEssay($scenario, $session, $this->scoresFor($session, self::PASS_SCORES))->assertSessionHas('success');

        $session->refresh();
        $this->assertEquals(ExamSession::STATUS_VERIFIED_PASS, $session->status);
        $this->assertEquals(87, $session->score);

        $this->managerApprove($scenario, $session)->assertSessionHas('success');

        $this->assertApprovedResult($scenario, $session->refresh(), 2);
    }

    // ============================================
    // LEVEL 3
    // ============================================

    public function test_level3_essay_below_threshold_not_processed(): void
    {
        $scenario = $this->buildCbtScenario();
        $this->setEmployeeLevel($scenario, 2);
        $exam = $this->essayExam($scenario, 3, 'FAIL');

        $session = $this->registerStartSubmit($scenario, $exam, $this->essayTextAnswers($exam));
        $this->assertUnscoredBeforeSupervisor($scenario, $session);

        $this->gradeEssay($scenario, $session, $this->scoresFor($session, self::BELOW_SCORES))->assertSessionHas('success');

        $session->refresh();
        $this->assertEquals(ExamSession::STATUS_VERIFIED_FAIL, $session->status);
        $this->assertEquals(50, $session->score);
        $this->assertInManagerQueue($scenario, $session, false);
        $this->assertEmployeeCompetencyLevel($scenario, 2);
    }

    public function test_level3_essay_pass_rejected_level_unchanged(): void
    {
        $scenario = $this->buildCbtScenario();
        $this->setEmployeeLevel($scenario, 2);
        $exam = $this->essayExam($scenario, 3, 'REJ');

        $session = $this->registerStartSubmit($scenario, $exam, $this->essayTextAnswers($exam));
        $this->gradeEssay($scenario, $session, $this->scoresFor($session, self::PASS_SCORES))->assertSessionHas('success');

        $session->refresh();
        $this->assertEquals(ExamSession::STATUS_VERIFIED_PASS, $session->status);
        $this->assertEquals(87, $session->score);

        $this->managerReject($scenario, $session, ['independence' => 'tidak_memenuhi'], 'Belum mandiri dalam bekerja.');

        $this->assertRejectedResult($scenario, $session->refresh(), 3);
        $this->assertEmployeeCompetencyLevel($scenario, 2);
    }

    public function test_level3_essay_pass_approved_matrix_updates(): void
    {
        $scenario = $this->buildCbtScenario();
        $this->setEmployeeLevel($scenario, 2);
        $exam = $this->essayExam($scenario, 3, 'OK');

        $session = $this->registerStartSubmit($scenario, $exam, $this->essayTextAnswers($exam));
        $this->gradeEssay($scenario, $session, $this->scoresFor($session, self::PASS_SCORES))->assertSessionHas('success');

        $session->refresh();
        $this->assertEquals(ExamSession::STATUS_VERIFIED_PASS, $session->status);
        $this->assertEquals(87, $session->score);

        $this->managerApprove($scenario, $session)->assertSessionHas('success');

        $this->assertApprovedResult($scenario, $session->refresh(), 3);
    }

    // ============================================
    // LEVEL 4
    // ============================================

    public function test_level4_essay_below_threshold_not_processed(): void
    {
        $scenario = $this->buildCbtScenario();
        $this->setEmployeeLevel($scenario, 3);
        $exam = $this->essayExam($scenario, 4, 'FAIL');

        $session = $this->registerStartSubmit($scenario, $exam, $this->essayTextAnswers($exam));
        $this->assertUnscoredBeforeSupervisor($scenario, $session);

        $this->gradeEssay($scenario, $session, $this->scoresFor($session, self::BELOW_SCORES))->assertSessionHas('success');

        $session->refresh();
        $this->assertEquals(ExamSession::STATUS_VERIFIED_FAIL, $session->status);
        $this->assertEquals(50, $session->score);
        $this->assertInManagerQueue($scenario, $session, false);
        $this->assertEmployeeCompetencyLevel($scenario, 3);
    }

    public function test_level4_essay_pass_rejected_level_unchanged(): void
    {
        $scenario = $this->buildCbtScenario();
        $this->setEmployeeLevel($scenario, 3);
        $exam = $this->essayExam($scenario, 4, 'REJ');

        $session = $this->registerStartSubmit($scenario, $exam, $this->essayTextAnswers($exam));
        $this->gradeEssay($scenario, $session, $this->scoresFor($session, self::PASS_SCORES))->assertSessionHas('success');

        $session->refresh();
        $this->assertEquals(ExamSession::STATUS_VERIFIED_PASS, $session->status);
        $this->assertEquals(87, $session->score);

        $this->managerReject($scenario, $session, ['problem_solving' => 'tidak_memenuhi'], 'Penyelesaian masalah belum mumpuni.');

        $this->assertRejectedResult($scenario, $session->refresh(), 4);
        $this->assertEmployeeCompetencyLevel($scenario, 3);
    }

    public function test_level4_essay_pass_approved_matrix_updates(): void
    {
        $scenario = $this->buildCbtScenario();
        $this->setEmployeeLevel($scenario, 3);
        $exam = $this->essayExam($scenario, 4, 'OK');

        $session = $this->registerStartSubmit($scenario, $exam, $this->essayTextAnswers($exam));
        $this->gradeEssay($scenario, $session, $this->scoresFor($session, self::PASS_SCORES))->assertSessionHas('success');

        $session->refresh();
        $this->assertEquals(ExamSession::STATUS_VERIFIED_PASS, $session->status);
        $this->assertEquals(87, $session->score);

        $this->managerApprove($scenario, $session)->assertSessionHas('success');

        $this->assertApprovedResult($scenario, $session->refresh(), 4);
    }

    // ============================================
    // ATURAN PENILAIAN ESSAY
    // ============================================

    public function test_essay_supervisor_score_may_not_exceed_question_weight(): void
    {
        $scenario = $this->buildCbtScenario();
        $this->setEmployeeLevel($scenario, 1);
        $exam = $this->essayExam($scenario, 2, 'WEIGHT');

        $session = $this->registerStartSubmit($scenario, $exam, $this->essayTextAnswers($exam));

        $scores = $this->scoresFor($session, [20, 31, 22, 17, 5]); // soal 2 berbobot 30, diberi 31
        $this->gradeEssay($scenario, $session, $scores)->assertSessionHasErrors('essay_score_'.$session->exam->questions->get(1)->id);

        $this->assertEquals(ExamSession::STATUS_SUBMITTED, $session->refresh()->status);
        $this->assertEmployeeCompetencyLevel($scenario, 1);
    }

    public function test_essay_zero_score_is_allowed(): void
    {
        $scenario = $this->buildCbtScenario();
        $this->setEmployeeLevel($scenario, 1);
        $exam = $this->essayExam($scenario, 2, 'ZERO');

        $session = $this->registerStartSubmit($scenario, $exam, $this->essayTextAnswers($exam));

        $this->gradeEssay($scenario, $session, $this->scoresFor($session, [0, 0, 0, 0, 0]))->assertSessionHas('success');

        $session->refresh();
        $this->assertEquals(ExamSession::STATUS_VERIFIED_FAIL, $session->status);
        $this->assertEquals(0, $session->score);
    }
}
