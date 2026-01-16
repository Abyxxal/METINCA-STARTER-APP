<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * DivisionSkill Model (Pivot)
 * 
 * Maps which skills are required for each division.
 */
class DivisionSkill extends Model
{
    use HasFactory;

    protected $table = 'division_skills';

    protected $fillable = [
        'division_id',
        'skill_id',
        'required_level',
        'description',
        'is_mandatory',
    ];

    protected $casts = [
        'is_mandatory' => 'boolean',
        'required_level' => 'integer',
    ];

    // ============================================
    // RELATIONSHIPS
    // ============================================

    /**
     * DivisionSkill belongs to a Division
     */
    public function division(): BelongsTo
    {
        return $this->belongsTo(Division::class);
    }

    /**
     * DivisionSkill belongs to a Skill
     */
    public function skill(): BelongsTo
    {
        return $this->belongsTo(Skill::class);
    }

    // ============================================
    // HELPERS
    // ============================================

    /**
     * Get required level name
     */
    public function getRequiredLevelName(): string
    {
        $levels = [
            1 => 'Novice',
            2 => 'Competent',
            3 => 'Proficient',
            4 => 'Expert',
        ];
        return $levels[$this->required_level] ?? 'Unknown';
    }
}
