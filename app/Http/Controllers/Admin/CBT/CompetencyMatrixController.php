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
        }

        return view('admin.cbt.matrix.index', compact('employees', 'skills', 'competencies', 'divisions', 'divisionId'));
    }
}
