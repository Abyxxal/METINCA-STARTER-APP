<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Exam Model
 * 
 * Represents an exam/test in the CBT system.
 * Exams contain questions with custom weights and have passing scores.
 */
class Exam extends Model
{
    use HasFactory;

    protected $fillable = [
        'skill_id',
        'title',
        'description',
        'target_level',
        'passing_score',
        'duration_minutes',
        'is_published',
        'status',
    ];

    protected $casts = [
        'is_published' => 'boolean',
        'target_level' => 'integer',
        'passing_score' => 'integer',
        'duration_minutes' => 'integer',
    ];

    // ============================================
    // RELATIONSHIPS
    // ============================================

    /**
     * Exam belongs to a Skill
     */
    public function skill(): BelongsTo
    {
        return $this->belongsTo(Skill::class);
    }

    /**
     * Exam has many Questions (via exam_question pivot)
     */
    public function questions(): BelongsToMany
    {
        return $this->belongsToMany(Question::class, 'exam_question')
            ->withPivot('weight', 'order')
            ->withTimestamps()
            ->orderBy('exam_question.order');
    }

    /**
     * Exam has many Sessions
     */
    public function sessions(): HasMany
    {
        return $this->hasMany(ExamSession::class);
    }

    // ============================================
    // SCOPES
    // ============================================

    /**
     * Scope: Get only published exams
     */
    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    /**
     * Scope: Get only active exams
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope: Get available exams (published & active)
     */
    public function scopeAvailable($query)
    {
        return $query->published()->active();
    }

    // ============================================
    // HELPERS
    // ============================================

    /**
     * Calculate total weight of all questions
     */
    public function getTotalWeight(): int
    {
        return $this->questions()->sum('exam_question.weight');
    }

    /**
     * Get question count
     */
    public function getQuestionCount(): int
    {
        return $this->questions()->count();
    }

    /**
     * Get level name for target level
     */
    public function getTargetLevelName(): string
    {
        $levels = [
            1 => 'Novice',
            2 => 'Competent',
            3 => 'Proficient',
            4 => 'Expert',
        ];
        return $levels[$this->target_level] ?? 'Unknown';
    }

    /**
     * Check if a score passes
     */
    public function isPassing(int $score): bool
    {
        return $score >= $this->passing_score;
    }

    /**
     * Format duration for display
     */
    public function getFormattedDuration(): string
    {
        $hours = floor($this->duration_minutes / 60);
        $minutes = $this->duration_minutes % 60;
        
        if ($hours > 0) {
            return "{$hours} jam {$minutes} menit";
        }
        return "{$minutes} menit";
    }
}
