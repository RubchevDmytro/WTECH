<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\OrderController;


Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register.form');
Route::post('/register', [AuthController::class, 'register'])->name('register');



Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login.form');
Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');



Route::get('/', [ProductController::class, 'index'])->name('main_page');
Route::get('/product/{product}', [ProductController::class, 'show'])->name('product.show');



Route::middleware('auth')->group(function () {
    Route::get('/cart', [CartController::class, 'index'])->name('cart');
    Route::post('/cart/add/{product}', [CartController::class, 'add'])->name('cart.add');
    Route::post('/order/create', [OrderController::class, 'create'])->name('order.create');
    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
});

Route::get('/admin', function () {
    return view('admin.admin_menu');
})->name('admin');

