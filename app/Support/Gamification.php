<?php

namespace App\Support;

use App\Models\Achievement;
use App\Models\User;

/**
 * Single source of truth for the dashboard's game-HUD progression visuals.
 *
 * Everything here is DERIVED — rank tiers come from the user's `level`, and
 * achievement rarity from the achievement's `experience` value — so the
 * gamification layer needs no new columns or migrations. The ProfileHud,
 * Leaderboard and AchievementsModal components all read from here so the
 * tier names, colours and icon set stay identical across the dashboard.
 */
class Gamification
{
    /**
     * Rank tiers keyed by the minimum level required to reach them.
     * `index` drives the number of insignia chevrons drawn in the badge.
     */
    private const TIERS = [
        ['min' => 50, 'index' => 4, 'name' => 'Алмаз',   'code' => 'DIAMOND',  'color' => '#60a5fa', 'reward' => 1000],
        ['min' => 35, 'index' => 3, 'name' => 'Платина', 'code' => 'PLATINUM', 'color' => '#2dd4bf', 'reward' => 500],
        ['min' => 20, 'index' => 2, 'name' => 'Золото',  'code' => 'GOLD',     'color' => '#f5a623', 'reward' => 250],
        ['min' => 10, 'index' => 1, 'name' => 'Серебро', 'code' => 'SILVER',   'color' => '#9aa4b2', 'reward' => 100],
        ['min' => 1,  'index' => 0, 'name' => 'Бронза',  'code' => 'BRONZE',   'color' => '#cd7c3a', 'reward' => 0],
    ];

    /**
     * Achievement rarity bands keyed by the minimum XP reward.
     * (Seed XP spans 10–200, so these four bands spread evenly across it.)
     */
    private const RARITIES = [
        ['min' => 150, 'tier' => 'legendary', 'label' => 'Легендарное', 'color' => '#f5a623'],
        ['min' => 75,  'tier' => 'epic',      'label' => 'Эпическое',   'color' => '#a855f7'],
        ['min' => 30,  'tier' => 'rare',      'label' => 'Редкое',      'color' => '#3b82f6'],
        ['min' => 0,   'tier' => 'common',    'label' => 'Обычное',     'color' => '#7d8694'],
    ];

    /**
     * @return array{name:string, code:string, color:string, index:int, min:int, next:?int, levelInTier:int, tierSpan:int}
     */
    public static function rankTier(int $level): array
    {
        foreach (self::TIERS as $i => $tier) {
            if ($level >= $tier['min']) {
                // The previous element (higher min) is the next tier up, if any.
                $next = $i > 0 ? self::TIERS[$i - 1]['min'] : null;
                $span = ($next ?? ($tier['min'] + 10)) - $tier['min'];

                return [
                    'name'        => $tier['name'],
                    'code'        => $tier['code'],
                    'color'       => $tier['color'],
                    'index'       => $tier['index'],
                    'min'         => $tier['min'],
                    'next'        => $next,
                    'levelInTier' => $level - $tier['min'],
                    'tierSpan'    => max(1, $span),
                ];
            }
        }

        return self::rankTier(1);
    }

    /**
     * Every rank tier, ascending (Новобранец → Алмаз), each with its unlock
     * level (`min`), the level the tier above starts at (`next`, null for the
     * top tier) and its one-time coin `reward`. Drives the "all ranks" modal.
     *
     * @return list<array{name:string, code:string, color:string, index:int, min:int, next:?int, reward:int}>
     */
    public static function tiers(): array
    {
        $asc = array_reverse(self::TIERS);

        return array_map(function (array $tier, int $i) use ($asc) {
            return [
                'name'   => $tier['name'],
                'code'   => $tier['code'],
                'color'  => $tier['color'],
                'index'  => $tier['index'],
                'min'    => $tier['min'],
                'next'   => $asc[$i + 1]['min'] ?? null,
                'reward' => $tier['reward'],
            ];
        }, $asc, array_keys($asc));
    }

    /**
     * Total coin reward for the rank tier(s) newly crossed when a user's level
     * rises from $oldLevel to $newLevel. A single big XP gain can leap several
     * tiers at once, so every tier whose unlock level falls in (old, new] pays
     * out. Returns 0 when no new tier was reached.
     */
    public static function rankRewardBetween(int $oldLevel, int $newLevel): int
    {
        $sum = 0;
        foreach (self::TIERS as $tier) {
            if ($tier['min'] > $oldLevel && $tier['min'] <= $newLevel) {
                $sum += $tier['reward'];
            }
        }

        return $sum;
    }

    /**
     * Per-rank emblem artwork (inner SVG markup for a 24×24 stroke icon), keyed
     * by tier code. Each rank gets a distinct shape; the RanksModal centres it
     * in a rounded-square badge tinted with the tier colour.
     */
    public static function rankIcon(string $code): string
    {
        return self::RANK_ICONS[$code] ?? self::RANK_ICONS['BRONZE'];
    }

    private const RANK_ICONS = [
        // Бронза — a guard's shield (the entry rank).
        'BRONZE' =>
            '<path d="M12 3 5 5.5v5c0 4.2 2.9 6.7 7 8 4.1-1.3 7-3.8 7-8v-5L12 3z"/>',
        // Серебро — a five-point star.
        'SILVER' =>
            '<polygon points="12 3 14.4 8.8 20.7 9.3 15.9 13.4 17.4 19.6 12 16.2 6.6 19.6 8.1 13.4 3.3 9.3 9.6 8.8 12 3"/>',
        // Золото — a victory trophy.
        'GOLD' =>
            '<path d="M8 21h8M12 17v4M7 4h10v4a5 5 0 0 1-10 0V4z"/>' .
            '<path d="M7 5H4.5v1.5A3 3 0 0 0 7.5 9.5M17 5h2.5v1.5a3 3 0 0 1-3 3"/>',
        // Платина — a crown.
        'PLATINUM' =>
            '<path d="M4 8l3.5 3.5L12 5l4.5 6.5L20 8l-1.5 11h-13L4 8z"/>' .
            '<line x1="5.5" y1="19" x2="18.5" y2="19"/>',
        // Алмаз — a faceted gem.
        'DIAMOND' =>
            '<path d="M6.5 3h11l3.5 6-9 12-9-12 3.5-6z"/>' .
            '<path d="M3 9h18M9 3l-2.5 6L12 21M15 3l2.5 6L12 21"/>',
    ];

    /**
     * @return array{tier:string, label:string, color:string}
     */
    public static function rarity(Achievement $achievement): array
    {
        // The registration achievement is a welcome gift everyone gets — it's
        // always "common" regardless of its XP reward, so it never shows up as
        // a rare/epic badge.
        if ($achievement->slug === 'registered') {
            return self::RARITIES[array_key_last(self::RARITIES)];
        }

        $xp = (int) $achievement->experience;

        foreach (self::RARITIES as $band) {
            if ($xp >= $band['min']) {
                return [
                    'tier'  => $band['tier'],
                    'label' => $band['label'],
                    'color' => $band['color'],
                ];
            }
        }

        return self::RARITIES[array_key_last(self::RARITIES)];
    }

    /** Length of the repeating daily-login reward cycle — a six-month wheel. */
    public const STREAK_CYCLE = 180;

    /** UI metadata (RU label + accent colour) for each reward tier.
     *  Colour is a CSS value (rendered only in style attributes of the
     *  profile HUD); var() lets the cosmic theme swap the warm orange. */
    public const STREAK_TIERS = [
        'daily'   => ['label' => 'Ежедневная',   'color' => 'var(--tier-daily, #f97316)'],
        'weekly'  => ['label' => 'Недельная',    'color' => '#22d3ee'],
        'monthly' => ['label' => 'Месячная',     'color' => '#a855f7'],
        'grand'   => ['label' => 'Главный приз', 'color' => '#fbbf24'],
    ];

    /**
     * Position (1-based) within the 180-day reward cycle for a given streak
     * length; 0 when there is no active streak. After day 180 the wheel loops
     * back to day 1.
     */
    public static function streakCycleDay(int $streak): int
    {
        return $streak <= 0 ? 0 : (($streak - 1) % self::STREAK_CYCLE) + 1;
    }

    /**
     * Milestone tier of a given cycle day (1–180): every 7th day is a weekly
     * bonus, every 30th a monthly one, and day 180 the six-month grand finale.
     */
    public static function streakDayTier(int $day): string
    {
        $d = max(1, min($day, self::STREAK_CYCLE));

        if ($d % self::STREAK_CYCLE === 0) return 'grand';
        if ($d % 30 === 0)                 return 'monthly';
        if ($d % 7 === 0)                  return 'weekly';

        return 'daily';
    }

    /**
     * Coins + XP paid out for a given day (1–180), by milestone tier.
     *
     * @return array{coins:int, xp:int, tier:string}
     */
    public static function streakReward(int $day): array
    {
        return match (self::streakDayTier($day)) {
            'grand'   => ['coins' => 300, 'xp' => 250, 'tier' => 'grand'],
            'monthly' => ['coins' => 75,  'xp' => 80,  'tier' => 'monthly'],
            'weekly'  => ['coins' => 30,  'xp' => 35,  'tier' => 'weekly'],
            default   => ['coins' => 5,   'xp' => 10,  'tier' => 'daily'],
        };
    }

    /**
     * Within-level XP progress as a 0–100 integer.
     * Each level costs a flat 100 XP (see User::addExperience), so this is
     * simply the remainder of the user's XP inside the current level.
     */
    public static function levelPercent(User $user): int
    {
        return max(0, min(100, (int) $user->experienceProgress));
    }

    /** XP still needed to reach the next level (0–100). */
    public static function xpToNext(User $user): int
    {
        return max(0, 100 - (int) $user->experienceProgress);
    }

    /**
     * Per-achievement icon artwork (inner SVG markup for a 24×24 stroke icon).
     * Mirrors the slugs defined in the achievement seeder; unknown slugs fall
     * back to a star. Returned strings are trusted, static markup.
     */
    public static function icon(?string $slug): string
    {
        return self::ICONS[$slug] ?? self::DEFAULT_ICON;
    }

    private const DEFAULT_ICON =
        '<polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2" />';

    private const ICONS = [
        'registered' =>
            '<path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z" />' .
            '<polyline points="9 12 11 14 15 10" />',
        'comment_1' =>
            '<path d="M21 15a2 2 0 01-2 2H7l-4 4V5a2 2 0 012-2h14a2 2 0 012 2z" />' .
            '<line x1="9" y1="10" x2="15" y2="10" />' .
            '<line x1="9" y1="14" x2="13" y2="14" />',
        'comment_3' =>
            '<path d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a2 2 0 01-2-2v-1" />' .
            '<path d="M15 5H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l4-4h4a2 2 0 002-2V7a2 2 0 00-2-2z" />',
        'comment_5' =>
            '<circle cx="12" cy="8" r="6" />' .
            '<path d="M15.477 12.89L17 22l-5-3-5 3 1.523-9.11" />',
        'first_order' =>
            '<path d="M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4z" />' .
            '<line x1="3" y1="6" x2="21" y2="6" />' .
            '<path d="M16 10a4 4 0 01-8 0" />',
        'order_10k' =>
            '<circle cx="12" cy="12" r="9" />' .
            '<path d="M11 17V7h1.5a2.5 2.5 0 0 1 0 5h-1.5M9 13.5h6M9 15.5h6" />',
        'order_50k' =>
            '<path d="M3 11C5 7 9 5 13 5c5 0 8 2.5 8 6.5s-3 7.5-8 7.5C9 19 5 17 3 13" />' .
            '<path d="M3 11L1 8M3 13L1 16" />' .
            '<path d="M10 5L12 2L14 5" />' .
            '<circle cx="18" cy="11" r="0.75" fill="currentColor" />',
        'all_categories' =>
            '<rect x="3" y="3" width="7" height="7" rx="1" />' .
            '<rect x="14" y="3" width="7" height="7" rx="1" />' .
            '<rect x="3" y="14" width="7" height="7" rx="1" />' .
            '<rect x="14" y="14" width="7" height="7" rx="1" />',
        // Streak achievements — a flame (matches the reward-wheel motif).
        'streak_7' => self::FLAME_ICON,
        'streak_30' => self::FLAME_ICON,
        'streak_180' => self::FLAME_ICON,
        // Likes given — a heart you hand out.
        'like_giver_1' => self::HEART_ICON,
        'like_giver_25' => self::HEART_ICON,
        'like_giver_100' => self::HEART_ICON,
        // Likes received — a heart with a sparkle of popularity.
        'liked_10' => self::HEART_ICON,
        'liked_50' => self::HEART_ICON,
        // Bought the whole store — a shopping bag.
        'store_complete' =>
            '<path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z" />' .
            '<line x1="3" y1="6" x2="21" y2="6" />' .
            '<path d="M16 10a4 4 0 0 1-8 0" />',
    ];

    private const FLAME_ICON =
        '<path d="M12.5 2c.4 2.8 2.3 4.4 3.6 6 1.2 1.5 1.9 3 1.9 4.8a6 6 0 1 1-12 0c0-1.6.6-3 1.6-4.2.2.9.9 1.5 1.8 1.5 1.2 0 1.8-1 1.3-2.5C11.8 5.4 12 3.6 12.5 2z" />';

    private const HEART_ICON =
        '<path d="M20.8 4.6a5.5 5.5 0 0 0-7.8 0L12 5.7l-1.1-1.1a5.5 5.5 0 0 0-7.8 7.8l1.1 1.1L12 21l7.8-7.8 1-1a5.5 5.5 0 0 0 0-7.6z" />';
}
