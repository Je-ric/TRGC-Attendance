<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\PersonController;
use App\Http\Controllers\FamilyController;

// ── Auth ──────────────────────────────────────────────────────────────
Route::get('/login',  [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// ── Protected app routes ──────────────────────────────────────────────
Route::middleware('auth')->group(function () {

    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Services (New simplified structure)
    Route::prefix('services')->group(function () {
        Route::get('/',                     [ServiceController::class, 'index'])->name('services.index');
        Route::get('/create',               [ServiceController::class, 'create'])->name('services.create');
        Route::post('/',                    [ServiceController::class, 'store'])->name('services.store');
        Route::get('/{service}',            [ServiceController::class, 'show'])->name('services.show');
        Route::get('/{service}/edit',      [ServiceController::class, 'edit'])->name('services.edit');
        Route::put('/{service}',            [ServiceController::class, 'update'])->name('services.update');
        Route::delete('/{service}',         [ServiceController::class, 'destroy'])->name('services.destroy');
        Route::get('/{service}/checkin',   [ServiceController::class, 'checkin'])->name('services.checkin');
        Route::get('/weekly/{week?}',      [ServiceController::class, 'weekly'])->name('services.weekly');
        Route::post('/quick-sunday',        [ServiceController::class, 'quickSundayService'])->name('services.quick-sunday');
    });

    // Exports
    Route::prefix('export')->group(function () {
        Route::get('/weekly/{week}',            [ExportController::class, 'exportWeeklyAttendance'])->name('export.weekly');
        Route::get('/date-range',               [ExportController::class, 'exportDateRange'])->name('export.date-range');
        Route::get('/service/{service}',        [ExportController::class, 'exportService'])->name('export.service');
        Route::get('/person/{personId}',        [ExportController::class, 'exportPersonAttendance'])->name('export.person');
    });

    // Attendance (Redirects to services for backward compatibility)
    Route::get('/attendance',         [AttendanceController::class, 'index'])->name('attendance.index');
    Route::get('/attendance-records', [AttendanceController::class, 'records'])->name('attendance.records');

    // People & Families
    Route::get('/people',   [PersonController::class, 'index'])->name('people.index');
    Route::get('/families', [FamilyController::class, 'index'])->name('families.index');
});
