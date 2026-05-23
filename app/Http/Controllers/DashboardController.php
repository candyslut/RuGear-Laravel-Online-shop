<?php

namespace App\Http\Controllers;

use App\Services\CartService;
use App\Models\Achievement;
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
        $userTickets = $user->tickets()->latest()->get();

        // Get user's orders
        $userOrders = $user->orders()->with('items.product')->latest()->paginate(5);
        
        // Get all achievements for the modal
        $allAchievements = Achievement::all();
        $userAchievementIds = $user->achievements->pluck('id')->toArray();

        return view('dashboard', compact('cartItems', 'userTickets', 'userOrders', 'allAchievements', 'userAchievementIds'));
    }

}