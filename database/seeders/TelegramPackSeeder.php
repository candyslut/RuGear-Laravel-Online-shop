<?php

namespace Database\Seeders;

use App\Models\ShopItem;
use App\Models\Sticker;
use App\Models\StickerPack;
use Illuminate\Database\Seeder;

class TelegramPackSeeder extends Seeder
{
    /**
     * Rebuilds sticker packs imported via `stickers:import-telegram` from the
     * pack.json manifests on disk, so they survive a DB reset / re-seed.
     */
    public function run(): void
    {
        foreach (glob(storage_path('app/public/stickers/*/pack.json')) ?: [] as $manifestPath) {
            $m = json_decode(file_get_contents($manifestPath), true);
            if (!is_array($m) || empty($m['slug'])) {
                continue;
            }

            $pack = StickerPack::updateOrCreate(
                ['slug' => $m['slug']],
                ['name' => $m['name'], 'sort_order' => $m['sort'] ?? 10, 'is_active' => true],
            );

            foreach ($m['stickers'] ?? [] as $i => $s) {
                Sticker::updateOrCreate(
                    ['sticker_pack_id' => $pack->id, 'file_path' => $s['file']],
                    [
                        'emoji'       => $s['emoji'] ?? null,
                        'is_animated' => $s['is_animated'] ?? false,
                        'sort_order'  => $i + 1,
                    ],
                );
            }

            ShopItem::updateOrCreate(
                ['slug' => 'pack_' . $m['slug']],
                [
                    'category'        => 'sticker_pack',
                    'sticker_pack_id' => $pack->id,
                    'name'            => $m['name'],
                    'description'     => $m['description'] ?? ('Стикерпак из Telegram: ' . $m['name']),
                    'price'           => $m['price'] ?? 300,
                    'css_value'       => null,
                ],
            );
        }
    }
}
