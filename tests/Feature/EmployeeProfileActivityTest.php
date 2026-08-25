<?php

namespace Tests\Feature;

use App\Models\Department;
use App\Models\Division;
use App\Models\Employee;
use App\Models\Exam;
use App\Models\ExamSession;
use App\Models\Position;
use App\Models\Skill;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EmployeeProfileActivityTest extends TestCase
{
    use RefreshDatabase;

    private function makeScenario(): array
    {
        $department = Department::create(['name' => 'DEP-ACT', 'status' => 'active']);
        $division = Division::create(['department_id' => $department->id, 'name' => 'DIV-ACT']);
        $position = Position::create(['division_id' => $division->id, 'name' => 'POS-ACT']);

        $employee = Employee::create([
            'nik' => 'EMP-ACT',
            'name' => 'Karyawan Aktif',
            'email' => 'emp-act@example.com',
            'department_id' => $department->id,
            'division_id' => $division->id,
            'position_id' => $position->id,
            'status' => 'Aktif',
        ]);

        $user = User::create([
            'name' => 'Karyawan Aktif',
            'email' => 'emp-act@example.com',
            'nik' => $employee->nik,
            'password' => bcrypt('rahasia123'),
            'role' => 'user',
            'employee_nik' => $employee->nik,
            'password_changed_at' => now(),
        ]);

        return compact('employee', 'user', 'department', 'division', 'position');
    }

    private function makeExam(string $title): Exam
    {
        $skill = Skill::create([
            'division_id' => Division::first()->id,
            'code' => 'SK-' . substr(md5($title), 0, 8),
            'name' => $title,
        ]);

        return Exam::create([
            'skill_id' => $skill->id,
            'title' => $title,
            'target_level' => 1,
            'passing_score' => 70,
            'duration_minutes' => 30,
            'is_published' => true,
            'status' => 'active',
        ]);
    }

    public function test_my_profile_menampilkan_aktivitas_nyata_karyawan(): void
    {
        $scenario = $this->makeScenario();
        $nik = $scenario['employee']->nik;

        $examMilikku = $this->makeExam('Ujian Milikku');
        ExamSession::create([
            'exam_id' => $examMilikku->id,
            'employee_nik' => $nik,
            'status' => ExamSession::STATUS_STARTED,
        ]);

        // Aktivitas karyawan LAIN tidak boleh bocor ke profil ini
        $employeeLain = Employee::create([
            'nik' => 'EMP-LAIN',
            'name' => 'Karyawan Lain',
            'email' => 'emp-lain@example.com',
            'department_id' => $scenario['department']->id,
            'division_id' => $scenario['division']->id,
            'position_id' => $scenario['position']->id,
            'status' => 'Aktif',
        ]);
        $examOrang = $this->makeExam('Ujian Orang Lain');
        ExamSession::create([
            'exam_id' => $examOrang->id,
            'employee_nik' => $employeeLain->nik,
            'status' => ExamSession::STATUS_SUBMITTED,
        ]);

        $this->actingAs($scenario['user'])
            ->get('/my-profile')
            ->assertOk()
            ->assertSee('Ujian Milikku')
            ->assertSee(ExamSession::find(1)->getStatusLabel() === null ? '' : 'Sedang Berlangsung', false)
            ->assertDontSee('Machine Operation - Advanced')
            ->assertDontSee('Quality Control Basics')
            ->assertDontSee('Ujian Orang Lain');
    }

    public function test_my_profile_kosong_tanpa_aktivitas(): void
    {
        $scenario = $this->makeScenario();

        $this->actingAs($scenario['user'])
            ->get('/my-profile')
            ->assertOk()
            ->assertSee('Belum ada aktivitas')
            ->assertDontSee('Machine Operation - Advanced');
    }
}
