<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Employee extends Model
{
    use HasFactory;

    protected $primaryKey = 'nik';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'nik',
        'nama_karyawan',
        'email',
        'password',
        'department_id',
        'division_id',
        'position_id',
        'status',
    ];

    protected $hidden = [
        'password',
    ];

    protected $casts = [
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
     * Relation: Employee dimiliki oleh satu Division
     */
    public function division(): BelongsTo
    {
        return $this->belongsTo(Division::class);
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

    /**
     * Relation: Employee memiliki banyak competencies (skills)
     */
    public function competencies(): HasMany
    {
        return $this->hasMany(EmployeeCompetency::class, 'nik', 'nik');
    }
}
