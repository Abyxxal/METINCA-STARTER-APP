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
        'verification_date',
        'manager_notes',
        'created_by',
    ];

    protected $casts = [
        'verification_date' => 'date',
    ];

    const METHOD_INTERVIEW = 'interview';
    const METHOD_OBSERVATION = 'observation';
    const METHOD_BOTH = 'both';

    const STATUS_MEMENUHI = 'memenuhi';
    const STATUS_PERLU_PERBAIKAN = 'perlu_perbaikan';
    const STATUS_TIDAK_MEMENUHI = 'tidak_memenuhi';

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
