<?php

namespace Tests\Feature;

use App\Models\EmployeeCompetency;
use App\Models\EmployeeCompetencyHistory;
use App\Models\ExamSession;
use App\Models\ManagerAssessment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\BuildsCbtScenario;
use Tests\TestCase;

/**
 * Manager approval flow (kualitatif):
 * - 5 kriteria: sop_understanding, competency_application, independence,
 *   problem_solving, readiness (memenuhi | perlu_perbaikan | tidak_memenuhi)
 * - 3 metode verifikasi: interview, observation, both
 * - approve -> status approved + level karyawan naik + riwayat tercatat
 * - reject  -> status rejected + level tidak berubah
 * - daftar Menunggu Persetujuan -> Riwayat Approval
 */
class CbtManagerApprovalComprehensiveTest extends TestCase
{
    use BuildsCbtScenario, RefreshDatabase;

    /**
     * Sesi sudah lolos verifikasi admin (verified_pass) dan menunggu keputusan manager.
     */
    private function pendingApprovalSession(array $scenario, int $score = 80): ExamSession
    {
        return ExamSession::create([
            'exam_id' => $scenario['mcExam']->id,
            'employee_nik' => $scenario['employee']->nik,
            'status' => ExamSession::STATUS_VERIFIED_PASS,
            'manager_decision' => ExamSession::DECISION_PENDING,
            'score' => $score,
            'verified_by' => $scenario['admin']->id,
            'verified_at' => now(),
        ]);
    }

    private function approvePayload(array $overrides = []): array
    {
        return array_merge([
            'assessment_method' => 'interview',
            'sop_understanding' => 'memenuhi',
            'competency_application' => 'memenuhi',
            'independence' => 'memenuhi',
            'problem_solving' => 'memenuhi',
            'readiness' => 'memenuhi',
            'verification_date' => now()->format('Y-m-d'),
            'manager_notes' => 'Karyawan siap naik level.',
        ], $overrides);
    }

    private function approve(array $scenario, ExamSession $session, array $payload)
    {
        return $this->actingAs($scenario['manager'])
            ->post(route('cbt.admin.sessions.approve-level', $session), $payload);
    }

    private function reject(array $scenario, ExamSession $session, array $payload)
    {
        return $this->actingAs($scenario['manager'])
            ->post(route('cbt.admin.sessions.reject-level', $session), $payload);
    }

    private function criteriaFields(): array
    {
        return ['sop_understanding', 'competency_application', 'independence', 'problem_solving', 'readiness'];
    }

    // ============================================
    // APPROVE - LULUS
    // ============================================

    public function test_approve_all_5_criteria_memenuhi_upgrades_level(): void
    {
        $scenario = $this->buildCbtScenario();
        $session = $this->pendingApprovalSession($scenario);

        $this->approve($scenario, $session, $this->approvePayload())
            ->assertRedirect(route('cbt.admin.sessions.pending-approval'))
            ->assertSessionHas('success');

        $session->refresh();
        $this->assertEquals(ExamSession::STATUS_APPROVED, $session->status);
        $this->assertEquals(ExamSession::DECISION_APPROVED, $session->manager_decision);
        $this->assertEquals($scenario['manager']->id, $session->decided_by);
        $this->assertNotNull($session->decided_at);

        $this->assertEquals(2, $scenario['competency']->refresh()->level);
        $this->assertDatabaseHas('manager_assessments', [
            'exam_session_id' => $session->id,
            'sop_understanding' => 'memenuhi',
            'competency_application' => 'memenuhi',
            'independence' => 'memenuhi',
            'problem_solving' => 'memenuhi',
            'readiness' => 'memenuhi',
        ]);
    }

    public function test_approve_with_4_memenuhi_1_perlu_perbaikan_still_upgrades(): void
    {
        $scenario = $this->buildCbtScenario();
        $session = $this->pendingApprovalSession($scenario);

        $this->approve($scenario, $session, $this->approvePayload([
            'independence' => 'perlu_perbaikan',
        ]))->assertSessionHas('success');

        $session->refresh();
        $this->assertEquals(ExamSession::STATUS_APPROVED, $session->status);
        $this->assertEquals(2, $scenario['competency']->refresh()->level);
    }

    public function test_approve_with_interview_method_stored(): void
    {
        $scenario = $this->buildCbtScenario();
        $session = $this->pendingApprovalSession($scenario);

        $this->approve($scenario, $session, $this->approvePayload(['assessment_method' => 'interview']))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('manager_assessments', [
            'exam_session_id' => $session->id,
            'assessment_method' => 'interview',
        ]);
    }

    public function test_approve_with_observation_method_stored(): void
    {
        $scenario = $this->buildCbtScenario();
        $session = $this->pendingApprovalSession($scenario);

        $this->approve($scenario, $session, $this->approvePayload(['assessment_method' => 'observation']))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('manager_assessments', [
            'exam_session_id' => $session->id,
            'assessment_method' => 'observation',
        ]);
    }

    public function test_approve_with_both_methods_stored(): void
    {
        $scenario = $this->buildCbtScenario();
        $session = $this->pendingApprovalSession($scenario);

        $this->approve($scenario, $session, $this->approvePayload(['assessment_method' => 'both']))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('manager_assessments', [
            'exam_session_id' => $session->id,
            'assessment_method' => 'both',
        ]);
    }

    public function test_approve_saves_all_5_assessment_fields(): void
    {
        $scenario = $this->buildCbtScenario();
        $session = $this->pendingApprovalSession($scenario);

        $this->approve($scenario, $session, $this->approvePayload([
            'sop_understanding' => 'memenuhi',
            'competency_application' => 'perlu_perbaikan',
            'independence' => 'memenuhi',
            'problem_solving' => 'perlu_perbaikan',
            'readiness' => 'memenuhi',
        ]))->assertSessionHas('success');

        $assessment = ManagerAssessment::where('exam_session_id', $session->id)->first();
        $this->assertNotNull($assessment);
        $this->assertEquals('memenuhi', $assessment->sop_understanding);
        $this->assertEquals('perlu_perbaikan', $assessment->competency_application);
        $this->assertEquals('memenuhi', $assessment->independence);
        $this->assertEquals('perlu_perbaikan', $assessment->problem_solving);
        $this->assertEquals('memenuhi', $assessment->readiness);
        $this->assertEquals($scenario['manager']->id, $assessment->created_by);
        $this->assertNotNull($assessment->verification_date);
    }

    public function test_approve_creates_competency_history(): void
    {
        $scenario = $this->buildCbtScenario();
        $session = $this->pendingApprovalSession($scenario);

        $this->approve($scenario, $session, $this->approvePayload())
            ->assertSessionHas('success');

        $competency = EmployeeCompetency::where('employee_nik', $scenario['employee']->nik)
            ->where('skill_id', $scenario['skill']->id)
            ->firstOrFail();

        $this->assertEquals(2, $competency->level);

        $history = EmployeeCompetencyHistory::where('exam_session_id', $session->id)->first();
        $this->assertNotNull($history);
        $this->assertEquals(1, $history->previous_level);
        $this->assertEquals(2, $history->new_level);
        $this->assertEquals('up', $history->change_type);
        $this->assertEquals('exam_pass', $history->change_source);
        $this->assertEquals($scenario['manager']->id, $history->changed_by);
        $this->assertEquals($competency->id, $history->employee_competency_id);
    }

    public function test_approve_removes_session_from_pending_queue(): void
    {
        $scenario = $this->buildCbtScenario();
        $session = $this->pendingApprovalSession($scenario);

        $this->actingAs($scenario['manager'])
            ->get(route('cbt.admin.sessions.pending-approval'))
            ->assertOk()
            ->assertSee($scenario['employee']->name);

        $this->approve($scenario, $session, $this->approvePayload())
            ->assertSessionHas('success');

        $this->actingAs($scenario['manager'])
            ->get(route('cbt.admin.sessions.pending-approval'))
            ->assertOk()
            ->assertDontSee($scenario['employee']->name);
    }

    public function test_approve_appears_in_history(): void
    {
        $scenario = $this->buildCbtScenario();
        $session = $this->pendingApprovalSession($scenario);

        $this->approve($scenario, $session, $this->approvePayload())
            ->assertSessionHas('success');

        $this->actingAs($scenario['manager'])
            ->get(route('cbt.admin.sessions.pending-approval', ['tab' => 'history']))
            ->assertOk()
            ->assertSee($scenario['employee']->name);
    }

    public function test_approve_notes_optional_when_no_tidak_memenuhi(): void
    {
        $scenario = $this->buildCbtScenario();
        $session = $this->pendingApprovalSession($scenario);

        $this->approve($scenario, $session, $this->approvePayload(['manager_notes' => null]))
            ->assertSessionHas('success');

        $this->assertEquals(ExamSession::STATUS_APPROVED, $session->refresh()->status);
        $this->assertNull($session->manager_notes);
    }

    public function test_approve_requires_notes_when_any_tidak_memenuhi(): void
    {
        $scenario = $this->buildCbtScenario();
        $session = $this->pendingApprovalSession($scenario);

        $this->approve($scenario, $session, $this->approvePayload([
            'readiness' => 'tidak_memenuhi',
            'manager_notes' => null,
        ]))->assertSessionHasErrors('manager_notes');

        $this->assertEquals(ExamSession::STATUS_VERIFIED_PASS, $session->refresh()->status);
    }

    public function test_approve_still_allowed_with_notes_when_tidak_memenuhi(): void
    {
        $scenario = $this->buildCbtScenario();
        $session = $this->pendingApprovalSession($scenario);

        $this->approve($scenario, $session, $this->approvePayload([
            'readiness' => 'tidak_memenuhi',
            'manager_notes' => 'Dengan catatan perbaikan pada kesiapan level berikutnya.',
        ]))->assertSessionHas('success');

        $this->assertEquals(ExamSession::STATUS_APPROVED, $session->refresh()->status);
        $this->assertEquals(2, $scenario['competency']->refresh()->level);
    }

    public function test_cannot_approve_already_approved_session(): void
    {
        $scenario = $this->buildCbtScenario();
        $session = $this->pendingApprovalSession($scenario);

        $this->approve($scenario, $session, $this->approvePayload())
            ->assertSessionHas('success');

        $this->approve($scenario, $session, $this->approvePayload())
            ->assertSessionHas('error');

        $this->assertEquals(ExamSession::STATUS_APPROVED, $session->refresh()->status);
        $this->assertEquals(1, EmployeeCompetencyHistory::where('exam_session_id', $session->id)->count());
    }

    // ============================================
    // REJECT - TIDAK LULUS
    // ============================================

    public function test_reject_marks_rejected_and_keeps_level(): void
    {
        $scenario = $this->buildCbtScenario();
        $session = $this->pendingApprovalSession($scenario);

        $this->reject($scenario, $session, ['manager_notes' => 'Perlu pendampingan lebih lanjut.'])
            ->assertRedirect(route('cbt.admin.sessions.pending-approval'))
            ->assertSessionHas('success');

        $session->refresh();
        $this->assertEquals(ExamSession::STATUS_REJECTED, $session->status);
        $this->assertEquals(ExamSession::DECISION_REJECTED, $session->manager_decision);
        $this->assertEquals($scenario['manager']->id, $session->decided_by);
        $this->assertNotNull($session->decided_at);
        $this->assertEquals('Perlu pendampingan lebih lanjut.', $session->manager_notes);

        // Level tidak berubah
        $this->assertEquals(1, $scenario['competency']->refresh()->level);
        $this->assertDatabaseMissing('employee_competency_histories', ['exam_session_id' => $session->id]);
    }

    public function test_reject_requires_manager_notes(): void
    {
        $scenario = $this->buildCbtScenario();
        $session = $this->pendingApprovalSession($scenario);

        $this->reject($scenario, $session, [])->assertSessionHasErrors('manager_notes');

        $this->assertEquals(ExamSession::STATUS_VERIFIED_PASS, $session->refresh()->status);
    }

    public function test_reject_with_4_memenuhi_1_tidak_memenuhi(): void
    {
        $scenario = $this->buildCbtScenario();
        $session = $this->pendingApprovalSession($scenario);

        $this->reject($scenario, $session, [
            'manager_notes' => 'Kesiapan belum memenuhi.',
            'assessment_method' => 'interview',
            'sop_understanding' => 'memenuhi',
            'competency_application' => 'memenuhi',
            'independence' => 'memenuhi',
            'problem_solving' => 'memenuhi',
            'readiness' => 'tidak_memenuhi',
        ])->assertSessionHas('success');

        $session->refresh();
        $this->assertEquals(ExamSession::STATUS_REJECTED, $session->status);
        $this->assertEquals(1, $scenario['competency']->refresh()->level);
    }

    public function test_reject_with_3_memenuhi_2_perlu_perbaikan(): void
    {
        $scenario = $this->buildCbtScenario();
        $session = $this->pendingApprovalSession($scenario);

        $this->reject($scenario, $session, [
            'manager_notes' => 'Masih perlu perbaikan di dua kriteria.',
            'assessment_method' => 'observation',
            'sop_understanding' => 'memenuhi',
            'competency_application' => 'memenuhi',
            'independence' => 'perlu_perbaikan',
            'problem_solving' => 'perlu_perbaikan',
            'readiness' => 'memenuhi',
        ])->assertSessionHas('success');

        $session->refresh();
        $this->assertEquals(ExamSession::STATUS_REJECTED, $session->status);
        $this->assertEquals(1, $scenario['competency']->refresh()->level);
    }

    public function test_reject_saves_assessment_data(): void
    {
        $scenario = $this->buildCbtScenario();
        $session = $this->pendingApprovalSession($scenario);

        $this->reject($scenario, $session, [
            'manager_notes' => 'Belum siap.',
            'assessment_method' => 'both',
            'sop_understanding' => 'memenuhi',
            'competency_application' => 'perlu_perbaikan',
            'independence' => 'tidak_memenuhi',
            'problem_solving' => 'perlu_perbaikan',
            'readiness' => 'tidak_memenuhi',
            'verification_date' => now()->format('Y-m-d'),
        ])->assertSessionHas('success');

        $assessment = ManagerAssessment::where('exam_session_id', $session->id)->first();
        $this->assertNotNull($assessment);
        $this->assertEquals('both', $assessment->assessment_method);
        $this->assertEquals('memenuhi', $assessment->sop_understanding);
        $this->assertEquals('tidak_memenuhi', $assessment->independence);
        $this->assertEquals($scenario['manager']->id, $assessment->created_by);
    }

    public function test_reject_removes_from_pending_queue(): void
    {
        $scenario = $this->buildCbtScenario();
        $session = $this->pendingApprovalSession($scenario);

        $this->reject($scenario, $session, ['manager_notes' => 'Ditolak.'])
            ->assertSessionHas('success');

        $this->actingAs($scenario['manager'])
            ->get(route('cbt.admin.sessions.pending-approval'))
            ->assertOk()
            ->assertDontSee($scenario['employee']->name);
    }

    public function test_reject_appears_in_history(): void
    {
        $scenario = $this->buildCbtScenario();
        $session = $this->pendingApprovalSession($scenario);

        $this->reject($scenario, $session, ['manager_notes' => 'Ditolak.'])
            ->assertSessionHas('success');

        $this->actingAs($scenario['manager'])
            ->get(route('cbt.admin.sessions.pending-approval', ['tab' => 'history']))
            ->assertOk()
            ->assertSee($scenario['employee']->name);
    }

    // ============================================
    // ASSESSMENT PAGE
    // ============================================

    public function test_manager_can_open_assessment_page_for_pending_session(): void
    {
        $scenario = $this->buildCbtScenario();
        $session = $this->pendingApprovalSession($scenario);

        $this->actingAs($scenario['manager'])
            ->get(route('cbt.admin.sessions.assessment', $session))
            ->assertOk()
            ->assertSee('Pemahaman dan penerapan SOP');
    }

    public function test_assessment_page_rejected_for_non_pending_session(): void
    {
        $scenario = $this->buildCbtScenario();
        $session = $this->pendingApprovalSession($scenario);

        $this->approve($scenario, $session, $this->approvePayload())
            ->assertSessionHas('success');

        $this->actingAs($scenario['manager'])
            ->get(route('cbt.admin.sessions.assessment', $session))
            ->assertRedirect(route('cbt.admin.sessions.pending-approval'))
            ->assertSessionHas('error');
    }

    public function test_admin_cannot_use_manager_approval_endpoint(): void
    {
        $scenario = $this->buildCbtScenario();

        // Admin bukan manager -> diblokir middleware is.manager (redirect + error)
        $session = $this->pendingApprovalSession($scenario);

        $this->actingAs($scenario['admin'])
            ->post(route('cbt.admin.sessions.approve-level', $session), $this->approvePayload())
            ->assertRedirect(route('dashboard'))
            ->assertSessionHas('error');

        $this->assertEquals(ExamSession::STATUS_VERIFIED_PASS, $session->refresh()->status);
    }

    // ============================================
    // OVERALL RESULT HELPER (kriteria tidak_memenuhi terdeteksi)
    // ============================================

    public function test_manager_assessment_overall_result_detects_tidak_memenuhi(): void
    {
        $scenario = $this->buildCbtScenario();
        $session = $this->pendingApprovalSession($scenario);

        $this->reject($scenario, $session, [
            'manager_notes' => 'Tidak memenuhi beberapa kriteria.',
            'assessment_method' => 'interview',
            'sop_understanding' => 'memenuhi',
            'competency_application' => 'memenuhi',
            'independence' => 'memenuhi',
            'problem_solving' => 'memenuhi',
            'readiness' => 'tidak_memenuhi',
        ])->assertSessionHas('success');

        $assessment = ManagerAssessment::where('exam_session_id', $session->id)->firstOrFail();
        $this->assertEquals('tidak_memenuhi', $assessment->getOverallResult());
    }

    public function test_manager_assessment_overall_result_memenuhi_when_all_pass(): void
    {
        $scenario = $this->buildCbtScenario();
        $session = $this->pendingApprovalSession($scenario);

        $this->approve($scenario, $session, $this->approvePayload())
            ->assertSessionHas('success');

        $assessment = ManagerAssessment::where('exam_session_id', $session->id)->firstOrFail();
        $this->assertEquals('memenuhi', $assessment->getOverallResult());
    }
}
