<?php

namespace App\Services;

use App\Models\Product;
use App\Models\User;

class CartService
{
    /**
     * Put one unit of the product in the cart. Every successful add — from the
     * catalog counter, the product page or the dashboard cart — advances the
     * "add_to_cart" daily quest right here, so no entry point can forget the
     * hook. Returns the quests completed by this add (empty when nothing was
     * actually added, e.g. the stock limit was reached).
     *
     * @return list<array{title: string, coins: int, xp: int}>
     */
    public function addToCart(User $user, Product $product): array
    {
        if ($product->quantity <= 0) {
            return [];
        }

        $cartItem = $user->cartItems()->where('product_id', $product->id)->first();

        if ($cartItem) {
            if ($cartItem->quantity >= $product->quantity) {
                return [];
            }
            $cartItem->increment('quantity');
        } else {
            $user->cartItems()->create([
                'product_id' => $product->id,
                'quantity' => 1
            ]);
        }

        return app(DailyQuestService::class)->progress($user, 'add_to_cart');
    }

    public function removeFromCart(User $user, Product $product): void
    {
        $cartItem = $user->cartItems()->where('product_id', $product->id)->first();

        if ($cartItem) {
            if ($cartItem->quantity > 1) {
                $cartItem->decrement('quantity');
            } else {
                $cartItem->delete();
            }
        }
    }

    public function getUserCart(User $user)
    {
        return $user->cartItems()->with('product.category')->get();
    }
}
