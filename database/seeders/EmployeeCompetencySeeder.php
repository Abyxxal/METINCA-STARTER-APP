<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Employee;

class EmployeeCompetencySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all employees
        $employees = Employee::all();
        
        if ($employees->isEmpty()) {
            echo "No employees found. Please seed employees first.\n";
            return;
        }

        // Assign competency levels to all employees
        $levels = [1, 2, 3, 4];
        $levelIndex = 0;

        foreach ($employees as $employee) {
            // Assign levels round-robin: 4, 3, 2, 1, 4, 3, 2, 1, ...
            $level = $levels[$levelIndex % count($levels)];
            
            DB::table('employee_competencies')->insert([
                'nik' => $employee->nik,
                'level' => $level,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $levelIndex++;
        }

        echo "Competency levels assigned to " . $employees->count() . " employees.\n";
    }
}
