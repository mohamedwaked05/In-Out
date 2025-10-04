<?php

use App\Http\Controllers\Api\EmployeeApiController;
use Illuminate\Support\Facades\Route;

Route::middleware('api.token')->prefix('v1')->group(function () {
    Route::get('/employee/dashboard', [EmployeeApiController::class, 'dashboard']);
    Route::post('/employee/check-in', [EmployeeApiController::class, 'checkIn']);
    Route::post('/employee/check-out', [EmployeeApiController::class, 'checkOut']);
});