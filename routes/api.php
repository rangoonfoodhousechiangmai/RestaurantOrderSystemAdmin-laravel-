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

Route::post('/tables/verify', [\App\Http\Controllers\Api\TableController::class, 'verify']);
