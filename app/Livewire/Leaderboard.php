<?php

namespace App\Livewire;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;

/**
 * Player-ranking modal. Visibility is Livewire state (`$open`) rendered as a
 * fixed overlay rather than a native <dialog>, so a morph never strips an
 * `open` attribute. Data is loaded fresh in render() only while open, so
 * re-opening after an in-session level-up always shows current standings.
 * Search is handled client-side (instant, no per-keystroke round-trip).
 */
class Leaderboard extends Component
{
    public bool $open = false;

    #[On('open-leaderboard')]
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

    /** Swap this modal for the ranks ladder (close → open-ranks). */
    public function openRanks(): void
    {
        $this->open = false;
        $this->dispatch('open-ranks');
    }

    /** Swap this modal for the game leaderboards (close → open-game-leaderboards). */
    public function openGameBoards(): void
    {
        $this->open = false;
        $this->dispatch('open-game-leaderboards');
    }

    public function render()
    {
        $users = $this->open
            ? User::with('achievements')
                ->orderByDesc('level')
                ->orderByDesc('experience')
                ->get()
            : collect();

        $myRank = $this->open
            ? $users->search(fn ($u) => $u->id === Auth::id())
            : false;

        return view('livewire.leaderboard', [
            'users'  => $users,
            'meId'   => Auth::id(),
            'myRank' => $myRank === false ? null : $myRank + 1,
            'total'  => $users->count(),
        ]);
    }
}
