<?php

namespace App\Http\Controllers;

use App\Models\ShopItem;
use App\Models\StickerPack;
use App\Models\Achievement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ShopController extends Controller
{
    public function index()
    {
        $user  = Auth::user();

        // Cosmetics live in the spotlight grid; sticker packs get their own section.
        $items = ShopItem::whereNotIn('category', ['font', 'sticker_pack'])
            ->get()
            ->groupBy('category');
        $owned = $user->shopItems()->pluck('shop_item_id')->toArray();

        // Imported Telegram sets get a curated rarity tier that drives the
        // showcase styling (glow, gem, stars).
        $rarityBySlug  = [
            'catsunicmass'              => 'mythic',     // 120 video stickers — the showpiece
            'cherryblack'               => 'legendary',
            'prettysailormoon'          => 'epic',
            'jjks2_pt2'                 => 'mythic',
            'skalddealsex_by_fstikbot'  => 'epic',
            'biscvit_vk'                => 'rare',
            'sti_586dd_by_tgemodzibot'  => 'rare',
        ];
        // Fallback rarity (for any future imported pack) derived from price.
        // Floored at "rare": the common tier was retired with the Noto packs.
        $priceRarity = fn ($p) => ($p = (int) $p) >= 400 ? 'legendary'
            : ($p >= 300 ? 'mythic' : ($p >= 200 ? 'epic' : 'rare'));

        $stickerPacks = StickerPack::active()
            ->with(['stickers', 'shopItem'])
            ->orderBy('sort_order')
            ->get()
            ->map(function (StickerPack $pack) use ($owned, $rarityBySlug, $priceRarity) {
                $shopItem = $pack->shopItem;
                return [
                    'slug'         => $pack->slug,
                    'name'         => $pack->name,
                    'count'        => $pack->stickers->count(),
                    'preview'      => $pack->stickers->take(4)
                        ->map(fn ($s) => \Illuminate\Support\Facades\Storage::url($s->file_path))
                        ->values()->all(),
                    // Full sticker list (url + render type) for the pre-purchase
                    // preview modal.
                    'stickers'     => $pack->stickers->map(fn ($s) => [
                        'url'  => \Illuminate\Support\Facades\Storage::url($s->file_path),
                        'type' => \Illuminate\Support\Str::endsWith($s->file_path, '.json') ? 'lottie'
                                : (\Illuminate\Support\Str::endsWith($s->file_path, '.webm') ? 'video' : 'image'),
                    ])->values()->all(),
                    'shop_item_id' => $shopItem?->id,
                    'price'        => $shopItem?->price,
                    'description'  => $shopItem?->description,
                    // Free packs (no shop listing) are owned by everyone.
                    'owned'        => $shopItem === null || in_array($shopItem->id, $owned),
                    'animated'     => $pack->stickers->contains(fn ($s) => $s->is_animated),
                    // Premium packs use their curated tier (or a price-derived fallback).
                    'rarity'       => $rarityBySlug[$pack->slug] ?? $priceRarity($shopItem?->price),
                ];
            });

        return view('market', compact('items', 'owned', 'stickerPacks'));
    }

    public function buy(ShopItem $item)
    {
        $user = Auth::user();

        if ($item->category === 'font') {
            return response()->json(['error' => 'unavailable'], 404);
        }

        if (in_array($item->id, $user->shopItems()->pluck('shop_item_id')->toArray())) {
            return response()->json(['error' => 'already_owned'], 400);
        }

        if ($user->coins < $item->price) {
            return response()->json(['error' => 'not_enough_coins'], 400);
        }

        $user->decrement('coins', $item->price);
        $user->shopItems()->attach($item->id, ['purchased_at' => now()]);

        $this->applyCosmetic($user, $item);

        $this->checkStoreCompletion($user);

        return response()->json([
            'coins'    => $user->fresh()->coins,
            'equipped' => true,
            'category' => $item->category,
            'css'      => $item->css_value,
        ]);
    }

    public function equip(ShopItem $item)
    {
        $user = Auth::user();

        if (!in_array($item->id, $user->shopItems()->pluck('shop_item_id')->toArray())) {
            return response()->json(['error' => 'not_owned'], 400);
        }

        $this->applyCosmetic($user, $item);

        return response()->json([
            'equipped' => true,
            'category' => $item->category,
            'css'      => $item->css_value,
        ]);
    }

    public function unequip(Request $request)
    {
        $user     = Auth::user();
        $category = $request->input('category');
        $col      = $this->col($category);

        if ($col) {
            $user->$col = null;
            $user->save();
        }

        return response()->json(['unequipped' => true, 'category' => $category]);
    }

    /**
     * Award the completionist achievement once the user owns every buyable
     * ShopItem — all cosmetics and every sticker pack (fonts aren't sold, so
     * they're excluded, mirroring the guard in buy()). awardAchievement()
     * dedupes, so this only ever pays out once.
     */
    private function checkStoreCompletion($user): void
    {
        $buyableIds = ShopItem::where('category', '!=', 'font')->pluck('id');
        $ownedIds   = $user->shopItems()->pluck('shop_item_id');

        if ($buyableIds->isNotEmpty() && $buyableIds->diff($ownedIds)->isEmpty()) {
            $achievement = Achievement::where('slug', 'store_complete')->first();
            if ($achievement) {
                $user->awardAchievement($achievement);
            }
        }
    }

    private function applyCosmetic($user, ShopItem $item): void
    {
        $col = $this->col($item->category);
        if ($col) {
            $user->$col = $item->css_value;
            $user->save();
        }
    }

    private function col(string $category): ?string
    {
        return match($category) {
            'font'           => 'cosmetic_font',
            'border'         => 'cosmetic_border',
            'nickname_color' => 'cosmetic_nickname_color',
            default          => null,
        };
    }
}
