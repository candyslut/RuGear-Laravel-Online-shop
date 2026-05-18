<?php

use App\Http\Controllers\{
    ProductsController, 
    ProfileController, 
    AdminController, 
    CartController, 
    DashboardController, 
    CommentaryController, 
    TicketController
};
use Illuminate\Support\Facades\Route;

Route::get('/', [ProductsController::class, 'index'])->name('products.index');
Route::get('/product/{product}', [ProductsController::class, 'show'])->name('products.show');
Route::get('/support', function() {
    return view('support');
})->name('support');


Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::post('/cart/add/{product}', [CartController::class, 'add'])->name('cart.add');
    Route::post('/cart/remove/{product}', [CartController::class, 'remove'])->name('cart.remove');
    Route::post('/product/{product}/commentary', [CommentaryController::class, 'store'])->name('product.commentary');

    Route::post('ticket/create', [TicketController::class, 'store'])->name('ticket.store');
});


Route::middleware('admin')->prefix('admin')->group(function () {
    
    Route::get('tickets', [TicketController::class, 'index'])->name('admin.tickets.index');
    Route::delete('tickets/{ticket}', [TicketController::class, 'destroy'])->name('admin.ticket.destroy');
    
    Route::get('users', [AdminController::class, 'index'])->name('admin.users.index');
    Route::delete('users/destroy/{user}', [AdminController::class, 'destroy'])->name('admin.users.destroy');
    
});

require __DIR__.'/auth.php';