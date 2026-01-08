<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Position Model
 * 
 * Merepresentasikan posisi/jabatan dalam divisi.
 * IMPORTANT CHANGE: Position sekarang adalah child dari Division (bukan Department).
 * 
 * Hierarchy: Department > Division > Position > Employee
 */
class Position extends Model
{
    use HasFactory;

    protected $fillable = [
        'division_id',
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
     * Relation: Position belongs to Division (parent)
     * IMPORTANT: Changed from department_id to division_id
     */
    public function division(): BelongsTo
    {
        return $this->belongsTo(Division::class);
    }

    /**
     * Relation: Position memiliki banyak Employees
     */
    public function employees(): HasMany
    {
        return $this->hasMany(Employee::class);
    }
}
