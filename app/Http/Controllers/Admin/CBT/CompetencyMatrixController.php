<?php

namespace App\Http\Controllers\Admin\CBT;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\Skill;
use App\Models\Division;
use App\Models\DivisionSkill;
use App\Models\EmployeeCompetency;
use Illuminate\Http\Request;

/**
 * CompetencyMatrixController
 * 
 * Controller for displaying competency matrix (skill vs employee grid)
 */
class CompetencyMatrixController extends Controller
{
    /**
     * Display competency matrix
     */
    public function index(Request $request)
    {
        // Get all divisions for dropdown filter
        $divisions = Division::with('department')->orderBy('name')->get();
        
        // Get selected division
        $divisionId = $request->division_id;
        
        $skills = collect();
        $employees = collect();
        $competencies = collect();
        $employeeAverages = collect();
        $skillAverages = collect();

        if ($divisionId) {
            // Get skills for selected division via division_skills pivot
            $divisionSkillIds = DivisionSkill::where('division_id', $divisionId)
                ->pluck('skill_id');

            $skills = Skill::whereIn('id', $divisionSkillIds)
                ->where('is_active', true)
                ->orderBy('name')
                ->get();

            // Get employees in that division
            $employees = Employee::where('division_id', $divisionId)
                ->where('status', 'Aktif')
                ->orderBy('name')
                ->get();

            // Get employee competencies grouped by employee_nik
            $competencies = EmployeeCompetency::whereIn('employee_nik', $employees->pluck('nik'))
                ->get()
                ->groupBy('employee_nik');

            // Aggregate average levels (missing records count as level 0, matching the matrix display)
            $levelMap = [];
            foreach ($competencies as $nik => $empCompetencies) {
                foreach ($empCompetencies as $competency) {
                    $levelMap[$nik][$competency->skill_id] = (int) $competency->level;
                }
            }

            $skillCount = max($skills->count(), 1);
            $employeeCount = max($employees->count(), 1);

            foreach ($employees as $employee) {
                $sum = 0;
                foreach ($skills as $skill) {
                    $sum += $levelMap[$employee->nik][$skill->id] ?? 0;
                }
                $employeeAverages[$employee->nik] = round($sum / $skillCount, 2);
            }

            foreach ($skills as $skill) {
                $sum = 0;
                foreach ($employees as $employee) {
                    $sum += $levelMap[$employee->nik][$skill->id] ?? 0;
                }
                $skillAverages[$skill->id] = round($sum / $employeeCount, 2);
            }
        }

        return view('admin.cbt.matrix.index', compact(
            'employees',
            'skills',
            'competencies',
            'divisions',
            'divisionId',
            'employeeAverages',
            'skillAverages'
        ));
    }
}
