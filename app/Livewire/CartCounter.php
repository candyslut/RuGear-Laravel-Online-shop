<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Product;
use App\Services\CartService;
use Illuminate\Support\Facades\Auth;

class CartCounter extends Component
{
    public Product $product;
    public $quantity = 0;

    public function mount(Product $product)
    {
        $this->product = $product;
        $this->updateQuantity();
    }

    protected function updateQuantity()
    {
        if (Auth::check()) {
            $item = Auth::user()->cartItems()->where('product_id', $this->product->id)->first();
            $this->quantity = $item ? $item->quantity : 0;
        } else {
            $this->quantity = 0;
        }
    }

    public function add(CartService $cartService)
    {
        if (!Auth::check()) {
            return $this->redirect(route('login'));
        }

        if ($this->quantity >= $this->product->quantity) {
            return;
        }

        // CartService advances the "add to cart" quest; surface completions
        // as the live bottom-right toast and refresh the HUD / quest panels.
        if ($done = $cartService->addToCart(Auth::user(), $this->product)) {
            $this->dispatch('quests-completed', quests: $done);
        }
        $this->dispatch('profile-refresh');
        $this->updateQuantity();
    }

    public function remove(CartService $cartService)
    {
        if (!Auth::check()) {
            return $this->redirect(route('login'));
        }

        $cartService->removeFromCart(Auth::user(), $this->product);

        $this->updateQuantity();
    }

    public function render()
    {
        return view('livewire.cart-counter');
    }
}
