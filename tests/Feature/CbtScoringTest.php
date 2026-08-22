<?php

namespace Tests\Feature;

use App\Models\ExamAnswer;
use App\Models\ExamSession;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\BuildsCbtScenario;
use Tests\Concerns\BuildsScoringData;
use Tests\TestCase;

/**
 * Verifies the scoring formulas and threshold (KKM) logic for all 3 question types:
 * - Pilihan Ganda: (benar / jumlah soal) x 100, dinilai otomatis.
 * - True/False:   (benar / jumlah soal) x 100, dinilai otomatis.
 * - Essay:        jumlah nilai per soal (bobot total 100), dinilai supervisor.
 *
 * Threshold rule verified: isPassed() memakai ">=" (nilai tepat KKM = LULUS).
 */
class CbtScoringTest extends TestCase
{
    use BuildsCbtScenario, BuildsScoringData, RefreshDatabase;

    // ============================================
    // FLOW HELPERS
    // ============================================

    private function submit(array $scenario, ExamSession $session, array $answers): void
    {
        $this->actingAs($scenario['employeeUser'])
            ->post(route('cbt.employee.submit', $session), ['answers' => $answers])
            ->assertRedirect(route('cbt.employee.result', $session));
    }

    private function verifyEssay(array $scenario, ExamSession $session, array $scores): void
    {
        $payload = ['action' => 'approve'];
        foreach ($scores as $questionId => $score) {
            $payload['essay_score_'.$questionId] = $score;
        }

        $this->actingAs($scenario['admin'])
            ->post(route('cbt.admin.sessions.verify', $session), $payload)
            ->assertSessionHas('success');
    }

    // ============================================
    // A. PILIHAN GANDA - FORMULA SKOR
    // ============================================

    public function test_mc_all_correct_gives_100_percent(): void
    {
        $scenario = $this->buildCbtScenario();
        $questions = $this->createMcQuestions($scenario['skill'], 4);
        $exam = $this->makeExam($scenario['skill'], $questions, 70);
        $session = $this->startedSession($exam, $scenario);

        $this->submit($scenario, $session, $this->answerMap($questions, 'A'));

        $session->refresh();
        $this->assertEquals(100, $session->score);
        $this->assertEquals(ExamSession::STATUS_VERIFIED_PASS, $session->status);
        $this->assertEquals(4, ExamAnswer::where('exam_session_id', $session->id)->where('is_correct', true)->count());
    }

    public function test_mc_half_correct_gives_50_percent(): void
    {
        $scenario = $this->buildCbtScenario();
        $questions = $this->createMcQuestions($scenario['skill'], 4);
        $exam = $this->makeExam($scenario['skill'], $questions, 70);
        $session = $this->startedSession($exam, $scenario);

        $ids = $questions->pluck('id')->values();
        $this->submit($scenario, $session, [
            $ids[0] => 'A',
            $ids[1] => 'A',
            $ids[2] => 'B',
            $ids[3] => 'B',
        ]);

        $session->refresh();
        $this->assertEquals(50, $session->score);
        $this->assertEquals(ExamSession::STATUS_VERIFIED_FAIL, $session->status);
    }

    public function test_mc_all_wrong_gives_0_percent(): void
    {
        $scenario = $this->buildCbtScenario();
        $questions = $this->createMcQuestions($scenario['skill'], 4);
        $exam = $this->makeExam($scenario['skill'], $questions, 70);
        $session = $this->startedSession($exam, $scenario);

        $this->submit($scenario, $session, $this->answerMap($questions, 'B'));

        $session->refresh();
        $this->assertEquals(0, $session->score);
        $this->assertEquals(ExamSession::STATUS_VERIFIED_FAIL, $session->status);
    }

    // ============================================
    // B. TRUE/FALSE - FORMULA SKOR
    // ============================================

    public function test_tf_correct_answers_give_100_percent(): void
    {
        $scenario = $this->buildCbtScenario();
        $questions = $this->createTfQuestions($scenario['skill'], 4, 'A');
        $exam = $this->makeExam($scenario['skill'], $questions, 70);
        $session = $this->startedSession($exam, $scenario);

        $this->submit($scenario, $session, $this->answerMap($questions, 'A'));

        $session->refresh();
        $this->assertEquals(100, $session->score);
        $this->assertEquals(ExamSession::STATUS_VERIFIED_PASS, $session->status);
    }

    public function test_tf_wrong_answers_give_0_percent(): void
    {
        $scenario = $this->buildCbtScenario();
        $questions = $this->createTfQuestions($scenario['skill'], 4, 'A');
        $exam = $this->makeExam($scenario['skill'], $questions, 70);
        $session = $this->startedSession($exam, $scenario);

        $this->submit($scenario, $session, $this->answerMap($questions, 'B'));

        $session->refresh();
        $this->assertEquals(0, $session->score);
        $this->assertEquals(ExamSession::STATUS_VERIFIED_FAIL, $session->status);
    }

    // ============================================
    // C. ESSAY - FORMULA SKOR (dinilai supervisor)
    // ============================================

    public function test_essay_all_full_scores_gives_100(): void
    {
        $scenario = $this->buildCbtScenario();
        $questions = $this->createEssayQuestions($scenario['skill'], 5, 20);
        $exam = $this->makeExam($scenario['skill'], $questions, 70);
        $session = $this->startedSession($exam, $scenario);

        $this->submit($scenario, $session, $this->answerMap($questions, 'Jawaban lengkap'));
        $this->assertEquals(ExamSession::STATUS_SUBMITTED, $session->refresh()->status);

        $this->verifyEssay($scenario, $session, $questions->mapWithKeys(fn ($q) => [$q->id => 20])->all());

        $session->refresh();
        $this->assertEquals(100, $session->score);
        $this->assertEquals(ExamSession::STATUS_VERIFIED_PASS, $session->status);
    }

    public function test_essay_partial_scores_are_summed(): void
    {
        $scenario = $this->buildCbtScenario();
        $questions = $this->createEssayQuestions($scenario['skill'], 5, 20);
        $exam = $this->makeExam($scenario['skill'], $questions, 70);
        $session = $this->startedSession($exam, $scenario);

        $this->submit($scenario, $session, $this->answerMap($questions, 'Jawaban'));
        $this->verifyEssay($scenario, $session, [
            $questions[0]->id => 18,
            $questions[1]->id => 20,
            $questions[2]->id => 17,
            $questions[3]->id => 19,
            $questions[4]->id => 16,
        ]);

        $session->refresh();
        $this->assertEquals(90, $session->score);
        $this->assertEquals(ExamSession::STATUS_VERIFIED_PASS, $session->status);
    }

    public function test_essay_with_zero_on_one_question_sums_the_rest(): void
    {
        $scenario = $this->buildCbtScenario();
        $questions = $this->createEssayQuestions($scenario['skill'], 5, 20);
        $exam = $this->makeExam($scenario['skill'], $questions, 70);
        $session = $this->startedSession($exam, $scenario);

        $this->submit($scenario, $session, $this->answerMap($questions, 'Jawaban'));
        $this->verifyEssay($scenario, $session, [
            $questions[0]->id => 20,
            $questions[1]->id => 20,
            $questions[2]->id => 0,
            $questions[3]->id => 20,
            $questions[4]->id => 20,
        ]);

        $session->refresh();
        $this->assertEquals(80, $session->score);
        $this->assertEquals(ExamSession::STATUS_VERIFIED_PASS, $session->status);
    }

    // ============================================
    // D. THRESHOLD MC
    // ============================================

    public function test_mc_below_threshold_fails(): void
    {
        $scenario = $this->buildCbtScenario();
        $questions = $this->createMcQuestions($scenario['skill'], 4);
        $exam = $this->makeExam($scenario['skill'], $questions, 70);
        $session = $this->startedSession($exam, $scenario);

        $ids = $questions->pluck('id')->values();
        $this->submit($scenario, $session, [$ids[0] => 'A', $ids[1] => 'B', $ids[2] => 'B', $ids[3] => 'B']); // 1/4 = 25

        $session->refresh();
        $this->assertEquals(25, $session->score);
        $this->assertEquals(ExamSession::STATUS_VERIFIED_FAIL, $session->status);
        $this->assertNull($session->manager_decision);
    }

    public function test_mc_at_threshold_passes(): void
    {
        $scenario = $this->buildCbtScenario();
        $questions = $this->createMcQuestions($scenario['skill'], 4);
        $exam = $this->makeExam($scenario['skill'], $questions, 75);
        $session = $this->startedSession($exam, $scenario);

        $ids = $questions->pluck('id')->values();
        $this->submit($scenario, $session, [$ids[0] => 'A', $ids[1] => 'A', $ids[2] => 'A', $ids[3] => 'B']); // 3/4 = 75

        $session->refresh();
        $this->assertEquals(75, $session->score);
        $this->assertEquals(ExamSession::STATUS_VERIFIED_PASS, $session->status);
        $this->assertEquals(ExamSession::DECISION_PENDING, $session->manager_decision);
    }

    public function test_mc_above_threshold_passes(): void
    {
        $scenario = $this->buildCbtScenario();
        $questions = $this->createMcQuestions($scenario['skill'], 4);
        $exam = $this->makeExam($scenario['skill'], $questions, 70);
        $session = $this->startedSession($exam, $scenario);

        $this->submit($scenario, $session, $this->answerMap($questions, 'A'));

        $session->refresh();
        $this->assertEquals(100, $session->score);
        $this->assertEquals(ExamSession::STATUS_VERIFIED_PASS, $session->status);
    }

    // ============================================
    // E. THRESHOLD TRUE/FALSE
    // ============================================

    public function test_tf_below_threshold_fails(): void
    {
        $scenario = $this->buildCbtScenario();
        $questions = $this->createTfQuestions($scenario['skill'], 4, 'A');
        $exam = $this->makeExam($scenario['skill'], $questions, 70);
        $session = $this->startedSession($exam, $scenario);

        $ids = $questions->pluck('id')->values();
        $this->submit($scenario, $session, [$ids[0] => 'A', $ids[1] => 'B', $ids[2] => 'B', $ids[3] => 'B']); // 25

        $session->refresh();
        $this->assertEquals(25, $session->score);
        $this->assertEquals(ExamSession::STATUS_VERIFIED_FAIL, $session->status);
    }

    public function test_tf_at_threshold_passes(): void
    {
        $scenario = $this->buildCbtScenario();
        $questions = $this->createTfQuestions($scenario['skill'], 4, 'A');
        $exam = $this->makeExam($scenario['skill'], $questions, 75);
        $session = $this->startedSession($exam, $scenario);

        $ids = $questions->pluck('id')->values();
        $this->submit($scenario, $session, [$ids[0] => 'A', $ids[1] => 'A', $ids[2] => 'A', $ids[3] => 'B']); // 75

        $session->refresh();
        $this->assertEquals(75, $session->score);
        $this->assertEquals(ExamSession::STATUS_VERIFIED_PASS, $session->status);
    }

    public function test_tf_above_threshold_passes(): void
    {
        $scenario = $this->buildCbtScenario();
        $questions = $this->createTfQuestions($scenario['skill'], 4, 'A');
        $exam = $this->makeExam($scenario['skill'], $questions, 70);
        $session = $this->startedSession($exam, $scenario);

        $this->submit($scenario, $session, $this->answerMap($questions, 'A'));

        $session->refresh();
        $this->assertEquals(100, $session->score);
        $this->assertEquals(ExamSession::STATUS_VERIFIED_PASS, $session->status);
    }

    // ============================================
    // F. THRESHOLD ESSAY
    // ============================================

    public function test_essay_below_threshold_fails(): void
    {
        $scenario = $this->buildCbtScenario();
        $questions = $this->createEssayQuestions($scenario['skill'], 5, 20);
        $exam = $this->makeExam($scenario['skill'], $questions, 70);
        $session = $this->startedSession($exam, $scenario);

        $this->submit($scenario, $session, $this->answerMap($questions, 'Jawaban'));
        $this->verifyEssay($scenario, $session, $questions->mapWithKeys(fn ($q) => [$q->id => 10])->all()); // 50

        $session->refresh();
        $this->assertEquals(50, $session->score);
        $this->assertEquals(ExamSession::STATUS_VERIFIED_FAIL, $session->status);
        $this->assertNull($session->manager_decision);
    }

    public function test_essay_at_threshold_passes(): void
    {
        $scenario = $this->buildCbtScenario();
        $questions = $this->createEssayQuestions($scenario['skill'], 5, 20);
        $exam = $this->makeExam($scenario['skill'], $questions, 70);
        $session = $this->startedSession($exam, $scenario);

        $this->submit($scenario, $session, $this->answerMap($questions, 'Jawaban'));
        $this->verifyEssay($scenario, $session, $questions->mapWithKeys(fn ($q) => [$q->id => 14])->all()); // 70

        $session->refresh();
        $this->assertEquals(70, $session->score);
        $this->assertEquals(ExamSession::STATUS_VERIFIED_PASS, $session->status);
        $this->assertEquals(ExamSession::DECISION_PENDING, $session->manager_decision);
    }

    public function test_essay_above_threshold_passes(): void
    {
        $scenario = $this->buildCbtScenario();
        $questions = $this->createEssayQuestions($scenario['skill'], 5, 20);
        $exam = $this->makeExam($scenario['skill'], $questions, 70);
        $session = $this->startedSession($exam, $scenario);

        $this->submit($scenario, $session, $this->answerMap($questions, 'Jawaban'));
        $this->verifyEssay($scenario, $session, [
            $questions[0]->id => 20,
            $questions[1]->id => 18,
            $questions[2]->id => 16,
            $questions[3]->id => 14,
            $questions[4]->id => 12,
        ]); // 80

        $session->refresh();
        $this->assertEquals(80, $session->score);
        $this->assertEquals(ExamSession::STATUS_VERIFIED_PASS, $session->status);
    }

    // ============================================
    // G. THRESHOLD -> MANAGER QUEUE
    // ============================================

    public function test_mc_pass_enters_manager_approval_queue(): void
    {
        $scenario = $this->buildCbtScenario();
        $questions = $this->createMcQuestions($scenario['skill'], 4);
        $exam = $this->makeExam($scenario['skill'], $questions, 70);
        $session = $this->startedSession($exam, $scenario);

        $this->submit($scenario, $session, $this->answerMap($questions, 'A'));

        $session->refresh();
        $this->assertEquals(ExamSession::STATUS_VERIFIED_PASS, $session->status);
        $this->assertEquals(ExamSession::DECISION_PENDING, $session->manager_decision);
        $this->assertTrue($session->isPendingManagerApproval());

        $this->actingAs($scenario['manager'])
            ->get(route('cbt.admin.sessions.pending-approval'))
            ->assertOk()
            ->assertSee($scenario['employee']->name);
    }

    public function test_mc_fail_does_not_enter_manager_queue(): void
    {
        $scenario = $this->buildCbtScenario();
        $questions = $this->createMcQuestions($scenario['skill'], 4);
        $exam = $this->makeExam($scenario['skill'], $questions, 70);
        $session = $this->startedSession($exam, $scenario);

        $this->submit($scenario, $session, $this->answerMap($questions, 'B'));

        $session->refresh();
        $this->assertEquals(ExamSession::STATUS_VERIFIED_FAIL, $session->status);
        $this->assertNull($session->manager_decision);
        $this->assertFalse($session->isPendingManagerApproval());

        $this->actingAs($scenario['manager'])
            ->get(route('cbt.admin.sessions.pending-approval'))
            ->assertOk()
            ->assertDontSee($scenario['employee']->name);
    }

    public function test_essay_submit_does_not_auto_verify(): void
    {
        $scenario = $this->buildCbtScenario();
        $questions = $this->createEssayQuestions($scenario['skill'], 5, 20);
        $exam = $this->makeExam($scenario['skill'], $questions, 70);
        $session = $this->startedSession($exam, $scenario);

        $this->submit($scenario, $session, $this->answerMap($questions, 'Jawaban'));

        $session->refresh();
        $this->assertEquals(ExamSession::STATUS_SUBMITTED, $session->status);
        $this->assertNull($session->verified_at);
        $this->assertEquals(0, $session->score);
    }
}
