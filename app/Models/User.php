<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'nik',
        'password',
        'role',
        'profile_photo_url',
        'employee_nik',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Relation: User dimiliki oleh satu Employee (opsional)
     */
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'employee_nik', 'nik');
    }

    // ============================================
    // CBT RELATIONSHIPS
    // ============================================

    /**
     * Relation: User verified many competencies
     */
    public function verifiedCompetencies(): HasMany
    {
        return $this->hasMany(EmployeeCompetency::class, 'verified_by');
    }

    /**
     * Relation: User verified many exam sessions
     */
    public function verifiedExamSessions(): HasMany
    {
        return $this->hasMany(ExamSession::class, 'verified_by');
    }

    // ============================================
    // HELPERS
    // ============================================

    /**
     * Check if user is admin (supervisor)
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Alias: Check if user is supervisor (same as admin)
     */
    public function isSupervisor(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Check if user is manager
     */
    public function isManager(): bool
    {
        return $this->role === 'manager';
    }

    /**
     * Check if user has admin panel access (admin OR manager)
     */
    public function isAdminOrManager(): bool
    {
        return in_array($this->role, ['admin', 'manager']);
    }

    /**
     * Check if user is employee
     */
    public function isEmployee(): bool
    {
        return $this->role === 'employee';
    }
}
