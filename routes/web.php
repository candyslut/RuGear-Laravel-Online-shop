<?php

use App\Http\Controllers\{ProductsController, ProfileController, CartController, DashboardController};
use Illuminate\Support\Facades\Route;

Route::get('/', [ProductsController::class, 'index'])->name('products.index');
Route::get('/product/{product}', [ProductsController::class, 'show'])->name('products.show');

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');
Route::middleware('auth')->group(function () {
    Route::post('/cart/add/{product}', [CartController::class, 'add'])->name('cart.add');
    Route::post('/cart/remove/{product}', [CartController::class, 'remove'])->name('cart.remove');
});

Route::get('admin/panel', fn() => 'Привет босс')->middleware(['auth', 'admin']);


require __DIR__.'/auth.php';