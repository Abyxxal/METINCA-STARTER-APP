<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmployeeCompetencyHistory extends Model
{
    protected $table = 'employee_competency_histories';

    public $timestamps = false;

    protected $fillable = [
        'employee_competency_id',
        'previous_level',
        'new_level',
        'change_type',
        'change_source',
        'changed_by',
        'exam_session_id',
        'notes',
        'created_at',
    ];

    protected $casts = [
        'previous_level' => 'integer',
        'new_level' => 'integer',
        'created_at' => 'datetime',
    ];

    public function competency(): BelongsTo
    {
        return $this->belongsTo(EmployeeCompetency::class, 'employee_competency_id');
    }

    public function changedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'changed_by');
    }

    public function examSession(): BelongsTo
    {
        return $this->belongsTo(ExamSession::class, 'exam_session_id');
    }

    public function getPreviousLevelLabelAttribute(): string
    {
        return EmployeeCompetency::$levelLabels[$this->previous_level] ?? 'Unknown';
    }

    public function getNewLevelLabelAttribute(): string
    {
        return EmployeeCompetency::$levelLabels[$this->new_level] ?? 'Unknown';
    }
}
