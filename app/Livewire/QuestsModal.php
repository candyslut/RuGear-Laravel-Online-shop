<?php

namespace App\Livewire;

use App\Services\DailyQuestService;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;

/**
 * Global "daily quests" modal, opened from the burger menu on any page. Same
 * overlay-as-state pattern as the Leaderboard / AchievementsModal. Loads the
 * day's quests fresh while open so progress made elsewhere is always current.
 */
class QuestsModal extends Component
{
    public bool $open = false;

    #[On('open-quests')]
    public function open(): void
    {
        $this->open = true;
        $this->dispatch('lock-body');
    }

    public function close(): void
    {
        $this->open = false;
        $this->dispatch('unlock-body');
    }

    /** Refresh while open if a quest completes elsewhere on the page. */
    #[On('profile-refresh')]
    #[On('quests-completed')]
    public function refresh(): void
    {
        //
    }

    /** Swap all active quests for new ones (costs Quests::REROLL_COST coins). */
    public function rerollAll(): void
    {
        $user   = Auth::user();
        $result = app(DailyQuestService::class)->rerollAll($user);

        if ($result === 'ok') {
            $this->dispatch('coins-changed', coins: (int) $user->fresh()->coins);
            $this->dispatch('quests-rerolled');
            $this->dispatch('profile-refresh');
        } else {
            $this->dispatch('quest-reroll-failed', reason: $result);
        }
    }

    public function render()
    {
        if (! $this->open) {
            return view('livewire.quests-modal', [
                'quests' => collect(), 'completed' => 0, 'total' => 0, 'resetIn' => 0,
                'coins' => 0, 'rerollUsed' => false,
            ]);
        }

        $quests = app(DailyQuestService::class)->todayFor(Auth::user());

        return view('livewire.quests-modal', [
            'quests'     => $quests,
            'completed'  => $quests->where('is_completed', true)->count(),
            'total'      => $quests->count(),
            'coins'      => (int) Auth::user()->coins,
            'rerollUsed' => (bool) Auth::user()->last_quest_reroll_date?->isToday(),
            'resetIn'    => (int) abs(Carbon::now()->diffInSeconds(Carbon::tomorrow())),
        ]);
    }
}
