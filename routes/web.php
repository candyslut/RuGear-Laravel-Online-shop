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
    FeedController,
    OrderController,
    SettingsController,
    ShopController
};
use App\Http\Controllers\Admin\AdminOrderController;
use App\Http\Controllers\Admin\AdminStatsController;
use Illuminate\Support\Facades\Route;

Route::get('/', [ProductsController::class, 'index'])->name('products.index');
Route::get('/product/{product}', [ProductsController::class, 'show'])->name('products.show');
Route::get('/compare', [ComparisonController::class, 'show'])->name('comparison.show');
Route::post('/compare/clear', [ComparisonController::class, 'clear'])->name('comparison.clear');
Route::get('/support', function () {
    return view('support');
})->name('support');

// Public "Live Feed" stream — polled by the ambient ticker + history panel.
Route::get('/feed', [FeedController::class, 'index'])->name('feed.index');


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
    Route::get('/orders/{order}/payment', [OrderController::class, 'payment'])->name('orders.payment');
    Route::get('/orders/{order}/payment/confirm', [OrderController::class, 'paymentConfirm'])->name('orders.payment.confirm');
    Route::patch('/orders/{order}/cancel', [OrderController::class, 'cancel'])->name('orders.cancel');

    Route::post('ticket/create', [TicketController::class, 'store'])->name('ticket.store');
    Route::delete('tickets/{ticket}', [TicketController::class, 'destroy'])->name('ticket.destroy');

    Route::get('/address-suggest', function (\Illuminate\Http\Request $request) {
        $q     = trim($request->input('q', ''));
        $token = config('services.dadata.token');

        if (!$token || mb_strlen($q) < 2) {
            return response()->json([]);
        }

        try {
            $resp = \Illuminate\Support\Facades\Http::withoutVerifying()->withHeaders([
                'Authorization' => 'Token ' . $token,
                'Content-Type'  => 'application/json',
                'Accept'        => 'application/json',
            ])->post('https://suggestions.dadata.ru/suggestions/api/4_1/rs/suggest/address', [
                'query'          => $q,
                'count'          => 7,
                'restrict_value' => false,
            ]);
        } catch (\Exception $e) {
            return response()->json([]);
        }

        if (!$resp->ok()) {
            return response()->json([]);
        }

        return response()->json(
            collect($resp->json('suggestions', []))->map(fn($s) => [
                'value'  => $s['value'],
                'region' => $s['data']['region_with_type'] ?? null,
                'city'   => $s['data']['city'] ?? $s['data']['settlement_with_type'] ?? null,
                'street' => $s['data']['street_with_type'] ?? null,
                'house'  => $s['data']['house'] ?? null,
            ])
        );
    })->name('address.suggest');

    Route::post('/game/reward', function () {
        $user = auth()->user();
        $user->addCoins(1);
        $user->addExperience(5);

        // Lifetime cleared-level counter — gates legendary cosmetics
        // (see User::legendaryUnlocked()).
        $user->increment('buzzword_levels');

        // Clearing a level advances the "play the mini-game" quest. Completed
        // quests (and any rank-up the rewards triggered) ride back in the JSON
        // so the in-page handler can toast them without a reload.
        $quests = app(\App\Services\DailyQuestService::class)->progress($user, 'game_play');

        return response()->json([
            'coins'   => 1,
            'xp'      => 5,
            'quests'  => $quests,
            'rank_up' => $user->lastRankUp,
        ]);
    })->name('game.reward');

    Route::post('/game/runner-reward', function (\Illuminate\Http\Request $request) {
        $user = auth()->user();
        $user->addCoins(1);
        $user->addExperience(5);

        // Milestone claims carry the current run distance; keep the best one
        // for the legendary-cosmetics gate.
        $m = min(100000, max(0, (int) $request->input('m')));
        if ($m > (int) $user->redline_best_distance) {
            $user->redline_best_distance = $m;
            $user->save();
        }

        // Each distance milestone in the runner (500, 1500, 3000 m…) counts
        // as a cleared mini-game level for the game_play daily quests.
        $quests = app(\App\Services\DailyQuestService::class)->progress($user, 'game_play');

        return response()->json([
            'coins'   => 1,
            'xp'      => 5,
            'quests'  => $quests,
            'rank_up' => $user->lastRankUp,
        ]);
    })->name('game.runner.reward');

    // Stats-only ping (no rewards): the runner reports the final distance on
    // death/close so a 10 000 m run counts even between milestones.
    Route::post('/game/runner-distance', function (\Illuminate\Http\Request $request) {
        $user = auth()->user();
        $m = min(100000, max(0, (int) $request->input('m')));
        if ($m > (int) $user->redline_best_distance) {
            $user->redline_best_distance = $m;
            $user->save();
        }

        return response()->json(['best' => (int) $user->redline_best_distance]);
    })->name('game.runner.distance');

    // End-of-run recorder: one row per finished run (death/abandon in Redline,
    // game over in Buzzword). This is the source of truth for the whole
    // game-stats surface — rewards stay on the endpoints above, here we only
    // snapshot the result. Returns whether the run set a new personal best.
    Route::post('/game/play', function (\Illuminate\Http\Request $request) {
        $data = $request->validate([
            'game'        => ['required', 'string', \Illuminate\Validation\Rule::in(\App\Support\GameStats::keys())],
            'score'       => ['required', 'integer', 'min:0', 'max:1000000'],
            'level'       => ['nullable', 'integer', 'min:0', 'max:65535'],
            'duration_ms' => ['nullable', 'integer', 'min:0', 'max:86400000'],
            'coins'       => ['nullable', 'integer', 'min:0', 'max:65535'],
            'xp'          => ['nullable', 'integer', 'min:0', 'max:65535'],
            'meta'        => ['nullable', 'array'],
        ]);

        // Keep only the whitelisted per-run counters from meta, each a small
        // non-negative int — never trust arbitrary client JSON wholesale.
        $meta = [];
        foreach (\App\Support\GameStats::metaKeys($data['game']) as $key) {
            if (isset($data['meta'][$key]) && is_numeric($data['meta'][$key])) {
                $meta[$key] = min(1000000, max(0, (int) $data['meta'][$key]));
            }
        }

        $user = auth()->user();

        // Previous best for this game before we insert (drives the "new record!"
        // flag and is unaffected by the row we're about to write).
        $prevBest = (int) \App\Models\GamePlay::where('user_id', $user->id)
            ->where('game', $data['game'])
            ->max('score');

        $play = \App\Models\GamePlay::create([
            'user_id'      => $user->id,
            'game'         => $data['game'],
            'score'        => $data['score'],
            'level'        => $data['level'] ?? null,
            'duration_ms'  => $data['duration_ms'] ?? 0,
            'coins_earned' => $data['coins'] ?? 0,
            'xp_earned'    => $data['xp'] ?? 0,
            'meta'         => $meta ?: null,
        ]);

        // Keep the lifetime best distance fresh for the legendary gate even if
        // the live distance ping was deduped (it's a max, so this is idempotent).
        if ($data['game'] === 'redline' && $data['score'] > (int) $user->redline_best_distance) {
            $user->redline_best_distance = $data['score'];
            $user->save();
        }

        return response()->json([
            'ok'      => true,
            'record'  => $data['score'] > 0 && $data['score'] > $prevBest,
            'play_id' => $play->id,
        ]);
    })->name('game.play');
});


Route::middleware('admin')->prefix('admin')->group(function () {

    Route::get('statistics', [AdminStatsController::class, 'index'])->name('admin.statistics');
    Route::get('statistics/export', [AdminStatsController::class, 'export'])->name('admin.statistics.export');

    Route::get('orders', [AdminOrderController::class, 'index'])->name('admin.orders.index');
    Route::get('orders/{order}', [AdminOrderController::class, 'show'])->name('admin.orders.show');
    Route::patch('orders/{order}/status', [AdminOrderController::class, 'updateStatus'])->name('admin.orders.updateStatus');

    Route::get('tickets', [TicketController::class, 'index'])->name('admin.tickets.index');
    Route::post('tickets/{ticket}/reply', [TicketController::class, 'reply'])->name('admin.tickets.reply');

    Route::get('users', [AdminController::class, 'index'])->name('admin.users');
    Route::delete('users/{user}/delete', [AdminController::class, 'deleteUser'])->name('admin.users.destroy');
    Route::post('users/{user}/coins', [AdminController::class, 'addCoins'])->name('admin.users.coins');

    Route::get('/products', [AdminController::class, 'productsIndex'])->name('admin.products.index');
    Route::get('/products/create', [AdminController::class, 'create'])->name('admin.products.create');
    Route::post('/products', [AdminController::class, 'store'])->name('admin.products.store');
    Route::get('/products/{product}/edit', [AdminController::class, 'edit'])->name('admin.products.edit');
    Route::put('/products/{product}', [AdminController::class, 'update'])->name('admin.products.update');
    Route::delete('/products/destroy/{product}', [AdminController::class, 'destroy'])->name('admin.products.destroy');
});

require __DIR__ . '/auth.php';
