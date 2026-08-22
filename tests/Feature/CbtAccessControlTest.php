<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\BuildsCbtScenario;
use Tests\TestCase;

class CbtAccessControlTest extends TestCase
{
    use BuildsCbtScenario, RefreshDatabase;

    public function test_guest_is_redirected_to_login_on_admin_cbt_routes(): void
    {
        $this->get(route('cbt.admin.questions.index'))->assertRedirect(route('login'));
        $this->get(route('cbt.admin.exams.index'))->assertRedirect(route('login'));
        $this->get(route('cbt.admin.sessions.index'))->assertRedirect(route('login'));
        $this->get(route('cbt.admin.sessions.pending-approval'))->assertRedirect(route('login'));
    }

    public function test_guest_is_redirected_to_login_on_employee_cbt_routes(): void
    {
        $this->get(route('cbt.employee.dashboard'))->assertRedirect(route('login'));
    }

    public function test_employee_user_redirected_away_from_admin_cbt_routes(): void
    {
        $scenario = $this->buildCbtScenario();

        $this->actingAs($scenario['employeeUser'])
            ->get(route('cbt.admin.questions.index'))
            ->assertRedirect(route('dashboard'));

        $this->actingAs($scenario['employeeUser'])
            ->get(route('cbt.admin.exams.index'))
            ->assertRedirect(route('dashboard'));

        $this->actingAs($scenario['employeeUser'])
            ->get(route('cbt.admin.sessions.index'))
            ->assertRedirect(route('dashboard'));
    }

    public function test_admin_can_access_admin_cbt_routes(): void
    {
        $scenario = $this->buildCbtScenario();

        $this->actingAs($scenario['admin'])->get(route('cbt.admin.questions.index'))->assertOk();
        $this->actingAs($scenario['admin'])->get(route('cbt.admin.exams.index'))->assertOk();
        $this->actingAs($scenario['admin'])->get(route('cbt.admin.sessions.index'))->assertOk();
        $this->actingAs($scenario['admin'])->get(route('cbt.admin.competency-matrix'))->assertOk();
    }

    public function test_manager_can_access_admin_cbt_routes(): void
    {
        $scenario = $this->buildCbtScenario();

        $this->actingAs($scenario['manager'])->get(route('cbt.admin.questions.index'))->assertOk();
        $this->actingAs($scenario['manager'])->get(route('cbt.admin.sessions.index'))->assertOk();
    }

    public function test_employee_user_cannot_access_manager_approval_page(): void
    {
        $scenario = $this->buildCbtScenario();

        $this->actingAs($scenario['employeeUser'])
            ->get(route('cbt.admin.sessions.pending-approval'))
            ->assertRedirect(route('dashboard'));
    }

    public function test_manager_cannot_act_as_employee_on_employee_cbt_flows(): void
    {
        // Manager tidak punya employee_nik -> redirect ke dashboard user.
        $scenario = $this->buildCbtScenario();

        $this->actingAs($scenario['manager'])
            ->post(route('cbt.employee.register', $scenario['mcExam']))
            ->assertRedirect(route('dashboard'))
            ->assertSessionHas('error');
    }
}
