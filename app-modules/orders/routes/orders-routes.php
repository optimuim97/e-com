<?php

use Illuminate\Support\Facades\Route;
use Modules\Orders\Http\Controllers\Api\OrderController;
use Modules\Orders\Http\Controllers\Api\PaymentController;

Route::prefix('api')->middleware(['api', 'auth:sanctum'])->group(function () {
    
    // Routes des commandes
    Route::prefix('orders')->group(function () {
        // Client routes
        Route::post('/checkout', [OrderController::class, 'checkout']);
        Route::get('/', [OrderController::class, 'index']);
        Route::get('/{orderId}', [OrderController::class, 'show']);
        Route::get('/number/{orderNumber}', [OrderController::class, 'showByNumber']);
        Route::post('/{orderId}/cancel', [OrderController::class, 'cancel']);
        
        // Admin routes (TODO: ajouter middleware admin)
        Route::middleware(['admin'])->group(function () {
            Route::get('/admin/all', [OrderController::class, 'adminIndex']);
            Route::put('/{orderId}/status', [OrderController::class, 'updateStatus']);
            Route::get('/admin/statistics', [OrderController::class, 'statistics']);
        });
    });

    // Routes des paiements
    Route::prefix('payments')->group(function () {
        // Client routes
        Route::post('/', [PaymentController::class, 'process']);
        Route::get('/orders/{orderId}', [PaymentController::class, 'orderPayments']);
        Route::get('/orders/{orderId}/check-partial', [PaymentController::class, 'checkPartialPayment']);
        
        // Admin routes (TODO: ajouter middleware admin)
        Route::middleware(['admin'])->group(function () {
            Route::post('/{paymentId}/confirm', [PaymentController::class, 'confirm']);
            Route::post('/{paymentId}/refund', [PaymentController::class, 'refund']);
            Route::get('/admin/statistics', [PaymentController::class, 'statistics']);
        });
    });
});
