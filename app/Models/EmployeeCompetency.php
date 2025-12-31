<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmployeeCompetency extends Model
{
    protected $fillable = ['nik', 'skill_id', 'level'];
    protected $primaryKey = 'nik';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = true;

    // Relationship to Employee
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'nik', 'nik');
    }

    // Relationship to Skill
    public function skill(): BelongsTo
    {
        return $this->belongsTo(Skill::class);
    }
}
