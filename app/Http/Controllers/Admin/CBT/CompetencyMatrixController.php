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
        $divisionSkillIds = collect();
        $summary = [
            'total_employees' => 0,
            'total_skills' => 0,
            'recorded' => 0,
            'experts' => 0,
        ];

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

            // Summary scoped to skills shown in the matrix (skill yang tampil di grid)
            $visibleCompetencies = $competencies->flatten()
                ->whereIn('skill_id', $divisionSkillIds);

            $summary = [
                'total_employees' => $employees->count(),
                'total_skills' => $skills->count(),
                'recorded' => $visibleCompetencies->count(),
                'experts' => $visibleCompetencies->where('level', 4)->count(),
            ];
        }

        return view('admin.cbt.matrix.index', compact(
            'employees',
            'skills',
            'competencies',
            'divisions',
            'divisionId',
            'summary'
        ));
    }
}
