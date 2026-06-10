<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\EmployeeCompetency;
use App\Models\EmployeeCompetencyHistory;
use Illuminate\Support\Facades\DB;

class SeedCompetencyHistory extends Command
{
    protected $signature = 'competency:seed-history {--dry-run : Preview without saving}';
    protected $description = 'Seed initial history records for existing competencies';

    public function handle()
    {
        $competencies = EmployeeCompetency::whereDoesntHave('histories')->get();

        if ($competencies->isEmpty()) {
            $this->info('All competencies already have history records.');
            return 0;
        }

        $this->info("Found {$competencies->count()} competency(ies) without history.");

        $bar = $this->output->createProgressBar($competencies->count());
        $bar->start();

        $seeded = 0;

        foreach ($competencies as $comp) {
            if ($this->option('dry-run')) {
                $bar->advance();
                continue;
            }

            try {
                EmployeeCompetencyHistory::create([
                    'employee_competency_id' => $comp->id,
                    'previous_level' => null,
                    'new_level' => $comp->level,
                    'change_type' => 'initial',
                    'change_source' => 'initial_assessment',
                    'changed_by' => null,
                    'notes' => $comp->notes ?? 'Initial assessment',
                    'created_at' => $comp->verified_at ?? $comp->created_at,
                ]);
                $seeded++;
            } catch (\Exception $e) {
                $this->newLine();
                $this->warn("Failed for competency ID {$comp->id}: {$e->getMessage()}");
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine();

        if ($this->option('dry-run')) {
            $this->info("Dry-run complete. {$competencies->count()} would be seeded.");
        } else {
            $this->info("Successfully seeded {$seeded} history record(s).");
        }

        return 0;
    }
}
