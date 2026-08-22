<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * ExamSession Model
 *
 * Represents an individual exam attempt by an employee.
 * Tracks status, score, and verification workflow.
 */
class ExamSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'exam_id',
        'employee_nik',
        'score',
        'status',
        'verified_by',
        'admin_notes',
        'manager_decision',
        'manager_notes',
        'decided_by',
        'decided_at',
        'started_at',
        'finished_at',
        'submitted_at',
        'verified_at',
        'deadline_at',
        'scheduled_start_at',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'finished_at' => 'datetime',
        'submitted_at' => 'datetime',
        'verified_at' => 'datetime',
        'deadline_at' => 'datetime',
        'scheduled_start_at' => 'datetime',
        'decided_at' => 'datetime',
        'score' => 'integer',
    ];

    // Status constants
    const STATUS_ASSIGNED = 'assigned';

    const STATUS_STARTED = 'started';

    const STATUS_SUBMITTED = 'submitted';

    const STATUS_VERIFIED_PASS = 'verified_pass';

    const STATUS_VERIFIED_FAIL = 'verified_fail';

    const STATUS_PENDING_APPROVAL = 'pending_approval';

    const STATUS_APPROVED = 'approved';

    const STATUS_REJECTED = 'rejected';

    // Manager decision constants
    const DECISION_PENDING = 'pending';

    const DECISION_APPROVED = 'approved';

    const DECISION_REJECTED = 'rejected';

    // ============================================
    // RELATIONSHIPS
    // ============================================

    /**
     * Session belongs to an Exam
     */
    public function exam(): BelongsTo
    {
        return $this->belongsTo(Exam::class);
    }

    /**
     * Session belongs to an Employee
     */
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'employee_nik', 'nik');
    }

    /**
     * Session was verified by a User/Admin
     */
    public function verifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    /**
     * Alias for verifier relationship
     */
    public function verifiedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    /**
     * Session was decided by a Manager
     */
    public function manager(): BelongsTo
    {
        return $this->belongsTo(User::class, 'decided_by');
    }

    /**
     * Session has one Manager Assessment (qualitative)
     */
    public function managerAssessment(): HasOne
    {
        return $this->hasOne(ManagerAssessment::class, 'exam_session_id');
    }

    /**
     * Session has many Answers
     */
    public function answers(): HasMany
    {
        return $this->hasMany(ExamAnswer::class);
    }

    // ============================================
    // SCOPES
    // ============================================

    /**
     * Scope: Get pending verification sessions
     */
    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_SUBMITTED);
    }

    /**
     * Scope: Get passed sessions
     */
    public function scopePassed($query)
    {
        return $query->where('status', self::STATUS_VERIFIED_PASS);
    }

    /**
     * Scope: Get failed sessions
     */
    public function scopeFailed($query)
    {
        return $query->where('status', self::STATUS_VERIFIED_FAIL);
    }

    /**
     * Scope: Get in-progress sessions
     */
    public function scopeInProgress($query)
    {
        return $query->where('status', self::STATUS_STARTED);
    }

    // ============================================
    // HELPERS
    // ============================================

    /**
     * Check if session passed the exam
     */
    public function hasPassed(): bool
    {
        return $this->status === self::STATUS_VERIFIED_PASS;
    }

    /**
     * Check if session is still in progress
     */
    public function isInProgress(): bool
    {
        return $this->status === self::STATUS_STARTED;
    }

    /**
     * Check if session is pending verification
     */
    public function isPendingVerification(): bool
    {
        return $this->status === self::STATUS_SUBMITTED;
    }

    /**
     * Check if exam time is expired
     */
    public function isTimeExpired(): bool
    {
        if (! $this->started_at || ! $this->exam) {
            return false;
        }

        $endTime = $this->started_at->copy()->addMinutes($this->exam->duration_minutes);

        return now()->gt($endTime);
    }

    /**
     * Alias for isTimeExpired()
     */
    public function isTimeUp(): bool
    {
        return $this->isTimeExpired();
    }

    /**
     * Get remaining time in seconds
     */
    public function getRemainingTime(): int
    {
        if (! $this->started_at || ! $this->exam) {
            return 0;
        }

        $endTime = $this->started_at->copy()->addMinutes($this->exam->duration_minutes);
        $remaining = now()->diffInSeconds($endTime, false);

        return max(0, (int) $remaining);
    }

    /**
     * Get status label in Indonesian
     */
    public function getStatusLabel(): string
    {
        $labels = [
            self::STATUS_ASSIGNED => 'Ditugaskan',
            self::STATUS_STARTED => 'Sedang Berlangsung',
            self::STATUS_SUBMITTED => 'Menunggu Verifikasi',
            self::STATUS_VERIFIED_PASS => 'Lulus - Menunggu Approval',
            self::STATUS_VERIFIED_FAIL => 'Tidak Lulus',
            self::STATUS_APPROVED => 'Lulus - Disetujui',
            self::STATUS_REJECTED => 'Lulus - Ditolak',
        ];

        return $labels[$this->status] ?? 'Unknown';
    }

    /**
     * Get status badge class
     */
    public function getStatusBadgeClass(): string
    {
        $classes = [
            self::STATUS_ASSIGNED => 'bg-secondary',
            self::STATUS_STARTED => 'bg-warning',
            self::STATUS_SUBMITTED => 'bg-info',
            self::STATUS_VERIFIED_PASS => 'bg-primary',
            self::STATUS_VERIFIED_FAIL => 'bg-danger',
            self::STATUS_APPROVED => 'bg-success',
            self::STATUS_REJECTED => 'bg-dark',
        ];

        return $classes[$this->status] ?? 'bg-secondary';
    }

    /**
     * Calculate final score based on exam type (0-100).
     *
     * - Ujian PG/True-False: persentase benar = (benar ÷ jumlah soal) × 100,
     *   dengan score_earned = 1 per jawaban benar dan 0 untuk salah.
     * - Ujian Esai: jumlah nilai per soal (maksimal per soal = bobot),
     *   total bobot esai dirancang 100 sehingga hasil langsung 0-100.
     */
    public function calculateScore(): int
    {
        if (! $this->exam) {
            return 0;
        }

        if ($this->hasEssayQuestions()) {
            $earned = $this->answers()->sum('score_earned');

            return (int) round($earned);
        }

        $totalQuestions = $this->exam->examQuestions()->count();

        if ($totalQuestions === 0) {
            return 0;
        }

        $correct = $this->answers()->where('is_correct', true)->count();

        return (int) round(($correct / $totalQuestions) * 100);
    }

    /**
     * Check if exam session is passed based on score and passing score
     */
    public function isPassed(): bool
    {
        if ($this->score === null || ! $this->exam) {
            return false;
        }

        return $this->score >= $this->exam->passing_score;
    }

    /**
     * Check if exam has not opened yet (before scheduled start)
     */
    public function isNotStartedYet(): bool
    {
        if (! $this->scheduled_start_at) {
            return false;
        }

        return now()->isBefore($this->scheduled_start_at);
    }

    /**
     * Get formatted scheduled start
     */
    public function getFormattedScheduledStart(): string
    {
        if (! $this->scheduled_start_at) {
            return '-';
        }

        return $this->scheduled_start_at->format('d M Y, H:i');
    }

    /**
     * Check if deadline has passed
     */
    public function isDeadlinePassed(): bool
    {
        if (! $this->deadline_at) {
            return false;
        }

        return now()->isAfter($this->deadline_at);
    }

    /**
     * Get deadline status (for badge)
     */
    public function getDeadlineStatus(): array
    {
        if (! $this->deadline_at) {
            return ['label' => '', 'class' => ''];
        }

        if ($this->isDeadlinePassed()) {
            return ['label' => 'Deadline Terlewat', 'class' => 'danger'];
        }

        $hoursRemaining = now()->diffInHours($this->deadline_at, false);

        if ($hoursRemaining <= 24) {
            return ['label' => 'Deadline < 24 jam', 'class' => 'warning'];
        }

        return ['label' => '', 'class' => ''];
    }

    /**
     * Get formatted deadline
     */
    public function getFormattedDeadline(): string
    {
        if (! $this->deadline_at) {
            return '-';
        }

        return $this->deadline_at->format('d M Y, H:i');
    }

    /**
     * Check if session needs manager approval (lulus tapi belum di-approve)
     */
    public function isPendingManagerApproval(): bool
    {
        return $this->status === self::STATUS_VERIFIED_PASS
            && ($this->manager_decision === null || $this->manager_decision === self::DECISION_PENDING);
    }

    /**
     * Check if session was approved by manager
     */
    public function isApprovedByManager(): bool
    {
        return $this->status === self::STATUS_APPROVED;
    }

    /**
     * Check if session was rejected by manager
     */
    public function isRejectedByManager(): bool
    {
        return $this->status === self::STATUS_REJECTED;
    }

    // ============================================
    // SCORING BREAKDOWN HELPERS
    // ============================================

    /**
     * Get exam type based on questions: 'pilihan_ganda', 'esai', or 'campuran'
     */
    public function getExamType(): string
    {
        if (! $this->exam || ! $this->exam->examQuestions->count()) {
            return 'pilihan_ganda';
        }

        $hasMc = $this->exam->examQuestions->contains(fn ($q) => in_array($q->type, ['multiple_choice', 'true_false']));
        $hasEssay = $this->exam->examQuestions->contains(fn ($q) => $q->type === 'essay');

        if ($hasMc && $hasEssay) {
            return 'campuran';
        }

        return $hasEssay ? 'esai' : 'pilihan_ganda';
    }

    /**
     * Get score formatted for display.
     *
     * - Ujian PG/True-False: nilai persentase, ditampilkan dengan tanda "%".
     * - Ujian Esai: jumlah poin (total bobot 100), ditampilkan tanpa tanda "%".
     */
    public function getFormattedScoreAttribute(): string
    {
        if ($this->score === null) {
            return '-';
        }

        return $this->hasEssayQuestions() ? (string) $this->score : $this->score.'%';
    }

    /**
     * Get exam type label in Indonesian
     */
    public function getExamTypeLabel(): string
    {
        $labels = [
            'pilihan_ganda' => 'Pilihan Ganda',
            'esai' => 'Esai',
            'campuran' => 'Pilihan Ganda & Esai',
        ];

        return $labels[$this->getExamType()] ?? 'Pilihan Ganda';
    }

    /**
     * Sum of MC/TF score_earned from answers
     */
    public function getMcScore(): int
    {
        $questionIds = $this->exam
            ? $this->exam->examQuestions()->whereIn('type', ['multiple_choice', 'true_false'])->pluck('question_id')
            : collect();

        return (int) $this->answers()->whereIn('question_id', $questionIds)->sum('score_earned');
    }

    /**
     * Sum of essay score_earned from answers
     */
    public function getEssayScore(): int
    {
        $questionIds = $this->exam
            ? $this->exam->examQuestions()->where('type', 'essay')->pluck('question_id')
            : collect();

        return (int) $this->answers()->whereIn('question_id', $questionIds)->sum('score_earned');
    }

    /**
     * Total weight of MC/TF questions in the exam
     */
    public function getMcTotalWeight(): int
    {
        if (! $this->exam) {
            return 0;
        }

        return (int) $this->exam->examQuestions()
            ->whereIn('type', ['multiple_choice', 'true_false'])
            ->sum('weight');
    }

    /**
     * Total weight of essay questions in the exam
     */
    public function getEssayTotalWeight(): int
    {
        if (! $this->exam) {
            return 0;
        }

        return (int) $this->exam->examQuestions()
            ->where('type', 'essay')
            ->sum('weight');
    }

    /**
     * Check if exam uses MC/TF questions
     */
    public function hasMcQuestions(): bool
    {
        if (! $this->exam) {
            return false;
        }

        return $this->exam->examQuestions->contains(fn ($q) => in_array($q->type, ['multiple_choice', 'true_false']));
    }

    /**
     * Check if exam uses essay questions
     */
    public function hasEssayQuestions(): bool
    {
        if (! $this->exam) {
            return false;
        }

        return $this->exam->examQuestions->contains(fn ($q) => $q->type === 'essay');
    }

    /**
     * Check if session has been decided by manager (approved or rejected)
     */
    public function hasBeenDecided(): bool
    {
        return in_array($this->status, [self::STATUS_APPROVED, self::STATUS_REJECTED]);
    }
}
