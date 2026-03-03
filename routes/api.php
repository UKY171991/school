<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\SchoolApiController;

// Existing routes
Route::prefix('schools')->group(function () {
    Route::get('/by-domain', [\App\Http\Controllers\Api\SchoolDataController::class, 'getByDomain']);
    Route::get('/list', [\App\Http\Controllers\Api\SchoolDataController::class, 'listWithDomains']);
});

// School Domain-based API Routes
Route::prefix('school-api')->middleware('api.domain')->group(function () {
    
    // School Information
    Route::get('/info', [SchoolApiController::class, 'getSchoolInfo']);
    Route::get('/statistics', [SchoolApiController::class, 'getStatistics']);
    Route::get('/branches', [SchoolApiController::class, 'getBranches']);
    
    // Students
    Route::get('/students', [SchoolApiController::class, 'getStudents']);
    Route::get('/students/{identifier}', [SchoolApiController::class, 'getStudent']);
    
    // Teachers
    Route::get('/teachers', [SchoolApiController::class, 'getTeachers']);
    
    // Academic
    Route::get('/grades', [SchoolApiController::class, 'getGrades']);
    Route::get('/sections', [SchoolApiController::class, 'getSections']);
    Route::get('/exams', [SchoolApiController::class, 'getExams']);
    
    // Attendance
    Route::get('/attendance', [SchoolApiController::class, 'getAttendance']);
    
    // Fees
    Route::get('/fee-payments', [SchoolApiController::class, 'getFeePayments']);
});

// Public API Routes (without middleware) - for testing
Route::prefix('public-api')->group(function () {
    
    // School Information
    Route::get('/info', [SchoolApiController::class, 'getSchoolInfo']);
    Route::get('/statistics', [SchoolApiController::class, 'getStatistics']);
    Route::get('/branches', [SchoolApiController::class, 'getBranches']);
    
    // Students
    Route::get('/students', [SchoolApiController::class, 'getStudents']);
    Route::get('/students/{identifier}', [SchoolApiController::class, 'getStudent']);
    
    // Teachers
    Route::get('/teachers', [SchoolApiController::class, 'getTeachers']);
    
    // Academic
    Route::get('/grades', [SchoolApiController::class, 'getGrades']);
    Route::get('/sections', [SchoolApiController::class, 'getSections']);
    Route::get('/exams', [SchoolApiController::class, 'getExams']);
    
    // Attendance
    Route::get('/attendance', [SchoolApiController::class, 'getAttendance']);
    
    // Fees
    Route::get('/fee-payments', [SchoolApiController::class, 'getFeePayments']);
});
