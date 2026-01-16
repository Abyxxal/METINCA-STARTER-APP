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
        'started_at',
        'submitted_at',
        'verified_at',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'submitted_at' => 'datetime',
        'verified_at' => 'datetime',
        'score' => 'integer',
    ];

    // Status constants
    const STATUS_ASSIGNED = 'assigned';
    const STATUS_STARTED = 'started';
    const STATUS_SUBMITTED = 'submitted';
    const STATUS_VERIFIED_PASS = 'verified_pass';
    const STATUS_VERIFIED_FAIL = 'verified_fail';

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
            self::STATUS_VERIFIED_PASS => 'Lulus',
            self::STATUS_VERIFIED_FAIL => 'Tidak Lulus',
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
            self::STATUS_VERIFIED_PASS => 'bg-success',
            self::STATUS_VERIFIED_FAIL => 'bg-danger',
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
}
