<?php

namespace App\Services;

use App\Models\User;
use App\Support\Gamification;
use Illuminate\Support\Carbon;

/**
 * Daily-login streak. Call touch() whenever an authenticated user is active
 * (on login and on each dashboard visit). It counts at most one tick per
 * calendar day, extends the streak on consecutive days, resets it after a
 * missed day, and pays out coins + XP (with a fire-and-forget bell
 * notification) the first time it runs each day.
 */
class StreakService
{
    /**
     * Register today's activity for the user.
     *
     * @return array{streak:int, coins:int, xp:int}|null  Reward summary on the
     *         first call of the day, or null if already counted today.
     */
    public function touch(User $user): ?array
    {
        $today = Carbon::today();
        $last  = $user->last_active_date;

        // Already counted today — nothing to do (keeps reloads idempotent).
        if ($last && $last->isSameDay($today)) {
            return null;
        }

        // Consecutive day extends the streak; any gap restarts it at 1.
        $user->streak = ($last && $last->isSameDay($today->copy()->subDay()))
            ? $user->streak + 1
            : 1;

        $user->best_streak = max($user->best_streak, $user->streak);
        $user->last_active_date = $today;
        $user->save();

        // Reward follows the 7-day cycle (climbs to a day-7 peak, then loops).
        $reward = Gamification::streakReward(Gamification::streakCycleDay($user->streak));
        $coins  = $reward['coins'];
        $xp     = $reward['xp'];

        $user->addCoins($coins);
        $user->addExperience($xp); // handles level-ups + their own notification

        app(NotificationService::class)->streak($user, $coins, $xp);

        // Milestone achievements mirror the reward-wheel tiers (weekly /
        // monthly / six-month grand finale). awardAchievement() dedupes, so a
        // looping streak only ever pays each one out once. touch() always runs
        // in a full-page request (login / dashboard GET), so the bottom-right
        // toast goes through the layout's achievements_awarded session queue.
        $milestones = [7 => 'streak_7', 30 => 'streak_30', 180 => 'streak_180'];
        if (isset($milestones[$user->streak])) {
            $achievement = \App\Models\Achievement::where('slug', $milestones[$user->streak])->first();
            if ($achievement && $user->awardAchievement($achievement)) {
                session()->flash('achievements_awarded', array_merge(
                    session('achievements_awarded', []),
                    [[
                        'title'      => $achievement->title,
                        'experience' => $achievement->experience,
                        'coins'      => $achievement->coins,
                    ]],
                ));
            }
        }

        return ['streak' => $user->streak, 'coins' => $coins, 'xp' => $xp];
    }
}
