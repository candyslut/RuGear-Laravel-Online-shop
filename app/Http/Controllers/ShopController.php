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
        // Внутри категории: бесплатные базовые → дешёвые → дорогие.
        // Порядок секций на странице: обводки → никнеймы → темы.
        $categoryOrder = ['border', 'nickname_color', 'theme'];
        $items = ShopItem::whereNotIn('category', ['font', 'sticker_pack'])
            ->get()
            ->groupBy('category')
            ->map(fn ($group) => $group->sortBy([['price', 'asc'], ['id', 'asc']])->values())
            ->sortBy(fn ($group, $cat) => array_search($cat, $categoryOrder));
        $owned = $user->shopItems()->pluck('shop_item_id')->toArray();

        $stickerPacks = StickerPack::active()
            ->with(['stickers', 'shopItem'])
            ->orderBy('sort_order')
            ->get()
            ->map(function (StickerPack $pack) use ($owned) {
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
                    'rarity'       => $this->packRarity($pack, $shopItem?->price),
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

        // Бесплатные базовые предметы (тёмная/светлая тема) есть у всех:
        // «покупка» для них — просто экипировка, без списания и pivot-записи.
        if ((int) $item->price === 0) {
            $this->applyCosmetic($user, $item);

            return response()->json([
                'coins'    => $user->coins,
                'equipped' => true,
                'category' => $item->category,
                'css'      => $item->css_value,
            ]);
        }

        // Легендарная косметика и легендарные стикерпаки заперты за игровыми
        // рубежами: 10 уровней в Buzzword Blast и 10 000 м в Redline Rush
        // (User::legendaryUnlocked()).
        if ($this->isLegendary($item) && !$user->legendaryUnlocked()) {
            return response()->json(['error' => 'legendary_locked'], 403);
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

        $awarded = $this->checkStoreCompletion($user);

        return response()->json([
            'coins'       => $user->fresh()->coins,
            'equipped'    => true,
            'category'    => $item->category,
            'css'         => $item->css_value,
            // Completionist achievement, if this purchase just unlocked it —
            // the market JS surfaces it as the bottom-right toast.
            'achievement' => $awarded,
        ]);
    }

    public function equip(ShopItem $item)
    {
        $user = Auth::user();

        // Бесплатные базовые предметы экипируются без записи о покупке.
        if ((int) $item->price > 0 && !in_array($item->id, $user->shopItems()->pluck('shop_item_id')->toArray())) {
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

        // «Отключить» на базовой тёмной/светлой теме включает противоположную;
        // покупные темы (космос/няшная) снимаются в обычные dark/light (null).
        // current приходит с клиента для случая, когда базовая тема активна
        // только локально (cosmetic_theme ещё не сохранялся).
        if ($category === 'theme') {
            $current = $user->cosmetic_theme ?: $request->input('current');
            $next    = match ($current) {
                'light' => 'dark',
                'dark'  => 'light',
                default => null,
            };
            $user->cosmetic_theme = $next;
            $user->save();

            return response()->json(['unequipped' => true, 'category' => $category, 'value' => $next]);
        }

        if ($col) {
            $user->$col = null;
            $user->save();
        }

        return response()->json(['unequipped' => true, 'category' => $category]);
    }

    /**
     * Award the completionist achievement once the user owns every buyable
     * ShopItem — all cosmetics and every sticker pack (fonts aren't sold and
     * price-0 base themes belong to everyone, so both are excluded, mirroring
     * the guards in buy()). awardAchievement() dedupes, so this only ever
     * pays out once.
     *
     * @return array{title:string, experience:int, coins:int}|null  Toast payload
     *         when the achievement was just granted.
     */
    private function checkStoreCompletion($user): ?array
    {
        $buyableIds = ShopItem::where('category', '!=', 'font')->where('price', '>', 0)->pluck('id');
        $ownedIds   = $user->shopItems()->pluck('shop_item_id');

        if ($buyableIds->isNotEmpty() && $buyableIds->diff($ownedIds)->isEmpty()) {
            $achievement = Achievement::where('slug', 'store_complete')->first();
            if ($achievement && $user->awardAchievement($achievement)) {
                return [
                    'title'      => $achievement->title,
                    'experience' => $achievement->experience,
                    'coins'      => $achievement->coins,
                ];
            }
        }

        return null;
    }

    /**
     * Imported Telegram sets get a curated rarity tier that drives the
     * showcase styling (glow, gem, stars).
     */
    private const PACK_RARITY_BY_SLUG = [
        'catsunicmass'              => 'mythic',     // 120 video stickers — the showpiece
        'cherryblack'               => 'legendary',
        'prettysailormoon'          => 'epic',
        'jjks2_pt2'                 => 'mythic',
        'skalddealsex_by_fstikbot'  => 'epic',
        'biscvit_vk'                => 'rare',
        'sti_586dd_by_tgemodzibot'  => 'rare',
    ];

    /**
     * Curated tier for imported packs, or a price-derived fallback for any
     * future pack. Floored at "rare": the common tier was retired with the
     * Noto packs.
     */
    private function packRarity(StickerPack $pack, $price): string
    {
        if (isset(self::PACK_RARITY_BY_SLUG[$pack->slug])) {
            return self::PACK_RARITY_BY_SLUG[$pack->slug];
        }
        $price = (int) $price;

        return $price >= 400 ? 'legendary'
            : ($price >= 300 ? 'mythic' : ($price >= 200 ? 'epic' : 'rare'));
    }

    /**
     * Mirrors the spotlight's rarity rule (resources/views/market.blade.php,
     * $rarityOf): cosmetics priced 250+ are legendary. Sticker packs follow
     * their showcase tier (packRarity).
     */
    private function isLegendary(ShopItem $item): bool
    {
        if ($item->category === 'sticker_pack') {
            $pack = $item->stickerPack;

            return $pack !== null && $this->packRarity($pack, $item->price) === 'legendary';
        }

        return in_array($item->category, ['border', 'nickname_color', 'theme'])
            && (int) $item->price >= 250;
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
            'theme'          => 'cosmetic_theme',
            default          => null,
        };
    }
}
