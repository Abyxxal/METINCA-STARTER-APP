<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Menyimpan skor kualitatif (0-10) dari penilaian 5 kriteria manager.
 *
 * Skor dihitung dari kriteria: memenuhi = 2, perlu_perbaikan = 1,
 * tidak_memenuhi = 0. Digunakan sebagai dasar aturan keputusan approval
 * (dibalik layar) dan untuk audit riwayat.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('manager_assessments', function (Blueprint $table) {
            $table->unsignedTinyInteger('qualitative_score')->nullable()->after('readiness');
        });

        $this->backfillQualitativeScore();
    }

    /**
     * Isi skor untuk penilaian yang sudah ada berdasarkan 5 kriteria.
     */
    private function backfillQualitativeScore(): void
    {
        $weights = [
            'memenuhi' => 2,
            'perlu_perbaikan' => 1,
            'tidak_memenuhi' => 0,
        ];

        $criteria = [
            'sop_understanding',
            'competency_application',
            'independence',
            'problem_solving',
            'readiness',
        ];

        DB::table('manager_assessments')->orderBy('id')->chunkById(500, function ($rows) use ($weights, $criteria) {
            foreach ($rows as $row) {
                $score = 0;

                foreach ($criteria as $field) {
                    $score += $weights[$row->{$field} ?? ''] ?? 0;
                }

                DB::table('manager_assessments')->where('id', $row->id)->update([
                    'qualitative_score' => $score,
                ]);
            }
        });
    }

    public function down(): void
    {
        Schema::table('manager_assessments', function (Blueprint $table) {
            $table->dropColumn('qualitative_score');
        });
    }
};
