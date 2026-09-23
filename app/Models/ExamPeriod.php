<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * ExamPeriod Model
 *
 * Representasi jadwal/rentang ujian (periode) yang ditetapkan Manager.
 * Supervisor menggunakannya sebagai acuan saat menugaskan ujian.
 * Status aktif dihitung otomatis dari tanggal (tanpa kolom status manual).
 */
class ExamPeriod extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'department_id',
        'division_id',
        'start_at',
        'end_at',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'start_at' => 'datetime',
        'end_at' => 'datetime',
    ];

    /**
     * Periode yang sedang berjalan (hari ini di dalam rentang start_at..end_at).
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('start_at', '<=', now())
            ->where('end_at', '>=', now());
    }

    public function division(): BelongsTo
    {
        return $this->belongsTo(Division::class);
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * True jika hari ini masih berada dalam rentang periode.
     */
    public function isActive(): bool
    {
        return now()->between($this->start_at, $this->end_at);
    }
}