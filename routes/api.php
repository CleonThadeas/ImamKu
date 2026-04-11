<?php

use App\Http\Controllers\Api;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| ImamKu Mobile API Routes (Imam Role)
|--------------------------------------------------------------------------
| Base URL: /api
| Auth: Laravel Sanctum (Bearer Token)
*/

// ── Public (Tanpa Auth) ──────────────────────────────────────────
Route::post('/login', [Api\AuthController::class, 'login']);

// ── Protected (auth:sanctum) ─────────────────────────────────────
Route::middleware('auth:sanctum')->group(function () {

    // Auth
    Route::post('/logout', [Api\AuthController::class, 'logout']);
    Route::get('/user', [Api\AuthController::class, 'user']);

    // Dashboard
    Route::get('/dashboard', [Api\DashboardController::class, 'index']);

    // Jadwal
    Route::get('/schedules', [Api\ScheduleController::class, 'index']);
    Route::get('/schedules/grid', [Api\ScheduleController::class, 'grid']);
    Route::get('/schedules/{schedule}', [Api\ScheduleController::class, 'show']);

    // Absensi (check-in photo)
    Route::post('/schedules/{schedule}/attendance', [Api\AttendanceController::class, 'store']);

    // Swap Jadwal
    Route::get('/swaps', [Api\SwapController::class, 'index']);
    Route::post('/swaps', [Api\SwapController::class, 'store']);
    Route::post('/swaps/{swap}/respond', [Api\SwapController::class, 'respond']);

    // Pendapatan / Fee
    Route::get('/fees', [Api\FeeController::class, 'index']);

    // Notifikasi
    Route::get('/notifications', [Api\NotificationController::class, 'index']);
    Route::post('/notifications/read-all', [Api\NotificationController::class, 'readAll']);
    Route::post('/notifications/{id}/read', [Api\NotificationController::class, 'read']);

    // Penalti & Ranking
    Route::get('/penalties', [Api\PenaltyController::class, 'index']);
    Route::get('/penalties/ranking', [Api\PenaltyController::class, 'ranking']);

    // Panduan & Ketentuan
    Route::get('/guidelines', [Api\GuidelineController::class, 'index']);
});
