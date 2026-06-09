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

    public function render()
    {
        $quests = app(DailyQuestService::class)->todayFor(Auth::user());

        return view('livewire.daily-quests', [
            'quests'    => $quests,
            'completed' => $quests->where('is_completed', true)->count(),
            'total'     => $quests->count(),
            // Seconds until local midnight — drives the "обновятся через" timer.
            'resetIn'   => (int) abs(Carbon::now()->diffInSeconds(Carbon::tomorrow())),
        ]);
    }
}
