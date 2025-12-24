<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Display dashboard dengan role-based redirect
     * Admin → dashboard admin
     * User → user dashboard
     */
    public function dashboard()
    {
        $user = Auth::user();

        // Check role dan redirect sesuai dengan peran user
        if ($user->role === 'admin') {
            return view('dashboard'); // Admin dashboard
        } elseif ($user->role === 'user') {
            return view('user.dashboard'); // User dashboard
        }

        // Default jika role tidak ada atau tidak valid
        return view('dashboard');
    }
}
