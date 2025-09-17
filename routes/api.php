<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\ReviewController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// API Version 1
Route::prefix('v1')->group(function () {
    
    // Authentication routes (no middleware)
    Route::prefix('auth')->group(function () {
        Route::post('register', [AuthController::class, 'register']);
        Route::post('login', [AuthController::class, 'login']);
        
        // Protected auth routes
        Route::middleware('auth:sanctum')->group(function () {
            Route::post('logout', [AuthController::class, 'logout']);
            Route::get('user', [AuthController::class, 'user']);
        });
    });

    // Public product routes (guests can view)
    Route::get('products', [ProductController::class, 'index']);
    Route::get('products/{product}', [ProductController::class, 'show']);
    
    // Public review routes (guests can view)
    Route::get('products/{product}/reviews', [ReviewController::class, 'index']);
    Route::get('reviews/{review}', [ReviewController::class, 'show']);

    // Protected routes (authenticated users)
    Route::middleware('auth:sanctum')->group(function () {
        
        // User routes (authenticated users only)
        Route::prefix('user')->group(function () {
            Route::get('orders', [OrderController::class, 'index']);
            Route::post('orders', [OrderController::class, 'store']);
            Route::get('orders/{order}', [OrderController::class, 'show']);
            
            // User review routes
            Route::post('products/{product}/reviews', [ReviewController::class, 'store']);
            Route::put('reviews/{review}', [ReviewController::class, 'update']);
            Route::delete('reviews/{review}', [ReviewController::class, 'destroy']);
        });

        // Admin routes (admin only)
        Route::middleware('admin')->prefix('admin')->group(function () {
            
            // Product management
            Route::post('products', [ProductController::class, 'store']);
            Route::put('products/{product}', [ProductController::class, 'update']);
            Route::delete('products/{product}', [ProductController::class, 'destroy']);
            
            // Order management
            Route::get('orders', [OrderController::class, 'index']);
            Route::put('orders/{order}', [OrderController::class, 'update']);
            Route::delete('orders/{order}', [OrderController::class, 'destroy']);
            
            // Review management
            Route::delete('reviews/{review}', [ReviewController::class, 'destroy']);
        });
    });
});
