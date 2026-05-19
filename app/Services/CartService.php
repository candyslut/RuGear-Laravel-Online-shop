<?php

namespace App\Services;

use App\Models\Product;
use App\Models\User;

class CartService
{
    public function addToCart(User $user, Product $product): void
    {
        if ($product->quantity <= 0) {
            return;
        }

        $cartItem = $user->cartItems()->where('product_id', $product->id)->first();

        if ($cartItem) {
            if ($cartItem->quantity < $product->quantity) {
                $cartItem->increment('quantity');
            }
        } else {
            $user->cartItems()->create([
                'product_id' => $product->id,
                'quantity' => 1
            ]);
        }
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
