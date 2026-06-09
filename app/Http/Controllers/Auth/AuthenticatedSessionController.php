<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        // Count today's login toward the user's daily streak (idempotent per day).
        // Flash any reward so the dashboard shows the streak toast after redirect.
        if ($streakReward = app(\App\Services\StreakService::class)->touch(Auth::user())) {
            session()->flash('streak_awarded', $streakReward);

            // The streak's XP can carry a level-up and, with it, a new rank.
            if (Auth::user()->lastRankUp) {
                session()->flash('rank_up', Auth::user()->lastRankUp);
            }
        }

        return redirect()->intended(route('dashboard', absolute: false));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
