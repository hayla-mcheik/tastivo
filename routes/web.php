<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FrontendController;
use App\Http\Controllers\ListingController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Middleware\Admin;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->middleware('verified')->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'updateInfo'])->name('profile.info');
    Route::put('/profile', [ProfileController::class, 'updatePassword'])->name('profile.password');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/restaurants', [RestaurantController::class, 'index'])->name('restaurant.index');
    Route::get('/restaurants/create', [RestaurantController::class, 'create'])->name('restaurant.create');
    Route::post('/restaurants/store', [RestaurantController::class, 'store'])->name('restaurant.store');
    Route::get('/restaurants/{id}', [RestaurantController::class, 'show'])->name('restaurant.show');
    Route::get('/restaurants/edit/{id}', [RestaurantController::class, 'edit'])->name('restaurant.edit');
    Route::put('/restaurants/update/{id}', [RestaurantController::class, 'update'])->name('restaurant.update');
    Route::get('/restaurants/destroy/{id}', [RestaurantController::class, 'destroy'])->name('restaurant.destroy');
});



Route::controller(FrontendController::class)->group(function () {
    Route::get('/', 'home')->name('home');
    Route::get('/categories', 'menu')->name('menu');
    Route::get('/categories/{slug}', 'menudetails')->name('menudetails');
});

Route::get('/search', [SearchController::class, 'index'])->name('search');


// Remove the auth middleware from the cart route
Route::get('/cart', [CartController::class, 'index']);
Route::get('/checkout', [CheckoutController::class, 'checkout']);
Route::get('/listingpage',[ListingController::class,'index'])->name('HomeListing');
Route::resource('listing', ListingController::class)->except('index');


Route::get('contact', [FrontendController::class,'contact'])->name('contact');

// routes/web.php
Route::prefix('cart')->group(function () {
    Route::get('/guest', [CartController::class, 'guestIndex']);
    Route::post('/guest', [CartController::class, 'guestAddToCart']);
    Route::delete('/guest/{cartItem}', [CartController::class, 'guestRemoveFromCart']);
    Route::put('/guest/{cartItem}', [CartController::class, 'guestUpdateQuantity']);
    Route::delete('/guest/clear', [CartController::class, 'guestClearCart']);
});

//Admin Routes

Route::middleware(['auth','verified', Admin::class])->prefix('admin')
->controller(AdminController::class)
->group(function () {
    Route::get('/dashboard','index')->name('admin.index');
    Route::resource('products',ProductController::class);
    Route::resource('categories',CategoryController::class);
    Route::resource('contact', ContactController::class)->names([
        'index' => 'admin.contact.index',
        'create' => 'admin.contact.create',
        'store' => 'admin.contact.store',
        'edit' => 'admin.contact.edit',
        'update' => 'admin.contact.update',
        'destroy' => 'admin.contact.destroy',
    ]);
});
require __DIR__ . '/auth.php';