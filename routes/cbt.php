<?php

use App\Http\Controllers\CBT\ExamController;
use App\Http\Controllers\CBT\QuestionController;
use App\Http\Controllers\CBT\ExamSessionController;
use App\Http\Controllers\CBT\EmployeeExamController;
use App\Http\Controllers\CBT\CompetencyMatrixController;
use App\Http\Controllers\CBT\DivisionSkillController;
use App\Http\Controllers\CBT\EmployeeCompetencyController;
use Illuminate\Support\Facades\Route;

/**
 * CBT (Computer Based Test) Routes
 * 
 * Routes untuk sistem ujian kompetensi karyawan.
 */

// ============================================
// ADMIN CBT ROUTES
// ============================================
Route::middleware(['auth', 'is.admin'])->prefix('cbt/admin')->name('cbt.admin.')->group(function () {

    // Dashboard CBT Admin
    Route::get('/', function () {
        return redirect()->route('cbt.admin.sessions.index');
    })->name('dashboard');

    // ----- QUESTIONS (Bank Soal) -----
    Route::prefix('questions')->name('questions.')->group(function () {
        Route::get('/', [QuestionController::class, 'index'])->name('index');
        Route::get('/create', [QuestionController::class, 'create'])->name('create');
        Route::post('/', [QuestionController::class, 'store'])->name('store');
        Route::get('/set/{questionSetId}', [QuestionController::class, 'showSet'])->name('show-set');
        Route::get('/set/{questionSetId}/edit', [QuestionController::class, 'editSet'])->name('edit-set');
        Route::put('/set/{questionSetId}', [QuestionController::class, 'updateSet'])->name('update-set');
        Route::delete('/set/{questionSetId}', [QuestionController::class, 'destroySet'])->name('destroy-set');
        Route::get('/{question}', [QuestionController::class, 'show'])->name('show');
        Route::get('/{question}/edit', [QuestionController::class, 'edit'])->name('edit');
        Route::put('/{question}', [QuestionController::class, 'update'])->name('update');
        Route::delete('/{question}', [QuestionController::class, 'destroy'])->name('destroy');
    });

    // ----- EXAMS (Setup Ujian) -----
    Route::prefix('exams')->name('exams.')->group(function () {
        Route::get('/', [ExamController::class, 'index'])->name('index');
        Route::get('/create', [ExamController::class, 'create'])->name('create');
        Route::post('/', [ExamController::class, 'store'])->name('store');
        Route::get('/{exam}', [ExamController::class, 'show'])->name('show');
        Route::get('/{exam}/edit', [ExamController::class, 'edit'])->name('edit');
        Route::put('/{exam}', [ExamController::class, 'update'])->name('update');
        Route::delete('/{exam}', [ExamController::class, 'destroy'])->name('destroy');
        Route::post('/{exam}/toggle-publish', [ExamController::class, 'togglePublish'])->name('toggle-publish');
        Route::get('/ajax/questions-by-skill', [ExamController::class, 'getQuestionsBySkill'])->name('questions-by-skill');
    });

    // ----- EXAM SESSIONS (Penugasan & Hasil) -----
    Route::prefix('sessions')->name('sessions.')->group(function () {
        Route::get('/', [ExamSessionController::class, 'index'])->name('index');
        Route::get('/pending', [ExamSessionController::class, 'pending'])->name('pending');
        Route::get('/create', [ExamSessionController::class, 'create'])->name('create');
        Route::post('/', [ExamSessionController::class, 'store'])->name('store');
        Route::get('/{session}', [ExamSessionController::class, 'show'])->name('show');
        Route::post('/{session}/verify', [ExamSessionController::class, 'verify'])->name('verify');
        Route::delete('/{session}/cancel', [ExamSessionController::class, 'cancel'])->name('cancel');
        Route::post('/bulk-assign', [ExamSessionController::class, 'bulkAssignByDivision'])->name('bulk-assign');
    });

    // ----- MANAGER APPROVAL (Hanya Manager) -----
    Route::middleware(['is.manager'])->group(function () {
        Route::get('/approval', [ExamSessionController::class, 'pendingApproval'])->name('sessions.pending-approval');
        Route::post('/sessions/{session}/approve-level', [ExamSessionController::class, 'approveLevel'])->name('sessions.approve-level');
        Route::post('/sessions/{session}/reject-level', [ExamSessionController::class, 'rejectLevel'])->name('sessions.reject-level');
    });

    // ----- COMPETENCY MATRIX (Matriks Kompetensi) -----
    Route::get('/competency-matrix', [CompetencyMatrixController::class, 'index'])->name('competency-matrix');

    // ----- DIVISION SKILLS (Customisasi Skill per Divisi) -----
    Route::prefix('division-skills')->name('division-skills.')->group(function () {
        Route::get('/', [DivisionSkillController::class, 'index'])->name('index');
        Route::post('/', [DivisionSkillController::class, 'store'])->name('store');
        Route::put('/{divisionSkill}', [DivisionSkillController::class, 'update'])->name('update');
        Route::delete('/{divisionSkill}', [DivisionSkillController::class, 'destroy'])->name('destroy');
    });

    // ----- EMPLOYEE COMPETENCIES (Manual Edit Level) -----
    Route::prefix('employee-competencies')->name('employee-competencies.')->group(function () {
        Route::get('/', [EmployeeCompetencyController::class, 'index'])->name('index');
        Route::get('/{employee}/edit', [EmployeeCompetencyController::class, 'edit'])->name('edit');
        Route::put('/{employee}', [EmployeeCompetencyController::class, 'update'])->name('update');
        Route::delete('/{employee}/skill/{skillId}', [EmployeeCompetencyController::class, 'destroy'])->name('destroy');
    });
});

// ============================================
// EMPLOYEE CBT ROUTES
// ============================================
Route::middleware(['auth'])->prefix('cbt')->name('cbt.employee.')->group(function () {

    // Dashboard CBT Karyawan
    Route::get('/my-exams', [EmployeeExamController::class, 'dashboard'])->name('dashboard');

    // Kompetensi Saya
    Route::get('/my-competencies', [EmployeeExamController::class, 'myCompetencies'])->name('competencies');

    // Riwayat Ujian
    Route::get('/history', [EmployeeExamController::class, 'history'])->name('history');

    // View exam info and register (for employees who want to take available exams)
    Route::get('/exam/{exam}/info', [EmployeeExamController::class, 'examInfo'])->name('exam-info');
    Route::post('/exam/{exam}/register', [EmployeeExamController::class, 'registerForExam'])->name('register');

    // ----- EXAM SESSION ROUTES -----
    Route::prefix('session/{session}')->group(function () {
        // Info ujian sebelum mulai
        Route::get('/', [EmployeeExamController::class, 'showExam'])->name('show');
        
        // Mulai ujian
        Route::post('/start', [EmployeeExamController::class, 'startExam'])->name('start');
        
        // Kerjakan ujian
        Route::get('/take', [EmployeeExamController::class, 'takeExam'])->name('take');
        
        // Simpan jawaban (AJAX)
        Route::post('/save-answer', [EmployeeExamController::class, 'saveAnswer'])->name('save-answer');
        
        // Submit ujian
        Route::post('/submit', [EmployeeExamController::class, 'submitExam'])->name('submit');
        
        // Lihat hasil
        Route::get('/result', [EmployeeExamController::class, 'showResult'])->name('result');
    });
});
