<?php

namespace App\Http\Controllers\Employee;

use Illuminate\Http\Request;
use App\Models\ExamSession;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
    /**
     * Display the user dashboard
     * GET /dashboard (for authenticated user)
     */
    public function dashboard()
    {
        $user = Auth::user();
        
        // Initialize stats
        $stats = [
            'active' => 0,
            'completed' => 0,
            'in_progress' => 0,
        ];
        
        $recentSessions = collect();
        
        if ($user->employee) {
            // Count active trainings
            $stats['active'] = ExamSession::where('employee_nik', $user->employee->nik)
                ->whereIn('status', ['assigned', 'started'])
                ->count();
            
            // Count completed trainings
            $stats['completed'] = ExamSession::where('employee_nik', $user->employee->nik)
                ->whereIn('status', ['submitted', 'verified_pass', 'verified_fail', 'approved', 'rejected'])
                ->count();
            
            // Count in progress
            $stats['in_progress'] = ExamSession::where('employee_nik', $user->employee->nik)
                ->where('status', 'started')
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

    /**
     * Display my training page
     * GET /my-training
     * Fungsi: Menampilkan daftar pelatihan yang BELUM/SEDANG dikerjakan (assigned, started)
     */
    public function myTraining(Request $request)
    {
        $user = Auth::user();
        
        // Pastikan user memiliki employee record
        if (!$user->employee) {
            return redirect()->route('dashboard')->with('error', 'Anda belum terdaftar sebagai karyawan.');
        }

        // Query exam sessions untuk karyawan ini
        // HANYA tampilkan yang belum/sedang dikerjakan
        $query = ExamSession::with(['exam.skill', 'verifier'])
            ->where('employee_nik', $user->employee->nik)
            ->whereIn('status', ['assigned', 'started']); // Hanya assigned dan started

        // Filter by status (hanya untuk assigned dan started)
        if ($request->status && in_array($request->status, ['assigned', 'started'])) {
            $query->where('status', $request->status);
        }

        // Filter by level (via exam->target_level)
        if ($request->level) {
            $query->whereHas('exam', function($q) use ($request) {
                $q->where('target_level', $request->level);
            });
        }

        // Search by exam title or skill name
        if ($request->search) {
            $query->whereHas('exam', function($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                  ->orWhereHas('skill', function($sq) use ($request) {
                      $sq->where('name', 'like', '%' . $request->search . '%');
                  });
            });
        }

        $sessions = $query->latest()->paginate(10);

        return view('user.my-training', compact('sessions'));
    }

    /**
     * Display training history page
     * GET /training-history
     * Fungsi: Menampilkan riwayat pelatihan yang sudah dikerjakan user (submitted, verified)
     */
    public function trainingHistory(Request $request)
    {
        $user = Auth::user();
        
        if (!$user->employee) {
            return redirect()->route('dashboard')->with('error', 'Anda belum terdaftar sebagai karyawan.');
        }

        // Query exam sessions for submitted and completed trainings
        $query = ExamSession::with(['exam.skill', 'verifier'])
            ->where('employee_nik', $user->employee->nik)
            ->whereIn('status', ['submitted', 'verified_pass', 'verified_fail', 'approved', 'rejected']);

        // Filter by year
        if ($request->year) {
            $query->where(function($q) use ($request) {
                $q->whereYear('verified_at', $request->year)
                  ->orWhere(function($sq) use ($request) {
                      $sq->whereYear('submitted_at', $request->year)
                         ->whereNull('verified_at');
                  });
            });
        }

        // Filter by level
        if ($request->level) {
            $query->whereHas('exam', function($q) use ($request) {
                $q->where('target_level', $request->level);
            });
        }

        // Search by exam title
        if ($request->search) {
            $query->whereHas('exam', function($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%');
            });
        }

        $sessions = $query->latest('submitted_at')->paginate(15);

        return view('user.training-history', compact('sessions'));
    }

    /**
     * Display my profile page
     * GET /my-profile
     * Fungsi: Menampilkan dan mengedit profil user
     */
    public function myProfile()
    {
        $user = Auth::user();
        
        // Calculate statistics
        $stats = [
            'active' => 0,
            'completed' => 0,
            'certificates' => 0
        ];
        
        if ($user->employee) {
            $stats['active'] = ExamSession::where('employee_nik', $user->employee->nik)
                ->whereIn('status', ['assigned', 'started'])
                ->count();
                
            $stats['completed'] = ExamSession::where('employee_nik', $user->employee->nik)
                ->whereIn('status', ['verified_pass', 'verified_fail'])
                ->count();
                
            $stats['certificates'] = ExamSession::where('employee_nik', $user->employee->nik)
                ->where('status', 'verified_pass')
                ->count();
        }
        
        return view('user.my-profile', compact('stats'));
    }

    /**
     * Display my competencies page
     * GET /my-competencies
     * Fungsi: Menampilkan kompetensi/skill yang dimiliki user berdasarkan exam yang lulus
     */
    public function myCompetencies()
    {
        $user = Auth::user();
        
        if (!$user->employee) {
            return redirect()->route('dashboard')->with('error', 'Anda belum terdaftar sebagai karyawan.');
        }

        // Get all passed exams grouped by skill
        $competencies = ExamSession::with(['exam.skill'])
            ->where('employee_nik', $user->employee->nik)
            ->where('status', 'verified_pass')
            ->get()
            ->groupBy(function($session) {
                return $session->exam->skill->name;
            })
            ->map(function($sessions, $skillName) {
                $skill = $sessions->first()->exam->skill;
                $maxLevel = $sessions->max(function($session) {
                    return $session->exam->target_level;
                });
                
                return [
                    'skill' => $skill,
                    'current_level' => $maxLevel,
                    'certificates_count' => $sessions->count(),
                    'latest_date' => $sessions->max('verified_at'),
                    'sessions' => $sessions
                ];
            });

        return view('user.my-competencies', compact('competencies'));
    }
}
