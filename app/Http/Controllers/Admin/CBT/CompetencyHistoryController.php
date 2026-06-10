<?php

namespace App\Http\Controllers\Admin\CBT;

use App\Http\Controllers\Controller;
use App\Models\Division;
use App\Models\DivisionSkill;
use App\Models\Employee;
use App\Models\EmployeeCompetencyHistory;
use Illuminate\Http\Request;

class CompetencyHistoryController extends Controller
{
    public function print(Request $request)
    {
        $divisionId = $request->division_id;

        $division = Division::with('department')->findOrFail($divisionId);

        $employees = Employee::where('division_id', $divisionId)
            ->where('status', 'Aktif')
            ->with(['competencies' => fn($q) => $q->with('skill')])
            ->orderBy('name')
            ->get();

        $histories = EmployeeCompetencyHistory::whereHas('competency.employee', function ($q) use ($divisionId) {
                $q->where('division_id', $divisionId);
            })
            ->with(['competency.employee', 'competency.skill', 'changedBy', 'examSession.exam'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->groupBy(fn($h) => $h->competency->employee_nik);

        // Get all skills assigned to this division (always show all columns)
        $skillIds = DivisionSkill::where('division_id', $divisionId)->pluck('skill_id');
        $skills = \App\Models\Skill::whereIn('id', $skillIds)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $chunks = $employees->chunk(10);

        return view('admin.cbt.competency-history.print', compact(
            'division', 'employees', 'histories', 'skills', 'chunks'
        ));
    }
}
