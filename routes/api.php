<?php

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

Route::get('/categories', [\App\Http\Controllers\Api\CategoryController::class, 'index']);
Route::get('/menus', [\App\Http\Controllers\Api\MenuController::class, 'index']);
Route::get('/menus/{menu}', [\App\Http\Controllers\Api\MenuController::class, 'show']);

// table verify
Route::post('/tables/verify', [\App\Http\Controllers\Api\TableController::class, 'verify']);

// waiter call
Route::post('/tables/call-waiter', [\App\Http\Controllers\Api\TableController::class, 'callWaiter'])->middleware('throttle:3,1');

// orders
Route::post('/orders', [App\Http\Controllers\Api\OrderController::class, 'store']);
Route::post('/orders/history', [App\Http\Controllers\Api\OrderController::class, 'getOrderHistory']);

// payment
Route::post('/payment', [\App\Http\Controllers\Api\PaymentController::class, 'store']);


