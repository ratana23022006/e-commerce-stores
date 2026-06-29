<?php

use App\Http\Controllers\CartController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\OrderItemController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Health Check
Route::get('/', function () {
    return response()->json(['msg' => 'API is running.']);
});

// ─────────────────────────────────────────────
// Public Routes (No Auth Required)
// ─────────────────────────────────────────────
Route::post('/register', [UserController::class, 'register']);
Route::post('/login', [UserController::class, 'login']);

// Public read-only
Route::apiResource('categories', CategoryController::class)->only(['index', 'show']);
Route::apiResource('products', ProductController::class)->only(['index', 'show']);
Route::apiResource('reviews', ReviewController::class)->only(['index', 'show']);

// ─────────────────────────────────────────────
// Authenticated Routes (Any logged-in user)
// ─────────────────────────────────────────────
Route::middleware('auth:sanctum')->group(function () {

    // Cart
    Route::apiResource('carts', CartController::class);

    // Orders
    Route::apiResource('orders', OrderController::class);

    // Order Items
    Route::apiResource('order-items', OrderItemController::class);

    // Payments
    Route::apiResource('payments', PaymentController::class);

    // Reviews (create, update, delete — own reviews)
    Route::apiResource('reviews', ReviewController::class)->except(['index', 'show']);

    // Current logged-in user profile
    Route::get('/profile', [UserController::class, 'getUser']);
});

// ─────────────────────────────────────────────
// Admin Routes (auth + admin middleware)
// ─────────────────────────────────────────────
Route::middleware(['auth:sanctum', 'admin'])->group(function () {

    // Manage Categories (create, update, delete)
    Route::apiResource('categories', CategoryController::class)->except(['index', 'show']);

    // Manage Products (create, update, delete)
    Route::apiResource('products', ProductController::class)->except(['index', 'show']);

    // User Management
    Route::get('/users', [UserController::class, 'getUser']);
    Route::get('/users/{id}', [UserController::class, 'getUserById']);
});