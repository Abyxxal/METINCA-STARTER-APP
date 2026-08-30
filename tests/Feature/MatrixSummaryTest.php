<?php

namespace Tests\Feature;

use App\Models\Department;
use App\Models\Division;
use App\Models\DivisionSkill;
use App\Models\Employee;
use App\Models\EmployeeCompetency;
use App\Models\Position;
use App\Models\Skill;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MatrixSummaryTest extends TestCase
{
    use RefreshDatabase;

    public function test_ringkasan_hanya_menghitung_skill_divisi_terpilih(): void
    {
        $department = Department::create(['name' => 'DEP-MTX', 'status' => 'active']);
        $division = Division::create(['department_id' => $department->id, 'name' => 'DIV-MTX']);
        $position = Position::create(['division_id' => $division->id, 'name' => 'POS-MTX']);

        $employee = Employee::create([
            'nik' => 'EMP-MTX',
            'name' => 'Karyawan Matriks',
            'email' => 'emp-mtx@example.com',
            'department_id' => $department->id,
            'division_id' => $division->id,
            'position_id' => $position->id,
            'status' => 'Aktif',
        ]);

        $admin = User::factory()->create(['role' => 'admin']);

        // Skill resmi divisi: 1 buah
        $skillDivisi = Skill::create([
            'division_id' => $division->id,
            'code' => 'SK-MTX-1',
            'name' => 'Skill Divisi',
            'is_active' => true,
        ]);
        DivisionSkill::create([
            'division_id' => $division->id,
            'skill_id' => $skillDivisi->id,
            'required_level' => 2,
        ]);

        // Kompetensi resmi (terlihat di grid)
        EmployeeCompetency::create([
            'employee_nik' => $employee->nik,
            'skill_id' => $skillDivisi->id,
            'level' => 4,
            'verified_by' => $admin->id,
            'verified_at' => now(),
        ]);

        // Dua kompetensi "hantu": skill TIDAK terdaftar di divisi ini
        foreach ([['SK-GHOST-1', 'Skill Hantu 1', 3], ['SK-GHOST-2', 'Skill Hantu 2', 4]] as [$code, $name, $lvl]) {
            $ghost = Skill::create([
                'division_id' => $division->id,
                'code' => $code,
                'name' => $name,
                'is_active' => true,
            ]);
            EmployeeCompetency::create([
                'employee_nik' => $employee->nik,
                'skill_id' => $ghost->id,
                'level' => $lvl,
                'verified_by' => $admin->id,
                'verified_at' => now(),
            ]);
        }

        $this->actingAs($admin)
            ->get(route('cbt.admin.competency-matrix', ['division_id' => $division->id]))
            ->assertOk()
            // Sebelum fix: Tercatat=3 & Expert=2 ikut ter-render (record hantu terhitung)
            ->assertDontSee('>3<', false)
            ->assertDontSee('>2<', false)
            // Setelah fix: hanya 1 record resmi yang dihitung
            ->assertSee('>1<', false);
    }
}
