<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AuthViewController extends Controller
{
    /**
     * Show login form
     */
    public function showLogin()
    {
        return view('auth.login');
    }

    /**
     * Show forgot password form
     */
    public function showForgotPassword()
    {
        return view('auth.forgot-password');
    }

    /**
     * Show reset password form
     */
    public function showResetPassword(Request $request)
    {
        return view('auth.reset-password', [
            'token' => $request->route('token'),
            'email' => $request->email
        ]);
    }
}