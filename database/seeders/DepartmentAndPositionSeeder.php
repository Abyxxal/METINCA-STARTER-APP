<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\Position;
use Illuminate\Database\Seeder;

class DepartmentAndPositionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // ============================================
        // DEPARTMENTS
        // ============================================
        $quality = Department::create([
            'name' => 'Quality',
            'description' => 'Quality Control & Assurance',
            'employee_count' => 10,
            'status' => 'active',
        ]);

        $maintenance = Department::create([
            'name' => 'Maintenance',
            'description' => 'Maintenance & Equipment',
            'employee_count' => 9,
            'status' => 'active',
        ]);

        $ppc = Department::create([
            'name' => 'PPC',
            'description' => 'Production Planning & Control',
            'employee_count' => 8,
            'status' => 'active',
        ]);

        $engineering = Department::create([
            'name' => 'Produksi & Dev Engineering',
            'description' => 'Production & Development Engineering',
            'employee_count' => 19,
            'status' => 'active',
        ]);

        // ============================================
        // POSITIONS - Quality Department
        // ============================================
        Position::create(['name' => 'Quality Manager', 'department_id' => $quality->id, 'level' => 3, 'status' => 'active']);
        Position::create(['name' => 'Quality Inspector', 'department_id' => $quality->id, 'level' => 2, 'status' => 'active']);
        Position::create(['name' => 'Quality Analyst', 'department_id' => $quality->id, 'level' => 2, 'status' => 'active']);
        Position::create(['name' => 'QA Specialist', 'department_id' => $quality->id, 'level' => 2, 'status' => 'active']);

        // ============================================
        // POSITIONS - Maintenance Department
        // ============================================
        Position::create(['name' => 'Maintenance Manager', 'department_id' => $maintenance->id, 'level' => 3, 'status' => 'active']);
        Position::create(['name' => 'Maintenance Technician', 'department_id' => $maintenance->id, 'level' => 2, 'status' => 'active']);
        Position::create(['name' => 'Maintenance Supervisor', 'department_id' => $maintenance->id, 'level' => 2, 'status' => 'active']);
        Position::create(['name' => 'Equipment Operator', 'department_id' => $maintenance->id, 'level' => 1, 'status' => 'active']);

        // ============================================
        // POSITIONS - PPC Department
        // ============================================
        Position::create(['name' => 'PPC Manager', 'department_id' => $ppc->id, 'level' => 3, 'status' => 'active']);
        Position::create(['name' => 'Production Planner', 'department_id' => $ppc->id, 'level' => 2, 'status' => 'active']);
        Position::create(['name' => 'Schedule Coordinator', 'department_id' => $ppc->id, 'level' => 2, 'status' => 'active']);
        Position::create(['name' => 'Inventory Controller', 'department_id' => $ppc->id, 'level' => 1, 'status' => 'active']);

        // ============================================
        // POSITIONS - Produksi & Dev Engineering Department
        // ============================================
        Position::create(['name' => 'Production Manager', 'department_id' => $engineering->id, 'level' => 4, 'status' => 'active']);
        Position::create(['name' => 'Engineering Manager', 'department_id' => $engineering->id, 'level' => 4, 'status' => 'active']);
        Position::create(['name' => 'Senior Operator', 'department_id' => $engineering->id, 'level' => 3, 'status' => 'active']);
        Position::create(['name' => 'Process Engineer', 'department_id' => $engineering->id, 'level' => 3, 'status' => 'active']);
        Position::create(['name' => 'Machine Operator', 'department_id' => $engineering->id, 'level' => 2, 'status' => 'active']);
        Position::create(['name' => 'Production Technician', 'department_id' => $engineering->id, 'level' => 2, 'status' => 'active']);
        Position::create(['name' => 'Junior Operator', 'department_id' => $engineering->id, 'level' => 1, 'status' => 'active']);
    }
}
