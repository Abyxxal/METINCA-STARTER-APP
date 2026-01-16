<?php

namespace App\Services;

use App\Models\Employee;
use App\Models\EmployeeCompetency;
use App\Models\Skill;
use App\Models\DivisionSkill;
use App\Models\ExamSession;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 * EmployeeCompetencyService
 * 
 * Business logic for managing employee skill competencies.
 * Handles CRUD operations and validation for competency levels.
 */
class EmployeeCompetencyService
{
    /**
     * Get employee's competencies with skills
     */
    public function getEmployeeCompetencies(Employee $employee)
    {
        return $employee->competencies()
            ->with(['skill', 'verifiedBy'])
            ->get()
            ->keyBy('skill_id');
    }

    /**
     * Get available skills for employee's division
     */
    public function getDivisionSkills(Employee $employee)
    {
        return DivisionSkill::where('division_id', $employee->division_id)
            ->with('skill')
            ->get()
            ->pluck('skill')
            ->filter();
    }

    /**
     * Update employee's competency level for a skill
     * 
     * @param Employee $employee
     * @param int $skillId
     * @param int $level
     * @param string|null $notes
     * @return EmployeeCompetency
     * @throws \Exception
     */
    public function updateCompetencyLevel(Employee $employee, int $skillId, int $level, ?string $notes = null): EmployeeCompetency
    {
        // Validate level range
        if ($level < 0 || $level > 4) {
            throw new \InvalidArgumentException('Level harus antara 0 sampai 4');
        }

        // Validate skill exists
        $skill = Skill::findOrFail($skillId);

        // Validate skill belongs to employee's division
        $divisionHasSkill = DivisionSkill::where('division_id', $employee->division_id)
            ->where('skill_id', $skillId)
            ->exists();

        if (!$divisionHasSkill) {
            throw new \InvalidArgumentException("Skill '{$skill->name}' tidak ada di divisi karyawan ini");
        }

        DB::beginTransaction();

        try {
            // Prepare notes
            $finalNotes = $notes ?: "Manual update oleh admin: " . Auth::user()->name;

            // Update or create competency
            $competency = EmployeeCompetency::updateOrCreate(
                [
                    'employee_nik' => $employee->nik,
                    'skill_id' => $skillId,
                ],
                [
                    'level' => $level,
                    'verified_by' => Auth::id(),
                    'verified_at' => now(),
                    'notes' => $finalNotes,
                ]
            );

            DB::commit();

            return $competency;

        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Delete/reset employee's competency for a skill
     * Also deletes all related exam sessions and history
     * 
     * @param Employee $employee
     * @param int $skillId
     * @return Skill
     * @throws \Exception
     */
    public function deleteCompetency(Employee $employee, int $skillId): Skill
    {
        $competency = EmployeeCompetency::where('employee_nik', $employee->nik)
            ->where('skill_id', $skillId)
            ->first();

        if (!$competency) {
            throw new \Exception('Kompetensi tidak ditemukan');
        }

        $skill = $competency->skill;
        
        DB::beginTransaction();

        try {
            // Hapus semua riwayat ujian untuk employee dan skill ini
            ExamSession::where('employee_nik', $employee->nik)
                ->whereHas('exam', function($query) use ($skillId) {
                    $query->where('skill_id', $skillId);
                })
                ->delete();

            // Hapus kompetensi
            $competency->delete();
            
            DB::commit();

            return $skill;

        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Get level label by level number
     */
    public function getLevelLabel(int $level): string
    {
        return EmployeeCompetency::$levelLabels[$level] ?? 'Unknown';
    }
}
