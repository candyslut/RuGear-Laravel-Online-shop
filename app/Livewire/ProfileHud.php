<?php

namespace App\Livewire;

use App\Models\User;
use App\Support\Gamification;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;

/**
 * The always-visible, reactive core of the dashboard: identity, the live stat
 * matrix (level / XP / coins / achievements), level progression and the
 * achievement strip.
 *
 * Reactivity: polled via `wire:poll` (external changes — e.g. an admin granting
 * XP — surface within a few seconds) and refreshed instantly in-session via the
 * `profile-refresh` / `show-levelup` / `show-achievement` events. When a poll
 * detects a level gain or a freshly-unlocked achievement it dispatches browser
 * events (`hud-levelup` / `hud-achievement`) so the page can play the reveal.
 */
class ProfileHud extends Component
{
    /** Baselines carried across poll requests to detect external changes. */
    public int $lastSeenLevel = 0;
    public array $lastSeenAchIds = [];

    private ?User $cachedUser = null;

    public function mount(): void
    {
        $u = $this->user();
        $this->lastSeenLevel = $u->level;
        $this->lastSeenAchIds = $u->achievements->pluck('id')->all();
    }

    #[On('profile-refresh')]
    #[On('show-levelup')]
    #[On('show-achievement')]
    public function refresh(): void
    {
        $u = $this->user();

        if ($u->level > $this->lastSeenLevel) {
            $this->dispatch('hud-levelup', level: $u->level, tier: Gamification::rankTier($u->level)['name']);
        }

        $newIds = array_diff($u->achievements->pluck('id')->all(), $this->lastSeenAchIds);
        if (! empty($newIds)) {
            $this->dispatch('hud-achievement', count: count($newIds));
        }

        $this->lastSeenLevel = $u->level;
        $this->lastSeenAchIds = $u->achievements->pluck('id')->all();
    }

    protected function user(): User
    {
        return $this->cachedUser ??= Auth::user()->fresh(['achievements']);
    }

    public function render()
    {
        $u = $this->user();

        $achievements = $u->achievements
            ->sortByDesc('pivot.awarded_at')
            ->values()
            ->map(fn ($a) => [
                'title'      => $a->title,
                'experience' => $a->experience,
                'icon'       => Gamification::icon($a->slug),
                'rarity'     => Gamification::rarity($a),
            ]);

        return view('livewire.profile-hud', [
            'u'           => $u,
            'tier'        => Gamification::rankTier($u->level),
            'levelPct'    => Gamification::levelPercent($u),
            'xpToNext'    => Gamification::xpToNext($u),
            'achCount'    => $u->achievements->count(),
            'achievements'=> $achievements,
        ]);
    }
}
