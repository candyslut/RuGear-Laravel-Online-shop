<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Services\CartService;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    public function __construct(
        protected CartService $cartService
    ) {}

    public function add(Product $product)
    {
        $this->cartService->addToCart(Auth::user(), $product);
        return redirect()->back();
    }

    public function remove(Product $product)
    {
        $this->cartService->removeFromCart(Auth::user(), $product);
        return redirect()->back();
    }
}