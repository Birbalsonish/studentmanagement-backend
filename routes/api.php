<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    StudentController,
    TeacherController,
    ClassController,
    SubjectController,
    GradeController,
    ResultController,
    EnrollmentController,
    AttendanceController,
    FeeController,
    DashboardController
};

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Dashboard
Route::get('/dashboard', [DashboardController::class, 'index']);
Route::get('/dashboard/statistics', [DashboardController::class, 'statistics']);

// Students
Route::apiResource('students', StudentController::class);

// Teachers
Route::apiResource('teachers', TeacherController::class);

// Classes
Route::apiResource('classes', ClassController::class);

// Subjects
Route::apiResource('subjects', SubjectController::class);

// Grades
Route::apiResource('grades', GradeController::class);

// Results
Route::apiResource('results', ResultController::class);

// Enrollments
Route::apiResource('enrollments', EnrollmentController::class);

// Attendance
Route::apiResource('attendance', AttendanceController::class);
Route::post('/attendance/bulk', [AttendanceController::class, 'bulkStore']);

// Fees
Route::apiResource('fees', FeeController::class);
Route::post('/fees/{id}/payment', [FeeController::class, 'recordPayment']);

// Health check
Route::get('/health', function () {
    return response()->json([
        'status' => 'ok',
        'message' => 'Student Management API is running',
        'timestamp' => now()
    ]);
});
