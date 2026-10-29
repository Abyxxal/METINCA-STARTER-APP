<?php

namespace App\Policies;

use App\Models\ExamSession;
use App\Models\User;

class ExamSessionPolicy
{
    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, ExamSession $examSession): bool
    {
        // Admin/manager bisa melihat
        if (in_array($user->role, ['admin', 'manager'])) {
            return true;
        }

        // Karyawan hanya bisa melihat sesi miliknya sendiri
        if ($user->role === 'user' && $user->employee) {
            return $examSession->employee_nik === $user->employee->nik;
        }

        return false;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, ExamSession $examSession): bool
    {
        if (in_array($user->role, ['admin', 'manager'])) {
            return true;
        }

        if ($user->role === 'user' && $user->employee) {
            return $examSession->employee_nik === $user->employee->nik && $examSession->status === 'assigned';
        }

        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, ExamSession $examSession): bool
    {
        if (in_array($user->role, ['admin', 'manager'])) {
            return true;
        }

        return false;
    }
}
