<?php

namespace App\Console\Commands;

use App\Models\ShopItem;
use App\Models\Sticker;
use App\Models\StickerPack;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ImportTelegramStickerPack extends Command
{
    protected $signature = 'stickers:import-telegram
        {name : Telegram sticker set short name (from t.me/addstickers/<name>)}
        {--slug= : Local pack slug (defaults to a slug of the set name)}
        {--title= : Display name (defaults to the Telegram set title)}
        {--price=300 : Price in coins for the shop listing}
        {--sort=10 : sort_order for the pack}';

    protected $description = 'Import a Telegram sticker pack (animated .tgs -> Lottie JSON, static .webp as image) into the local sticker system.';

    public function handle(): int
    {
        $token = config('services.telegram.bot_token');
        if (!$token) {
            $this->error('TELEGRAM_BOT_TOKEN is not set in .env');
            return self::FAILURE;
        }

        $name  = $this->argument('name');
        $slug  = $this->option('slug') ?: Str::slug($name, '_');
        $api   = "https://api.telegram.org/bot{$token}";
        $files = "https://api.telegram.org/file/bot{$token}";

        // Many local PHP/XAMPP installs lack a CA bundle; use a bundled cacert.pem
        // if present so we still verify TLS instead of disabling it.
        $caBundle = is_file(storage_path('app/cacert.pem')) ? storage_path('app/cacert.pem') : true;
        $client   = Http::withOptions(['verify' => $caBundle]);

        $this->info("Fetching sticker set «{$name}»...");
        $set = $client->get("{$api}/getStickerSet", ['name' => $name])->json();

        if (!($set['ok'] ?? false)) {
            $this->error('Telegram error: ' . ($set['description'] ?? 'unknown'));
            return self::FAILURE;
        }

        $title    = $this->option('title') ?: ($set['result']['title'] ?? $name);
        $stickers = $set['result']['stickers'] ?? [];
        $this->info("Found {$title}: " . count($stickers) . ' stickers.');

        $pack = StickerPack::updateOrCreate(
            ['slug' => $slug],
            ['name' => $title, 'sort_order' => (int) $this->option('sort'), 'is_active' => true],
        );

        $dir = "stickers/{$slug}";
        Storage::disk('public')->makeDirectory($dir);

        $imported = 0;
        $skipped  = 0;
        $manifest = []; // persisted to pack.json so the pack survives DB re-seeds

        foreach ($stickers as $i => $st) {
            $video    = $st['is_video'] ?? false;       // .webm (VP9, rendered via <video>)
            $animated = $st['is_animated'] ?? false;    // .tgs -> Lottie JSON (rendered on <canvas>)
            $ext      = $video ? 'webm' : ($animated ? 'json' : 'webp');
            $index    = str_pad((string) ($i + 1), 2, '0', STR_PAD_LEFT);
            $relPath  = "{$dir}/{$index}.{$ext}";

            // Resolve the file path, then download from the file CDN.
            $fileInfo = $client->get("{$api}/getFile", ['file_id' => $st['file_id']])->json();
            $tgPath   = $fileInfo['result']['file_path'] ?? null;
            if (!$tgPath) {
                $skipped++;
                continue;
            }

            $body = $client->get("{$files}/{$tgPath}")->body();

            if ($animated) {
                // .tgs is gzip-compressed Lottie JSON.
                $json = @gzdecode($body);
                if ($json === false) {
                    $skipped++;
                    continue;
                }
                Storage::disk('public')->put($relPath, $json);
            } else {
                // Static .webp and video .webm are stored verbatim.
                Storage::disk('public')->put($relPath, $body);
            }

            // Video stickers animate too — flag them animated so the pack is
            // labelled "Animated" and the picker treats them like motion stickers.
            $isAnimated = $animated || $video;

            Sticker::updateOrCreate(
                ['sticker_pack_id' => $pack->id, 'file_path' => $relPath],
                [
                    'is_animated' => $isAnimated,
                    'emoji'       => $st['emoji'] ?? null,
                    'sort_order'  => $i + 1,
                ],
            );

            $manifest[] = ['file' => $relPath, 'emoji' => $st['emoji'] ?? null, 'is_animated' => $isAnimated];

            $imported++;
            usleep(150000); // be gentle with the Bot API
        }

        $description = "Стикерпак из Telegram: {$title}";
        $price       = (int) $this->option('price');

        // Make the pack purchasable, like the seeded packs.
        ShopItem::updateOrCreate(
            ['slug' => 'pack_' . $slug],
            [
                'category'        => 'sticker_pack',
                'sticker_pack_id' => $pack->id,
                'name'            => $title,
                'description'     => $description,
                'price'           => $price,
                'css_value'       => null,
            ],
        );

        // Manifest so TelegramPackSeeder can rebuild this pack after a DB reset
        // (imported packs are not part of the regular seeders).
        Storage::disk('public')->put("{$dir}/pack.json", json_encode([
            'slug'        => $slug,
            'name'        => $title,
            'description' => $description,
            'price'       => $price,
            'sort'        => (int) $this->option('sort'),
            'stickers'    => $manifest,
        ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));

        $this->info("Imported {$imported} stickers" . ($skipped ? " ({$skipped} skipped: unreadable)" : '') . " into pack «{$title}» (slug: {$slug}).");

        return self::SUCCESS;
    }
}
