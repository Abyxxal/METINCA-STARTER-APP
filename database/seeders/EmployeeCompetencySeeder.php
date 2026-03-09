<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Employee;
use App\Models\Skill;
use App\Models\EmployeeCompetency;

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

        $skills = Skill::all();

        if ($skills->isEmpty()) {
            echo "No skills found. Please seed skills first.\n";
            return;
        }

        // Assign competency levels to all employees for each skill
        $levels = [1, 2, 3, 4];
        $levelIndex = 0;

        foreach ($employees as $employee) {
            foreach ($skills as $skill) {
                $level = $levels[$levelIndex % count($levels)];

                EmployeeCompetency::updateOrCreate(
                    ['employee_nik' => $employee->nik, 'skill_id' => $skill->id],
                    ['level' => $level]
                );

                $levelIndex++;
            }
        }

        echo "Competency levels assigned to " . $employees->count() . " employees across " . $skills->count() . " skills.\n";
    }
}
