<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Auth\VerifyEmailController;
use Illuminate\Support\Facades\Route;

// ============================================
// AUTHENTICATION ROUTES (Laravel Breeze)
// ============================================
// Form & proses login/password reset didaftarkan di routes/web.php.
// File ini hanya route verifikasi email dan logout agar tidak ada
// URI ganda yang saling menimpa.

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
