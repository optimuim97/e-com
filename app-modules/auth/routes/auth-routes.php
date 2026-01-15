<?php

use Illuminate\Support\Facades\Route;
use Modules\Auth\Http\Controllers\Api\AuthController;

// Public API routes
Route::prefix('api')->group(function () {
    Route::post('/register', [AuthController::class, 'register'])->name('auth.register');
    Route::post('/login', [AuthController::class, 'login'])->name('auth.login');
    Route::post('/forgot-password', [AuthController::class, 'forgotPassword'])->name('auth.forgot-password');
    Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('auth.reset-password');
    
    // Basic Auth endpoint (for axios basic authentication)
    Route::middleware('auth.basic')->group(function () {
        Route::get('/users/me', [AuthController::class, 'basicAuthLogin'])->name('auth.basic');
    });
});

// Protected API routes (require Sanctum authentication)
Route::prefix('api')->middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('auth.logout');
    Route::get('/me', [AuthController::class, 'me'])->name('auth.me');
    Route::post('/refresh', [AuthController::class, 'refresh'])->name('auth.refresh');
    Route::post('/update-password', [AuthController::class, 'updatePassword'])->name('auth.update-password');
});

