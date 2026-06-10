<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * EmployeeCompetency Model
 * 
 * Pivot model representing an employee's skill competency level.
 * Tracks: employee_nik, skill_id, level (0-4), verification status.
 */
class EmployeeCompetency extends Model
{
    protected $table = 'employee_competencies';

    protected $fillable = [
        'employee_nik',
        'skill_id',
        'level',
        'verified_by',
        'verified_at',
        'notes',
    ];

    protected $casts = [
        'level' => 'integer',
        'verified_at' => 'datetime',
    ];

    public $timestamps = true;

    // ============================================
    // SKILL LEVEL CONSTANTS
    // ============================================
    const LEVEL_NONE = 0;
    const LEVEL_NOVICE = 1;      // Basic awareness
    const LEVEL_COMPETENT = 2;   // Can work independently
    const LEVEL_PROFICIENT = 3;  // Can train others
    const LEVEL_EXPERT = 4;      // Subject matter expert

    public static $levelLabels = [
        0 => 'None',
        1 => 'Novice',
        2 => 'Competent',
        3 => 'Proficient',
        4 => 'Expert',
    ];

    // ============================================
    // RELATIONSHIPS
    // ============================================

    /**
     * Competency belongs to Employee
     */
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'employee_nik', 'nik');
    }

    /**
     * Competency belongs to Skill
     */
    public function skill(): BelongsTo
    {
        return $this->belongsTo(Skill::class);
    }

    /**
     * Competency has many history records
     */
    public function histories(): HasMany
    {
        return $this->hasMany(EmployeeCompetencyHistory::class, 'employee_competency_id');
    }

    /**
     * Competency verified by User (admin)
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

    // ============================================
    // HELPERS
    // ============================================

    /**
     * Get level label
     */
    public function getLevelLabelAttribute(): string
    {
        return self::$levelLabels[$this->level] ?? 'Unknown';
    }

    /**
     * Check if competency is verified
     */
    public function isVerified(): bool
    {
        return !is_null($this->verified_at);
    }

    /**
     * Verify this competency
     */
    public function verify(int $userId): void
    {
        $this->update([
            'verified_by' => $userId,
            'verified_at' => now(),
        ]);
    }
}
