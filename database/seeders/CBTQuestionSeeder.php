<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Question;
use App\Models\Skill;

/**
 * CBTQuestionSeeder
 * 
 * Seeds real-world questions for CMM Level 2 exam.
 * Questions cover: Alignment, Probe calibration, Temperature control, Part preparation, Tolerances.
 */
class CBTQuestionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Seeding CBT Questions...');

        // Get CMM Skill
        $cmmSkill = Skill::where('code', 'SKILL-CMM')->first();

        if (!$cmmSkill) {
            $this->command->warn('CMM Skill not found. Run CBTSkillSeeder first.');
            return;
        }

        // ============================================
        // CMM LEVEL 2 QUESTIONS
        // ============================================

        // Question 1: Alignment
        Question::updateOrCreate(
            ['skill_id' => $cmmSkill->id, 'question_text' => 'Apa fungsi utama alignment dalam operasi CMM?'],
            [
                'for_level' => 2,
                'type' => 'multiple_choice',
                'options' => [
                    'A' => 'Memastikan probe CMM sejajar dengan datum part yang diukur',
                    'B' => 'Mengurangi konsumsi listrik mesin CMM',
                    'C' => 'Membersihkan permukaan part yang diukur',
                    'D' => 'Memanaskan ruangan pengukuran',
                ],
                'correct_answer' => 'A',
                'status' => 'active',
            ]
        );

        // Question 2: Probe Calibration Frequency
        Question::updateOrCreate(
            ['skill_id' => $cmmSkill->id, 'question_text' => 'Berapa sering kalibrasi probe CMM harus dilakukan dalam kondisi operasi normal?'],
            [
                'for_level' => 2,
                'type' => 'multiple_choice',
                'options' => [
                    'A' => 'Setiap hari sebelum mulai pengukuran',
                    'B' => 'Setiap minggu',
                    'C' => 'Setiap bulan',
                    'D' => 'Setiap tahun saat kalibrasi tahunan',
                ],
                'correct_answer' => 'A',
                'status' => 'active',
            ]
        );

        // Question 3: Temperature Control
        Question::updateOrCreate(
            ['skill_id' => $cmmSkill->id, 'question_text' => 'Temperatur ideal untuk ruangan pengukuran CMM sesuai standar ISO adalah?'],
            [
                'for_level' => 2,
                'type' => 'multiple_choice',
                'options' => [
                    'A' => '15°C ± 1°C',
                    'B' => '20°C ± 1°C',
                    'C' => '25°C ± 1°C',
                    'D' => '30°C ± 1°C',
                ],
                'correct_answer' => 'B',
                'status' => 'active',
            ]
        );

        // Question 4: Part Preparation
        Question::updateOrCreate(
            ['skill_id' => $cmmSkill->id, 'question_text' => 'Apa yang harus dilakukan sebelum mengukur part baru di CMM?'],
            [
                'for_level' => 2,
                'type' => 'multiple_choice',
                'options' => [
                    'A' => 'Langsung letakkan part di CMM dan mulai pengukuran',
                    'B' => 'Bersihkan part, stabilkan suhu, dan pastikan part dalam kondisi bebas dari kontaminan',
                    'C' => 'Panaskan CMM selama 30 menit terlebih dahulu',
                    'D' => 'Ganti semua probe dengan yang baru',
                ],
                'correct_answer' => 'B',
                'status' => 'active',
            ]
        );

        // Question 5: Error Sources
        Question::updateOrCreate(
            ['skill_id' => $cmmSkill->id, 'question_text' => 'Sumber error utama dalam pengukuran CMM yang perlu diminimalisir adalah?'],
            [
                'for_level' => 2,
                'type' => 'multiple_choice',
                'options' => [
                    'A' => 'Warna part yang diukur',
                    'B' => 'Variasi temperatur, getaran, dan kontaminan pada part',
                    'C' => 'Ukuran ruangan yang terlalu besar',
                    'D' => 'Jumlah operator yang bekerja',
                ],
                'correct_answer' => 'B',
                'status' => 'active',
            ]
        );

        // Question 6: Probe Types
        Question::updateOrCreate(
            ['skill_id' => $cmmSkill->id, 'question_text' => 'Jenis probe CMM yang paling umum digunakan untuk pengukuran kontak adalah?'],
            [
                'for_level' => 2,
                'type' => 'multiple_choice',
                'options' => [
                    'A' => 'Laser scanning probe',
                    'B' => 'Touch trigger probe (TP probe)',
                    'C' => 'Optical probe',
                    'D' => 'Ultrasonic probe',
                ],
                'correct_answer' => 'B',
                'status' => 'active',
            ]
        );

        // Question 7: Datum Reference
        Question::updateOrCreate(
            ['skill_id' => $cmmSkill->id, 'question_text' => 'Apa yang dimaksud dengan datum dalam konteks pengukuran CMM?'],
            [
                'for_level' => 2,
                'type' => 'multiple_choice',
                'options' => [
                    'A' => 'Software yang digunakan untuk CMM',
                    'B' => 'Titik, garis, atau bidang referensi untuk pengukuran geometri',
                    'C' => 'Nama merk mesin CMM',
                    'D' => 'Laporan hasil pengukuran',
                ],
                'correct_answer' => 'B',
                'status' => 'active',
            ]
        );

        // Question 8: GD&T Basic
        Question::updateOrCreate(
            ['skill_id' => $cmmSkill->id, 'question_text' => 'Symbol GD&T "⌀" pada drawing menunjukkan?'],
            [
                'for_level' => 2,
                'type' => 'multiple_choice',
                'options' => [
                    'A' => 'Flatness (kerataan)',
                    'B' => 'Diameter',
                    'C' => 'Perpendicularity (tegak lurus)',
                    'D' => 'Parallelism (kesejajaran)',
                ],
                'correct_answer' => 'B',
                'status' => 'active',
            ]
        );

        // Question 9: Measurement Speed
        Question::updateOrCreate(
            ['skill_id' => $cmmSkill->id, 'question_text' => 'Apa dampak dari kecepatan probing yang terlalu tinggi pada CMM?'],
            [
                'for_level' => 2,
                'type' => 'multiple_choice',
                'options' => [
                    'A' => 'Hasil pengukuran lebih akurat',
                    'B' => 'Tidak ada dampak sama sekali',
                    'C' => 'Dapat menyebabkan error pengukuran dan kerusakan probe',
                    'D' => 'Part menjadi lebih bersih',
                ],
                'correct_answer' => 'C',
                'status' => 'active',
            ]
        );

        // Question 10: Calibration Sphere
        Question::updateOrCreate(
            ['skill_id' => $cmmSkill->id, 'question_text' => 'Fungsi calibration sphere pada CMM adalah untuk?'],
            [
                'for_level' => 2,
                'type' => 'multiple_choice',
                'options' => [
                    'A' => 'Mengukur diameter part bulat',
                    'B' => 'Mengkalibrasi probe untuk menentukan diameter efektif dan posisi center probe',
                    'C' => 'Membersihkan tip probe',
                    'D' => 'Menguji kekerasan material',
                ],
                'correct_answer' => 'B',
                'status' => 'active',
            ]
        );

        $questionCount = Question::where('skill_id', $cmmSkill->id)->count();
        $this->command->info("✅ CBT Questions seeded successfully! ({$questionCount} questions for CMM skill)");
    }
}
