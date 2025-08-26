<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\ManagerController;
use Illuminate\Support\Facades\Route;

// Redirect root to welcome page for guests
Route::get('/', function () {
    return view('welcome');
});

// Redirect authenticated users to their role-specific dashboard
Route::get('/dashboard', function () {
    return auth()->user()->role === 'manager'
        ? redirect()->route('manager.dashboard')
        : redirect()->route('employee.dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Employee Routes - protected by auth and verification
Route::middleware(['auth', 'verified'])->name('employee.')->group(function () {
    Route::get('/employee/dashboard', [EmployeeController::class, 'dashboard'])->name('dashboard');
    Route::post('/employee/check-in', [EmployeeController::class, 'checkIn'])->name('check-in');  // Changed to 'check-in'
    Route::post('/employee/check-out', [EmployeeController::class, 'checkOut'])->name('check-out'); // Changed to 'check-out'
});

// Manager Routes - protected by auth, verification, and role middleware
Route::middleware(['auth', 'verified', 'role:manager'])->name('manager.')->group(function () {
    Route::get('/manager/dashboard', [ManagerController::class, 'dashboard'])->name('dashboard');
});

// Profile routes (accessible to all authenticated users regardless of role)
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Include authentication routes (login, register, password reset)
require __DIR__.'/auth.php';
