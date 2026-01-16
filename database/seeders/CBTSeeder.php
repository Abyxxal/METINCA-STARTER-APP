<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

/**
 * CBTSeeder
 * 
 * Master seeder for the CBT (Computer Based Test) system.
 * Runs all CBT-related seeders in the correct order.
 */
class CBTSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('');
        $this->command->info('╔════════════════════════════════════════════════════════════╗');
        $this->command->info('║          CBT SYSTEM - SEEDING DATABASE                     ║');
        $this->command->info('╚════════════════════════════════════════════════════════════╝');
        $this->command->info('');

        $this->call([
            CBTSkillSeeder::class,      // Skills and Division mappings
            CBTQuestionSeeder::class,   // Questions for exams
            CBTExamSeeder::class,       // Exams with questions attached
            CBTEmployeeSeeder::class,   // Admin and Employee users
        ]);

        $this->command->info('');
        $this->command->info('╔════════════════════════════════════════════════════════════╗');
        $this->command->info('║          CBT SYSTEM - SEEDING COMPLETE ✅                  ║');
        $this->command->info('╚════════════════════════════════════════════════════════════╝');
        $this->command->info('');
    }
}
