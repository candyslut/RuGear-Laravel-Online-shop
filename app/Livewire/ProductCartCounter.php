<?php

namespace App\Livewire;

use App\Models\Product;
use Livewire\Attributes\Computed;
use Livewire\Component;

class ProductCartCounter extends Component
{
    public Product $product;
    public $quantity = 0;

    #[Computed]
    public function itemInCart()
    {
        return auth()->user()?->cartItems()->where('product_id', $this->product->id)->first();
    }

    public function mount()
    {
        $item = $this->itemInCart();
        if ($item) {
            $this->quantity = $item->quantity;
        }
    }

    public function addToCart(\App\Services\CartService $cartService)
    {
        // Goes through CartService so the "add to cart" quest is advanced
        // exactly like every other entry point (stock guards live there too).
        if ($done = $cartService->addToCart(auth()->user(), $this->product)) {
            $this->dispatch('quests-completed', quests: $done);
        }
        $this->dispatch('profile-refresh');

        unset($this->itemInCart); // bust the #[Computed] cache after the write
        $this->quantity = $this->itemInCart()?->quantity ?? 0;

        $this->dispatch('cartUpdated');
    }

    public function removeFromCart()
    {
        $item = auth()->user()->cartItems()->where('product_id', $this->product->id)->first();

        if ($item) {
            if ($item->quantity > 1) {
                $item->decrement('quantity');
                $this->quantity = $item->quantity;
            } else {
                $item->delete();
                $this->quantity = 0;
            }
        }

        $this->dispatch('cartUpdated');
    }

    public function render()
    {
        return view('livewire.product-cart-counter', [
            'itemInCart' => $this->itemInCart(),
        ]);
    }
}
