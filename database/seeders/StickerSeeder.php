<?php

namespace Database\Seeders;

use App\Models\StickerPack;
use App\Models\ShopItem;
use Illuminate\Database\Seeder;

class StickerSeeder extends Seeder
{
    /**
     * Retires the "common" (Обычный) sticker packs — the built-in Google Noto
     * animated-emoji sets (Смайлы / Жесты / Символы) that were not imported from
     * Telegram. Only the curated Telegram packs (seeded by TelegramPackSeeder)
     * remain in the app.
     *
     * Deleting a pack cascades to its stickers; commentaries that used one of
     * those stickers keep the comment but drop the sticker (nullOnDelete), and
     * sticker_usages rows are removed (onDelete cascade).
     */
    public function run(): void
    {
        // Legacy/common slugs to purge. The kawaii/monsters/black_cherry packs are
        // older retired sets; smileys/gestures/symbols are the common Noto emoji
        // packs being removed now.
        $retired = ['reactions', 'emotions', 'kawaii', 'monsters', 'black_cherry', 'smileys', 'gestures', 'symbols'];

        ShopItem::where('category', 'sticker_pack')
            ->whereIn('slug', array_map(fn ($s) => 'pack_' . $s, $retired))
            ->delete();

        StickerPack::whereIn('slug', $retired)->delete();
    }
}
