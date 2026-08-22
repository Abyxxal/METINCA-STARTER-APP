<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Menyimpan snapshot data soal di tabel exam_question.
 *
 * Tujuan: history ujian tidak boleh bergantung pada keberadaan soal di
 * Bank Soal (tabel questions). Kolom snapshot diisi saat soal dipasang ke
 * ujian, sehingga jika soal dihapus dari Bank Soal, history (daftar soal,
 * jawaban, nilai, approval) tetap utuh.
 *
 * Sekaligus melepas relasi ON DELETE CASCADE dari:
 * - exam_question.question_id  -> questions (agar pivot tidak ikut terhapus)
 * - exam_answers.question_id   -> questions (agar jawaban tidak ikut terhapus)
 * Kolom question_id tetap dipertahankan sebagai angka id asli soal.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('exam_question', function (Blueprint $table) {
            $table->text('question_text')->nullable()->after('question_id');
            $table->string('type')->nullable()->after('question_text');
            $table->json('options')->nullable()->after('type');
            $table->string('correct_answer')->nullable()->after('options');
            $table->unsignedTinyInteger('for_level')->nullable()->after('correct_answer');
            $table->unsignedBigInteger('skill_id')->nullable()->after('for_level');
        });

        $this->backfillSnapshots();

        Schema::table('exam_question', function (Blueprint $table) {
            $table->dropForeign(['question_id']);
        });

        Schema::table('exam_answers', function (Blueprint $table) {
            $table->dropForeign(['question_id']);
        });
    }

    /**
     * Salin data soal yang sudah ada ke kolom snapshot exam_question.
     */
    private function backfillSnapshots(): void
    {
        DB::table('exam_question')->orderBy('id')->chunkById(500, function ($rows) {
            foreach ($rows as $row) {
                $question = DB::table('questions')->where('id', $row->question_id)->first();

                if ($question === null) {
                    continue;
                }

                DB::table('exam_question')->where('id', $row->id)->update([
                    'question_text' => $question->question_text,
                    'type' => $question->type,
                    'options' => $question->options,
                    'correct_answer' => $question->correct_answer,
                    'for_level' => $question->for_level,
                    'skill_id' => $question->skill_id,
                ]);
            }
        });
    }

    public function down(): void
    {
        Schema::table('exam_answers', function (Blueprint $table) {
            $table->foreign('question_id')->references('id')->on('questions')->onDelete('cascade');
        });

        Schema::table('exam_question', function (Blueprint $table) {
            $table->foreign('question_id')->references('id')->on('questions')->onDelete('cascade');
        });

        Schema::table('exam_question', function (Blueprint $table) {
            $table->dropColumn(['question_text', 'type', 'options', 'correct_answer', 'for_level', 'skill_id']);
        });
    }
};
