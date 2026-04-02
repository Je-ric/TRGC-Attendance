<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\AttendanceTypeController;
use App\Http\Controllers\PersonController;
use App\Http\Controllers\FamilyController;

Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

// Attendance
Route::get('/attendance',         [AttendanceController::class, 'index'])->name('attendance.index');
Route::get('/attendance/{type}',  [AttendanceController::class, 'show'])->name('attendance.show');
Route::get('/attendance-records', [AttendanceController::class, 'records'])->name('attendance.records');

// Attendance Types (Services)
Route::post('/attendance-types',                    [AttendanceTypeController::class, 'store'])->name('attendance-types.store');
Route::put('/attendance-types/{attendanceType}',    [AttendanceTypeController::class, 'update'])->name('attendance-types.update');
Route::delete('/attendance-types/{attendanceType}', [AttendanceTypeController::class, 'destroy'])->name('attendance-types.destroy');

// People & Families
Route::get('/people',   [PersonController::class, 'index'])->name('people.index');
Route::get('/families', [FamilyController::class, 'index'])->name('families.index');
