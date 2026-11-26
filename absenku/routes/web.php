<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FaceRegisterController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AttendanceController;

Route::get('/', function () {
    return view('welcome');
});

// ── Dashboard (hanya role user) ───────────────────────────
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified', 'role:user'])
    ->name('dashboard');

// ── Route yang butuh login ────────────────────────────────
Route::middleware(['auth', 'verified'])->group(function () {

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Face Registration (hanya role user)
    Route::post('/face-register/store', [FaceRegisterController::class, 'store'])
        ->middleware('role:user')
        ->name('face.register.store');

    // Attendance
    Route::post('/attendance/check-in', [AttendanceController::class, 'checkIn'])
    ->middleware('role:user')
    ->name('attendance.checkin');
});

require __DIR__.'/auth.php';