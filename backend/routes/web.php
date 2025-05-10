<?php
 
 use App\Http\Controllers\AuthController;
 use App\Http\Controllers\CategoryController;
 use App\Http\Controllers\ProductController;
 use App\Http\Controllers\CartController;
 use App\Http\Controllers\OrderController;
 use App\Http\Controllers\ProductImageController;
 use App\Http\Middleware\AdminMiddleware;
 use App\Http\Controllers\AdminController;

 
 
 Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register.form');
 Route::post('/register', [AuthController::class, 'register'])->name('register');
 
 Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login.form');
 Route::post('/login', [AuthController::class, 'login'])->name('login');
 Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
 
 Route::get('/', [ProductController::class, 'index'])->name('main_page');
 //temperary
Route::get('/product/upload-image', [ProductImageController::class, 'showUploadForm'])->name('product.upload_image');
Route::post('/product/upload-image', [ProductImageController::class, 'uploadImage'])->name('product.upload_image.store');
 
Route::get('/product/{product}', [ProductController::class, 'show'])->name('product.show');
 
Route::get('/order/confirm', [OrderController::class, 'confirm'])->name('order.confirm');
Route::post('/order/create', [OrderController::class, 'create'])->name('order.create');
 
Route::get('/autocomplete', [ProductController::class, 'autocomplete'])->name('autocomplete');
Route::get('/cart', [CartController::class, 'index'])->name('cart');
Route::post('/cart/add/{product}', [CartController::class, 'add'])->name('cart.add');
Route::patch('/cart/update/{cart}', [CartController::class, 'update'])->name('cart.update');
Route::delete('/cart/remove/{cart}', [CartController::class, 'remove'])->name('cart.remove');
Route::post('/order/create', [OrderController::class, 'create'])->name('order.create');



Route::middleware('adminAccess')->group(function () {

        Route::get('/admin', [AdminController::class, 'index'])->name('admin');

        Route::get('/admin/admin_menu', [AdminController::class, 'index'])->name('admin.menu');

        Route::resource('categories', CategoryController::class);
        Route::resource('products', ProductController::class)->except(['index']);
        Route::get('/products', [ProductController::class, 'adminIndex'])->name('products.index');
    });


;

