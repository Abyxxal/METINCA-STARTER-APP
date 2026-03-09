<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserManagementController extends Controller
{
    /**
     * Display list of all users with role management.
     */
    public function index(Request $request)
    {
        $activeTab = $request->get('tab', 'staff');

        // Tab Supervisor & Manager
        $staffQuery = User::whereIn('role', ['admin', 'manager']);
        if ($request->filled('staff_search')) {
            $s = $request->staff_search;
            $staffQuery->where(fn($q) => $q->where('name', 'like', "%{$s}%")->orWhere('email', 'like', "%{$s}%"));
        }
        $staffUsers = $staffQuery->orderBy('name')->paginate(20, ['*'], 'staff_page')->withQueryString();

        // Tab Karyawan
        $employeeQuery = User::where('role', 'user');
        if ($request->filled('employee_search')) {
            $s = $request->employee_search;
            $employeeQuery->where(fn($q) => $q->where('name', 'like', "%{$s}%")->orWhere('email', 'like', "%{$s}%"));
        }
        $employeeUsers = $employeeQuery->orderBy('name')->paginate(20, ['*'], 'employee_page')->withQueryString();

        return view('admin.user-management', compact('staffUsers', 'employeeUsers', 'activeTab'));
    }

    /**
     * Update user role.
     */
    public function updateRole(Request $request, User $user)
    {
        $validated = $request->validate([
            'role' => 'required|in:admin,manager,user',
        ]);

        // Prevent changing own role
        if ($user->id === Auth::id()) {
            return back()->with('error', 'Anda tidak bisa mengubah role diri sendiri.');
        }

        $oldRole = $user->role;
        $user->update(['role' => $validated['role']]);

        $roleLabels = ['admin' => 'Supervisor', 'manager' => 'Manager', 'user' => 'Karyawan'];

        $tab = $request->get('tab', 'staff');

        return redirect()->route('admin.users.index', ['tab' => $tab])
            ->with('success', "Role {$user->name} berhasil diubah dari {$roleLabels[$oldRole]} menjadi {$roleLabels[$validated['role']]}.");
    }
}
