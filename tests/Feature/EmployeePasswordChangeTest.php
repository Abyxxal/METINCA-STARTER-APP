<?php

namespace Tests\Feature;

use App\Models\Department;
use App\Models\Division;
use App\Models\Employee;
use App\Models\Position;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class EmployeePasswordChangeTest extends TestCase
{
    use RefreshDatabase;

    private array $hierarchy;

    private User $admin;

    private function makeHierarchy(): void
    {
        $this->hierarchy = [
            'department' => Department::create(['name' => 'DEP-TEST', 'status' => 'active']),
            'division' => null,
            'position' => null,
        ];
        $this->hierarchy['division'] = Division::create([
            'department_id' => $this->hierarchy['department']->id,
            'name' => 'DIV-TEST',
        ]);
        $this->hierarchy['position'] = Position::create([
            'division_id' => $this->hierarchy['division']->id,
            'name' => 'POS-TEST',
        ]);
    }

    private function makeAdmin(): User
    {
        $this->admin = User::create([
            'name' => 'Admin Test',
            'email' => 'admin-test@example.com',
            'password' => bcrypt('adminpass'),
            'role' => 'admin',
            'password_changed_at' => now(),
        ]);

        return $this->admin;
    }

    private function makeEmployeeUser(array $overrides = []): User
    {
        $this->makeHierarchy();

        $employee = Employee::create([
            'nik' => $overrides['nik'] ?? 'EMP-T1',
            'name' => 'Karyawan Test',
            'email' => 'emp-test@example.com',
            'department_id' => $this->hierarchy['department']->id,
            'division_id' => $this->hierarchy['division']->id,
            'position_id' => $this->hierarchy['position']->id,
            'status' => 'Aktif',
        ]);

        return User::create([
            'name' => 'Karyawan Test',
            'email' => 'emp-test@example.com',
            'nik' => $employee->nik,
            'password' => bcrypt($overrides['password'] ?? 'default123'),
            'role' => 'user',
            'employee_nik' => $employee->nik,
            'password_changed_at' => $overrides['password_changed_at'] ?? null,
        ]);
    }

    // ============================================
    // RED -> GREEN: paksa ganti password
    // ============================================

    public function test_karyawan_baru_diarahkan_ke_ganti_password(): void
    {
        $user = $this->makeEmployeeUser();
        $this->actingAs($user)
            ->get(route('dashboard'))
            ->assertRedirect(route('user.password.change'));
    }

    public function test_admin_tidak_diarahkan(): void
    {
        $this->makeAdmin();
        $this->actingAs($this->admin)
            ->get(route('dashboard'))
            ->assertOk();
    }

    public function test_user_lama_tidak_diarahkan(): void
    {
        $user = $this->makeEmployeeUser(['password_changed_at' => now()]);
        $this->actingAs($user)
            ->get(route('dashboard'))
            ->assertOk();
    }

    public function test_ganti_password_membuka_akses_penuh(): void
    {
        $user = $this->makeEmployeeUser();
        $oldHash = $user->password;

        $this->actingAs($user)
            ->post(route('user.password.update'), [
                'password' => 'passwordBaru8',
                'password_confirmation' => 'passwordBaru8',
            ])
            ->assertRedirect(route('dashboard'));

        $user->refresh();
        $this->assertNotEquals($oldHash, $user->password);
        $this->assertTrue(Hash::check('passwordBaru8', $user->password));
        $this->assertNotNull($user->password_changed_at);

        $this->actingAs($user)
            ->get(route('dashboard'))
            ->assertOk();
    }

    public function test_konfirmasi_password_harus_sama(): void
    {
        $user = $this->makeEmployeeUser();

        $this->actingAs($user)
            ->from(route('user.password.change'))
            ->post(route('user.password.update'), [
                'password' => 'passwordBaru8',
                'password_confirmation' => 'bedaLagi8',
            ])
            ->assertRedirect(route('user.password.change'))
            ->assertSessionHasErrors(['password']);

        $user->refresh();
        $this->assertNull($user->password_changed_at);
    }

    public function test_reset_admin_memaksa_ganti_ulang(): void
    {
        $admin = $this->makeAdmin();
        $user = $this->makeEmployeeUser(['password_changed_at' => now()]);

        $this->actingAs($admin)
            ->post("/api/employees/{$user->employee->nik}/reset-password")
            ->assertJson(['success' => true]);

        $user->refresh();
        $this->assertNull($user->password_changed_at);

        $this->actingAs($user)
            ->get(route('dashboard'))
            ->assertRedirect(route('user.password.change'));
    }

    public function test_store_employee_tanpa_password_pakai_nik_default(): void
    {
        $this->makeHierarchy();
        $admin = $this->makeAdmin();

        $this->actingAs($admin)
            ->post('/api/employees', [
                'nik' => 'EMP-T2',
                'name' => 'Karyawan Tanpa Password',
                'email' => 'emp-t2@example.com',
                'department_id' => $this->hierarchy['department']->id,
                'division_id' => $this->hierarchy['division']->id,
                'position_id' => $this->hierarchy['position']->id,
                'status' => 'Aktif',
            ])
            ->assertJson(['success' => true]);

        $user = User::where('employee_nik', 'EMP-T2')->first();
        $this->assertNotNull($user);
        $this->assertTrue(Hash::check('EMP-T2', $user->password), 'password default harus NIK');
        $this->assertNull($user->password_changed_at, 'wajib dipaksa ganti saat login pertama');
    }
}
