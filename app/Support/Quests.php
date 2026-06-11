<?php

namespace App\Support;

use App\Models\User;
use Illuminate\Support\Carbon;

/**
 * Single source of truth for daily quests — the template pool, its rewards and
 * icon artwork. Three templates are drawn from POOL for each user each day
 * (see App\Services\DailyQuestService); the draw is deterministic per user+day
 * so reloading the dashboard never reshuffles the day's quests.
 *
 * `type` is the progress event a quest listens for; services call
 * DailyQuestService::progress($user, $type) from the matching action:
 *   product_view — opening a product page (deduped per product per day)
 *   compare      — adding a product to the comparison
 *   comment      — leaving a top-level review
 *   like_give    — liking someone else's comment
 *   game_play    — clearing a mini-game level / distance milestone
 *   add_to_cart  — adding a product to the cart
 *   order        — paying for an order
 */
class Quests
{
    /** @var array<string, array{title:string, description:string, type:string, goal:int, coins:int, xp:int}> */
    private const POOL = [
        'view_5' => [
            'title'       => 'Разведка рынка',
            'description' => 'Изучите пять разных карточек товаров.',
            'type'        => 'product_view',
            'goal'        => 5,
            'coins'       => 10,
            'xp'          => 15,
        ],
        'view_10' => [
            'title'       => 'Глубокое погружение',
            'description' => 'Просмотрите десять разных товаров за день.',
            'type'        => 'product_view',
            'goal'        => 10,
            'coins'       => 18,
            'xp'          => 30,
        ],
        'compare_2' => [
            'title'       => 'Дуэль железа',
            'description' => 'Добавьте два товара одного типа в сравнение.',
            'type'        => 'compare',
            'goal'        => 2,
            'coins'       => 12,
            'xp'          => 20,
        ],
        'comment_1' => [
            'title'       => 'Слово за тобой',
            'description' => 'Оставьте один отзыв к любому товару.',
            'type'        => 'comment',
            'goal'        => 1,
            'coins'       => 10,
            'xp'          => 15,
        ],
        'comment_2' => [
            'title'       => 'На связи с сообществом',
            'description' => 'Оставьте два отзыва за день.',
            'type'        => 'comment',
            'goal'        => 2,
            'coins'       => 18,
            'xp'          => 25,
        ],
        'comment_3' => [
            'title'       => 'Главный обозреватель',
            'description' => 'Оставьте три отзыва за день. Настоящая колонка эксперта!',
            'type'        => 'comment',
            'goal'        => 3,
            'coins'       => 28,
            'xp'          => 45,
        ],
        'like_5' => [
            'title'       => 'Поддержи своих',
            'description' => 'Поставьте лайк пяти чужим отзывам.',
            'type'        => 'like_give',
            'goal'        => 5,
            'coins'       => 12,
            'xp'          => 18,
        ],
        'like_10' => [
            'title'       => 'Душа сообщества',
            'description' => 'Поставьте десять лайков чужим отзывам.',
            'type'        => 'like_give',
            'goal'        => 10,
            'coins'       => 22,
            'xp'          => 35,
        ],
        'game_3' => [
            'title'       => 'Разминка для глаз',
            'description' => 'Пройдите три уровня в Buzzword Blast или возьмите три рубежа в Redline Rush.',
            'type'        => 'game_play',
            'goal'        => 3,
            'coins'       => 14,
            'xp'          => 22,
        ],
        'game_6' => [
            'title'       => 'Космический ас',
            'description' => 'Пройдите шесть уровней в Buzzword Blast или возьмите шесть рубежей в Redline Rush.',
            'type'        => 'game_play',
            'goal'        => 6,
            'coins'       => 25,
            'xp'          => 40,
        ],
        'game_10' => [
            'title'       => 'Аркадный марафонец',
            'description' => 'Возьмите десять игровых рубежей за день — уровни и дистанции считаются вместе.',
            'type'        => 'game_play',
            'goal'        => 10,
            'coins'       => 40,
            'xp'          => 65,
        ],
        'cart_2' => [
            'title'       => 'Присмотрись к железу',
            'description' => 'Добавьте два товара в корзину.',
            'type'        => 'add_to_cart',
            'goal'        => 2,
            'coins'       => 10,
            'xp'          => 12,
        ],
        'order_1' => [
            'title'       => 'Сделка дня',
            'description' => 'Оформите и оплатите заказ.',
            'type'        => 'order',
            'goal'        => 1,
            'coins'       => 40,
            'xp'          => 60,
        ],
    ];

    /** How many quests a user gets each day. */
    public const DAILY_COUNT = 3;

    /** Reroll: swap every still-active quest for random unused ones. */
    public const REROLL_COST = 25;

    /** @return array<string, array> */
    public static function all(): array
    {
        return self::POOL;
    }

    public static function get(?string $slug): ?array
    {
        return self::POOL[$slug] ?? null;
    }

    public static function exists(string $slug): bool
    {
        return isset(self::POOL[$slug]);
    }

    /**
     * The (deterministic) set of quest slugs for a given user and day. Seeding
     * the shuffle with user id + date means the choice is stable across reloads
     * yet differs per user and rotates daily.
     *
     * @return list<string>
     */
    public static function pickFor(User $user, Carbon $date): array
    {
        $slugs = array_keys(self::POOL);

        // Seeded Fisher–Yates so the order is reproducible for this user/day.
        $seed = crc32($user->id . ':' . $date->toDateString());
        mt_srand($seed);
        for ($i = count($slugs) - 1; $i > 0; $i--) {
            $j = mt_rand(0, $i);
            [$slugs[$i], $slugs[$j]] = [$slugs[$j], $slugs[$i]];
        }
        mt_srand(); // restore non-deterministic randomness for the rest of the request

        return array_slice($slugs, 0, self::DAILY_COUNT);
    }

    /**
     * Inner SVG markup for a 24×24 stroke icon. Every quest has its own
     * artwork (keyed by slug); unknown slugs fall back to their type's icon
     * so a retired catalog entry still renders something sensible.
     */
    public static function icon(string $slug, ?string $type = null): string
    {
        return self::ICONS[$slug]
            ?? self::TYPE_ICONS[$type ?? self::POOL[$slug]['type'] ?? '']
            ?? self::TYPE_ICONS['visit'];
    }

    /** Per-quest artwork — each daily card gets a unique pictogram. */
    private const ICONS = [
        // Глаз: быстрый осмотр витрины.
        'view_5' =>
            '<path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7S1 12 1 12z"/><circle cx="12" cy="12" r="3"/>',
        // Лупа: глубокий ресёрч каталога.
        'view_10' =>
            '<circle cx="11" cy="11" r="7"/><line x1="21" y1="21" x2="16.65" y2="16.65"/><line x1="11" y1="8" x2="11" y2="14"/><line x1="8" y1="11" x2="14" y2="11"/>',
        // Весы: сравнение двух товаров.
        'compare_2' =>
            '<line x1="12" y1="3" x2="12" y2="21"/><path d="M8 21h8"/><path d="M5 7h14"/><path d="M5 7l-2.5 6a3 3 0 005 0L5 7z"/><path d="M19 7l-2.5 6a3 3 0 005 0L19 7z"/>',
        // Один пузырь диалога.
        'comment_1' =>
            '<path d="M21 15a2 2 0 01-2 2H7l-4 4V5a2 2 0 012-2h14a2 2 0 012 2z"/>' .
            '<line x1="9" y1="10" x2="15" y2="10"/><line x1="9" y1="14" x2="13" y2="14"/>',
        // Два пузыря: серия отзывов.
        'comment_2' =>
            '<path d="M14 9a2 2 0 01-2 2H6l-4 4V4a2 2 0 012-2h8a2 2 0 012 2z"/>' .
            '<path d="M18 9h2a2 2 0 012 2v11l-4-4h-6a2 2 0 01-2-2v-1"/>',
        // Рупор: главный обозреватель дня.
        'comment_3' =>
            '<path d="M3 11l18-7-4 14-6-3-3 4-1-6-4-2z"/>',
        // Сердце: поддержка лайками.
        'like_5' =>
            '<path d="M20.8 4.6a5.5 5.5 0 0 0-7.8 0L12 5.7l-1.1-1.1a5.5 5.5 0 0 0-7.8 7.8l1.1 1.1L12 21l7.8-7.8 1-1a5.5 5.5 0 0 0 0-7.6z"/>',
        // Сердце с искрой: лайковый марафон.
        'like_10' =>
            '<path d="M19.5 7.6a4.6 4.6 0 0 0-6.5 0l-1 1-1-1a4.6 4.6 0 0 0-6.5 6.5l.9.9 6.6 6.5 6.6-6.5.9-.9a4.6 4.6 0 0 0 0-6.5z"/>' .
            '<path d="M19 1.5l.7 1.8 1.8.7-1.8.7-.7 1.8-.7-1.8-1.8-.7 1.8-.7z"/>',
        // Молния: лёгкая игровая разминка.
        'game_3' =>
            '<path d="M13 10V3L4 14h7v7l9-11h-7z"/>',
        // Геймпад: уверенная игровая сессия.
        'game_6' =>
            '<path d="M6 11h4M8 9v4"/><circle cx="15.5" cy="10.5" r=".75"/><circle cx="18" cy="13" r=".75"/>' .
            '<path d="M17.32 5H6.68a4 4 0 00-3.97 3.6L2 17a2.5 2.5 0 004.39 1.9L9 16h6l2.61 2.9A2.5 2.5 0 0022 17l-.71-8.4A4 4 0 0017.32 5z"/>',
        // Кубок: аркадный марафон.
        'game_10' =>
            '<path d="M8 21h8M12 17v4M7 4h10v5a5 5 0 01-10 0z"/>' .
            '<path d="M7 6H4a1 1 0 00-1 1 4 4 0 004 4M17 6h3a1 1 0 011 1 4 4 0 01-4 4"/>',
        // Корзина: подбор железа.
        'cart_2' =>
            '<circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/>' .
            '<path d="M1 1h4l2.68 13.39a2 2 0 002 1.61h9.72a2 2 0 002-1.61L23 6H6"/>',
        // Коробка заказа: сделка дня.
        'order_1' =>
            '<path d="M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4z"/>' .
            '<line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 01-8 0"/>',
    ];

    /** Fallback artwork per event type (covers slugs retired from the pool). */
    private const TYPE_ICONS = [
        'visit' =>
            '<path d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>',
        'product_view' =>
            '<path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7S1 12 1 12z"/><circle cx="12" cy="12" r="3"/>',
        'compare' =>
            '<line x1="12" y1="3" x2="12" y2="21"/><path d="M8 21h8"/><path d="M5 7h14"/><path d="M5 7l-2.5 6a3 3 0 005 0L5 7z"/><path d="M19 7l-2.5 6a3 3 0 005 0L19 7z"/>',
        'comment' =>
            '<path d="M21 15a2 2 0 01-2 2H7l-4 4V5a2 2 0 012-2h14a2 2 0 012 2z"/>' .
            '<line x1="9" y1="10" x2="15" y2="10"/><line x1="9" y1="14" x2="13" y2="14"/>',
        'like_give' =>
            '<path d="M20.8 4.6a5.5 5.5 0 0 0-7.8 0L12 5.7l-1.1-1.1a5.5 5.5 0 0 0-7.8 7.8l1.1 1.1L12 21l7.8-7.8 1-1a5.5 5.5 0 0 0 0-7.6z"/>',
        'game_play' =>
            '<path d="M13 10V3L4 14h7v7l9-11h-7z"/>',
        'add_to_cart' =>
            '<circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/>' .
            '<path d="M1 1h4l2.68 13.39a2 2 0 002 1.61h9.72a2 2 0 002-1.61L23 6H6"/>',
        'order' =>
            '<path d="M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4z"/>' .
            '<line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 01-8 0"/>',
    ];
}
