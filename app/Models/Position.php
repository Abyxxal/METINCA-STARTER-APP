<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Position extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'department_id',
        'level',
        'description',
        'status',
    ];

    protected $casts = [
        'level' => 'integer',
        'status' => 'string',
    ];

    /**
     * Relation: Position dimiliki oleh satu Department
     */
    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    /**
     * Relation: Position memiliki banyak Employees
     */
    public function employees(): HasMany
    {
        return $this->hasMany(Employee::class);
    }
}
