<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
        if (!$this->started_at || !$this->exam) {
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
        if (!$this->started_at || !$this->exam) {
            return 0;
        }
        
        $endTime = $this->started_at->copy()->addMinutes($this->exam->duration_minutes);
        $remaining = now()->diffInSeconds($endTime, false);
        
        return max(0, (int)$remaining);
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
     * Calculate score from answers
     */
    public function calculateScore(): int
    {
        $totalWeight = $this->exam->getTotalWeight();
        $earnedPoints = $this->answers()->sum('score_earned');
        
        if ($totalWeight === 0) {
            return 0;
        }
        
        return (int) round(($earnedPoints / $totalWeight) * 100);
    }

    /**
     * Check if exam session is passed based on score and passing score
     */
    public function isPassed(): bool
    {
        if ($this->score === null || !$this->exam) {
            return false;
        }
        
        return $this->score >= $this->exam->passing_score;
    }

    /**
     * Check if exam has not opened yet (before scheduled start)
     */
    public function isNotStartedYet(): bool
    {
        if (!$this->scheduled_start_at) {
            return false;
        }

        return now()->isBefore($this->scheduled_start_at);
    }

    /**
     * Get formatted scheduled start
     */
    public function getFormattedScheduledStart(): string
    {
        if (!$this->scheduled_start_at) {
            return '-';
        }

        return $this->scheduled_start_at->format('d M Y, H:i');
    }

    /**
     * Check if deadline has passed
     */
    public function isDeadlinePassed(): bool
    {
        if (!$this->deadline_at) {
            return false;
        }
        
        return now()->isAfter($this->deadline_at);
    }

    /**
     * Get deadline status (for badge)
     */
    public function getDeadlineStatus(): array
    {
        if (!$this->deadline_at) {
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
        if (!$this->deadline_at) {
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
}
