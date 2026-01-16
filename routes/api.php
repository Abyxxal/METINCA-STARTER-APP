<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MasterDataController;
use App\Http\Controllers\CBT\QuestionController;

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});

// ============================================
// PUBLIC DROPDOWN ROUTES (tanpa auth requirement)
// ============================================
Route::get('/dropdowns/departments', [MasterDataController::class, 'getDepartments']);
Route::get('/dropdowns/divisions', [MasterDataController::class, 'getDivisions']);
Route::get('/dropdowns/positions', [MasterDataController::class, 'getPositionsByDivision']);

// PUBLIC READ ROUTES (untuk DataTables yang loaded saat page load, sebelum authentication)
Route::get('/departments', [MasterDataController::class, 'getDepartments']);
Route::get('/employees', [MasterDataController::class, 'getEmployees']);
Route::get('/positions', [MasterDataController::class, 'getPositionsByDivision']);
Route::get('/divisions', [MasterDataController::class, 'getDivisions']);
Route::get('/competencies', [MasterDataController::class, 'getCompetencies']);
Route::get('/questions/set/{questionSetId}', [QuestionController::class, 'getQuestionsFromSet']);

// API Routes untuk Master Data (CRUD)
Route::middleware(['auth'])->group(function () {
    
    // ============================================
    // EMPLOYEE ROUTES
    // ============================================
    Route::post('/employees', [MasterDataController::class, 'storeEmployee']);
    Route::put('/employees/{id}', [MasterDataController::class, 'updateEmployee']);
    Route::delete('/employees/{id}', [MasterDataController::class, 'destroyEmployee']);
    Route::get('/employees/{id}', [MasterDataController::class, 'getEmployee']); // Fetch single employee
    Route::post('/employees/{id}/reset-password', [MasterDataController::class, 'resetEmployeePassword']); // Reset employee password

    // ============================================
    // DEPARTMENT ROUTES
    // ============================================
    Route::post('/departments', [MasterDataController::class, 'storeDepartment']);
    Route::put('/departments/{id}', [MasterDataController::class, 'updateDepartment']);
    Route::delete('/departments/{id}', [MasterDataController::class, 'destroyDepartment']);
    Route::get('/departments/list', [MasterDataController::class, 'listDepartments']); // For dropdown

    // ============================================
    // POSITION ROUTES
    // ============================================
    Route::post('/positions', [MasterDataController::class, 'storePosition']);
    Route::delete('/positions/{id}', [MasterDataController::class, 'destroyPosition']);

    // ============================================
    // COMPETENCY ROUTES
    // ============================================
    Route::post('/competencies', [MasterDataController::class, 'storeCompetency']);

    // ============================================
    // DIVISION ROUTES
    // ============================================
    Route::post('/divisions', [MasterDataController::class, 'storeDivision']);
    Route::delete('/divisions/{id}', [MasterDataController::class, 'destroyDivision']);
    Route::get('/divisions/{id}/skills', [MasterDataController::class, 'getSkillsByDivision']);

    // ============================================
    // SKILL ROUTES
    // ============================================
    Route::post('/skills', [MasterDataController::class, 'storeSkill']);
    Route::delete('/skills/{id}', [MasterDataController::class, 'destroySkill']);

    // ============================================
    // SKILL-BASED COMPETENCY ROUTES
    // ============================================
    Route::get('/competencies/skills', [MasterDataController::class, 'getSkillBasedCompetencies']);
    Route::post('/competencies/skills', [MasterDataController::class, 'storeSkillCompetency']);

});

