<?php

namespace App\Http\Controllers;

use App\Services\CartService;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function __construct(
        protected CartService $cartService
    ) {}

    public function index()
    {
        $cartItems = $this->cartService->getUserCart(Auth::user());
        $userTickets = auth()->user()->tickets()->latest()->paginate(2);

        return view('dashboard', compact('cartItems', 'userTickets'));
    }

}