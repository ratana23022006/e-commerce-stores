<?php

use App\Http\Controllers\CartController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\OrderItemController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\UserController;
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

Route::get('/',function(){
    return response()->json([
        'msg'=>"I miss youu..."
    ]);
});
Route::controller(UserController::class)->group(function(){
    Route::post('/register','register');
    Route::post('/login','login');
    
});

Route::post('/addProduct', [ProductController::class, 'addProduct']);

Route::apiResource('categories', CategoryController::class);
Route::apiResource('products', ProductController::class);
Route::apiResource('orders', OrderController::class);
Route::apiResource('order-items', OrderItemController::class);
Route::apiResource('payments', PaymentController::class);
Route::apiResource('reviews', ReviewController::class);
Route::apiResource('carts', CartController::class);

Route::middleware(['auth:sanctum','admin'])->group(function(){
    Route::controller(UserController::class)->group(function(){
        Route::get('/user','getUser');
        Route::get('/user/{id}','getUserById');
    });

    // Route::controller(ProductController::class)->group(function(){
    //     Route::post('/addProduct','addProduct');
    // });
});
