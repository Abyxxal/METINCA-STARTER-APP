<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmployeeCompetency extends Model
{
    protected $fillable = ['nik', 'level'];
    protected $primaryKey = 'nik';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = true;

    // Relationship to Employee
    public function employee()
    {
        return $this->belongsTo(Employee::class, 'nik', 'nik');
    }
}
