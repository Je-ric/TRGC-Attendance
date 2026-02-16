<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\PersonController;


use App\Http\Controllers\AttendanceTypeController;


Route::get('/', fn() => redirect()->route('attendance.index'));

Route::get('/attendance', [AttendanceController::class, 'index'])->name('attendance.index');
Route::get('/attendance/{type}', [AttendanceController::class, 'show'])->name('attendance.show');
Route::get('/attendance-records', [AttendanceController::class, 'records'])->name('attendance.records');

Route::get('/people', [PersonController::class, 'index'])->name('people.index');

Route::get('/families', [App\Http\Controllers\FamilyController::class, 'index'])->name('families.index');

Route::post('/attendance-types', [AttendanceTypeController::class, 'store'])->name('attendance-types.store');
Route::delete('/attendance-types/{attendanceType}', [AttendanceTypeController::class, 'destroy'])->name('attendance-types.destroy');
