<?php

namespace App\Livewire;

use App\Models\Achievement;
use App\Support\Gamification;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;

/**
 * "All achievements" modal — a tiered medallion grid showing unlocked vs locked
 * entries. Same overlay-as-state pattern as Leaderboard (morph-safe, loads
 * fresh while open). Filtering (all / unlocked / locked) is client-side.
 */
class AchievementsModal extends Component
{
    public bool $open = false;

    private const RARITY_ORDER = ['legendary' => 0, 'epic' => 1, 'rare' => 2, 'common' => 3];

    #[On('open-achievements')]
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
            return view('livewire.achievements-modal', [
                'items' => collect(), 'unlocked' => 0, 'total' => 0, 'earnedXp' => 0,
            ]);
        }

        $user = Auth::user()->fresh(['achievements']);
        $owned = $user->achievements->keyBy('id');

        $items = Achievement::all()
            ->map(function (Achievement $a) use ($owned) {
                $got = $owned->has($a->id);

                return [
                    'title'       => $a->title,
                    'description' => $a->description,
                    'experience'  => $a->experience,
                    'icon'        => Gamification::icon($a->slug),
                    'rarity'      => Gamification::rarity($a),
                    'got'         => $got,
                    'awarded_at'  => $got ? $owned->get($a->id)->pivot->awarded_at : null,
                ];
            })
            ->sortBy(fn ($i) => [$i['got'] ? 0 : 1, self::RARITY_ORDER[$i['rarity']['tier']], -$i['experience']])
            ->values();

        return view('livewire.achievements-modal', [
            'items'    => $items,
            'unlocked' => $items->where('got', true)->count(),
            'total'    => $items->count(),
            'earnedXp' => $items->where('got', true)->sum('experience'),
        ]);
    }
}
