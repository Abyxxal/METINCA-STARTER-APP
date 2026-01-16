<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Question Model
 * 
 * Represents a single question in the CBT system.
 * Questions are linked to skills and can be assigned to multiple exams.
 */
class Question extends Model
{
    use HasFactory;

    protected $fillable = [
        'question_set_id',
        'set_title',
        'skill_id',
        'for_level',
        'question_text',
        'type',
        'options',
        'correct_answer',
        'status',
    ];

    protected $casts = [
        'options' => 'array',
        'for_level' => 'integer',
    ];

    // ============================================
    // RELATIONSHIPS
    // ============================================

    /**
     * Question belongs to a Skill
     */
    public function skill(): BelongsTo
    {
        return $this->belongsTo(Skill::class);
    }

    /**
     * Question can belong to many Exams (via exam_question pivot)
     */
    public function exams(): BelongsToMany
    {
        return $this->belongsToMany(Exam::class, 'exam_question')
            ->withPivot('weight', 'order')
            ->withTimestamps();
    }

    /**
     * Question has many Answers
     */
    public function answers(): HasMany
    {
        return $this->hasMany(ExamAnswer::class);
    }

    // ============================================
    // SCOPES
    // ============================================

    /**
     * Scope: Get only active questions
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope: Get questions for specific level
     */
    public function scopeForLevel($query, int $level)
    {
        return $query->where('for_level', $level);
    }

    /**
     * Scope: Get multiple choice questions only
     */
    public function scopeMultipleChoice($query)
    {
        return $query->where('type', 'multiple_choice');
    }

    // ============================================
    // HELPERS
    // ============================================

    /**
     * Get level name
     */
    public function getLevelName(): string
    {
        $levels = [
            1 => 'Novice',
            2 => 'Competent',
            3 => 'Proficient',
            4 => 'Expert',
        ];
        return $levels[$this->for_level] ?? 'Unknown';
    }

    /**
     * Check if answer is correct
     */
    public function isCorrectAnswer(string $answer): bool
    {
        return strtoupper(trim($answer)) === strtoupper(trim($this->correct_answer));
    }
}
