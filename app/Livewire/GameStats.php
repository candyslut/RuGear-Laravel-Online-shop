<?php

namespace App\Livewire;

use App\Models\User;
use App\Support\GameStats as Stats;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;

/**
 * Global mini-game statistics modal — the deep-dive surface opened from the
 * dashboard widget ("Подробнее") and from leaderboard rows (to inspect another
 * player). Same overlay-as-state pattern as Leaderboard / QuestsModal so a
 * morph never strips an `open` attribute; data is loaded fresh only while open.
 *
 * `targetUserId` lets the same modal render someone else's profile. Other
 * players' stats are shown only when they keep `stats_public` on; otherwise a
 * privacy notice is rendered instead.
 */
class GameStats extends Component
{
    public bool $open = false;
    public ?int $targetUserId = null;

    #[On('open-game-stats')]
    public function open(?int $userId = null): void
    {
        // The leaderboard passes the row's user id; null means "show me".
        $this->targetUserId = ($userId && $userId !== Auth::id()) ? $userId : null;
        $this->open = true;
        $this->dispatch('lock-body');
    }

    public function close(): void
    {
        $this->open = false;
        $this->targetUserId = null;
        $this->dispatch('unlock-body');
    }

    /** A run logged elsewhere on the page refreshes the open modal. */
    #[On('game-logged')]
    public function refresh(): void
    {
        //
    }

    /** Hand off to the game leaderboards modal. */
    public function openLeaderboards(): void
    {
        $this->open = false;
        $this->dispatch('open-game-leaderboards');
    }

    public function render()
    {
        if (! $this->open) {
            return view('livewire.game-stats', ['ready' => false]);
        }

        $me = Auth::user();
        $target = $this->targetUserId ? User::find($this->targetUserId) : $me;

        if (! $target) {
            return view('livewire.game-stats', ['ready' => false]);
        }

        $isSelf  = $target->id === $me->id;
        $private = ! $isSelf && ! (bool) $target->stats_public;

        if ($private) {
            return view('livewire.game-stats', [
                'ready'    => true,
                'private'  => true,
                'isSelf'   => false,
                'target'   => $target,
                'overview' => null,
                'games'    => [],
            ]);
        }

        // One bundle per registered game: career summary, 30-day series, score
        // histogram, the player's best runs and their global standing.
        $games = [];
        foreach (Stats::keys() as $key) {
            $games[$key] = [
                'meta'    => Stats::game($key),
                'summary' => Stats::summary($target->id, $key),
                'daily'   => Stats::dailySeries($target->id, $key, 30),
                'bests'   => Stats::bests($target->id, $key, 5),
                'rank'    => Stats::globalRank($target->id, $key),
            ];
        }

        return view('livewire.game-stats', [
            'ready'    => true,
            'private'  => false,
            'isSelf'   => $isSelf,
            'target'   => $target,
            'overview' => Stats::overview($target->id),
            'games'    => $games,
        ]);
    }
}
