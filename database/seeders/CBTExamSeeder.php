<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Exam;
use App\Models\Question;
use App\Models\Skill;

/**
 * CBTExamSeeder
 * 
 * Seeds CMM Level 2 Upgrade Test exam with questions.
 */
class CBTExamSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Seeding CBT Exams...');

        // Get CMM Skill
        $cmmSkill = Skill::where('code', 'SKILL-CMM')->first();

        if (!$cmmSkill) {
            $this->command->warn('CMM Skill not found. Run CBTSkillSeeder first.');
            return;
        }

        // ============================================
        // CMM LEVEL 2 UPGRADE EXAM
        // ============================================
        $cmmExam = Exam::updateOrCreate(
            ['skill_id' => $cmmSkill->id, 'title' => 'CMM Level 2 Upgrade Test'],
            [
                'description' => 'Ujian kompetensi CMM untuk kenaikan dari Level 1 (Novice) ke Level 2 (Competent). 
                                  Mencakup materi: alignment, probe calibration, temperature control, part preparation, 
                                  dan pemahaman dasar GD&T.',
                'target_level' => 2,
                'passing_score' => 80,
                'duration_minutes' => 60,
                'is_published' => true,
            ]
        );

        $this->command->info("Created Exam: {$cmmExam->title}");

        // Attach questions with weights
        $questions = Question::where('skill_id', $cmmSkill->id)
            ->where('for_level', 2)
            ->where('status', 'active')
            ->get();

        if ($questions->isEmpty()) {
            $this->command->warn('No questions found for CMM Level 2. Run CBTQuestionSeeder first.');
            return;
        }

        // Detach existing questions first
        $cmmExam->questions()->detach();

        // Attach questions with equal weight (10 points each for 10 questions = 100 total)
        $order = 1;
        foreach ($questions as $question) {
            $cmmExam->questions()->attach($question->id, [
                'weight' => 10, // Each question worth 10 points
                'order' => $order++,
            ]);
        }

        $this->command->info("✅ Attached {$questions->count()} questions to CMM Level 2 Exam");

        // ============================================
        // CMM LEVEL 3 UPGRADE EXAM (Placeholder)
        // ============================================
        Exam::updateOrCreate(
            ['skill_id' => $cmmSkill->id, 'title' => 'CMM Level 3 Proficient Test'],
            [
                'description' => 'Ujian kompetensi CMM untuk kenaikan dari Level 2 (Competent) ke Level 3 (Proficient).
                                  Mencakup materi lanjutan: complex part measurement, GD&T advanced, 
                                  measurement uncertainty, dan troubleshooting.',
                'target_level' => 3,
                'passing_score' => 85,
                'duration_minutes' => 90,
                'is_published' => false, // Not ready yet
            ]
        );

        // ============================================
        // CMM LEVEL 4 UPGRADE EXAM (Placeholder)
        // ============================================
        Exam::updateOrCreate(
            ['skill_id' => $cmmSkill->id, 'title' => 'CMM Level 4 Expert Test'],
            [
                'description' => 'Ujian kompetensi CMM untuk kenaikan dari Level 3 (Proficient) ke Level 4 (Expert).
                                  Mencakup materi: programming, fixture design, process optimization, 
                                  dan training capability.',
                'target_level' => 4,
                'passing_score' => 90,
                'duration_minutes' => 120,
                'is_published' => false, // Not ready yet
            ]
        );

        $examCount = Exam::where('skill_id', $cmmSkill->id)->count();
        $this->command->info("✅ CBT Exams seeded successfully! ({$examCount} exams for CMM skill)");
    }
}
