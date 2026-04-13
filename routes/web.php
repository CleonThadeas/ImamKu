<?php

use App\Http\Controllers\Admin;
use App\Http\Controllers\Imam;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (auth()->check()) {
        return auth()->user()->isAdmin()
            ? redirect()->route('admin.dashboard')
            : redirect()->route('imam.dashboard');
    }
    return view('welcome');
});

Route::get('/dashboard', function () {
    if (auth()->user()->isAdmin()) {
        return redirect()->route('admin.dashboard');
    }
    return redirect()->route('imam.dashboard');
})->middleware(['auth'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // Peraturan & Ketentuan Page
    Route::get('/guidelines', function() {
        return view('guidelines');
    })->name('guidelines');
});

// ── Admin Routes ────────────────────────────────────────────────
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [Admin\DashboardController::class, 'index'])->name('dashboard');

    // Imam Management
    Route::resource('imams', Admin\ImamController::class)->parameters(['imams' => 'imam'])->except(['show']);

    // Season Management
    Route::resource('seasons', Admin\SeasonController::class)->except(['show']);

    // Schedule Management
    Route::get('schedules', [Admin\ScheduleController::class, 'index'])->name('schedules.index');
    Route::post('schedules/generate', [Admin\ScheduleController::class, 'generate'])->name('schedules.generate');
    Route::post('schedules/assign', [Admin\ScheduleController::class, 'assign'])->name('schedules.assign');
    Route::delete('schedules/{schedule}/remove', [Admin\ScheduleController::class, 'removeAssignment'])->name('schedules.remove');
    Route::get('schedules/available-imams', [Admin\ScheduleController::class, 'getAvailableImams'])->name('schedules.available-imams');

    // Prayer Times
    Route::get('prayer-times', [Admin\PrayerTimeController::class, 'index'])->name('prayer-times.index');
    Route::post('prayer-times', [Admin\PrayerTimeController::class, 'store'])->name('prayer-times.store');
    Route::post('prayer-times/sync', [Admin\PrayerTimeController::class, 'syncFromApi'])->name('prayer-times.sync');
    Route::patch('prayer-times/{prayerTime}/override', [Admin\PrayerTimeController::class, 'override'])->name('prayer-times.override');
    Route::post('prayer-times/{prayerTime}/reset', [Admin\PrayerTimeController::class, 'resetOverride'])->name('prayer-times.reset');
    Route::delete('prayer-times/{prayerTime}', [Admin\PrayerTimeController::class, 'destroy'])->name('prayer-times.destroy');

    // Fee Management
    Route::get('fees', [Admin\FeeController::class, 'index'])->name('fees.index');
    Route::post('fees', [Admin\FeeController::class, 'update'])->name('fees.update');
    Route::get('fees/report', [Admin\FeeController::class, 'report'])->name('fees.report');

    // Export Hub
    Route::get('exports', [Admin\ExportController::class, 'index'])->name('exports.index');
    Route::post('exports/download', [Admin\ExportController::class, 'download'])->name('exports.download');

    // Log & Broadcast
    Route::get('notification-logs', [Admin\NotificationLogController::class, 'index'])->name('notification-logs.index');
    Route::get('broadcast', [Admin\BroadcastNotificationController::class, 'index'])->name('broadcast.index');
    Route::post('broadcast', [Admin\BroadcastNotificationController::class, 'store'])->name('broadcast.send');
    Route::post('notification-config/update', [Admin\NotificationConfigController::class, 'update'])->name('notification-config.update');
    
    // Manajemen Absensi
    Route::get('attendances', [Admin\AttendanceController::class, 'index'])->name('attendances.index');
    Route::post('attendances/{attendance}/approve', [Admin\AttendanceController::class, 'approve'])->name('attendances.approve');
    Route::post('attendances/{attendance}/reject', [Admin\AttendanceController::class, 'reject'])->name('attendances.reject');
    Route::post('fee-configs/toggle-auto-approve', [Admin\AttendanceController::class, 'updateConfig'])->name('fee-configs.toggle-auto-approve');
    
    // Monitoring Swaps
    Route::get('swaps', [Admin\SwapController::class, 'index'])->name('swaps.index');

    // Mosque Configuration (GPS)
    Route::get('mosque-config', [Admin\MosqueConfigController::class, 'index'])->name('mosque-config.index');
    Route::post('mosque-config', [Admin\MosqueConfigController::class, 'store'])->name('mosque-config.store');

    // Penalty System
    Route::get('penalties', [Admin\PenaltyController::class, 'index'])->name('penalties.index');
    Route::get('penalties/{user}/history', [Admin\PenaltyController::class, 'history'])->name('penalties.history');
    Route::post('penalties/{user}/lift', [Admin\PenaltyController::class, 'liftRestriction'])->name('penalties.lift');
});

// ── Imam Routes ─────────────────────────────────────────────────
Route::middleware(['auth'])->prefix('imam')->name('imam.')->group(function () {
    Route::get('/', [Imam\DashboardController::class, 'index'])->name('dashboard');
    Route::get('schedules', [Imam\ScheduleController::class, 'index'])->name('schedules.index');

    // Notifications
    Route::get('notifications', [Imam\NotificationController::class, 'index'])->name('notifications.index');
    
    Route::name('swaps.')->prefix('swaps')->group(function() {
        Route::get('/', [Imam\SwapController::class, 'index'])->name('index');
        Route::get('create', [Imam\SwapController::class, 'create'])->name('create');
        Route::post('/', [Imam\SwapController::class, 'store'])->name('store');
        Route::post('{swap}/respond', [Imam\SwapController::class, 'respond'])->name('respond');
    });
    
    // Attendance
    Route::post('schedules/{schedule}/attendance', [Imam\AttendanceController::class, 'store'])->name('schedules.attendance');
    
    // Income/Fee
    Route::get('fees', [Imam\FeeController::class, 'index'])->name('fees.index');
    
    // Points / Penalties
    Route::get('points', [Imam\PenaltyController::class, 'index'])->name('points.index');
});



require __DIR__.'/auth.php';
