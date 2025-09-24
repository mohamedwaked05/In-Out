<?php

use App\Http\Controllers\EmployeeController;
use Illuminate\Support\Facades\Route;

Route::middleware('api.token')->prefix('v1')->group(function () {
    Route::get('/employee/dashboard', [EmployeeController::class, 'dashboard']);
    Route::post('/employee/check-in', [EmployeeController::class, 'checkIn']);
    Route::post('/employee/check-out', [EmployeeController::class, 'checkOut']);
});