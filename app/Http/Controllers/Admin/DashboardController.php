<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\ExamSession;
use App\Models\Employee;
use App\Models\Question;
use App\Models\Skill;
use App\Models\EmployeeCompetency;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

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
        if ($user->role === 'admin' || $user->role === 'manager') {
            // Ambil data statistik untuk admin dashboard
            $stats = [
                'total_employees' => Employee::where('status', 'Aktif')->count(),
                'total_questions' => Question::where('status', 'active')->count(),
                'pending_verification' => ExamSession::where('status', 'submitted')->count(),
                'pending_approval' => ExamSession::where('status', 'verified_pass')
                    ->where(function($q) { $q->where('manager_decision', 'pending')->orWhereNull('manager_decision'); })
                    ->count(),
                'active_exams_this_month' => ExamSession::whereMonth('created_at', Carbon::now()->month)
                    ->whereYear('created_at', Carbon::now()->year)
                    ->whereIn('status', ['assigned', 'started', 'submitted'])
                    ->count(),
            ];

            // Ambil data rata-rata nilai per skill untuk chart
            $skillStats = DB::table('exam_sessions')
                ->join('exams', 'exam_sessions.exam_id', '=', 'exams.id')
                ->join('skills', 'exams.skill_id', '=', 'skills.id')
                ->whereIn('exam_sessions.status', ['verified_pass', 'verified_fail'])
                ->whereNotNull('exam_sessions.score')
                ->select('skills.code', 'skills.name', DB::raw('ROUND(AVG(exam_sessions.score), 2) as avg_score'))
                ->groupBy('skills.id', 'skills.code', 'skills.name')
                ->orderBy('skills.code')
                ->get();

            // Ambil aktivitas terakhir
            $recentActivities = ExamSession::with(['employee', 'exam.skill'])
                ->whereIn('status', ['verified_pass', 'verified_fail', 'submitted', 'started'])
                ->latest('updated_at')
                ->take(5)
                ->get();

            // Ambil passing rate per skill untuk progress bars
            $skillPassingRates = DB::table('exam_sessions')
                ->join('exams', 'exam_sessions.exam_id', '=', 'exams.id')
                ->join('skills', 'exams.skill_id', '=', 'skills.id')
                ->select(
                    'skills.code',
                    'skills.name',
                    DB::raw('COUNT(*) as total'),
                    DB::raw('SUM(CASE WHEN exam_sessions.status = "verified_pass" THEN 1 ELSE 0 END) as passed'),
                    DB::raw('ROUND(SUM(CASE WHEN exam_sessions.status = "verified_pass" THEN 1 ELSE 0 END) * 100.0 / COUNT(*), 0) as pass_rate')
                )
                ->whereIn('exam_sessions.status', ['verified_pass', 'verified_fail'])
                ->groupBy('skills.id', 'skills.code', 'skills.name')
                ->orderBy('pass_rate', 'desc')
                ->take(4)
                ->get();

            return view('admin.dashboard', compact('stats', 'skillStats', 'recentActivities', 'skillPassingRates')); // Admin dashboard
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
        return view('admin.dashboard');
    }
}
