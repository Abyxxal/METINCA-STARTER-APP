<?php

namespace Tests\Feature;

use App\Models\EmployeeCompetencyHistory;
use App\Models\ExamSession;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\BuildsCbtScenario;
use Tests\TestCase;

class CbtManagerApprovalFlowTest extends TestCase
{
    use BuildsCbtScenario, RefreshDatabase;

    /**
     * End-to-end: karyawan mengerjakan ujian MC -> auto-verify (verified_pass)
     * -> manager melihat di halaman approval -> approve -> level naik -> riwayat tercatat.
     */
    public function test_full_flow_employee_to_manager_approval(): void
    {
        $scenario = $this->buildCbtScenario();
        $employeeUser = $scenario['employeeUser'];
        $manager = $scenario['manager'];
        $mcExam = $scenario['mcExam'];
        $employee = $scenario['employee'];

        // 1. Karyawan mendaftar dan mengerjakan ujian MC (semua benar)
        $this->actingAs($employeeUser)->post(route('cbt.employee.register', $mcExam))->assertRedirect();

        $session = ExamSession::where('exam_id', $mcExam->id)
            ->where('employee_nik', $employee->nik)
            ->firstOrFail();

        $this->actingAs($employeeUser)->post(route('cbt.employee.start', $session))->assertRedirect();
        $answers = $scenario['mcQuestions']->mapWithKeys(fn ($q) => [$q->id => 'A'])->all();
        $this->actingAs($employeeUser)
            ->post(route('cbt.employee.submit', $session), ['answers' => $answers])
            ->assertRedirect(route('cbt.employee.result', $session));

        $session->refresh();
        $this->assertEquals(ExamSession::STATUS_VERIFIED_PASS, $session->status);
        $this->assertEquals(ExamSession::DECISION_PENDING, $session->manager_decision);

        // 2. Manager melihat sesi di halaman pending approval
        $this->actingAs($manager)
            ->get(route('cbt.admin.sessions.pending-approval'))
            ->assertOk()
            ->assertSee($employee->name);

        // 3. Manager menyetujui kenaikan level
        $this->actingAs($manager)
            ->post(route('cbt.admin.sessions.approve-level', $session), [
                'assessment_method' => 'observation',
                'sop_understanding' => 'memenuhi',
                'competency_application' => 'memenuhi',
                'independence' => 'memenuhi',
                'problem_solving' => 'memenuhi',
                'readiness' => 'memenuhi',
                'verification_date' => now()->format('Y-m-d'),
                'manager_notes' => 'Karyawan siap naik ke level 2.',
            ])
            ->assertRedirect(route('cbt.admin.sessions.pending-approval'));

        $session->refresh();
        $this->assertEquals(ExamSession::STATUS_APPROVED, $session->status);
        $this->assertEquals($manager->id, $session->decided_by);
        $this->assertNotNull($session->decided_at);

        // 4. Kompetensi karyawan naik ke level target (2)
        $competency = $scenario['competency']->refresh();
        $this->assertEquals(2, $competency->level);

        // 5. Riwayat kenaikan tercatat
        $history = EmployeeCompetencyHistory::where('exam_session_id', $session->id)->first();
        $this->assertNotNull($history);
        $this->assertEquals(1, $history->previous_level);
        $this->assertEquals(2, $history->new_level);
        $this->assertEquals('up', $history->change_type);
        $this->assertEquals('exam_pass', $history->change_source);

        // 6. Halaman hasil karyawan menampilkan catatan manager
        $this->actingAs($employeeUser)
            ->get(route('cbt.employee.result', $session))
            ->assertOk()
            ->assertSee('Karyawan siap naik ke level 2.');
    }

    public function test_full_flow_manager_rejection_keeps_level(): void
    {
        $scenario = $this->buildCbtScenario();
        $employeeUser = $scenario['employeeUser'];
        $manager = $scenario['manager'];
        $mcExam = $scenario['mcExam'];
        $employee = $scenario['employee'];

        $this->actingAs($employeeUser)->post(route('cbt.employee.register', $mcExam))->assertRedirect();
        $session = ExamSession::where('exam_id', $mcExam->id)
            ->where('employee_nik', $employee->nik)
            ->firstOrFail();

        $this->actingAs($employeeUser)->post(route('cbt.employee.start', $session))->assertRedirect();
        $answers = $scenario['mcQuestions']->mapWithKeys(fn ($q) => [$q->id => 'A'])->all();
        $this->actingAs($employeeUser)
            ->post(route('cbt.employee.submit', $session), ['answers' => $answers])
            ->assertRedirect(route('cbt.employee.result', $session));

        $session->refresh();
        $this->assertEquals(ExamSession::STATUS_VERIFIED_PASS, $session->status);

        $this->actingAs($manager)
            ->post(route('cbt.admin.sessions.reject-level', $session), [
                'manager_notes' => 'Perlu pendampingan lebih lanjut.',
            ])
            ->assertRedirect(route('cbt.admin.sessions.pending-approval'));

        $session->refresh();
        $this->assertEquals(ExamSession::STATUS_REJECTED, $session->status);

        // Level tidak naik
        $this->assertEquals(1, $scenario['competency']->refresh()->level);

        // Halaman hasil menampilkan catatan penolakan manager
        $this->actingAs($employeeUser)
            ->get(route('cbt.employee.result', $session))
            ->assertOk()
            ->assertSee('Perlu pendampingan lebih lanjut.');
    }
}
