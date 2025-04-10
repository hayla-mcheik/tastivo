<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CartController;

Route::middleware(['auth:sanctum'])->group(function () {
    Route::prefix('cart')->group(function () {
        Route::get('/', [CartController::class, 'index']);
        Route::post('/', [CartController::class, 'addToCart']);
        Route::delete('/{cartItem}', [CartController::class, 'removeFromCart']);
        Route::put('/{cartItem}', [CartController::class, 'updateQuantity']);
        Route::delete('/clear', [CartController::class, 'clearCart']);
    });
});
Route::get('/additions', [CartController::class, 'additions']);

// Guest cart routes (using session)
// Route::prefix('cart')->group(function () {
//     Route::get('/guest', [CartController::class, 'guestIndex']);
//     Route::post('/guest', [CartController::class, 'guestAddToCart']);
//     Route::delete('/guest/{cartItem}', [CartController::class, 'guestRemoveFromCart']);
//     Route::put('/guest/{cartItem}', [CartController::class, 'guestUpdateQuantity']);
//     Route::delete('/guest/clear', [CartController::class, 'guestClearCart']);
// });