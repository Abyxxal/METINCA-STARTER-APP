<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Employee Model
 * 
 * Merepresentasikan karyawan dalam organisasi.
 * Employee terasign ke Department, Division, dan Position.
 * 
 * Hierarchy Path: Department > Division > Position > Employee
 */
class Employee extends Model
{
    use HasFactory;

    protected $primaryKey = 'nik';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'nik',
        'name',
        'email',
        'phone',
        'department_id',
        'division_id',
        'position_id',
        'status',
        'join_date',
    ];

    protected $hidden = [
        'password',
    ];

    protected $casts = [
        'status' => 'string',
        'join_date' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // ============================================
    // RELATIONSHIPS
    // ============================================

    /**
     * Relation: Employee belongs to Department
     */
    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    /**
     * Relation: Employee belongs to Division
     */
    public function division(): BelongsTo
    {
        return $this->belongsTo(Division::class);
    }

    /**
     * Relation: Employee belongs to Position
     */
    public function position(): BelongsTo
    {
        return $this->belongsTo(Position::class);
    }

    /**
     * Relation: Employee memiliki satu User account (opsional)
     */
    public function user(): HasOne
    {
        return $this->hasOne(User::class);
    }

    /**
     * Relation: Employee memiliki banyak competencies (skills)
     */
    public function competencies(): HasMany
    {
        return $this->hasMany(EmployeeCompetency::class, 'nik', 'nik');
    }
}
