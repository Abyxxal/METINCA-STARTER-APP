<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MasterDataController;

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});

// API Routes untuk Master Data (CRUD)
Route::middleware(['auth'])->group(function () {
    
    // ============================================
    // EMPLOYEE ROUTES
    // ============================================
    Route::post('/employees', [MasterDataController::class, 'storeEmployee']);
    Route::put('/employees/{id}', [MasterDataController::class, 'updateEmployee']);
    Route::delete('/employees/{id}', [MasterDataController::class, 'destroyEmployee']);
    Route::get('/employees/{id}', [MasterDataController::class, 'getEmployee']); // Fetch single employee
    Route::get('/employees', [MasterDataController::class, 'getEmployees']);

    // ============================================
    // DEPARTMENT ROUTES
    // ============================================
    Route::post('/departments', [MasterDataController::class, 'storeDepartment']);
    Route::put('/departments/{id}', [MasterDataController::class, 'updateDepartment']);
    Route::delete('/departments/{id}', [MasterDataController::class, 'destroyDepartment']);
    Route::get('/departments', [MasterDataController::class, 'getDepartments']);
    Route::get('/departments/list', [MasterDataController::class, 'listDepartments']); // For dropdown

    // ============================================
    // POSITION ROUTES
    // ============================================
    Route::post('/positions', [MasterDataController::class, 'storePosition']);
    Route::get('/positions', [MasterDataController::class, 'getPositionsByDepartment']);
    Route::delete('/positions/{id}', [MasterDataController::class, 'destroyPosition']);

    // ============================================
    // COMPETENCY ROUTES
    // ============================================
    Route::get('/competencies', [MasterDataController::class, 'getCompetencies']);
    Route::post('/competencies', [MasterDataController::class, 'storeCompetency']);

});

