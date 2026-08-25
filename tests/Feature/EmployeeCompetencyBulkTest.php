<?php

namespace Tests\Feature;

use App\Models\Department;
use App\Models\Division;
use App\Models\Employee;
use App\Models\EmployeeCompetency;
use App\Models\Position;
use App\Models\Skill;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EmployeeCompetencyBulkTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    private Employee $employee;

    private array $skills = [];

    protected function setUp(): void
    {
        parent::setUp();

        $department = Department::create(['name' => 'DEP-BULK', 'status' => 'active']);
        $division = Division::create(['department_id' => $department->id, 'name' => 'DIV-BULK']);
        $position = Position::create(['division_id' => $division->id, 'name' => 'POS-BULK']);

        $this->employee = Employee::create([
            'nik' => 'EMP-BULK',
            'name' => 'Karyawan Bulk',
            'email' => 'emp-bulk@example.com',
            'department_id' => $department->id,
            'division_id' => $division->id,
            'position_id' => $position->id,
            'status' => 'Aktif',
        ]);

        $this->admin = User::factory()->create(['role' => 'admin']);

        foreach (['Welding', 'Cutting', 'Painting'] as $i => $name) {
            $skill = Skill::create([
                'division_id' => $division->id,
                'code' => 'SK-BULK-' . ($i + 1),
                'name' => $name,
            ]);
            \App\Models\DivisionSkill::create([
                'division_id' => $division->id,
                'skill_id' => $skill->id,
                'required_level' => 2,
            ]);
            $this->skills[$i + 1] = $skill;
        }
    }

    private function addCompetency(int $skillId, int $level): EmployeeCompetency
    {
        return EmployeeCompetency::create([
            'employee_nik' => $this->employee->nik,
            'skill_id' => $skillId,
            'level' => $level,
            'verified_by' => $this->admin->id,
            'verified_at' => now(),
        ]);
    }

    public function test_bulk_set_level_hanya_mengubah_skill_terpilih(): void
    {
        $this->addCompetency($this->skills[1]->id, 1);
        $this->addCompetency($this->skills[2]->id, 1);

        $this->actingAs($this->admin)
            ->post(route('cbt.admin.employee-competencies.bulk', $this->employee), [
                'skill_ids' => [$this->skills[1]->id],
                'action' => 'set_level',
                'level' => 3,
                'notes' => 'naik massal',
            ])
            ->assertRedirect()
            ->assertSessionHas('success');

        $this->assertEquals(3, EmployeeCompetency::where('skill_id', $this->skills[1]->id)->first()->level);
        $this->assertEquals(1, EmployeeCompetency::where('skill_id', $this->skills[2]->id)->first()->level);
        $this->assertNull(EmployeeCompetency::where('skill_id', $this->skills[3]->id)->first());
    }

    public function test_bulk_reset_menghapus_record_kompetensi(): void
    {
        $this->addCompetency($this->skills[1]->id, 2);
        $this->addCompetency($this->skills[2]->id, 3);

        $this->actingAs($this->admin)
            ->post(route('cbt.admin.employee-competencies.bulk', $this->employee), [
                'skill_ids' => [$this->skills[1]->id, $this->skills[2]->id],
                'action' => 'reset',
            ])
            ->assertRedirect()
            ->assertSessionHas('success');

        foreach ([$this->skills[1]->id, $this->skills[2]->id] as $sid) {
            $this->assertNull(EmployeeCompetency::where([
                'employee_nik' => $this->employee->nik,
                'skill_id' => $sid,
            ])->first());
        }
    }

    public function test_bulk_wajib_level_bila_aksi_set_level(): void
    {
        $this->actingAs($this->admin)
            ->post(route('cbt.admin.employee-competencies.bulk', $this->employee), [
                'skill_ids' => [$this->skills[1]->id],
                'action' => 'set_level',
            ])
            ->assertSessionHasErrors(['level']);
    }

    public function test_bulk_tanpa_pilihan_ditolak(): void
    {
        $this->actingAs($this->admin)
            ->post(route('cbt.admin.employee-competencies.bulk', $this->employee), [
                'skill_ids' => [],
                'action' => 'reset',
            ])
            ->assertSessionHasErrors(['skill_ids']);
    }
}
