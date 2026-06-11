<?php

namespace App\Http\Controllers;

use App\Services\CartService;
use App\Services\DailyQuestService;
use App\Services\StreakService;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function __construct(
        protected CartService $cartService,
        protected StreakService $streakService,
        protected DailyQuestService $questService,
    ) {}

    public function index()
    {
        $user = Auth::user();

        // Daily visit also counts toward the streak (covers users who stay
        // logged in for days and never hit the login form). Idempotent per day.
        // The reward (when first earned today) drives the bottom-left toast.
        if ($streakReward = $this->streakService->touch($user)) {
            session()->flash('streak_awarded', $streakReward);
        }

        // A rank-up can come from the streak reward's XP (the "visit" quest
        // was retired from the pool, so the dashboard no longer advances any
        // quest by itself).
        if ($rankUp = $user->lastRankUp) {
            session()->flash('rank_up', $rankUp);
        }

        $cartItems = $this->cartService->getUserCart($user);
        $userTickets = $user->tickets()->latest()->get();
        $userOrders = $user->orders()->with('items.product')->latest()->paginate(5);

        // Profile stats, the achievements grid and the leaderboard are now
        // owned by their own reactive Livewire components (ProfileHud,
        // AchievementsModal, Leaderboard), which load their data on demand.
        return view('dashboard', compact('cartItems', 'userTickets', 'userOrders'));
    }
}