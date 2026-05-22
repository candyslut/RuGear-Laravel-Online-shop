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
        $user = Auth::user();
        $user->load('achievements');

        $cartItems = $this->cartService->getUserCart($user);
        $userTickets = $user->tickets()->latest()->paginate(2);

        return view('dashboard', compact('cartItems', 'userTickets'));
    }

}