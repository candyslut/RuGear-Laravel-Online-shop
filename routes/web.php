<?php

use App\Http\Controllers\{
    ProductsController,
    ProfileController,
    AdminController,
    CartController,
    DashboardController,
    CommentaryController,
    TicketController,
    ComparisonController,
    OrderController,
    SettingsController,
    ShopController
};
use App\Http\Controllers\Admin\AdminOrderController;
use Illuminate\Support\Facades\Route;

Route::get('/', [ProductsController::class, 'index'])->name('products.index');
Route::get('/product/{product}', [ProductsController::class, 'show'])->name('products.show');
Route::get('/compare', [ComparisonController::class, 'show'])->name('comparison.show');
Route::post('/compare/clear', [ComparisonController::class, 'clear'])->name('comparison.clear');
Route::get('/support', function () {
    return view('support');
})->name('support');


Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/settings', [SettingsController::class, 'edit'])->name('settings.edit');
    Route::patch('/settings', [SettingsController::class, 'update'])->name('settings.update');

    Route::get('/market', [ShopController::class, 'index'])->name('market.index');
    Route::post('/market/buy/{item}', [ShopController::class, 'buy'])->name('market.buy');
    Route::post('/market/equip/{item}', [ShopController::class, 'equip'])->name('market.equip');
    Route::post('/market/unequip', [ShopController::class, 'unequip'])->name('market.unequip');

    Route::post('/cart/add/{product}', [CartController::class, 'add'])->name('cart.add');
    Route::post('/cart/remove/{product}', [CartController::class, 'remove'])->name('cart.remove');
    Route::post('/product/{product}/commentary', [CommentaryController::class, 'store'])->name('product.commentary');
    
    Route::post('/orders', [OrderController::class, 'store'])->name('orders.store');
    Route::patch('/orders/{order}/cancel', [OrderController::class, 'cancel'])->name('orders.cancel');

    Route::post('ticket/create', [TicketController::class, 'store'])->name('ticket.store');
    Route::delete('tickets/{ticket}', [TicketController::class, 'destroy'])->name('ticket.destroy');

    Route::post('/game/reward', function () {
        $user = auth()->user();
        $user->addCoins(1);
        $user->addExperience(5);
        return response()->json([
            'coins' => 1,
            'xp'    => 5,
        ]);
    })->name('game.reward');
});


Route::middleware('admin')->prefix('admin')->group(function () {

    Route::get('orders', [AdminOrderController::class, 'index'])->name('admin.orders.index');
    Route::get('orders/{order}', [AdminOrderController::class, 'show'])->name('admin.orders.show');
    Route::patch('orders/{order}/status', [AdminOrderController::class, 'updateStatus'])->name('admin.orders.updateStatus');

    Route::get('tickets', [TicketController::class, 'index'])->name('admin.tickets.index');
    Route::post('tickets/{ticket}/reply', [TicketController::class, 'reply'])->name('admin.tickets.reply');

    Route::get('users', [AdminController::class, 'index'])->name('admin.users');
    Route::delete('users/{user}/delete', [AdminController::class, 'deleteUser'])->name('admin.users.destroy');

    Route::get('/products', [AdminController::class, 'productsIndex'])->name('admin.products.index');
    Route::get('/products/create', [AdminController::class, 'create'])->name('admin.products.create');
    Route::post('/products', [AdminController::class, 'store'])->name('admin.products.store');
    Route::get('/products/{product}/edit', [AdminController::class, 'edit'])->name('admin.products.edit');
    Route::put('/products/{product}', [AdminController::class, 'update'])->name('admin.products.update');
    Route::delete('/products/destroy/{product}', [AdminController::class, 'destroy'])->name('admin.products.destroy');
});

require __DIR__ . '/auth.php';
