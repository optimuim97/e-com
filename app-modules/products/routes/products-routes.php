<?php

use Illuminate\Support\Facades\Route;
use Modules\Products\Http\Controllers\Api\CategoryController;
use Modules\Products\Http\Controllers\Api\ProductController;

// Public API routes - Products
Route::prefix('api/products')->group(function () {
    // List and filter products
    Route::get('/', [ProductController::class, 'index'])->name('products.index');
    
    // Featured products
    Route::get('/featured', [ProductController::class, 'featured'])->name('products.featured');
    
    // Get product by slug
    Route::get('/slug/{slug}', [ProductController::class, 'showBySlug'])->name('products.show.slug');
    
    // Get single product
    Route::get('/{id}', [ProductController::class, 'show'])->name('products.show');
    
    // Related products
    Route::get('/{id}/related', [ProductController::class, 'related'])->name('products.related');
});

// Public API routes - Categories
Route::prefix('api/categories')->group(function () {
    // List all categories
    Route::get('/', [CategoryController::class, 'index'])->name('categories.index');
    
    // Get category tree
    Route::get('/tree', [CategoryController::class, 'tree'])->name('categories.tree');
    
    // Get root categories
    Route::get('/roots', [CategoryController::class, 'roots'])->name('categories.roots');
    
    // Categories with product count
    Route::get('/with-product-count', [CategoryController::class, 'withProductCount'])->name('categories.with-product-count');
    
    // Get category by slug
    Route::get('/slug/{slug}', [CategoryController::class, 'showBySlug'])->name('categories.show.slug');
    
    // Get single category
    Route::get('/{id}', [CategoryController::class, 'show'])->name('categories.show');
});

// Protected API routes - Products (require authentication)
Route::prefix('api/products')->middleware('auth:sanctum')->group(function () {
    // Create product
    Route::post('/', [ProductController::class, 'store'])->name('products.store');
    
    // Update product
    Route::put('/{id}', [ProductController::class, 'update'])->name('products.update');
    Route::patch('/{id}', [ProductController::class, 'update'])->name('products.patch');
    
    // Delete product
    Route::delete('/{id}', [ProductController::class, 'destroy'])->name('products.destroy');
    
    // Duplicate product
    Route::post('/{id}/duplicate', [ProductController::class, 'duplicate'])->name('products.duplicate');
    
    // Update stock
    Route::patch('/{id}/stock', [ProductController::class, 'updateStock'])->name('products.update-stock');
    
    // Bulk operations
    Route::post('/bulk/status', [ProductController::class, 'bulkUpdateStatus'])->name('products.bulk-status');
    
    // Stock management
    Route::get('/inventory/low-stock', [ProductController::class, 'lowStock'])->name('products.low-stock');
    Route::get('/inventory/out-of-stock', [ProductController::class, 'outOfStock'])->name('products.out-of-stock');
});

// Protected API routes - Categories (require authentication)
Route::prefix('api/categories')->middleware('auth:sanctum')->group(function () {
    // Create category
    Route::post('/', [CategoryController::class, 'store'])->name('categories.store');
    
    // Update category
    Route::put('/{id}', [CategoryController::class, 'update'])->name('categories.update');
    Route::patch('/{id}', [CategoryController::class, 'update'])->name('categories.patch');
    
    // Delete category
    Route::delete('/{id}', [CategoryController::class, 'destroy'])->name('categories.destroy');
    
    // Reorder categories
    Route::post('/reorder', [CategoryController::class, 'reorder'])->name('categories.reorder');
});
