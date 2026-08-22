<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * ExamQuestion Model
 *
 * Model untuk tabel pivot exam_question.
 *
 * Selain menghubungkan Exam dengan Question, tabel ini menyimpan snapshot
 * data soal (question_text, type, options, correct_answer, for_level,
 * skill_id) yang diambil saat soal dipasang ke ujian. Dengan snapshot ini,
 * history ujian tetap dapat ditampilkan walaupun soal sudah dihapus dari
 * Bank Soal.
 */
class ExamQuestion extends Model
{
    protected $table = 'exam_question';

    protected $fillable = [
        'exam_id',
        'question_id',
        'weight',
        'order',
        'question_text',
        'type',
        'options',
        'correct_answer',
        'for_level',
        'skill_id',
    ];

    protected $casts = [
        'options' => 'array',
        'weight' => 'integer',
        'order' => 'integer',
        'for_level' => 'integer',
    ];

    /**
     * Pivot milik sebuah Exam.
     */
    public function exam(): BelongsTo
    {
        return $this->belongsTo(Exam::class);
    }

    /**
     * Pivot merujuk ke Question di Bank Soal.
     *
     * Akan bernilai null jika soal sudah dihapus dari Bank Soal.
     */
    public function question(): BelongsTo
    {
        return $this->belongsTo(Question::class);
    }
}
