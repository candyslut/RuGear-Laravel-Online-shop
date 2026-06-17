<?php

namespace App\Livewire;

use App\Support\GameStats as Stats;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;

/**
 * The stats panel inside the dashboard "Игротека" block. Shows a compact,
 * at-a-glance summary for one game at a time (record, rank, a few headline
 * numbers and a recent-activity sparkline); a slider switches between games
 * and "Подробнее" hands off to the full GameStats modal where the charts and
 * records live. Refreshes itself when a run is logged elsewhere on the page.
 */
class GameStatsWidget extends Component
{
    /** A run logged on the dashboard refreshes the compact panel live. */
    #[On('game-logged')]
    public function refresh(): void
    {
        //
    }

    public function render()
    {
        $uid = Auth::id();

        // One compact bundle per game: career summary, global standing and a
        // 14-day activity series (runs per day) feeding the interactive sparkline.
        $games = [];
        foreach (Stats::keys() as $key) {
            $games[$key] = [
                'meta'    => Stats::game($key),
                'summary' => Stats::summary($uid, $key),
                'rank'    => Stats::globalRank($uid, $key),
                'series'  => Stats::dailySeries($uid, $key, 14),
            ];
        }

        return view('livewire.game-stats-widget', ['games' => $games]);
    }
}
