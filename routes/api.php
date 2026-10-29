<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\MasterDataController;
use App\Http\Controllers\Admin\CBT\QuestionController;

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});

// ============================================
// PUBLIC DROPDOWN ROUTES (minimal, rate-limited)
// ============================================
Route::middleware(['throttle:60,1'])->group(function () {
    Route::get('/dropdowns/departments', [MasterDataController::class, 'getDepartments']);
    Route::get('/dropdowns/divisions', [MasterDataController::class, 'getDivisions']);
    Route::get('/dropdowns/positions', [MasterDataController::class, 'getPositionsByDivision']);
});

// ============================================
// MASTER DATA ROUTES (admin/manager only)
// ============================================
Route::middleware(['auth', 'password.changed', 'is.admin'])->group(function () {

    // READ ROUTES
    Route::get('/departments', [MasterDataController::class, 'getDepartments']);
    Route::get('/departments/list', [MasterDataController::class, 'listDepartments']);
    Route::get('/departments/{id}', [MasterDataController::class, 'showDepartment']);
    Route::get('/positions', [MasterDataController::class, 'getPositionsByDivision']);
    Route::get('/divisions', [MasterDataController::class, 'getDivisions']);
    Route::get('/divisions/{id}/skills', [MasterDataController::class, 'getSkillsByDivision']);
    Route::get('/employees', [MasterDataController::class, 'getEmployees']);
    Route::get('/employees/{id}', [MasterDataController::class, 'getEmployee']);
    Route::get('/competencies', [MasterDataController::class, 'getCompetencies']);
    Route::get('/competencies/skills', [MasterDataController::class, 'getSkillBasedCompetencies']);
    Route::get('/questions/set/{questionSetId}', [QuestionController::class, 'getQuestionsFromSet']);

    // EMPLOYEE ROUTES
    Route::post('/employees', [MasterDataController::class, 'storeEmployee']);
    Route::put('/employees/{id}', [MasterDataController::class, 'updateEmployee']);
    Route::delete('/employees/{id}', [MasterDataController::class, 'destroyEmployee']);
    Route::post('/employees/{id}/reset-password', [MasterDataController::class, 'resetEmployeePassword']);

    // DEPARTMENT ROUTES
    Route::post('/departments', [MasterDataController::class, 'storeDepartment']);
    Route::put('/departments/{id}', [MasterDataController::class, 'updateDepartment']);
    Route::delete('/departments/{id}', [MasterDataController::class, 'destroyDepartment']);

    // POSITION ROUTES
    Route::post('/positions', [MasterDataController::class, 'storePosition']);
    Route::delete('/positions/{id}', [MasterDataController::class, 'destroyPosition']);

    // COMPETENCY ROUTES
    Route::post('/competencies', [MasterDataController::class, 'storeCompetency']);
    Route::post('/competencies/skills', [MasterDataController::class, 'storeSkillCompetency']);

    // DIVISION ROUTES
    Route::post('/divisions', [MasterDataController::class, 'storeDivision']);
    Route::delete('/divisions/{id}', [MasterDataController::class, 'destroyDivision']);

    // SKILL ROUTES
    Route::post('/skills', [MasterDataController::class, 'storeSkill']);
    Route::delete('/skills/{id}', [MasterDataController::class, 'destroySkill']);
});
