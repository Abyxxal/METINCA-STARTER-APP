<?php

namespace Tests\Feature;

use App\Models\EmployeeCompetency;
use App\Models\EmployeeCompetencyHistory;
use App\Models\Exam;
use App\Models\ExamAnswer;
use App\Models\ExamSession;
use App\Models\ManagerAssessment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\BuildsCbtScenario;
use Tests\Concerns\BuildsScoringData;
use Tests\TestCase;

/**
 * End-to-end lifecycle of a CBT exam:
 *   admin assigns (store) -> employee starts -> submits -> auto/essay verify
 *   -> manager approve/reject -> level change + history recorded.
 */
class CbtEndToEndFlowTest extends TestCase
{
    use BuildsCbtScenario, BuildsScoringData, RefreshDatabase;

    /**
     * Assign exam via the real admin store endpoint using a question set.
     */
    private function assignThroughStore(array $scenario, string $setId, int $passingScore = 70, ?string $scheduledStartAt = null): ExamSession
    {
        $this->actingAs($scenario['admin'])
            ->post(route('cbt.admin.sessions.store'), [
                'duration_minutes' => 60,
                'passing_score' => $passingScore,
                'deadline_at' => now()->addDays(7)->format('Y-m-d H:i:s'),
                'scheduled_start_at' => $scheduledStartAt,
                'question_set_ids' => [$setId],
                'employee_niks' => [$scenario['employee']->nik],
            ])
            ->assertRedirect(route('cbt.admin.sessions.index'))
            ->assertSessionHas('success');

        return ExamSession::where('employee_nik', $scenario['employee']->nik)
            ->orderByDesc('id')
            ->firstOrFail();
    }

    private function start(array $scenario, ExamSession $session)
    {
        return $this->actingAs($scenario['employeeUser'])
            ->post(route('cbt.employee.start', $session));
    }

    private function submit(array $scenario, ExamSession $session, array $answers)
    {
        return $this->actingAs($scenario['employeeUser'])
            ->post(route('cbt.employee.submit', $session), ['answers' => $answers]);
    }

    private function verifyEssays(array $scenario, ExamSession $session, array $scores, string $action = 'approve')
    {
        $payload = ['action' => $action];
        foreach ($scores as $questionId => $score) {
            $payload['essay_score_'.$questionId] = $score;
        }

        return $this->actingAs($scenario['admin'])
            ->post(route('cbt.admin.sessions.verify', $session), $payload);
    }

    private function approve(array $scenario, ExamSession $session)
    {
        return $this->actingAs($scenario['manager'])
            ->post(route('cbt.admin.sessions.approve-level', $session), [
                'assessment_method' => 'interview',
                'sop_understanding' => 'memenuhi',
                'competency_application' => 'memenuhi',
                'independence' => 'memenuhi',
                'problem_solving' => 'memenuhi',
                'readiness' => 'memenuhi',
                'verification_date' => now()->format('Y-m-d'),
                'manager_notes' => 'Karyawan siap naik level.',
            ]);
    }

    private function allCorrectAnswers(ExamSession $session): array
    {
        return $session->exam->questions
            ->mapWithKeys(fn ($q) => [$q->id => $q->correct_answer])
            ->all();
    }

    private function allWrongAnswers(ExamSession $session): array
    {
        return $session->exam->questions
            ->mapWithKeys(fn ($q) => [$q->id => $q->correct_answer === 'A' ? 'B' : 'A'])
            ->all();
    }

    private function essayTextAnswers(ExamSession $session): array
    {
        return $session->exam->questions
            ->mapWithKeys(fn ($q) => [$q->id => 'Jawaban essay karyawan untuk soal #'.$q->id])
            ->all();
    }

    // ============================================
    // FULL FLOW - MULTIPLE CHOICE
    // ============================================

    public function test_full_flow_mc_pass_assign_start_submit_approve(): void
    {
        $scenario = $this->buildCbtScenario();
        $mcQuestions = $this->createMcQuestions($scenario['skill'], 4, 'A', 'QS-E2E-MC');
        $session = $this->assignThroughStore($scenario, 'QS-E2E-MC');

        $this->assertEquals(ExamSession::STATUS_ASSIGNED, $session->status);

        $this->start($scenario, $session)
            ->assertRedirect(route('cbt.employee.take', $session));

        $this->assertEquals(ExamSession::STATUS_STARTED, $session->refresh()->status);

        $this->submit($scenario, $session, $this->allCorrectAnswers($session))
            ->assertRedirect();

        $session->refresh();
        $this->assertEquals(ExamSession::STATUS_VERIFIED_PASS, $session->status);
        $this->assertEquals(100, $session->score);
        $this->assertEquals(ExamSession::DECISION_PENDING, $session->manager_decision);

        $this->approve($scenario, $session)->assertSessionHas('success');

        $session->refresh();
        $this->assertEquals(ExamSession::STATUS_APPROVED, $session->status);
        $this->assertEquals(2, $scenario['competency']->refresh()->level);
        $this->assertEquals($mcQuestions->count(), ExamAnswer::where('exam_session_id', $session->id)->count());
    }

    public function test_full_flow_mc_fail_never_reaches_manager_queue(): void
    {
        $scenario = $this->buildCbtScenario();
        $this->createMcQuestions($scenario['skill'], 4, 'A', 'QS-E2E-MC-FAIL');
        $session = $this->assignThroughStore($scenario, 'QS-E2E-MC-FAIL');

        $this->start($scenario, $session)->assertRedirect();
        $this->submit($scenario, $session, $this->allWrongAnswers($session))->assertRedirect();

        $session->refresh();
        $this->assertEquals(ExamSession::STATUS_VERIFIED_FAIL, $session->status);
        $this->assertEquals(0, $session->score);

        $this->actingAs($scenario['manager'])
            ->get(route('cbt.admin.sessions.pending-approval'))
            ->assertOk()
            ->assertDontSee($scenario['employee']->name);

        $this->assertEquals(1, $scenario['competency']->refresh()->level);
    }

    // ============================================
    // FULL FLOW - TRUE/FALSE
    // ============================================

    public function test_full_flow_tf_pass_assign_start_submit_approve(): void
    {
        $scenario = $this->buildCbtScenario();
        $this->createTfQuestions($scenario['skill'], 4, 'A', 'QS-E2E-TF');
        $session = $this->assignThroughStore($scenario, 'QS-E2E-TF');

        $this->start($scenario, $session)->assertRedirect();
        $this->submit($scenario, $session, $this->allCorrectAnswers($session))->assertRedirect();

        $session->refresh();
        $this->assertEquals(ExamSession::STATUS_VERIFIED_PASS, $session->status);
        $this->assertEquals(100, $session->score);

        $this->approve($scenario, $session)->assertSessionHas('success');

        $this->assertEquals(ExamSession::STATUS_APPROVED, $session->refresh()->status);
        $this->assertEquals(2, $scenario['competency']->refresh()->level);
    }

    public function test_full_flow_tf_fail_partial_answers(): void
    {
        $scenario = $this->buildCbtScenario();
        $this->createTfQuestions($scenario['skill'], 4, 'A', 'QS-E2E-TF-FAIL');
        $session = $this->assignThroughStore($scenario, 'QS-E2E-TF-FAIL', 70);

        $this->start($scenario, $session)->assertRedirect();

        $answers = $session->exam->questions
            ->mapWithKeys(fn ($q) => [$q->id => 'A'])
            ->all();
        // Only answer 2 of 4 correctly (50) - below KKM 70
        $keys = array_slice(array_keys($answers), 2);
        foreach ($keys as $key) {
            $answers[$key] = 'B';
        }

        $this->submit($scenario, $session, $answers)->assertRedirect();

        $session->refresh();
        $this->assertEquals(ExamSession::STATUS_VERIFIED_FAIL, $session->status);
        $this->assertEquals(50, $session->score);
    }

    // ============================================
    // FULL FLOW - ESSAY
    // ============================================

    public function test_full_flow_essay_pass_admin_verify_manager_approve(): void
    {
        $scenario = $this->buildCbtScenario();
        $this->createEssayQuestions($scenario['skill'], 5, 20, 'QS-E2E-ESS');
        $session = $this->assignThroughStore($scenario, 'QS-E2E-ESS', 70);

        $this->start($scenario, $session)->assertRedirect();
        $this->submit($scenario, $session, $this->essayTextAnswers($session))->assertRedirect();

        // Essay -> menunggu verifikasi admin
        $session->refresh();
        $this->assertEquals(ExamSession::STATUS_SUBMITTED, $session->status);

        $scores = $session->exam->questions->mapWithKeys(fn ($q) => [$q->id => 20])->all();
        $this->verifyEssays($scenario, $session, $scores)->assertSessionHas('success');

        $session->refresh();
        $this->assertEquals(ExamSession::STATUS_VERIFIED_PASS, $session->status);
        $this->assertEquals(100, $session->score);
        $this->assertEquals($scenario['admin']->id, $session->verified_by);

        $this->approve($scenario, $session)->assertSessionHas('success');

        $session->refresh();
        $this->assertEquals(ExamSession::STATUS_APPROVED, $session->status);
        $this->assertEquals(2, $scenario['competency']->refresh()->level);
    }

    public function test_full_flow_essay_fail_after_grading(): void
    {
        $scenario = $this->buildCbtScenario();
        $this->createEssayQuestions($scenario['skill'], 5, 20, 'QS-E2E-ESS-FAIL');
        $session = $this->assignThroughStore($scenario, 'QS-E2E-ESS-FAIL', 70);

        $this->start($scenario, $session)->assertRedirect();
        $this->submit($scenario, $session, $this->essayTextAnswers($session))->assertRedirect();

        // Admin menilai rendah -> total 25 dari 100 -> gagal
        $scores = $session->exam->questions->mapWithKeys(fn ($q) => [$q->id => 5])->all();
        $this->verifyEssays($scenario, $session, $scores)->assertSessionHas('success');

        $session->refresh();
        $this->assertEquals(ExamSession::STATUS_VERIFIED_FAIL, $session->status);
        $this->assertEquals(25, $session->score);
        $this->assertEquals(1, $scenario['competency']->refresh()->level);

        $this->actingAs($scenario['manager'])
            ->get(route('cbt.admin.sessions.pending-approval'))
            ->assertOk()
            ->assertDontSee($scenario['employee']->name);
    }

    // ============================================
    // THRESHOLD
    // ============================================

    public function test_exact_threshold_score_still_passes(): void
    {
        $scenario = $this->buildCbtScenario();
        $this->createMcQuestions($scenario['skill'], 4, 'A', 'QS-E2E-THOLD');
        $session = $this->assignThroughStore($scenario, 'QS-E2E-THOLD', 75);

        $this->start($scenario, $session)->assertRedirect();

        $answers = $this->allCorrectAnswers($session);
        $keys = array_keys($answers);
        $answers[$keys[3]] = 'B'; // 3/4 = 75, KKM = 75 -> LULUS (>=)

        $this->submit($scenario, $session, $answers)->assertRedirect();

        $session->refresh();
        $this->assertEquals(75, $session->score);
        $this->assertEquals(ExamSession::STATUS_VERIFIED_PASS, $session->status);

        $this->approve($scenario, $session)->assertSessionHas('success');
        $this->assertEquals(ExamSession::STATUS_APPROVED, $session->refresh()->status);
        $this->assertEquals(2, $scenario['competency']->refresh()->level);
    }

    public function test_below_threshold_fails(): void
    {
        $scenario = $this->buildCbtScenario();
        $this->createMcQuestions($scenario['skill'], 4, 'A', 'QS-E2E-BELOW');
        $session = $this->assignThroughStore($scenario, 'QS-E2E-BELOW', 75);

        $this->start($scenario, $session)->assertRedirect();

        $answers = $this->allCorrectAnswers($session);
        $keys = array_keys($answers);
        $answers[$keys[1]] = 'B';
        $answers[$keys[2]] = 'B';

        $this->submit($scenario, $session, $answers)->assertRedirect();

        $session->refresh();
        $this->assertEquals(50, $session->score);
        $this->assertEquals(ExamSession::STATUS_VERIFIED_FAIL, $session->status);
    }

    // ============================================
    // STATUS TRANSITIONS
    // ============================================

    public function test_status_transitions_full_lifecycle(): void
    {
        $scenario = $this->buildCbtScenario();
        $this->createMcQuestions($scenario['skill'], 4, 'A', 'QS-E2E-TRANS');
        $session = $this->assignThroughStore($scenario, 'QS-E2E-TRANS');

        $this->assertEquals(ExamSession::STATUS_ASSIGNED, $session->status);

        $this->start($scenario, $session)->assertRedirect();
        $this->assertEquals(ExamSession::STATUS_STARTED, $session->refresh()->status);

        $this->submit($scenario, $session, $this->allCorrectAnswers($session))->assertRedirect();
        $this->assertEquals(ExamSession::STATUS_VERIFIED_PASS, $session->refresh()->status);

        $this->approve($scenario, $session)->assertSessionHas('success');
        $this->assertEquals(ExamSession::STATUS_APPROVED, $session->refresh()->status);
        $this->assertNotNull($session->submitted_at);
        $this->assertNotNull($session->finished_at);
        $this->assertNotNull($session->verified_at);
        $this->assertNotNull($session->decided_at);
    }

    // ============================================
    // HISTORY DATA INTEGRITY
    // ============================================

    public function test_approval_history_preserves_all_quantitative_and_qualitative_data(): void
    {
        $scenario = $this->buildCbtScenario();
        $this->createMcQuestions($scenario['skill'], 4, 'A', 'QS-E2E-HIST');
        $session = $this->assignThroughStore($scenario, 'QS-E2E-HIST');

        $this->start($scenario, $session)->assertRedirect();
        $this->submit($scenario, $session, $this->allCorrectAnswers($session))->assertRedirect();

        $this->approve($scenario, $session)->assertSessionHas('success');

        $session->refresh();

        // Kuantitatif tersimpan di session
        $this->assertEquals(100, $session->score);
        $this->assertEquals(ExamSession::DECISION_APPROVED, $session->manager_decision);
        $this->assertEquals($scenario['manager']->id, $session->decided_by);
        $this->assertNotNull($session->decided_at);

        // Jawaban per soal tersimpan
        foreach ($session->exam->questions as $question) {
            $this->assertDatabaseHas('exam_answers', [
                'exam_session_id' => $session->id,
                'question_id' => $question->id,
                'is_correct' => true,
            ]);
        }

        // Kualitatif 5 kriteria + metode tersimpan
        $this->assertDatabaseHas('manager_assessments', [
            'exam_session_id' => $session->id,
            'assessment_method' => 'interview',
            'sop_understanding' => 'memenuhi',
            'competency_application' => 'memenuhi',
            'independence' => 'memenuhi',
            'problem_solving' => 'memenuhi',
            'readiness' => 'memenuhi',
        ]);

        // Riwayat kompetensi tersimpan
        $this->assertDatabaseHas('employee_competency_histories', [
            'exam_session_id' => $session->id,
            'previous_level' => 1,
            'new_level' => 2,
            'change_type' => 'up',
            'change_source' => 'exam_pass',
            'changed_by' => $scenario['manager']->id,
        ]);

        // Muncul di tab riwayat approval manager
        $this->actingAs($scenario['manager'])
            ->get(route('cbt.admin.sessions.pending-approval', ['tab' => 'history']))
            ->assertOk()
            ->assertSee($scenario['employee']->name);
    }

    public function test_rejection_history_preserves_notes_and_keeps_level(): void
    {
        $scenario = $this->buildCbtScenario();
        $this->createMcQuestions($scenario['skill'], 4, 'A', 'QS-E2E-REJ');
        $session = $this->assignThroughStore($scenario, 'QS-E2E-REJ');

        $this->start($scenario, $session)->assertRedirect();
        $this->submit($scenario, $session, $this->allCorrectAnswers($session))->assertRedirect();

        $this->actingAs($scenario['manager'])
            ->post(route('cbt.admin.sessions.reject-level', $session), [
                'manager_notes' => 'Perlu pendampingan lebih lanjut sebelum naik level.',
                'assessment_method' => 'observation',
                'sop_understanding' => 'memenuhi',
                'competency_application' => 'perlu_perbaikan',
                'independence' => 'perlu_perbaikan',
                'problem_solving' => 'memenuhi',
                'readiness' => 'tidak_memenuhi',
            ])
            ->assertRedirect(route('cbt.admin.sessions.pending-approval'))
            ->assertSessionHas('success');

        $session->refresh();
        $this->assertEquals(ExamSession::STATUS_REJECTED, $session->status);
        $this->assertEquals(ExamSession::DECISION_REJECTED, $session->manager_decision);
        $this->assertEquals('Perlu pendampingan lebih lanjut sebelum naik level.', $session->manager_notes);
        $this->assertEquals($scenario['manager']->id, $session->decided_by);

        // Kualitatif tersimpan meskipun ditolak
        $assessment = ManagerAssessment::where('exam_session_id', $session->id)->firstOrFail();
        $this->assertEquals('observation', $assessment->assessment_method);
        $this->assertEquals('tidak_memenuhi', $assessment->readiness);

        // Level tetap 1 dan tidak ada riwayat kenaikan
        $this->assertEquals(1, $scenario['competency']->refresh()->level);
        $this->assertDatabaseMissing('employee_competency_histories', ['exam_session_id' => $session->id]);

        $this->actingAs($scenario['manager'])
            ->get(route('cbt.admin.sessions.pending-approval', ['tab' => 'history']))
            ->assertOk()
            ->assertSee($scenario['employee']->name);
    }

    // ============================================
    // GUARDS / ELIGIBILITY
    // ============================================

    public function test_employee_cannot_start_before_scheduled_start(): void
    {
        $scenario = $this->buildCbtScenario();
        $this->createMcQuestions($scenario['skill'], 4, 'A', 'QS-E2E-SCHED');
        $session = $this->assignThroughStore(
            $scenario,
            'QS-E2E-SCHED',
            70,
            now()->addDay()->format('Y-m-d H:i:s')
        );

        $this->assertEquals(ExamSession::STATUS_ASSIGNED, $session->status);

        $this->start($scenario, $session)
            ->assertRedirect(route('cbt.employee.dashboard'))
            ->assertSessionHas('error');

        $this->assertEquals(ExamSession::STATUS_ASSIGNED, $session->refresh()->status);
    }

    public function test_employee_cannot_submit_without_starting(): void
    {
        $scenario = $this->buildCbtScenario();
        $this->createMcQuestions($scenario['skill'], 4, 'A', 'QS-E2E-NOSTART');
        $session = $this->assignThroughStore($scenario, 'QS-E2E-NOSTART');

        $this->assertEquals(ExamSession::STATUS_ASSIGNED, $session->status);

        $this->submit($scenario, $session, $this->allCorrectAnswers($session))
            ->assertRedirect(route('cbt.employee.dashboard'))
            ->assertSessionHas('error');

        $this->assertEquals(ExamSession::STATUS_ASSIGNED, $session->refresh()->status);
    }

    public function test_admin_assign_skips_employee_with_wrong_level(): void
    {
        $scenario = $this->buildCbtScenario();
        $this->createMcQuestions($scenario['skill'], 4, 'A', 'QS-E2E-LEVEL');

        // Karyawan sudah level 2 -> tidak boleh ikut ujian target level 2 (harus level 1)
        $scenario['competency']->update(['level' => 2]);

        $this->actingAs($scenario['admin'])
            ->post(route('cbt.admin.sessions.store'), [
                'duration_minutes' => 60,
                'passing_score' => 70,
                'deadline_at' => now()->addDays(7)->format('Y-m-d H:i:s'),
                'question_set_ids' => ['QS-E2E-LEVEL'],
                'employee_niks' => [$scenario['employee']->nik],
            ])
            ->assertRedirect(route('cbt.admin.sessions.index'))
            ->assertSessionHas('success');

        // Tidak ada session yang dibuat untuk karyawan tersebut
        $this->assertDatabaseMissing('exam_sessions', [
            'employee_nik' => $scenario['employee']->nik,
        ]);
    }
}
