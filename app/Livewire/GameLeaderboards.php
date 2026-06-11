<?php

namespace App\Livewire;

use App\Support\GameStats as Stats;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;

/**
 * Game leaderboards modal: per-game top players, all-time and this-week. Opened
 * from the GameStats modal footer and from the main Leaderboard's footer. Rows
 * are clickable — they hand off to the GameStats modal to inspect that player
 * (subject to their privacy setting). Both periods are loaded up front so the
 * all-time/week toggle is instant (client-side via Alpine).
 */
class GameLeaderboards extends Component
{
    public bool $open = false;

    #[On('open-game-leaderboards')]
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

    /** Inspect a player's full game profile. */
    public function viewPlayer(int $userId): void
    {
        $this->open = false;
        $this->dispatch('open-game-stats', userId: $userId);
    }

    public function render()
    {
        if (! $this->open) {
            return view('livewire.game-leaderboards', ['ready' => false]);
        }

        $boards = [];
        foreach (Stats::keys() as $key) {
            $boards[$key] = [
                'meta'    => Stats::game($key),
                'alltime' => Stats::topPlayers($key, null, 10),
                'week'    => Stats::topPlayers($key, 7, 10),
            ];
        }

        return view('livewire.game-leaderboards', [
            'ready'      => true,
            'boards'     => $boards,
            'meId'       => Auth::id(),
            'defaultTab' => array_key_first($boards) ?: 'redline',
        ]);
    }
}
