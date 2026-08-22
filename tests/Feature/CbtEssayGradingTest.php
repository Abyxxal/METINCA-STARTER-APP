<?php

namespace Tests\Feature;

use App\Models\ExamAnswer;
use App\Models\ExamSession;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\BuildsCbtScenario;
use Tests\Concerns\BuildsScoringData;
use Tests\TestCase;

/**
 * Supervisor (admin) essay grading flow:
 * - grades per essay question (0 .. bobot maksimal soal)
 * - skor dihitung ulang = jumlah nilai semua soal essay
 * - approve (>= KKM -> verified_pass) / reject (verified_fail)
 * - validasi: nilai tidak boleh melebihi bobot, action wajib approve|reject
 */
class CbtEssayGradingTest extends TestCase
{
    use BuildsCbtScenario, BuildsScoringData, RefreshDatabase;

    /**
     * Sesi esai 5 soal (bobot 20 tiap soal) sudah dikerjakan karyawan
     * dan masuk status "submitted", menunggu verifikasi supervisor.
     */
    private function submittedEssaySession(array $scenario, int $passingScore = 70): array
    {
        $questions = $this->createEssayQuestions($scenario['skill'], 5, 20);
        $exam = $this->makeExam($scenario['skill'], $questions, $passingScore);
        $session = ExamSession::create([
            'exam_id' => $exam->id,
            'employee_nik' => $scenario['employee']->nik,
            'status' => ExamSession::STATUS_SUBMITTED,
            'score' => 0,
            'submitted_at' => now(),
        ]);

        foreach ($questions as $q) {
            ExamAnswer::create([
                'exam_session_id' => $session->id,
                'question_id' => $q->id,
                'selected_answer' => 'Jawaban esai karyawan',
                'is_correct' => false,
                'score_earned' => 0,
            ]);
        }

        return ['questions' => $questions, 'session' => $session];
    }

    private function verify(array $scenario, ExamSession $session, array $payload)
    {
        return $this->actingAs($scenario['admin'])
            ->post(route('cbt.admin.sessions.verify', $session), $payload);
    }

    public function test_admin_grades_essay_all_full_scores(): void
    {
        $scenario = $this->buildCbtScenario();
        $data = $this->submittedEssaySession($scenario);

        $this->verify($scenario, $data['session'], [
            'action' => 'approve',
            'essay_score_'.$data['questions'][0]->id => 20,
            'essay_score_'.$data['questions'][1]->id => 20,
            'essay_score_'.$data['questions'][2]->id => 20,
            'essay_score_'.$data['questions'][3]->id => 20,
            'essay_score_'.$data['questions'][4]->id => 20,
        ])->assertSessionHas('success');

        $session = $data['session']->refresh();
        $this->assertEquals(100, $session->score);
        $this->assertEquals(ExamSession::STATUS_VERIFIED_PASS, $session->status);
        $this->assertEquals($scenario['admin']->id, $session->verified_by);
        $this->assertNotNull($session->verified_at);
        $this->assertEquals(ExamSession::DECISION_PENDING, $session->manager_decision);

        foreach ($data['questions'] as $q) {
            $this->assertDatabaseHas('exam_answers', [
                'exam_session_id' => $session->id,
                'question_id' => $q->id,
                'score_earned' => 20,
            ]);
        }
    }

    public function test_admin_grades_essay_partial_scores(): void
    {
        $scenario = $this->buildCbtScenario();
        $data = $this->submittedEssaySession($scenario);
        $questions = $data['questions'];

        $this->verify($scenario, $data['session'], [
            'action' => 'approve',
            'essay_score_'.$questions[0]->id => 18,
            'essay_score_'.$questions[1]->id => 20,
            'essay_score_'.$questions[2]->id => 17,
            'essay_score_'.$questions[3]->id => 19,
            'essay_score_'.$questions[4]->id => 16,
        ])->assertSessionHas('success');

        $this->assertEquals(90, $data['session']->refresh()->score);
        $this->assertDatabaseHas('exam_answers', ['exam_session_id' => $data['session']->id, 'question_id' => $questions[0]->id, 'score_earned' => 18]);
        $this->assertDatabaseHas('exam_answers', ['exam_session_id' => $data['session']->id, 'question_id' => $questions[1]->id, 'score_earned' => 20]);
        $this->assertDatabaseHas('exam_answers', ['exam_session_id' => $data['session']->id, 'question_id' => $questions[4]->id, 'score_earned' => 16]);
    }

    public function test_admin_grades_essay_with_zero_on_one_question(): void
    {
        $scenario = $this->buildCbtScenario();
        $data = $this->submittedEssaySession($scenario);
        $questions = $data['questions'];

        $this->verify($scenario, $data['session'], [
            'action' => 'approve',
            'essay_score_'.$questions[0]->id => 20,
            'essay_score_'.$questions[1]->id => 20,
            'essay_score_'.$questions[2]->id => 0,
            'essay_score_'.$questions[3]->id => 20,
            'essay_score_'.$questions[4]->id => 20,
        ])->assertSessionHas('success');

        $this->assertEquals(80, $data['session']->refresh()->score);
        $this->assertDatabaseHas('exam_answers', ['exam_session_id' => $data['session']->id, 'question_id' => $questions[2]->id, 'score_earned' => 0]);
    }

    public function test_essay_score_cannot_exceed_question_weight(): void
    {
        $scenario = $this->buildCbtScenario();
        $data = $this->submittedEssaySession($scenario);
        $question = $data['questions']->first(); // bobot 20

        $this->verify($scenario, $data['session'], [
            'action' => 'approve',
            'essay_score_'.$question->id => 25,
        ])->assertSessionHasErrors('essay_score_'.$question->id);

        $session = $data['session']->refresh();
        $this->assertEquals(ExamSession::STATUS_SUBMITTED, $session->status);
        $this->assertEquals(0, $session->score);
        $this->assertDatabaseHas('exam_answers', ['exam_session_id' => $session->id, 'question_id' => $question->id, 'score_earned' => 0]);
    }

    public function test_essay_score_recalculated_after_grading(): void
    {
        $scenario = $this->buildCbtScenario();
        $data = $this->submittedEssaySession($scenario);
        $questions = $data['questions'];

        $this->assertEquals(0, $data['session']->score);

        $this->verify($scenario, $data['session'], [
            'action' => 'approve',
            'essay_score_'.$questions[0]->id => 15,
            'essay_score_'.$questions[1]->id => 15,
            'essay_score_'.$questions[2]->id => 15,
            'essay_score_'.$questions[3]->id => 15,
            'essay_score_'.$questions[4]->id => 15,
        ])->assertSessionHas('success');

        $this->assertEquals(75, $data['session']->refresh()->score);
    }

    public function test_admin_approve_above_threshold_marks_pass(): void
    {
        $scenario = $this->buildCbtScenario();
        $data = $this->submittedEssaySession($scenario, 70);
        $questions = $data['questions'];

        $this->verify($scenario, $data['session'], [
            'action' => 'approve',
            'notes' => 'Jawaban esai memuaskan.',
            'essay_score_'.$questions[0]->id => 16,
            'essay_score_'.$questions[1]->id => 16,
            'essay_score_'.$questions[2]->id => 16,
            'essay_score_'.$questions[3]->id => 16,
            'essay_score_'.$questions[4]->id => 16,
        ])->assertSessionHas('success');

        $session = $data['session']->refresh();
        $this->assertEquals(80, $session->score);
        $this->assertEquals(ExamSession::STATUS_VERIFIED_PASS, $session->status);
        $this->assertEquals('Jawaban esai memuaskan.', $session->admin_notes);
        $this->assertEquals(ExamSession::DECISION_PENDING, $session->manager_decision);
        $this->assertTrue($session->isPendingManagerApproval());
    }

    public function test_admin_approve_below_threshold_marks_fail(): void
    {
        $scenario = $this->buildCbtScenario();
        $data = $this->submittedEssaySession($scenario, 70);
        $questions = $data['questions'];

        $this->verify($scenario, $data['session'], [
            'action' => 'approve',
            'essay_score_'.$questions[0]->id => 10,
            'essay_score_'.$questions[1]->id => 10,
            'essay_score_'.$questions[2]->id => 10,
            'essay_score_'.$questions[3]->id => 10,
            'essay_score_'.$questions[4]->id => 10,
        ])->assertSessionHas('success');

        $session = $data['session']->refresh();
        $this->assertEquals(50, $session->score);
        $this->assertEquals(ExamSession::STATUS_VERIFIED_FAIL, $session->status);
        $this->assertNull($session->manager_decision);
        $this->assertFalse($session->isPendingManagerApproval());
    }

    public function test_admin_reject_marks_fail_and_allows_retake(): void
    {
        $scenario = $this->buildCbtScenario();
        $data = $this->submittedEssaySession($scenario);
        $questions = $data['questions'];

        $this->verify($scenario, $data['session'], [
            'action' => 'reject',
            'notes' => 'Jawaban tidak sesuai.',
            'essay_score_'.$questions[0]->id => 20,
            'essay_score_'.$questions[1]->id => 20,
            'essay_score_'.$questions[2]->id => 20,
            'essay_score_'.$questions[3]->id => 20,
            'essay_score_'.$questions[4]->id => 20,
        ])->assertSessionHas('success');

        $session = $data['session']->refresh();
        $this->assertEquals(ExamSession::STATUS_VERIFIED_FAIL, $session->status);
        $this->assertEquals($scenario['admin']->id, $session->verified_by);
        $this->assertEquals('Jawaban tidak sesuai.', $session->admin_notes);
        $this->assertNull($session->manager_decision);
    }

    public function test_cannot_verify_session_not_in_submitted_status(): void
    {
        $scenario = $this->buildCbtScenario();
        $questions = $this->createEssayQuestions($scenario['skill'], 1, 100);
        $exam = $this->makeExam($scenario['skill'], $questions);
        $session = $this->startedSession($exam, $scenario);

        $this->verify($scenario, $session, ['action' => 'approve'])
            ->assertSessionHas('error');

        $this->assertEquals(ExamSession::STATUS_STARTED, $session->refresh()->status);
    }

    public function test_verify_requires_approve_or_reject_action(): void
    {
        $scenario = $this->buildCbtScenario();
        $data = $this->submittedEssaySession($scenario);

        $this->verify($scenario, $data['session'], ['action' => 'rahasia'])
            ->assertSessionHasErrors('action');

        $this->assertEquals(ExamSession::STATUS_SUBMITTED, $data['session']->refresh()->status);
    }
}
