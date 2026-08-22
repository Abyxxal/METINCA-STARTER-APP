<?php

namespace Tests\Feature;

use App\Models\ExamAnswer;
use App\Models\ExamSession;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\BuildsCbtScenario;
use Tests\TestCase;

class CbtAdminVerifyTest extends TestCase
{
    use BuildsCbtScenario, RefreshDatabase;

    private function submittedEssaySession(array $scenario): ExamSession
    {
        $session = $this->makeSession($scenario['essayExam'], $scenario['employee'], ExamSession::STATUS_SUBMITTED, [
            'score' => 0,
            'submitted_at' => now(),
        ]);

        ExamAnswer::create([
            'exam_session_id' => $session->id,
            'question_id' => $scenario['essayQuestion']->id,
            'selected_answer' => 'Jawaban esai karyawan',
            'is_correct' => false,
            'score_earned' => 0,
        ]);

        return $session;
    }

    public function test_admin_can_verify_passed_essay_session(): void
    {
        $scenario = $this->buildCbtScenario();
        $admin = $scenario['admin'];
        $session = $this->submittedEssaySession($scenario);

        $this->actingAs($admin)
            ->post(route('cbt.admin.sessions.verify', $session), [
                'action' => 'approve',
                'notes' => 'Jawaban esai bagus.',
                'essay_score_'.$scenario['essayQuestion']->id => 80,
            ])
            ->assertRedirect()
            ->assertSessionHas('success');

        $session->refresh();
        $this->assertEquals(ExamSession::STATUS_VERIFIED_PASS, $session->status);
        $this->assertEquals(80, $session->score);
        $this->assertEquals($admin->id, $session->verified_by);
        $this->assertNotNull($session->verified_at);
        $this->assertEquals('Jawaban esai bagus.', $session->admin_notes);
        $this->assertEquals(ExamSession::DECISION_PENDING, $session->manager_decision);
    }

    public function test_admin_verify_marks_fail_when_below_passing_score(): void
    {
        $scenario = $this->buildCbtScenario();
        $admin = $scenario['admin'];
        $session = $this->submittedEssaySession($scenario);

        $this->actingAs($admin)
            ->post(route('cbt.admin.sessions.verify', $session), [
                'action' => 'approve',
                'essay_score_'.$scenario['essayQuestion']->id => 50, // KKM 70
            ])
            ->assertSessionHas('success');

        $session->refresh();
        $this->assertEquals(ExamSession::STATUS_VERIFIED_FAIL, $session->status);
        $this->assertEquals(50, $session->score);
        $this->assertNull($session->manager_decision);
    }

    public function test_admin_can_reject_session_allowing_retake(): void
    {
        $scenario = $this->buildCbtScenario();
        $admin = $scenario['admin'];
        $session = $this->submittedEssaySession($scenario);

        $this->actingAs($admin)
            ->post(route('cbt.admin.sessions.verify', $session), [
                'action' => 'reject',
                'notes' => 'Jawaban tidak sesuai.',
            ])
            ->assertSessionHas('success');

        $session->refresh();
        $this->assertEquals(ExamSession::STATUS_VERIFIED_FAIL, $session->status);
        $this->assertEquals($admin->id, $session->verified_by);
        $this->assertEquals('Jawaban tidak sesuai.', $session->admin_notes);
    }

    public function test_essay_score_cannot_exceed_question_weight(): void
    {
        $scenario = $this->buildCbtScenario();
        $admin = $scenario['admin'];
        $session = $this->submittedEssaySession($scenario);

        $this->actingAs($admin)
            ->post(route('cbt.admin.sessions.verify', $session), [
                'action' => 'approve',
                'essay_score_'.$scenario['essayQuestion']->id => 150, // bobot maksimal 100
            ])
            ->assertSessionHasErrors('essay_score_'.$scenario['essayQuestion']->id);

        $session->refresh();
        $this->assertEquals(ExamSession::STATUS_SUBMITTED, $session->status);
        $this->assertEquals(0, $session->score);
    }

    public function test_cannot_verify_session_not_in_submitted_status(): void
    {
        $scenario = $this->buildCbtScenario();
        $admin = $scenario['admin'];
        $session = $this->makeSession($scenario['essayExam'], $scenario['employee'], ExamSession::STATUS_STARTED);

        $this->actingAs($admin)
            ->post(route('cbt.admin.sessions.verify', $session), [
                'action' => 'approve',
            ])
            ->assertSessionHas('error');

        $session->refresh();
        $this->assertEquals(ExamSession::STATUS_STARTED, $session->status);
    }

    public function test_verify_requires_valid_action(): void
    {
        $scenario = $this->buildCbtScenario();
        $admin = $scenario['admin'];
        $session = $this->submittedEssaySession($scenario);

        $this->actingAs($admin)
            ->post(route('cbt.admin.sessions.verify', $session), [
                'action' => 'invalid-action',
            ])
            ->assertSessionHasErrors('action');
    }
}
