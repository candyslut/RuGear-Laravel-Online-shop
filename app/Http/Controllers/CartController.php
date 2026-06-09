<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Services\CartService;
use App\Services\DailyQuestService;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    public function __construct(
        protected CartService $cartService,
        protected DailyQuestService $questService,
    ) {}

    public function add(Product $product)
    {
        $user = Auth::user();
        $this->cartService->addToCart($user, $product);

        // Adding a product advances the "add to cart" quest.
        if ($done = $this->questService->progress($user, 'add_to_cart')) {
            session()->flash('quests_completed', $done);
            if ($user->lastRankUp) {
                session()->flash('rank_up', $user->lastRankUp);
            }
        }

        return redirect()->back();
    }

    public function remove(Product $product)
    {
        $this->cartService->removeFromCart(Auth::user(), $product);
        return redirect()->back();
    }
}