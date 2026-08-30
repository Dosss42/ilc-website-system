<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\GradeController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\StudentController;
use App\Http\Controllers\FeeController;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Public API routes
Route::post('/auth/register', [AuthController::class, 'register']);
Route::post('/auth/login', [AuthController::class, 'login']);
Route::get('/auth/google', [AuthController::class, 'googleRedirect']);
Route::get('/auth/google/callback', [AuthController::class, 'googleCallback']);

// Protected API routes (require authentication)
Route::middleware('auth:sanctum')->group(function () {
    // Auth routes
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::get('/auth/me', [AuthController::class, 'me']);
    
    // Profile routes
    Route::get('/profile/status', [ProfileController::class, 'checkCompletionStatus']);
    Route::post('/profile/personal', [ProfileController::class, 'updatePersonal']);
    Route::post('/profile/address', [ProfileController::class, 'updateAddress']);
    Route::post('/profile/guardian', [ProfileController::class, 'updateGuardian']);
    Route::post('/profile/school', [ProfileController::class, 'updatePreviousSchool']);
    Route::post('/profile/enrollment', [ProfileController::class, 'updateEnrollment']);
    
    // Grade management routes
    Route::get('/grades/student', [GradeController::class, 'getStudentGrades']);
    Route::get('/grades/class', [GradeController::class, 'getClassGrades']);
    Route::post('/grades/upsert', [GradeController::class, 'upsertGrades']);
    Route::get('/grades/subjects', [GradeController::class, 'getTeacherSubjects']);
    Route::get('/grades/statistics', [GradeController::class, 'getGradeStatistics']);
    Route::delete('/grades/{id}', [GradeController::class, 'deleteGrade']);
    
    // Student routes
    Route::get('/student/grades', [StudentController::class, 'getGrades']);
    Route::get('/student/schedule', [StudentController::class, 'getSchedule']);
    Route::get('/student/announcements', [StudentController::class, 'getAnnouncements']);
    Route::get('/student/portal-data', [StudentController::class, 'getPortalData']);
    
    // Admin dashboard routes
    Route::get('/admin/dashboard-stats', [StudentController::class, 'getDashboardStats']);
    
    // Enrollment routes
    Route::post('/enrollment/submit', [ProfileController::class, 'submitEnrollment']);
    
});

// Fee management routes (accessible via session auth for admin dashboard)
Route::middleware('auth')->group(function () {
    Route::get('/fees/settings', [FeeController::class, 'getFeeSettings']);
    Route::post('/fees/calculate', [FeeController::class, 'calculateFee']);
    Route::get('/fees/payment-options', [FeeController::class, 'getPaymentOptions']);
    Route::get('/fees/summary', [FeeController::class, 'getFeeSummary']);
});

// Rate limiting for sensitive endpoints
Route::middleware(['throttle:5,1'])->group(function () {
    Route::post('/auth/register', [AuthController::class, 'register']);
    Route::post('/auth/login', [AuthController::class, 'login']);
});
