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

        $cartItems = $this->cartService->getUserCart($user);
        $userTickets = $user->tickets()->latest()->get();
        $userOrders = $user->orders()->with('items.product')->latest()->paginate(5);

        // Profile stats, the achievements grid and the leaderboard are now
        // owned by their own reactive Livewire components (ProfileHud,
        // AchievementsModal, Leaderboard), which load their data on demand.
        return view('dashboard', compact('cartItems', 'userTickets', 'userOrders'));
    }
}