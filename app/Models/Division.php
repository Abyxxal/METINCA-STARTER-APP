<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Division Model
 * 
 * Merepresentasikan divisi dalam departemen.
 * Division adalah child dari Department dan parent dari Position.
 * 
 * Hierarchy: Department > Division > Position > Employee
 */
class Division extends Model
{
    use HasFactory;

    protected $fillable = [
        'department_id',
        'name',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // ============================================
    // RELATIONSHIPS
    // ============================================

    /**
     * Relation: Division belongs to Department (parent)
     */
    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    /**
     * Relation: Division memiliki banyak Positions (anak)
     * IMPORTANT: Positions are now children of Divisions, NOT Departments
     */
    public function positions(): HasMany
    {
        return $this->hasMany(Position::class);
    }

    /**
     * Relation: Division memiliki banyak Skills
     */
    public function skills(): HasMany
    {
        return $this->hasMany(Skill::class);
    }
}
