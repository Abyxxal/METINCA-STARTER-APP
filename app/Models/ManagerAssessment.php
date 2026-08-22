<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ManagerAssessment extends Model
{
    protected $table = 'manager_assessments';

    protected $fillable = [
        'exam_session_id',
        'assessment_method',
        'sop_understanding',
        'competency_application',
        'independence',
        'problem_solving',
        'readiness',
        'qualitative_score',
        'verification_date',
        'manager_notes',
        'created_by',
    ];

    protected $casts = [
        'verification_date' => 'date',
        'qualitative_score' => 'integer',
    ];

    const METHOD_INTERVIEW = 'interview';
    const METHOD_OBSERVATION = 'observation';
    const METHOD_BOTH = 'both';

    const STATUS_MEMENUHI = 'memenuhi';
    const STATUS_PERLU_PERBAIKAN = 'perlu_perbaikan';
    const STATUS_TIDAK_MEMENUHI = 'tidak_memenuhi';

    const WEIGHT_MEMENUHI = 2;
    const WEIGHT_PERLU_PERBAIKAN = 1;
    const WEIGHT_TIDAK_MEMENUHI = 0;
    const MIN_APPROVAL_SCORE = 7;

    const CRITERIA_FIELDS = [
        'sop_understanding',
        'competency_application',
        'independence',
        'problem_solving',
        'readiness',
    ];

    protected static function booted(): void
    {
        static::saving(function (self $assessment) {
            $assessment->qualitative_score = static::scoreFrom($assessment->only(self::CRITERIA_FIELDS));
        });
    }

    public static $methodLabels = [
        'interview' => 'Wawancara Kompetensi',
        'observation' => 'Observasi Kerja',
        'both' => 'Wawancara dan Observasi',
    ];

    public static $criteriaLabels = [
        'sop_understanding' => 'Pemahaman dan Penerapan SOP',
        'competency_application' => 'Kemampuan Menerapkan Kompetensi di Tempat Kerja',
        'independence' => 'Kemandirian dalam Menjalankan Pekerjaan',
        'problem_solving' => 'Kemampuan Menyelesaikan Masalah',
        'readiness' => 'Kesiapan Menjalankan Tanggung Jawab pada Level Berikutnya',
    ];

    public static $criteriaStatusLabels = [
        'memenuhi' => 'Memenuhi',
        'perlu_perbaikan' => 'Perlu Perbaikan',
        'tidak_memenuhi' => 'Tidak Memenuhi',
    ];

    public static $criteriaBadgeClasses = [
        'memenuhi' => 'bg-success',
        'perlu_perbaikan' => 'bg-warning text-dark',
        'tidak_memenuhi' => 'bg-danger',
    ];

    public function session(): BelongsTo
    {
        return $this->belongsTo(ExamSession::class, 'exam_session_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Hitung skor kualitatif dari nilai 5 kriteria (0-10).
     */
    public static function scoreFrom(array $values): int
    {
        $weights = [
            self::STATUS_MEMENUHI => self::WEIGHT_MEMENUHI,
            self::STATUS_PERLU_PERBAIKAN => self::WEIGHT_PERLU_PERBAIKAN,
            self::STATUS_TIDAK_MEMENUHI => self::WEIGHT_TIDAK_MEMENUHI,
        ];

        $score = 0;

        foreach (self::CRITERIA_FIELDS as $field) {
            $score += $weights[$values[$field] ?? ''] ?? 0;
        }

        return $score;
    }

    public function getQualitativeScore(): int
    {
        if ($this->qualitative_score !== null) {
            return (int) $this->qualitative_score;
        }

        return static::scoreFrom($this->only(self::CRITERIA_FIELDS));
    }

    public function getOverallResult(): string
    {
        $criteria = [
            $this->sop_understanding,
            $this->competency_application,
            $this->independence,
            $this->problem_solving,
            $this->readiness,
        ];

        if (in_array(self::STATUS_TIDAK_MEMENUHI, $criteria)) {
            return self::STATUS_TIDAK_MEMENUHI;
        }

        if (in_array(self::STATUS_PERLU_PERBAIKAN, $criteria)) {
            return self::STATUS_PERLU_PERBAIKAN;
        }

        return self::STATUS_MEMENUHI;
    }

    public function isComplete(): bool
    {
        return $this->assessment_method
            && $this->sop_understanding
            && $this->competency_application
            && $this->independence
            && $this->problem_solving
            && $this->readiness;
    }
}
