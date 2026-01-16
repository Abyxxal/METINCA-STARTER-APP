<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * ExamAnswer Model
 * 
 * Represents an individual answer to a question within an exam session.
 */
class ExamAnswer extends Model
{
    use HasFactory;

    protected $fillable = [
        'exam_session_id',
        'question_id',
        'selected_answer',
        'is_correct',
        'score_earned',
    ];

    protected $casts = [
        'is_correct' => 'boolean',
        'score_earned' => 'integer',
    ];

    // ============================================
    // RELATIONSHIPS
    // ============================================

    /**
     * Answer belongs to an ExamSession
     */
    public function session(): BelongsTo
    {
        return $this->belongsTo(ExamSession::class, 'exam_session_id');
    }

    /**
     * Answer belongs to a Question
     */
    public function question(): BelongsTo
    {
        return $this->belongsTo(Question::class);
    }

    // ============================================
    // HELPERS
    // ============================================

    /**
     * Grade this answer (for multiple choice)
     */
    public function grade(): void
    {
        $question = $this->question;
        
        if ($question->type === 'multiple_choice' || $question->type === 'true_false') {
            $this->is_correct = $question->isCorrectAnswer($this->selected_answer ?? '');
            
            // Get weight from exam_question pivot
            $examSession = $this->session;
            $weight = $examSession->exam->questions()
                ->where('questions.id', $this->question_id)
                ->first()
                ?->pivot
                ?->weight ?? 0;
            
            $this->score_earned = $this->is_correct ? $weight : 0;
            $this->save();
        }
    }
}
