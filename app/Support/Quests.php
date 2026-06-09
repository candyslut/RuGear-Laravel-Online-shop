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
 *   visit        — opening the dashboard (auto-completes the day it's drawn)
 *   comment      — leaving a top-level review
 *   like_give    — liking someone else's comment
 *   game_play    — clearing a mini-game level
 *   add_to_cart  — adding a product to the cart
 *   order        — paying for an order
 */
class Quests
{
    /** @var array<string, array{title:string, description:string, type:string, goal:int, coins:int, xp:int}> */
    private const POOL = [
        'daily_visit' => [
            'title'       => 'Отметься на базе',
            'description' => 'Загляните в личный кабинет сегодня.',
            'type'        => 'visit',
            'goal'        => 1,
            'coins'       => 5,
            'xp'          => 10,
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
        'like_3' => [
            'title'       => 'Поддержи своих',
            'description' => 'Поставьте лайк трём чужим отзывам.',
            'type'        => 'like_give',
            'goal'        => 3,
            'coins'       => 10,
            'xp'          => 15,
        ],
        'like_5' => [
            'title'       => 'Душа сообщества',
            'description' => 'Поставьте пять лайков чужим отзывам.',
            'type'        => 'like_give',
            'goal'        => 5,
            'coins'       => 18,
            'xp'          => 25,
        ],
        'game_2' => [
            'title'       => 'Разминка для глаз',
            'description' => 'Пройдите два уровня в Buzzword Blast или возьмите два рубежа в Redline Rush.',
            'type'        => 'game_play',
            'goal'        => 2,
            'coins'       => 12,
            'xp'          => 20,
        ],
        'game_4' => [
            'title'       => 'Космический ас',
            'description' => 'Пройдите четыре уровня в Buzzword Blast или возьмите четыре рубежа в Redline Rush.',
            'type'        => 'game_play',
            'goal'        => 4,
            'coins'       => 22,
            'xp'          => 35,
        ],
        'cart_1' => [
            'title'       => 'Присмотрись к железу',
            'description' => 'Добавьте любой товар в корзину.',
            'type'        => 'add_to_cart',
            'goal'        => 1,
            'coins'       => 8,
            'xp'          => 10,
        ],
        'order_1' => [
            'title'       => 'Сделка дня',
            'description' => 'Оформите и оплатите заказ.',
            'type'        => 'order',
            'goal'        => 1,
            'coins'       => 40,
            'xp'          => 50,
        ],
    ];

    /** How many quests a user gets each day. */
    public const DAILY_COUNT = 3;

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

    /** Inner SVG markup for a 24×24 stroke icon, keyed by quest type. */
    public static function icon(string $type): string
    {
        return self::ICONS[$type] ?? self::ICONS['visit'];
    }

    private const ICONS = [
        'visit' =>
            '<path d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>',
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
