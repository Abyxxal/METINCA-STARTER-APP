<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UserDashboardController extends Controller
{
    /**
     * Display the user dashboard
     * GET /dashboard (for authenticated user)
     */
    public function dashboard()
    {
        return view('user.dashboard');
    }

    /**
     * Display my training page
     * GET /my-training
     * Fungsi: Menampilkan daftar pelatihan yang ditugaskan ke user
     */
    public function myTraining()
    {
        return view('user.my-training');
    }

    /**
     * Display training history page
     * GET /training-history
     * Fungsi: Menampilkan riwayat pelatihan yang sudah dikerjakan user
     */
    public function trainingHistory()
    {
        return view('user.training-history');
    }

    /**
     * Display my profile page
     * GET /my-profile
     * Fungsi: Menampilkan dan mengedit profil user
     */
    public function myProfile()
    {
        return view('user.my-profile');
    }
}
