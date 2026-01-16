<?php

use Illuminate\Support\Facades\Route;
use Modules\Cart\Http\Controllers\Api\CartController;

Route::prefix('api/cart')->middleware('api')->group(function () {
    // Routes publiques (pour les invités et utilisateurs connectés)
    Route::get('/', [CartController::class, 'index']);
    Route::post('/add', [CartController::class, 'store']);
    Route::put('/items/{cartItemId}', [CartController::class, 'update']);
    Route::delete('/items/{cartItemId}', [CartController::class, 'destroy']);
    Route::delete('/clear', [CartController::class, 'clear']);
    Route::post('/coupon', [CartController::class, 'applyCoupon']);
    Route::delete('/coupon', [CartController::class, 'removeCoupon']);
    Route::post('/shipping/calculate', [CartController::class, 'calculateShipping']);
});
