<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FrontendController;
use App\Http\Controllers\ListingController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Middleware\Admin;
use Illuminate\Support\Facades\Route;


Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->middleware('verified')->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'updateInfo'])->name('profile.info');
    Route::put('/profile', [ProfileController::class, 'updatePassword'])->name('profile.password');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});



Route::controller(FrontendController::class)->group(function () {
    Route::get('/', 'home')->name('home');
    Route::get('/categories', 'menu')->name('menu');
    Route::get('/categories/{slug}', 'menudetails')->name('menudetails');

});


Route::controller(CartController::class)->group(function () {
    Route::get('/cart', 'index')->name('cart');
    Route::post('/cart/add', 'addToCart')->name('cart.add');
    Route::delete('/cart/{cartItem}', 'removeFromCart')->name('cart.remove');
    Route::patch('/cart/{cartItem}', 'updateQuantity')->name('cart.update');
});
Route::get('/listingpage',[ListingController::class,'index'])->name('HomeListing');
Route::resource('listing', ListingController::class)->except('index');

//Admin Routes

Route::middleware(['auth','verified', Admin::class])->prefix('admin')
->controller(AdminController::class)
->group(function () {
    Route::get('/dashboard','index')->name('admin.index');
    Route::resource('products',ProductController::class);
});
require __DIR__ . '/auth.php';