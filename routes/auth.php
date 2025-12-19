<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\VerifyEmailController;
use Illuminate\Support\Facades\Route;

// ============================================
// AUTHENTICATION ROUTES (Laravel Breeze)
// ============================================
// Routes untuk proses autentikasi user
// Semua route ini dari Laravel Breeze starter kit

// ---- REGISTRATION ----
// POST /register - Proses registrasi user baru
// Middleware: guest (hanya untuk user yang belum login)
// Handler: RegisteredUserController@store
Route::post('/register', [RegisteredUserController::class, 'store'])
    ->middleware('guest')
    ->name('register');

// ---- LOGIN ----
// POST /login - Proses autentikasi login user
// Middleware: guest (hanya untuk user yang belum login)
// Handler: AuthenticatedSessionController@store
Route::post('/login', [AuthenticatedSessionController::class, 'store'])
    ->middleware('guest')
    ->name('login');

// ---- PASSWORD RESET ----
// POST /forgot-password - Proses kirim link reset password ke email user
// Middleware: guest (hanya untuk user yang belum login)
// Handler: PasswordResetLinkController@store
Route::post('/forgot-password', [PasswordResetLinkController::class, 'store'])
    ->middleware('guest')
    ->name('password.email');

// POST /reset-password - Proses reset password dengan token
// Middleware: guest (hanya untuk user yang belum login)
// Handler: NewPasswordController@store
Route::post('/reset-password', [NewPasswordController::class, 'store'])
    ->middleware('guest')
    ->name('password.store');

// ---- EMAIL VERIFICATION ----
// GET /verify-email/{id}/{hash} - Proses verifikasi email user
// Middleware: auth (harus login), signed (URL harus valid), throttle (limit request)
// Handler: VerifyEmailController
Route::get('/verify-email/{id}/{hash}', VerifyEmailController::class)
    ->middleware(['auth', 'signed', 'throttle:6,1'])
    ->name('verification.verify');

// POST /email/verification-notification - Kirim ulang verification email
// Middleware: auth (harus login), throttle (limit request)
// Handler: EmailVerificationNotificationController@store
Route::post('/email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
    ->middleware(['auth', 'throttle:6,1'])
    ->name('verification.send');

// ---- LOGOUT ----
// POST /logout - Proses logout/end session user
// Middleware: auth (harus login)
// Handler: AuthenticatedSessionController@destroy
Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
    ->middleware('auth')
    ->name('logout');
