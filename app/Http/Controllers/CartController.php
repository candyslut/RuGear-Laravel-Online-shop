<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Services\CartService;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    public function __construct(
        protected CartService $cartService,
    ) {}

    public function add(Product $product)
    {
        $user = Auth::user();

        // CartService advances the "add to cart" quest itself; here we only
        // surface what it completed (full-page flow → session toast queue).
        if ($done = $this->cartService->addToCart($user, $product)) {
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