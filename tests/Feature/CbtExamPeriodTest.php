<?php

namespace Tests\Feature;

use App\Models\Division;
use App\Models\ExamPeriod;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\BuildsCbtScenario;
use Tests\TestCase;

/**
 * Pengujian fitur Periode Ujian (Exam Period).
 *
 * Alur baru: Manager menentukan jadwal/rentang ujian (periode),
 * Supervisor (admin) hanya melihat periode tersebut sebagai acuan.
 *
 * Matriks akses:
 *   | Role     | Lihat (index) | Kelola (create/store/update/delete) |
 *   | Manager  | Boleh         | Boleh                                |
 *   | Supervisor | Boleh       | TIDAK BOLEH                          |
 *   | Karyawan | TIDAK BOLEH    | TIDAK BOLEH                          |
 */
class CbtExamPeriodTest extends TestCase
{
    use BuildsCbtScenario, RefreshDatabase;

    /**
     * Payload valid untuk membuat/mengubah periode ujian.
     * division_id kosong (string '') -> dianggap global (null).
     */
    private function periodPayload(array $overrides = []): array
    {
        return array_merge([
            'name' => 'Periode Ujian QC Sep 2026',
            'department_id' => '',
            'division_id' => '',
            'start_at' => now()->addDay()->format('Y-m-d H:i'),
            'end_at' => now()->addDays(7)->format('Y-m-d H:i'),
            'notes' => 'Periode ujian reguler.',
        ], $overrides);
    }

    private function makePeriod(\App\Models\User $manager, array $overrides = []): ExamPeriod
    {
        return ExamPeriod::create(array_merge([
            'name' => 'Periode Ujian QC Sep 2026',
            'start_at' => now()->addDay(),
            'end_at' => now()->addDays(7),
            'created_by' => $manager->id,
        ], $overrides));
    }

    // ============================================
    // 1. MANAGER - KELOLA PERIODE (CRUD)
    // ============================================

    public function test_manager_can_create_exam_period_global(): void
    {
        $scenario = $this->buildCbtScenario();

        $this->actingAs($scenario['manager'])
            ->post(route('cbt.admin.exam-periods.store'), $this->periodPayload())
            ->assertRedirect(route('cbt.admin.exam-periods.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('exam_periods', [
            'name' => 'Periode Ujian QC Sep 2026',
            'department_id' => null,
            'division_id' => null,
            'notes' => 'Periode ujian reguler.',
            'created_by' => $scenario['manager']->id,
        ]);
    }

    public function test_manager_can_create_exam_period_scoped_to_department(): void
    {
        $scenario = $this->buildCbtScenario();

        $this->actingAs($scenario['manager'])
            ->post(route('cbt.admin.exam-periods.store'), $this->periodPayload([
                'department_id' => $scenario['department']->id,
                'name' => 'Periode Dept QC',
            ]))
            ->assertRedirect(route('cbt.admin.exam-periods.index'));

        $this->assertDatabaseHas('exam_periods', [
            'name' => 'Periode Dept QC',
            'department_id' => $scenario['department']->id,
            'division_id' => null,
        ]);
    }

    public function test_manager_can_create_exam_period_with_department_and_matching_division(): void
    {
        $scenario = $this->buildCbtScenario();

        $this->actingAs($scenario['manager'])
            ->post(route('cbt.admin.exam-periods.store'), $this->periodPayload([
                'department_id' => $scenario['department']->id,
                'division_id' => $scenario['division']->id,
                'name' => 'Periode Dept+Divisi',
            ]))
            ->assertRedirect(route('cbt.admin.exam-periods.index'));

        $this->assertDatabaseHas('exam_periods', [
            'name' => 'Periode Dept+Divisi',
            'department_id' => $scenario['department']->id,
            'division_id' => $scenario['division']->id,
        ]);
    }

    public function test_validation_rejects_division_not_belonging_to_selected_department(): void
    {
        $scenario = $this->buildCbtScenario();
        $otherDepartment = \App\Models\Department::create(['name' => 'Dept Lain']);
        $otherDivision = \App\Models\Division::create(['department_id' => $otherDepartment->id, 'name' => 'Div Lain']);

        // Divisi milik dept lain dibalut dept pilihan -> error.
        $this->actingAs($scenario['manager'])
            ->post(route('cbt.admin.exam-periods.store'), $this->periodPayload([
                'department_id' => $scenario['department']->id,
                'division_id' => $otherDivision->id,
            ]))
            ->assertSessionHasErrors(['division_id']);
    }

    public function test_manager_can_update_exam_period_to_department_scope(): void
    {
        $scenario = $this->buildCbtScenario();
        $period = $this->makePeriod($scenario['manager']);

        $this->actingAs($scenario['manager'])
            ->put(route('cbt.admin.exam-periods.update', $period), $this->periodPayload([
                'name' => 'Periode Diubah Dept',
                'department_id' => $scenario['department']->id,
                'division_id' => $scenario['division']->id,
            ]))
            ->assertRedirect(route('cbt.admin.exam-periods.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('exam_periods', [
            'id' => $period->id,
            'name' => 'Periode Diubah Dept',
            'department_id' => $scenario['department']->id,
            'division_id' => $scenario['division']->id,
        ]);
    }

    public function test_manager_can_create_exam_period_for_specific_division(): void
    {
        $scenario = $this->buildCbtScenario();

        $this->actingAs($scenario['manager'])
            ->post(route('cbt.admin.exam-periods.store'), $this->periodPayload([
                'division_id' => $scenario['division']->id,
            ]))
            ->assertRedirect(route('cbt.admin.exam-periods.index'));

        $this->assertDatabaseHas('exam_periods', [
            'name' => 'Periode Ujian QC Sep 2026',
            'division_id' => $scenario['division']->id,
        ]);
    }

    public function test_manager_can_open_create_and_edit_pages(): void
    {
        $scenario = $this->buildCbtScenario();
        $period = $this->makePeriod($scenario['manager']);

        $this->actingAs($scenario['manager'])
            ->get(route('cbt.admin.exam-periods.create'))
            ->assertOk();

        $this->actingAs($scenario['manager'])
            ->get(route('cbt.admin.exam-periods.edit', $period))
            ->assertOk()
            ->assertSee($period->name);
    }

    public function test_manager_can_update_exam_period(): void
    {
        $scenario = $this->buildCbtScenario();
        $period = $this->makePeriod($scenario['manager']);

        $this->actingAs($scenario['manager'])
            ->put(route('cbt.admin.exam-periods.update', $period), $this->periodPayload([
                'name' => 'Periode Ujian QC Okt 2026',
                'division_id' => $scenario['division']->id,
            ]))
            ->assertRedirect(route('cbt.admin.exam-periods.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('exam_periods', [
            'id' => $period->id,
            'name' => 'Periode Ujian QC Okt 2026',
            'division_id' => $scenario['division']->id,
        ]);
    }

    public function test_manager_can_delete_exam_period(): void
    {
        $scenario = $this->buildCbtScenario();
        $period = $this->makePeriod($scenario['manager']);

        $this->actingAs($scenario['manager'])
            ->delete(route('cbt.admin.exam-periods.destroy', $period))
            ->assertRedirect(route('cbt.admin.exam-periods.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseMissing('exam_periods', ['id' => $period->id]);
    }

    // ============================================
    // 2. SUPERVISOR - BISA LIHAT, TIDAK BOLEH KELOLA
    // ============================================

    public function test_supervisor_can_view_exam_period_list(): void
    {
        $scenario = $this->buildCbtScenario();
        $this->makePeriod($scenario['manager'], ['name' => 'Periode Tampil Supervisor']);

        $this->actingAs($scenario['admin'])
            ->get(route('cbt.admin.exam-periods.index'))
            ->assertOk()
            ->assertSee('Periode Tampil Supervisor');
    }

    public function test_supervisor_cannot_open_create_or_edit_pages(): void
    {
        $scenario = $this->buildCbtScenario();
        $period = $this->makePeriod($scenario['manager']);

        $this->actingAs($scenario['admin'])
            ->get(route('cbt.admin.exam-periods.create'))
            ->assertRedirect(route('dashboard'))
            ->assertSessionHas('error');

        $this->actingAs($scenario['admin'])
            ->get(route('cbt.admin.exam-periods.edit', $period))
            ->assertRedirect(route('dashboard'))
            ->assertSessionHas('error');
    }

    public function test_supervisor_cannot_store_update_or_delete_exam_period(): void
    {
        $scenario = $this->buildCbtScenario();
        $period = $this->makePeriod($scenario['manager'], ['name' => 'Periode Milik Manager']);

        $countBefore = ExamPeriod::count();

        // Store -> ditolak, tidak ada data baru.
        $this->actingAs($scenario['admin'])
            ->post(route('cbt.admin.exam-periods.store'), $this->periodPayload(['name' => 'Periode Curian']))
            ->assertRedirect(route('dashboard'));

        $this->assertSame($countBefore, ExamPeriod::count());
        $this->assertDatabaseMissing('exam_periods', ['name' => 'Periode Curian']);

        // Update -> ditolak, data tetap.
        $this->actingAs($scenario['admin'])
            ->put(route('cbt.admin.exam-periods.update', $period), $this->periodPayload(['name' => 'Periode Diubah Wasit']))
            ->assertRedirect(route('dashboard'));

        $this->assertDatabaseHas('exam_periods', ['id' => $period->id, 'name' => 'Periode Milik Manager']);

        // Delete -> ditolak, data tetap.
        $this->actingAs($scenario['admin'])
            ->delete(route('cbt.admin.exam-periods.destroy', $period))
            ->assertRedirect(route('dashboard'));

        $this->assertDatabaseHas('exam_periods', ['id' => $period->id]);
    }

    // ============================================
    // 3. KARYAWAN - TIDAK BOLEH AKSES SAMA SEKALI
    // ============================================

    public function test_employee_cannot_access_exam_period_pages(): void
    {
        $scenario = $this->buildCbtScenario();
        $period = $this->makePeriod($scenario['manager']);

        $this->actingAs($scenario['employeeUser'])
            ->get(route('cbt.admin.exam-periods.index'))
            ->assertRedirect(route('dashboard'))
            ->assertSessionHas('error');

        $this->actingAs($scenario['employeeUser'])
            ->get(route('cbt.admin.exam-periods.create'))
            ->assertRedirect(route('dashboard'));

        $this->actingAs($scenario['employeeUser'])
            ->post(route('cbt.admin.exam-periods.store'), $this->periodPayload())
            ->assertRedirect(route('dashboard'));

        $this->actingAs($scenario['employeeUser'])
            ->delete(route('cbt.admin.exam-periods.destroy', $period))
            ->assertRedirect(route('dashboard'));

        $this->assertDatabaseHas('exam_periods', ['id' => $period->id]);
    }

    // ============================================
    // 4. VALIDASI & BISNIS LOGIC
    // ============================================

    public function test_validation_requires_name_and_valid_date_range(): void
    {
        $scenario = $this->buildCbtScenario();

        // Nama kosong + end_at sebelum start_at -> dua-duanya error.
        $this->actingAs($scenario['manager'])
            ->post(route('cbt.admin.exam-periods.store'), $this->periodPayload([
                'name' => '',
                'start_at' => now()->addDays(5)->format('Y-m-d H:i'),
                'end_at' => now()->addDay()->format('Y-m-d H:i'),
            ]))
            ->assertSessionHasErrors(['name', 'end_at']);
    }

    public function test_validation_rejects_invalid_division_id(): void
    {
        $scenario = $this->buildCbtScenario();

        $this->actingAs($scenario['manager'])
            ->post(route('cbt.admin.exam-periods.store'), $this->periodPayload([
                'division_id' => 999999,
            ]))
            ->assertSessionHasErrors(['division_id']);
    }

    public function test_validation_rejects_invalid_department_id(): void
    {
        $scenario = $this->buildCbtScenario();

        $this->actingAs($scenario['manager'])
            ->post(route('cbt.admin.exam-periods.store'), $this->periodPayload([
                'department_id' => 999999,
            ]))
            ->assertSessionHasErrors(['department_id']);
    }

    public function test_is_active_helper_returns_true_only_inside_date_range(): void
    {
        $scenario = $this->buildCbtScenario();

        $active = $this->makePeriod($scenario['manager'], [
            'name' => 'Periode Aktif',
            'start_at' => now()->subDay(),
            'end_at' => now()->addDay(),
        ]);

        $finished = $this->makePeriod($scenario['manager'], [
            'name' => 'Periode Selesai',
            'start_at' => now()->subDays(5),
            'end_at' => now()->subDay(),
        ]);

        $upcoming = $this->makePeriod($scenario['manager'], [
            'name' => 'Periode Belum Mulai',
            'start_at' => now()->addDay(),
            'end_at' => now()->addDays(5),
        ]);

        $this->assertTrue($active->isActive());
        $this->assertFalse($finished->isActive());
        $this->assertFalse($upcoming->isActive());
    }

    public function test_active_scope_returns_only_periods_within_date_range(): void
    {
        $scenario = $this->buildCbtScenario();
        $this->makePeriod($scenario['manager'], ['name' => 'Aktif Sekarang', 'start_at' => now()->subDay(), 'end_at' => now()->addDay()]);
        $this->makePeriod($scenario['manager'], ['name' => 'Sudah Lewat', 'start_at' => now()->subDays(5), 'end_at' => now()->subDay()]);

        $activeNames = ExamPeriod::active()->pluck('name');
        $this->assertTrue($activeNames->contains('Aktif Sekarang'));
        $this->assertFalse($activeNames->contains('Sudah Lewat'));
    }
}