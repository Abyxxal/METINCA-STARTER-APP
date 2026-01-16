<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Employee;
use App\Models\Skill;
use App\Models\EmployeeCompetency;

/**
 * CBTEmployeeSeeder
 * 
 * Uses EXISTING users and employees from database.
 * Does NOT create any new users or employees.
 */
class CBTEmployeeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Setting up CBT data using EXISTING users...');

        // Get CMM skill
        $cmmSkill = Skill::where('code', 'SKILL-CMM')->first();
        
        if (!$cmmSkill) {
            $this->command->warn('CMM Skill not found. Run CBTSkillSeeder first.');
            return;
        }

        // ============================================
        // USE EXISTING ADMIN (admin@example.com)
        // ============================================
        $adminUser = User::where('role', 'admin')->first();
        
        if (!$adminUser) {
            $this->command->warn('No admin user found in database.');
            return;
        }

        $this->command->info("✅ Using existing Admin: {$adminUser->name} ({$adminUser->email})");

        // ============================================
        // USE EXISTING EMPLOYEES (Niko & Damar)
        // ============================================
        $employees = Employee::all();
        
        if ($employees->isEmpty()) {
            $this->command->warn('No employees found in database. Please add employees first via Master Data.');
            return;
        }

        foreach ($employees as $employee) {
            $this->command->info("✅ Found Employee: {$employee->name} ({$employee->nik})");
            
            // Check if competency exists
            $existingCompetency = EmployeeCompetency::where('employee_nik', $employee->nik)
                ->where('skill_id', $cmmSkill->id)
                ->first();
            
            if (!$existingCompetency) {
                EmployeeCompetency::create([
                    'employee_nik' => $employee->nik,
                    'skill_id' => $cmmSkill->id,
                    'level' => 1, // Novice - ready for Level 2 exam
                    'verified_by' => $adminUser->id,
                    'verified_at' => now()->subMonth(),
                    'notes' => 'Initial assessment - basic training completed',
                ]);
                $this->command->info("   ➕ Assigned CMM Level 1 skill");
            } else {
                $this->command->info("   ℹ️  Already has CMM skill at Level {$existingCompetency->level}");
            }
        }

        // ============================================
        // SUMMARY
        // ============================================
        $this->command->info('');
        $this->command->info('╔════════════════════════════════════════════════════════════╗');
        $this->command->info('║              CBT READY - USE EXISTING ACCOUNTS             ║');
        $this->command->info('╚════════════════════════════════════════════════════════════╝');
        $this->command->info("Admin: {$adminUser->email} (can manage exams & verify results)");
        
        foreach ($employees as $employee) {
            $user = User::where('employee_nik', $employee->nik)->first();
            if ($user) {
                $this->command->info("Employee: {$user->email} ({$employee->name}) - ready for CBT exams");
            }
        }
        $this->command->info('');
    }
}
