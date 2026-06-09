<?php

namespace App\Livewire\Concerns;

use App\Models\Sticker;
use App\Models\StickerPack;
use App\Models\StickerUsage;
use Livewire\Attributes\Computed;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Shared sticker-picker data for any Livewire component that hosts the
 * partials/sticker-picker popover (the comment form and inline reply forms).
 */
trait InteractsWithStickerPicker
{
    /**
     * Packs available to this user in the picker: free packs (no shop listing)
     * plus premium packs they have purchased in the shop.
     */
    #[Computed]
    public function stickerPacks()
    {
        $ownedPackIds = Auth::check()
            ? Auth::user()->shopItems()
                ->where('category', 'sticker_pack')
                ->pluck('sticker_pack_id')
                ->filter()
                ->all()
            : [];

        return StickerPack::active()
            ->with(['stickers', 'shopItem'])
            ->orderBy('sort_order')
            ->get()
            ->filter(fn ($pack) => !$pack->isPremium() || in_array($pack->id, $ownedPackIds))
            ->values();
    }

    /** This user's most recently used stickers, for the "Recent" tab. */
    #[Computed]
    public function recentStickers()
    {
        if (!Auth::check()) {
            return collect();
        }

        return StickerUsage::where('user_id', Auth::id())
            ->with('sticker')
            ->orderByDesc('used_at')
            ->take(16)
            ->get()
            ->pluck('sticker')
            ->filter();
    }

    /**
     * Client-side payload for a sticker that was just sent, so the picker can
     * prepend it to its "Recent" tab without a page reload. Mirrors the
     * type/url derivation in partials/sticker-button.blade.php.
     */
    protected function stickerUsedPayload(?int $stickerId): ?array
    {
        if ($stickerId === null) {
            return null;
        }

        $sticker = Sticker::find($stickerId);
        if (!$sticker) {
            return null;
        }

        $type = Str::endsWith($sticker->file_path, '.json') ? 'lottie'
              : (Str::endsWith($sticker->file_path, '.webm') ? 'video' : 'image');

        return [
            'id'    => $sticker->id,
            'url'   => Storage::url($sticker->file_path),
            'type'  => $type,
            'emoji' => $sticker->emoji,
        ];
    }
}
