<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Department extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'employee_count',
        'status',
    ];

    protected $casts = [
        'status' => 'string',
    ];

    /**
     * Relation: Department memiliki banyak Positions
     */
    public function positions(): HasMany
    {
        return $this->hasMany(Position::class);
    }

    /**
     * Relation: Department memiliki banyak Employees
     */
    public function employees(): HasMany
    {
        return $this->hasMany(Employee::class);
    }
}
