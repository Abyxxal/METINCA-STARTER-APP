<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\ExamSession;

class DashboardController extends Controller
{
    /**
     * Display dashboard dengan role-based redirect
     * Admin → dashboard admin
     * User → user dashboard dengan data
     */
    public function dashboard()
    {
        $user = Auth::user();

        // Check role dan redirect sesuai dengan peran user
        if ($user->role === 'admin') {
            return view('dashboard'); // Admin dashboard
        } elseif ($user->role === 'user') {
            // Initialize stats for employee
            $stats = [
                'active' => 0,
                'completed' => 0,
                'in_progress' => 0,
                'certificates' => 0
            ];
            
            $recentSessions = collect();
            
            if ($user->employee) {
                // Count active trainings
                $stats['active'] = ExamSession::where('employee_nik', $user->employee->nik)
                    ->whereIn('status', ['assigned', 'started'])
                    ->count();
                
                // Count completed trainings
                $stats['completed'] = ExamSession::where('employee_nik', $user->employee->nik)
                    ->whereIn('status', ['verified_pass', 'verified_fail'])
                    ->count();
                
                // Count in progress
                $stats['in_progress'] = ExamSession::where('employee_nik', $user->employee->nik)
                    ->where('status', 'started')
                    ->count();
                
                // Count certificates (passed)
                $stats['certificates'] = ExamSession::where('employee_nik', $user->employee->nik)
                    ->where('status', 'verified_pass')
                    ->count();
                
                // Get recent 3 training sessions
                $recentSessions = ExamSession::with(['exam.skill'])
                    ->where('employee_nik', $user->employee->nik)
                    ->latest('created_at')
                    ->take(3)
                    ->get();
            }
            
            return view('user.employee-dashboard', compact('stats', 'recentSessions'));
        }

        // Default jika role tidak ada atau tidak valid
        return view('dashboard');
    }
}
