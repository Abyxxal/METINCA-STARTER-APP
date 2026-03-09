<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Department;
use App\Models\Division;
use App\Models\Skill;
use App\Models\Employee;
use App\Models\EmployeeCompetency;

class SkillsSeeder extends Seeder
{
    public function run(): void
    {
        // Get Quality Department
        $qualityDept = Department::where('name', 'Quality')->first();
        
        if ($qualityDept) {
            // Create QC Division
            $qcDivision = Division::firstOrCreate(
                ['name' => 'QC (Quality Control)', 'department_id' => $qualityDept->id]
            );

            // Create skills for QC Division
            $skillCodes = [
                ['code' => 'CMM', 'name' => 'CMM (Chemical Material Management)'],
                ['code' => 'PT', 'name' => 'PT (Product Testing)'],
                ['code' => 'MPL', 'name' => 'MPL (Material Physical Lab)'],
                ['code' => 'RT', 'name' => 'RT (Raw Testing)'],
                ['code' => 'UT', 'name' => 'UT (Unit Testing)'],
                ['code' => 'US', 'name' => 'US (Utility System)'],
                ['code' => 'DIM', 'name' => 'DIM (Dimension Control)'],
            ];

            foreach ($skillCodes as $skill) {
                Skill::firstOrCreate(
                    ['code' => $skill['code']],
                    [
                        'division_id' => $qcDivision->id,
                        'name' => $skill['name'],
                        'description' => 'Skill ' . $skill['code'],
                        'is_active' => true,
                    ]
                );
            }

            // Seed some sample competencies for existing employees in Quality department
            $employees = Employee::where('department_id', $qualityDept->id)->get();
            $skills = Skill::where('division_id', $qcDivision->id)->get();

            if ($employees->count() > 0 && $skills->count() > 0) {
                foreach ($employees as $emp) {
                    foreach ($skills as $idx => $skill) {
                        // Assign different levels round-robin (0-4)
                        $level = ($idx % 5);
                        
                        EmployeeCompetency::updateOrCreate(
                            ['employee_nik' => $emp->nik, 'skill_id' => $skill->id],
                            ['level' => $level]
                        );
                    }
                }
            }
        }
    }
}
