<?php

namespace App\Services;

use App\Models\Skill;
use App\Models\Division;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

/**
 * SkillService
 * 
 * Service layer untuk business logic terkait Skill/Competency
 */
class SkillService
{
    /**
     * Create skill baru
     */
    public function createSkill(array $data)
    {
        DB::beginTransaction();
        try {
            $skill = Skill::create([
                'division_id' => $data['division_id'],
                'name' => $data['name'],
                'description' => $data['description'] ?? null,
                'category' => $data['category'] ?? 'technical',
                'max_level' => $data['max_level'] ?? 5,
                'status' => $data['status'] ?? 'active',
                'is_active' => $data['is_active'] ?? true,
            ]);

            DB::commit();

            // Clear cache
            Cache::forget('active_skills');
            Cache::tags(['skills'])->flush();

            return $skill;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Update skill
     */
    public function updateSkill(Skill $skill, array $data)
    {
        DB::beginTransaction();
        try {
            $skill->update([
                'division_id' => $data['division_id'] ?? $skill->division_id,
                'name' => $data['name'] ?? $skill->name,
                'description' => $data['description'] ?? $skill->description,
                'category' => $data['category'] ?? $skill->category,
                'max_level' => $data['max_level'] ?? $skill->max_level,
                'status' => $data['status'] ?? $skill->status,
                'is_active' => $data['is_active'] ?? $skill->is_active,
            ]);

            DB::commit();

            // Clear cache
            Cache::forget('active_skills');
            Cache::forget("skills_division_{$skill->division_id}");
            Cache::tags(['skills'])->flush();

            return $skill->fresh();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Delete skill dengan validasi
     */
    public function deleteSkill(Skill $skill)
    {
        // Cek apakah skill masih digunakan
        $hasExams = $skill->exams()->count() > 0;
        $hasQuestions = $skill->questions()->count() > 0;
        $hasCompetencies = $skill->competencies()->count() > 0;

        if ($hasExams || $hasQuestions || $hasCompetencies) {
            throw new \Exception('Skill tidak dapat dihapus karena masih digunakan di ujian, soal, atau kompetensi karyawan');
        }

        DB::beginTransaction();
        try {
            $divisionId = $skill->division_id;
            $skill->delete();

            DB::commit();

            // Clear cache
            Cache::forget('active_skills');
            Cache::forget("skills_division_{$divisionId}");
            Cache::tags(['skills'])->flush();

            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Get skills by division dengan cache
     */
    public function getSkillsByDivision(int $divisionId, bool $activeOnly = true)
    {
        $cacheKey = "skills_division_{$divisionId}" . ($activeOnly ? '_active' : '_all');

        return Cache::remember($cacheKey, 3600, function() use ($divisionId, $activeOnly) {
            $query = Skill::where('division_id', $divisionId);
            
            if ($activeOnly) {
                $query->where('is_active', true);
            }

            return $query->orderBy('name')->get();
        });
    }

    /**
     * Get all active skills dengan cache
     */
    public function getActiveSkills()
    {
        return Cache::remember('active_skills', 3600, function() {
            return Skill::where('is_active', true)
                ->orderBy('name')
                ->get();
        });
    }

    /**
     * Toggle skill active status
     */
    public function toggleActiveStatus(Skill $skill)
    {
        DB::beginTransaction();
        try {
            $skill->is_active = !$skill->is_active;
            $skill->save();

            DB::commit();

            // Clear cache
            Cache::forget('active_skills');
            Cache::forget("skills_division_{$skill->division_id}");
            Cache::tags(['skills'])->flush();

            return $skill;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Get skill statistics
     */
    public function getSkillStatistics(Skill $skill)
    {
        return [
            'total_employees' => $skill->competencies()->distinct('employee_nik')->count(),
            'total_exams' => $skill->exams()->count(),
            'total_questions' => $skill->questions()->count(),
            'avg_level' => $skill->competencies()->avg('current_level') ?? 0,
            'level_distribution' => $skill->competencies()
                ->select('current_level', DB::raw('count(*) as count'))
                ->groupBy('current_level')
                ->pluck('count', 'current_level')
                ->toArray(),
        ];
    }

    /**
     * Bulk update skills status
     */
    public function bulkUpdateStatus(array $skillIds, string $status)
    {
        if (!in_array($status, ['active', 'inactive'])) {
            throw new \Exception('Invalid status');
        }

        DB::beginTransaction();
        try {
            $isActive = $status === 'active';
            
            Skill::whereIn('id', $skillIds)->update([
                'is_active' => $isActive,
                'status' => $status,
            ]);

            DB::commit();

            // Clear all skill caches
            Cache::forget('active_skills');
            Cache::tags(['skills'])->flush();

            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
