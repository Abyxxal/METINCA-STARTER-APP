<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Skill extends Model
{
    use HasFactory;

    protected $fillable = [
        'division_id',
        'code',
        'name',
        'description',
        'status',
    ];

    public function division(): BelongsTo
    {
        return $this->belongsTo(Division::class);
    }

    public function competencies(): HasMany
    {
        return $this->hasMany(EmployeeCompetency::class);
    }

    /**
     * Skill has many Questions (for CBT)
     */
    public function questions(): HasMany
    {
        return $this->hasMany(Question::class);
    }

    /**
     * Skill has many Exams (for CBT)
     */
    public function exams(): HasMany
    {
        return $this->hasMany(Exam::class);
    }

    /**
     * Skill belongs to many Divisions (via division_skills)
     */
    public function divisions(): BelongsToMany
    {
        return $this->belongsToMany(Division::class, 'division_skills')
            ->withPivot('required_level', 'is_mandatory', 'description')
            ->withTimestamps();
    }
}
