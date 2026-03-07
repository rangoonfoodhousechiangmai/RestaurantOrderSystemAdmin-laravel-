<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ModifierController;
use App\Http\Controllers\MenuVariantController;
use App\Http\Controllers\MenuModifierController;
use App\Http\Controllers\TableController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    if (auth()->check()) {
        return redirect('/dashboard');
    }
    return view('auth.login');
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.index');
    Route::get('/dashboard/order-items/{period}', [DashboardController::class, 'getData'])->name('dashboard.data');

    // categories
    Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
    Route::post('/categories', [CategoryController::class, 'store'])->name('categories.store');
    Route::get('/categories/{category}', [CategoryController::class, 'show'])->name('categories.show');
    Route::post('/categories/{category}', [CategoryController::class, 'update'])->name('categories.update');
    Route::delete('/categories/{category}', [CategoryController::class, 'destroy'])->name('categories.destroy');


    // menus
    Route::get('/menus', [MenuController::class, 'index'])->name('menus.index');
    Route::post('/menus', [MenuController::class, 'store'])->name('menus.store');
    Route::get('/menus/{menu}', [MenuController::class, 'show'])->name('menus.show');
    Route::post('/menus/{menu}', [MenuController::class, 'update'])->name('menus.update');
    Route::delete('/menus/{menu}', [MenuController::class, 'destroy'])->name('menus.destroy');

    // menu modifiers
    Route::get('/menu-modifiers', [MenuModifierController::class, 'index'])->name('menu-modifiers.index');
    Route::get('/menu/{menu}/menu-modifiers', [MenuModifierController::class, 'create'])->name('menus.modifiers.create');
    Route::post('/menu/{menu}/menu-modifiers', [MenuModifierController::class, 'store'])->name('menus.modifiers.store');

    // Route::post('/menus/{menu}/modifiers', [MenuModifierController::class, 'store'])
    //     ->name('menus.modifiers.store');

    // modifiers
    Route::get('/modifiers', [ModifierController::class, 'index'])->name('modifiers.index');
    Route::post('/modifiers', [ModifierController::class, 'store'])->name('modifiers.store');
    Route::get('/modifiers/{modifier}', [ModifierController::class, 'show'])->name('modifiers.show');
    Route::post('/modifiers/{modifier}', [ModifierController::class, 'update'])->name('modifiers.update');
    Route::delete('/modifiers/{modifier}', [ModifierController::class, 'destroy'])->name('modifiers.destroy');

    // tables
    Route::get('/tables', [TableController::class, 'index'])->name('tables.index');
    Route::post('/tables', [TableController::class, 'store'])->name('tables.store');
    Route::get('/tables/{table}', [TableController::class, 'show'])->name('tables.show');
    Route::post('/tables/{table}', [TableController::class, 'update'])->name('tables.update');
    Route::delete('/tables/{table}', [TableController::class, 'destroy'])->name('tables.destroy');
    // Route::post('/tables/{table}/generate-qr', [TableController::class, 'generateQr'])->name('tables.generate-qr');


    // generate QR for table
    Route::post('/tables/{table}/regenerate-qr', [TableController::class, 'reGenerateQr'])->name('tables.regenerate-qr');

    // orders
    Route::get('/orders', [\App\Http\Controllers\OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/fetch', [\App\Http\Controllers\OrderController::class, 'fetchOrders'])->name('orders.fetch');
    Route::get('/orders/{order}', [\App\Http\Controllers\OrderController::class, 'show'])->name('orders.show');
    Route::get('/orders/{order}/payment-image', [\App\Http\Controllers\OrderController::class, 'showPaymentImage'])->name('orders.payment-image');
    Route::post('/orders/{order}/update-status', [\App\Http\Controllers\OrderController::class, 'updateStatus'])->name('orders.update-status');
    Route::post('/orders/{order}/update-payment-verification', [\App\Http\Controllers\OrderController::class, 'updatePaymentVerification'])->name('orders.update-payment-verification');
    Route::post('/orders/{order}/update-payment-type', [\App\Http\Controllers\OrderController::class, 'updatePaymentType'])->name('orders.update-payment-type');
    Route::post('/orders/{order}/items/{itemId}', [\App\Http\Controllers\OrderController::class, 'updateOrderItem'])->name('orders.update');
    Route::delete('/orders/{order}/items/{itemId}', [\App\Http\Controllers\OrderController::class, 'deleteOrderItem'])->name('orders.deleteItem');

    // order history
    Route::get('/order-history', [\App\Http\Controllers\OrderController::class, 'history'])->name('orders.history');
    Route::get('/order-history/{order}', [\App\Http\Controllers\OrderController::class, 'showHistoryOrder'])->name('orders.history.show');

    Route::get('call-waiter', [\App\Http\Controllers\TableController::class, 'waiterCallList'])->name('waiter-call-list');
    Route::post('/waiter-calls/{waiterCall}/update-status', [\App\Http\Controllers\TableController::class, 'updateWaiterCallStatus'])->name('waiter-calls.update-status');
    Route::get('/waiter-calls/count', [\App\Http\Controllers\TableController::class, 'getWaiterCallCount']);



});
