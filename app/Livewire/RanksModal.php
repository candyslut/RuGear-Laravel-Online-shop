<?php

namespace App\Livewire;

use App\Support\Gamification;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;

/**
 * "All ranks" modal — the full tier ladder (Новобранец → Алмаз) with each
 * tier's level requirement, one-time coin reward and the user's standing
 * (achieved / current / locked). Same overlay-as-state pattern as the
 * Leaderboard and AchievementsModal so a Livewire morph never strips it.
 */
class RanksModal extends Component
{
    public bool $open = false;

    #[On('open-ranks')]
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

    public function render()
    {
        if (! $this->open) {
            return view('livewire.ranks-modal', ['tiers' => [], 'level' => 0, 'current' => null]);
        }

        $level   = (int) Auth::user()->level;
        $current = Gamification::rankTier($level);

        $tiers = collect(Gamification::tiers())
            ->map(function (array $tier) use ($level) {
                $achieved = $level >= $tier['min'];
                $isCurrent = $tier['code'] === Gamification::rankTier($level)['code'];

                // Progress through this tier's level band (only meaningful while
                // the user is standing in it).
                $span = ($tier['next'] ?? ($tier['min'] + 15)) - $tier['min'];
                $into = max(0, min($span, $level - $tier['min']));
                $pct  = $span > 0 ? (int) round($into / $span * 100) : 100;

                return array_merge($tier, [
                    'achieved'  => $achieved,
                    'isCurrent' => $isCurrent,
                    'pct'       => $isCurrent ? $pct : ($achieved ? 100 : 0),
                ]);
            })
            ->values()
            ->all();

        return view('livewire.ranks-modal', [
            'tiers'   => $tiers,
            'level'   => $level,
            'current' => $current,
        ]);
    }
}
