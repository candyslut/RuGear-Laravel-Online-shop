<?php

namespace App\Livewire;

use App\Services\DailyQuestService;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;

/**
 * The dashboard's daily-quests panel: today's three quests with live progress
 * meters, conditions and rewards. Polled (like ProfileHud) and refreshed
 * instantly via `profile-refresh` so a like/comment/game completed elsewhere
 * on the page advances the bars without a reload.
 */
class DailyQuests extends Component
{
    #[On('profile-refresh')]
    #[On('quests-completed')]
    public function refresh(): void
    {
        // No state to mutate — the attribute simply forces a re-render so the
        // freshly-advanced quest rows are pulled from the DB.
    }

    /** Swap all active quests for new ones (costs Quests::REROLL_COST coins). */
    public function rerollAll(): void
    {
        $user   = Auth::user();
        $result = app(DailyQuestService::class)->rerollAll($user);

        if ($result === 'ok') {
            // Live-sync every coin counter (header chip + HUD stat), let the
            // Alpine wrapper end its rolling state, refresh sibling panels.
            $this->dispatch('coins-changed', coins: (int) $user->fresh()->coins);
            $this->dispatch('quests-rerolled');
            $this->dispatch('profile-refresh');
        } else {
            // Un-stick the rolling animation (e.g. balance raced below cost).
            $this->dispatch('quest-reroll-failed', reason: $result);
        }
    }

    public function render()
    {
        $quests = app(DailyQuestService::class)->todayFor(Auth::user());

        return view('livewire.daily-quests', [
            'quests'    => $quests,
            'completed' => $quests->where('is_completed', true)->count(),
            'total'     => $quests->count(),
            // Viewer's balance + today's reroll usage — drive the reroll button.
            'coins'      => (int) Auth::user()->coins,
            'rerollUsed' => (bool) Auth::user()->last_quest_reroll_date?->isToday(),
            // Seconds until local midnight — drives the "обновятся через" timer.
            'resetIn'   => (int) abs(Carbon::now()->diffInSeconds(Carbon::tomorrow())),
        ]);
    }
}
