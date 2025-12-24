<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\AuthViewController;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\VerifyEmailController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EmployeeImportController;
use App\Http\Controllers\UserDashboardController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// ============================================
// ROOT ROUTE - Redirect berdasarkan auth status
// ============================================
// Route: / (home)
// Fungsi: Redirect ke dashboard jika sudah login, atau ke home.main jika belum
Route::get('/',function(){
    if(Auth::check()){
        // Jika sudah login, redirect ke dashboard (akan di-handle oleh DashboardController)
        return redirect()->route('dashboard');
    }
    return redirect()->route('home.main');
});

require __DIR__.'/auth.php';

// ============================================
// GUEST ROUTES - Untuk user yang belum login
// ============================================
// Middleware: 'guest' - hanya accessible jika belum login
// Jika sudah login akan redirect ke dashboard

Route::middleware('guest')->group(function () {
    
    // ---- AUTHENTICATION FORMS ----
    // Menampilkan form login/register/password reset
    
    // GET /login - Halaman form login
    Route::get('login', [AuthViewController::class, 'showLogin'])
        ->name('login');
    
    // GET /register - Halaman form registrasi
    Route::get('register', [AuthViewController::class, 'showRegister'])
        ->name('register');
    
    // GET /forgot-password - Halaman form lupa password
    Route::get('forgot-password', [AuthViewController::class, 'showForgotPassword'])
        ->name('password.request');
    
    // GET /reset-password/{token} - Halaman form reset password dengan token
    Route::get('reset-password/{token}', [AuthViewController::class, 'showResetPassword'])
        ->name('password.reset');

    // ---- AUTHENTICATION LOGIC (POST) ----
    // Menghandle submit form login/register/password reset
    
    // POST /register - Proses registrasi user baru
    Route::post('register', [RegisteredUserController::class, 'store'])
        ->name('register.store');
    
    // POST /login - Proses autentikasi login
    Route::post('login', [AuthenticatedSessionController::class, 'store'])
        ->name('login.store');
    
    // POST /forgot-password - Proses kirim link reset password ke email
    Route::post('forgot-password', [PasswordResetLinkController::class, 'store'])
        ->name('password.email');
    
    // POST /reset-password - Proses reset password dengan token
    Route::post('reset-password', [NewPasswordController::class, 'store'])
        ->name('password.update');

    // ---- HOME / PUBLIC PAGES ----
    // Halaman publik yang bisa dilihat tanpa login
    
    Route::prefix('home')->group(function(){

        // GET /home/ - Halaman utama perusahaan (landing page)
        // Isi: Informasi perusahaan, visi misi, dll
        Route::get('/',function(){
            return view('home.main');
        })->name('home.main');

        // GET /home/products - Halaman daftar produk perusahaan
        Route::get('/products',function(){
            return view('home.products');
        })->name('home.products');

        // GET /home/divisions - Halaman daftar divisi/departemen perusahaan
        Route::get('/divisions',function(){
            return view('home.divisions');
        })->name('home.divisions');

        // GET /home/facilities - Halaman daftar fasilitas pabrik
        Route::get('/facilities',function(){
            return view('home.facilities');
        })->name('home.facilities');

        // GET /home/gallery - Halaman galeri foto pabrik/produk
        Route::get('/gallery',function(){
            return view('home.galleries');
        })->name('home.gallery');

    });
});

// ============================================
// AUTHENTICATED ROUTES - Untuk user yang sudah login
// ============================================
// Middleware: 'auth' - hanya accessible jika sudah login
// Jika belum login akan redirect ke login page

Route::middleware(['auth'])->group(function(){

    // ============================================
    // SHARED ROUTES - Dashboard untuk Admin & User
    // ============================================
    
    // GET /dashboard - Dashboard untuk both admin dan user
    // Fungsi: Redirect atau show sesuai dengan role
    Route::get('/dashboard',[DashboardController::class,'dashboard'])->name('dashboard');

    // ============================================
    // ADMIN ROUTES - Hanya untuk Admin
    // ============================================
    // Middleware: 'is.admin' - hanya accessible untuk user dengan role 'admin'
    
    Route::middleware(['is.admin'])->group(function(){

        // GET /master-data - Halaman Master Data
        // Fungsi: Mengelola data referensi (karyawan, departemen, jabatan)
        // Submenu: Data Karyawan, Departemen & Line, Jabatan
        Route::get('/master-data',function(){
            return view('master-data');
        })->name('master-data');

        // ============================================
        // EMPLOYEE IMPORT ROUTES
        // ============================================
        // Routes untuk import data karyawan dari Excel
        
        // GET /employee-import - Form import
        Route::get('/employee-import', [EmployeeImportController::class, 'showImportForm'])->name('employee.import.form');
        
        // POST /employee-import - Process import
        Route::post('/employee-import', [EmployeeImportController::class, 'import'])->name('employee.import');
        
        // GET /employee-import/template - Download template
        Route::get('/employee-import/template', [EmployeeImportController::class, 'downloadTemplate'])->name('employee.template');

        // GET /material-management - Halaman Material Management
        // Fungsi: Mengelola materi pelatihan dan dokumen SOP
        // Submenu: Katalog Pelatihan, Pustaka SOP/WI, Media Library
        Route::get('/material-management',function(){
            return view('material-management');
        })->name('material-management');

        // GET /evaluation-and-exam - Halaman Evaluation & Exam
        // Fungsi: Mengelola soal ujian, setup ujian, dan hasil ujian
        // Submenu: Bank Soal, Setup Ujian, Hasil Ujian
        Route::get('/evaluation-and-exam',function(){
            return view('evaluation-and-exam');
        })->name('evaluation-and-exam');

        // GET /socialization-and-news - Halaman Socialization & News
        // Fungsi: Mengelola pengumuman ke karyawan dan tracking pembacaan
        // Submenu: Buat Pengumuman, Status Baca
        Route::get('/socialization-and-news',function(){
            return view('socialization-and-news');
        })->name('socialization-and-news');

        // GET /report-and-audit - Halaman Report & Audit
        // Fungsi: Reporting dan compliance untuk audit ISO 9001
        // Submenu: Matriks Kompetensi, Riwayat Pelatihan, Cetak Sertifikat
        Route::get('/report-and-audit',function(){
            return view('report-and-audit');
        })->name('report-and-audit');

        // GET /settings - Halaman Settings
        // Fungsi: Pengaturan sistem dan manajemen user admin
        // Submenu: Admin Management, Audit Log
        Route::get('/settings',function(){
            return view('settings');
        })->name('settings');

        // ============================================
        // MACHINING PROCESS ROUTES (Sub-module)
        // ============================================
        // Routes untuk fitur machining/monitoring produksi
        
        Route::prefix('machining')->name('machining.')->group(function(){

            // Monitoring sub-routes
            Route::prefix('monitoring')->name('monitoring.')->group(function(){

                // GET /machining/monitoring/ - Halaman monitoring proses produksi
                // Fungsi: Monitoring real-time status mesin dan line produksi
                Route::get('/',function(){
                    return view('machining.monitoring.index');
                })->name('index');

            });

        });

    });

    // ============================================
    // USER ROUTES - Hanya untuk User Biasa
    // ============================================
    // Routes untuk fitur yang diakses user/karyawan
    // Middleware: 'is.user' - hanya accessible untuk user dengan role 'user'
    
    Route::middleware(['is.user'])->group(function(){

        // GET /my-training - Halaman pelatihan saya
        // Fungsi: Menampilkan daftar pelatihan yang ditugaskan ke user
        Route::get('/my-training', [UserDashboardController::class, 'myTraining'])->name('my-training');

        // GET /training-history - Halaman riwayat pelatihan
        // Fungsi: Menampilkan riwayat pelatihan yang sudah dikerjakan user
        Route::get('/training-history', [UserDashboardController::class, 'trainingHistory'])->name('training-history');

        // GET /my-profile - Halaman profil saya
        // Fungsi: Menampilkan dan mengedit profil user
        Route::get('/my-profile', [UserDashboardController::class, 'myProfile'])->name('my-profile');

    });

});