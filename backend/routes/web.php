<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
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


Route::get('/order/confirm', [OrderController::class, 'confirm'])->name('order.confirm');
Route::post('/order/create', [OrderController::class, 'create'])->name('order.create');

Route::get('/autocomplete', [ProductController::class, 'autocomplete'])->name('autocomplete');

Route::middleware('auth')->group(function () {
    Route::get('/cart', [CartController::class, 'index'])->name('cart');
    Route::post('/cart/add/{product}', [CartController::class, 'add'])->name('cart.add');
    Route::patch('/cart/update/{cart}', [CartController::class, 'update'])->name('cart.update');
    Route::delete('/cart/remove/{cart}', [CartController::class, 'remove'])->name('cart.remove');
    Route::post('/order/create', [OrderController::class, 'create'])->name('order.create');


    Route::middleware('admin')->group(function () {
        Route::get('/admin', function () {
            return view('admin.admin_menu');
        })->name('admin');

        Route::get('/admin/admin_menu', function () {
            return view('admin.admin_menu');
        })->name('admin.menu');

        Route::resource('categories', CategoryController::class);
        Route::resource('products', ProductController::class)->except(['index']);
        Route::get('/products', [ProductController::class, 'adminIndex'])->name('products.index');
    });
});
