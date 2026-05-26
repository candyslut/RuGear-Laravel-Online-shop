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

    public function addToCart()
    {
        if ($this->product->quantity <= 0) {
            return;
        }

        $item = auth()->user()->cartItems()->where('product_id', $this->product->id)->first();

        if ($item && $item->quantity < $this->product->quantity) {
            $item->increment('quantity');
            $this->quantity = $item->quantity;
        } elseif (!$item) {
            auth()->user()->cartItems()->create([
                'product_id' => $this->product->id,
                'quantity' => 1,
            ]);
            $this->quantity = 1;
        }

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
