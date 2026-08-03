<?php

namespace Tests\Feature;

use App\Models\Department;
use App\Models\Division;
use App\Models\Employee;
use App\Models\EmployeeCompetency;
use App\Models\EmployeeCompetencyHistory;
use App\Models\Exam;
use App\Models\ExamSession;
use App\Models\ManagerAssessment;
use App\Models\Position;
use App\Models\Skill;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ManagerApprovalTest extends TestCase
{
    use RefreshDatabase;

    private array $scenario = [];

    private function createScenario(): array
    {
        $this->scenario = $this->buildScenario();

        return $this->scenario;
    }

    private function buildScenario(): array
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $manager = User::factory()->create(['role' => 'manager']);

        $department = Department::create(['name' => 'Dept Test']);
        $division = Division::create(['department_id' => $department->id, 'name' => 'Div Test']);
        $position = Position::create(['division_id' => $division->id, 'name' => 'Pos Test']);

        $employee = Employee::create([
            'nik' => 'TST001',
            'name' => 'Test Employee',
            'email' => 'test.employee@example.com',
            'department_id' => $department->id,
            'division_id' => $division->id,
            'position_id' => $position->id,
            'status' => 'Aktif',
        ]);

        $skill = Skill::create([
            'code' => 'TST-SKILL',
            'name' => 'Test Skill',
            'category' => 'Technical',
        ]);

        $exam = Exam::create([
            'skill_id' => $skill->id,
            'title' => 'Test Exam Level 2',
            'target_level' => 2,
            'passing_score' => 70,
            'duration_minutes' => 60,
            'is_published' => true,
            'status' => 'active',
        ]);

        EmployeeCompetency::create([
            'employee_nik' => $employee->nik,
            'skill_id' => $skill->id,
            'level' => 1,
            'verified_by' => $admin->id,
            'verified_at' => now(),
            'notes' => 'Initial level',
        ]);

        $session = ExamSession::create([
            'exam_id' => $exam->id,
            'employee_nik' => $employee->nik,
            'score' => 85,
            'status' => ExamSession::STATUS_VERIFIED_PASS,
            'verified_by' => $admin->id,
            'verified_at' => now(),
            'admin_notes' => 'Verified by admin',
        ]);

        return compact('admin', 'manager', 'employee', 'skill', 'exam', 'session');
    }

    // ============================================
    // ACCESS CONTROL
    // ============================================

    public function test_manager_can_access_pending_approval_page(): void
    {
        $this->createScenario();
        $manager = $this->scenario['manager'];

        $this->actingAs($manager)
            ->get(route('cbt.admin.sessions.pending-approval'))
            ->assertOk();
    }

    public function test_admin_cannot_access_manager_only_approval_page(): void
    {
        $this->createScenario();
        $admin = $this->scenario['admin'];

        $this->actingAs($admin)
            ->get(route('cbt.admin.sessions.pending-approval'))
            ->assertRedirect(route('dashboard'));
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $this->get(route('cbt.admin.sessions.pending-approval'))
            ->assertRedirect(route('login'));
    }

    // ============================================
    // QUALITATIVE ASSESSMENT PAGE
    // ============================================

    public function test_manager_can_view_qualitative_assessment_page(): void
    {
        $scenario = $this->createScenario();
        $manager = $scenario['manager'];
        $session = $scenario['session'];

        $this->actingAs($manager)
            ->get(route('cbt.admin.sessions.assessment', $session))
            ->assertOk()
            ->assertSee($scenario['employee']->name);
    }

    public function test_qualitative_assessment_redirects_when_session_not_pending(): void
    {
        $scenario = $this->createScenario();
        $manager = $scenario['manager'];
        $session = $scenario['session'];

        $session->update([
            'status' => ExamSession::STATUS_APPROVED,
            'manager_decision' => ExamSession::DECISION_APPROVED,
        ]);

        $this->actingAs($manager)
            ->get(route('cbt.admin.sessions.assessment', $session))
            ->assertRedirect(route('cbt.admin.sessions.pending-approval'));
    }

    // ============================================
    // APPROVE LEVEL
    // ============================================

    public function test_manager_can_approve_level_upgrade(): void
    {
        $scenario = $this->createScenario();
        $manager = $scenario['manager'];
        $session = $scenario['session'];

        $this->actingAs($manager)
            ->post(route('cbt.admin.sessions.approve-level', $session), [
                'assessment_method' => 'interview',
                'sop_understanding' => 'memenuhi',
                'competency_application' => 'memenuhi',
                'independence' => 'memenuhi',
                'problem_solving' => 'memenuhi',
                'readiness' => 'memenuhi',
                'verification_date' => now()->format('Y-m-d'),
                'manager_notes' => 'Karyawan sudah siap naik level.',
            ])
            ->assertRedirect(route('cbt.admin.sessions.pending-approval'));

        $session->refresh();

        $this->assertEquals(ExamSession::STATUS_APPROVED, $session->status);
        $this->assertEquals(ExamSession::DECISION_APPROVED, $session->manager_decision);
        $this->assertEquals($manager->id, $session->decided_by);
        $this->assertNotNull($session->decided_at);
        $this->assertEquals('Karyawan sudah siap naik level.', $session->manager_notes);

        // Employee competency updated to target level
        $competency = EmployeeCompetency::where('employee_nik', $scenario['employee']->nik)
            ->where('skill_id', $scenario['skill']->id)
            ->first();

        $this->assertEquals($scenario['exam']->target_level, $competency->level);

        // History record created
        $history = EmployeeCompetencyHistory::where('employee_competency_id', $competency->id)
            ->where('exam_session_id', $session->id)
            ->first();

        $this->assertNotNull($history);
        $this->assertEquals(1, $history->previous_level);
        $this->assertEquals(2, $history->new_level);
        $this->assertEquals('up', $history->change_type);
        $this->assertEquals('exam_pass', $history->change_source);

        // Manager assessment saved
        $assessment = ManagerAssessment::where('exam_session_id', $session->id)->first();
        $this->assertNotNull($assessment);
        $this->assertEquals('interview', $assessment->assessment_method);
        $this->assertEquals('memenuhi', $assessment->sop_understanding);
    }

    public function test_approve_level_requires_notes_when_tidak_memenuhi(): void
    {
        $scenario = $this->createScenario();
        $manager = $scenario['manager'];
        $session = $scenario['session'];

        $this->actingAs($manager)
            ->post(route('cbt.admin.sessions.approve-level', $session), [
                'sop_understanding' => 'tidak_memenuhi',
                'competency_application' => 'memenuhi',
                'independence' => 'memenuhi',
                'problem_solving' => 'memenuhi',
                'readiness' => 'memenuhi',
            ])
            ->assertSessionHasErrors('manager_notes');

        $session->refresh();
        $this->assertEquals(ExamSession::STATUS_VERIFIED_PASS, $session->status);
    }

    public function test_approve_level_allows_no_notes_when_all_memenuhi(): void
    {
        $scenario = $this->createScenario();
        $manager = $scenario['manager'];
        $session = $scenario['session'];

        $this->actingAs($manager)
            ->post(route('cbt.admin.sessions.approve-level', $session), [
                'sop_understanding' => 'memenuhi',
                'competency_application' => 'memenuhi',
                'independence' => 'memenuhi',
                'problem_solving' => 'memenuhi',
                'readiness' => 'memenuhi',
            ])
            ->assertRedirect(route('cbt.admin.sessions.pending-approval'));

        $session->refresh();
        $this->assertEquals(ExamSession::STATUS_APPROVED, $session->status);
        $this->assertNull($session->manager_notes);
    }

    public function test_approve_level_redirects_when_session_not_pending(): void
    {
        $scenario = $this->createScenario();
        $manager = $scenario['manager'];
        $session = $scenario['session'];

        $session->update([
            'status' => ExamSession::STATUS_APPROVED,
            'manager_decision' => ExamSession::DECISION_APPROVED,
        ]);

        $this->actingAs($manager)
            ->post(route('cbt.admin.sessions.approve-level', $session), [
                'manager_notes' => 'Sudah disetujui sebelumnya.',
            ])
            ->assertSessionHas('error')
            ->assertRedirect();

        $session->refresh();
        $this->assertEquals(ExamSession::STATUS_APPROVED, $session->status);
    }

    // ============================================
    // REJECT LEVEL
    // ============================================

    public function test_manager_can_reject_level_upgrade(): void
    {
        $scenario = $this->createScenario();
        $manager = $scenario['manager'];
        $session = $scenario['session'];

        $this->actingAs($manager)
            ->post(route('cbt.admin.sessions.reject-level', $session), [
                'manager_notes' => 'Karyawan belum siap naik level.',
            ])
            ->assertRedirect(route('cbt.admin.sessions.pending-approval'));

        $session->refresh();

        $this->assertEquals(ExamSession::STATUS_REJECTED, $session->status);
        $this->assertEquals(ExamSession::DECISION_REJECTED, $session->manager_decision);
        $this->assertEquals($manager->id, $session->decided_by);
        $this->assertEquals('Karyawan belum siap naik level.', $session->manager_notes);

        // Level should NOT be upgraded
        $competency = EmployeeCompetency::where('employee_nik', $scenario['employee']->nik)
            ->where('skill_id', $scenario['skill']->id)
            ->first();

        $this->assertEquals(1, $competency->level);
    }

    public function test_reject_level_requires_notes(): void
    {
        $scenario = $this->createScenario();
        $manager = $scenario['manager'];
        $session = $scenario['session'];

        $this->actingAs($manager)
            ->post(route('cbt.admin.sessions.reject-level', $session))
            ->assertSessionHasErrors('manager_notes');

        $session->refresh();
        $this->assertEquals(ExamSession::STATUS_VERIFIED_PASS, $session->status);
    }

    // ============================================
    // HISTORY TAB
    // ============================================

    public function test_decided_sessions_appear_in_history_tab(): void
    {
        $scenario = $this->createScenario();
        $manager = $scenario['manager'];
        $session = $scenario['session'];

        $this->actingAs($manager)
            ->post(route('cbt.admin.sessions.reject-level', $session), [
                'manager_notes' => 'Belum siap.',
            ])
            ->assertRedirect(route('cbt.admin.sessions.pending-approval'));

        $this->actingAs($manager)
            ->get(route('cbt.admin.sessions.pending-approval', ['tab' => 'history']))
            ->assertOk()
            ->assertSee($scenario['employee']->name)
            ->assertSee('Ditolak');
    }
}
