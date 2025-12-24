<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Employee extends Model
{
    use HasFactory;

    protected $fillable = [
        'nik',
        'name',
        'email',
        'department_id',
        'position_id',
        'phone',
        'address',
        'photo_path',
        'hire_date',
        'status',
    ];

    protected $casts = [
        'hire_date' => 'date',
        'status' => 'string',
    ];

    /**
     * Relation: Employee dimiliki oleh satu Department
     */
    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    /**
     * Relation: Employee dimiliki oleh satu Position
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
}
