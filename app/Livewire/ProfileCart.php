<?php

namespace App\Livewire;

use Livewire\Component;
use App\Services\CartService;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;

class ProfileCart extends Component
{
    public $cartItems = [];
    public $totalSum = 0;
    public $totalQuantity = 0;
    public function mount(CartService $cartService)
    {
        $this->loadCart($cartService);
    }

    protected function loadCart(CartService $cartService)
    {
        if (Auth::check()) {
            $this->cartItems = $cartService->getUserCart(Auth::user());

            $this->totalSum = $this->cartItems->sum(function ($item) {
                return $item->product->price * $item->quantity;
            });
            $this->totalQuantity = $this->cartItems->sum('quantity');
        }
    }

    public function increment(Product $product, CartService $cartService)
    {
        $item = Auth::user()->cartItems()->where('product_id', $product->id)->first();

        if ($item && $item->quantity >= $product->quantity) {
            return;
        }

        $cartService->addToCart(Auth::user(), $product);
        $this->loadCart($cartService); 
        $this->dispatch('cart-updated');
    }

    public function decrement(Product $product, CartService $cartService)
    {
        $cartService->removeFromCart(Auth::user(), $product);
        $this->loadCart($cartService);

        $this->dispatch('cart-updated');
    }

    public function render()
    {
        return view('livewire.profile-cart');
    }
}
