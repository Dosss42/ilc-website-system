<?php

use App\Http\Controllers\Api\AuthController;
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
Route::post('/auth/login', [AuthController::class, 'login']);
Route::get('/auth/google', [AuthController::class, 'googleRedirect']);
Route::get('/auth/google/callback', [AuthController::class, 'googleCallback']);

// Protected API routes (require authentication)
Route::middleware('auth:sanctum')->group(function () {
    // Auth routes
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::get('/auth/me', [AuthController::class, 'me']);

    // Student routes — the only two of this API's student/grade/profile
    // surface actually used anywhere: the live student portal
    // (studentportal.blade.php) calls these two directly. Everything else
    // that used to live in this group (profile/*, grades/*, the old
    // student/announcements, portal-data, admin/dashboard-stats, and a
    // shadowed enrollment/submit pointing at a method that didn't even
    // exist) had zero live callers — the real app uses session-based web
    // routes instead — and was removed rather than left as dead surface.
    Route::get('/student/grades', [StudentController::class, 'getGrades']);
    Route::get('/student/schedule', [StudentController::class, 'getSchedule']);
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
    Route::post('/auth/login', [AuthController::class, 'login']);
});
