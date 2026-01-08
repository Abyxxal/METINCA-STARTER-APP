<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Department Model
 * 
 * Merepresentasikan departemen dalam organisasi.
 * Departemen adalah parent dari Divisions.
 * 
 * Hierarchy: Department > Division > Position > Employee
 */
class Department extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // ============================================
    // RELATIONSHIPS
    // ============================================

    /**
     * Relation: Department memiliki banyak Divisions (anak)
     */
    public function divisions(): HasMany
    {
        return $this->hasMany(Division::class);
    }

    /**
     * Relation: Department memiliki banyak Employees
     * (Employee langsung terasign ke department, meski berada di division)
     */
    public function employees(): HasMany
    {
        return $this->hasMany(Employee::class);
    }
}

