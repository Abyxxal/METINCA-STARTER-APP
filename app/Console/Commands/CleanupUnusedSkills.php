<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Skill;
use App\Models\DivisionSkill;
use Illuminate\Support\Facades\DB;

class CleanupUnusedSkills extends Command
{
    protected $signature = 'cbt:cleanup-unused-skills {--dry-run : Preview skills to be deleted without actually deleting}';
    protected $description = 'Delete skills that are not assigned to any division';

    public function handle()
    {
        $this->info('🔍 Checking for unused skills...');
        
        // Get skill IDs that are used in division_skills
        $usedSkillIds = DivisionSkill::distinct()->pluck('skill_id')->toArray();
        
        // Get unused skills
        $unusedSkills = Skill::whereNotIn('id', $usedSkillIds)->get();
        
        if ($unusedSkills->isEmpty()) {
            $this->info('✅ No unused skills found. All skills are assigned to divisions.');
            return 0;
        }
        
        $this->warn("Found {$unusedSkills->count()} unused skill(s):");
        $this->newLine();
        
        // Display unused skills in table
        $tableData = $unusedSkills->map(function($skill) {
            return [
                'ID' => $skill->id,
                'Code' => $skill->code,
                'Name' => $skill->name,
                'Division' => $skill->division_id ? "Division {$skill->division_id}" : 'None',
                'Active' => $skill->is_active ? 'Yes' : 'No',
            ];
        })->toArray();
        
        $this->table(['ID', 'Code', 'Name', 'Division', 'Active'], $tableData);
        
        // Dry run mode
        if ($this->option('dry-run')) {
            $this->info('🔒 Dry-run mode: No skills will be deleted.');
            $this->info('Run without --dry-run to actually delete these skills.');
            return 0;
        }
        
        // Confirm deletion
        if (!$this->option('no-interaction') && !$this->confirm('Do you want to delete these unused skills?', false)) {
            $this->info('Cancelled. No skills were deleted.');
            return 0;
        }
        
        if ($this->option('no-interaction')) {
            $this->info('⚠️  Running in non-interactive mode. Deleting skills...');
        }
        
        // Delete unused skills
        DB::beginTransaction();
        
        try {
            $deletedCount = Skill::whereNotIn('id', $usedSkillIds)->delete();
            
            DB::commit();
            
            $this->info("✅ Successfully deleted {$deletedCount} unused skill(s).");
            $this->newLine();
            $this->info('💡 Tip: Run "php artisan cache:clear" to clear cache.');
            
            return 0;
            
        } catch (\Exception $e) {
            DB::rollBack();
            $this->error('❌ Failed to delete skills: ' . $e->getMessage());
            return 1;
        }
    }
}
