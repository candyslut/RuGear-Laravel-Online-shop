<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\On;
use Illuminate\Support\Facades\Auth;

class NotificationBell extends Component
{
    public array $notifications = [];
    public int $unreadCount = 0;

    public function mount()
    {
        $this->load();
    }

    public function load()
    {
        $user = Auth::user();
        if (!$user) {
            $this->notifications = [];
            $this->unreadCount = 0;
            return;
        }

        $this->notifications = $user->notifications()
            ->latest()
            ->limit(15)
            ->get()
            ->toArray();

        $this->unreadCount = $user->notifications()->whereNull('read_at')->count();
    }

    /**
     * Refresh the bell when an achievement / level-up happens in the same
     * page session (these fire via Livewire without a full page reload, so
     * the bell would otherwise miss the freshly-created notification).
     */
    #[On('show-achievement')]
    #[On('show-levelup')]
    #[On('quests-completed')]
    public function onActivity($payload = null)
    {
        $this->load();
    }

    /**
     * Lightweight poll target — only refreshes the unread badge.
     */
    public function refresh()
    {
        $user = Auth::user();
        $this->unreadCount = $user ? $user->notifications()->whereNull('read_at')->count() : 0;
    }

    public function markAllRead()
    {
        $user = Auth::user();
        if (!$user) {
            return;
        }

        $user->notifications()->whereNull('read_at')->update(['read_at' => now()]);
        $this->load();
    }

    public function openNotification(int $id)
    {
        $user = Auth::user();
        if (!$user) {
            return;
        }

        $notification = $user->notifications()->find($id);
        if (!$notification) {
            return;
        }

        if ($notification->read_at === null) {
            $notification->update(['read_at' => now()]);
        }

        if ($notification->url) {
            return $this->redirect($notification->url);
        }

        $this->load();
    }

    public function render()
    {
        return view('livewire.notification-bell');
    }
}
