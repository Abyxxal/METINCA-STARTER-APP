<?php

namespace App\Http\Controllers\CBT;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\Division;
use App\Services\EmployeeCompetencyService;
use Illuminate\Http\Request;

/**
 * EmployeeCompetencyController
 * 
 * Admin controller for manually managing employee skill levels.
 * Allows admin to view and edit competency levels directly.
 * 
 * Uses EmployeeCompetencyService for business logic.
 */
class EmployeeCompetencyController extends Controller
{
    protected $competencyService;

    public function __construct(EmployeeCompetencyService $competencyService)
    {
        $this->competencyService = $competencyService;
    }
    /**
     * Display list of employees with their competencies
     */
    public function index(Request $request)
    {
        $divisions = Division::with('department')->orderBy('name')->get();
        $divisionId = $request->division_id;

        $query = Employee::with(['division.department', 'position'])
            ->where('status', 'Aktif');

        if ($divisionId) {
            $query->where('division_id', $divisionId);
        }

        $employees = $query->orderBy('name')->paginate(20);

        return view('cbt.admin.competencies.index', compact('employees', 'divisions', 'divisionId'));
    }

    /**
     * Show edit form for employee's competencies
     */
    public function edit(Employee $employee)
    {
        $employee->load(['division.department', 'position']);

        // Get skills and competencies from service
        $divisionSkills = $this->competencyService->getDivisionSkills($employee);
        $existingCompetencies = $this->competencyService->getEmployeeCompetencies($employee);

        return view('cbt.admin.competencies.edit', compact('employee', 'divisionSkills', 'existingCompetencies'));
    }

    /**
     * Update employee's competency level for a skill
     */
    public function update(Request $request, Employee $employee)
    {
        $validated = $request->validate([
            'skill_id' => 'required|exists:skills,id',
            'level' => 'required|integer|min:0|max:4',
            'notes' => 'nullable|string|max:500',
        ]);

        try {
            $competency = $this->competencyService->updateCompetencyLevel(
                $employee,
                $validated['skill_id'],
                $validated['level'],
                $validated['notes'] ?? null
            );

            $levelLabel = $this->competencyService->getLevelLabel($validated['level']);

            return back()->with('success', "Level skill '{$competency->skill->name}' berhasil diubah menjadi Level {$validated['level']} ({$levelLabel})");

        } catch (\InvalidArgumentException $e) {
            return back()->with('error', $e->getMessage());
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal mengubah level: ' . $e->getMessage());
        }
    }

    /**
     * Delete/reset employee's competency for a skill
     */
    public function destroy(Employee $employee, $skillId)
    {
        try {
            $skill = $this->competencyService->deleteCompetency($employee, $skillId);

            return back()->with('success', "Kompetensi skill '{$skill->name}' berhasil dihapus (reset ke Level 0)");

        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
