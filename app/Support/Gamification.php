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
        ['min' => 50, 'index' => 5, 'name' => 'Алмаз',      'code' => 'DIAMOND',  'color' => '#60a5fa'],
        ['min' => 35, 'index' => 4, 'name' => 'Платина',    'code' => 'PLATINUM', 'color' => '#2dd4bf'],
        ['min' => 20, 'index' => 3, 'name' => 'Золото',     'code' => 'GOLD',     'color' => '#f5a623'],
        ['min' => 10, 'index' => 2, 'name' => 'Серебро',    'code' => 'SILVER',   'color' => '#9aa4b2'],
        ['min' => 5,  'index' => 1, 'name' => 'Бронза',     'code' => 'BRONZE',   'color' => '#cd7c3a'],
        ['min' => 1,  'index' => 0, 'name' => 'Новобранец', 'code' => 'RECRUIT',  'color' => '#7d8694'],
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
     * @return array{tier:string, label:string, color:string}
     */
    public static function rarity(Achievement $achievement): array
    {
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
    ];
}
