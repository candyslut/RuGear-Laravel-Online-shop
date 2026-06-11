<?php

namespace App\Support;

use App\Models\GamePlay;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Single source of truth for the mini-game statistics layer.
 *
 * Two concerns live here:
 *   1. The game REGISTRY (GAMES) — display name, accent colour, score unit and
 *      icon for each mini-game. Adding a game = one entry here (+ a client hook
 *      that POSTs to /game/play). The recorder, the stats modal and the
 *      leaderboards all read this so names/colours/units never drift.
 *   2. AGGREGATIONS over the game_plays table — per-game summaries, daily
 *      series, histograms, records and global ranks that feed the inline-SVG
 *      widgets. Everything is derived from rows, so there are no per-game
 *      counter columns to keep in sync.
 *
 * Score semantics differ per game (metres in Redline, points in Buzzword) but
 * the table and these helpers stay game-agnostic — the registry carries the
 * unit/label so the UI can phrase each number correctly.
 */
class GameStats
{
    /**
     * Game registry. `color` is a static hex (for any non-CSS use); `accent` is
     * a theme-aware CSS var (defined in partials/stats/styles.blade.php) so each
     * game keeps its banner colour across every site theme — buzzword is mint
     * (matching its dashboard banner), redline is red. `extras` are per-run
     * counters stored in game_plays.meta and summed into bonus stat tiles.
     *
     * @var array<string, array{
     *   key:string, name:string, short:string, color:string, accent:string,
     *   unit:string, unit_long:string, score_label:string, level_label:string,
     *   icon:string, extras:list<array{key:string,label:string,icon:string}>
     * }>
     */
    private const GAMES = [
        'buzzword' => [
            'key'         => 'buzzword',
            'name'        => 'Buzzword Blast',
            'short'       => 'Buzzword',
            'color'       => '#20f8c0',
            'accent'      => 'var(--gs-bw, #20f8c0)',
            'unit'        => 'очк.',
            'unit_long'   => 'очков',
            'score_label' => 'Счёт',
            'level_label' => 'Уровень',
            // Взрывная звезда-астероид — мотив аркады.
            'icon'        => '<path d="M11.05 2.93c.3-.92 1.6-.92 1.9 0l1.52 4.67a1 1 0 0 0 .95.69h4.91c.97 0 1.37 1.24.59 1.81l-3.98 2.89a1 1 0 0 0-.36 1.11l1.52 4.68c.3.92-.76 1.68-1.54 1.11l-3.98-2.88a1 1 0 0 0-1.17 0l-3.98 2.88c-.78.57-1.84-.19-1.54-1.11l1.52-4.68a1 1 0 0 0-.36-1.11L2.54 10.8c-.78-.57-.38-1.81.59-1.81h4.91a1 1 0 0 0 .95-.69l1.52-4.67z"/>',
            'extras'      => [
                // Астероид — подбитые камни.
                ['key' => 'asteroids', 'label' => 'Подбито астероидов', 'icon' => '<circle cx="12" cy="12" r="8"/><path d="M9 8.5 11 7l2 1.5M8 13l1.5 2.5M15.5 11l1 3"/>'],
                // Череп — потерянные жизни.
                ['key' => 'deaths', 'label' => 'Смертей', 'icon' => '<path d="M12 2a8 8 0 0 0-5 14.3V20a1 1 0 0 0 1 1h8a1 1 0 0 0 1-1v-3.7A8 8 0 0 0 12 2z"/><circle cx="9" cy="12" r="1.4"/><circle cx="15" cy="12" r="1.4"/>'],
            ],
        ],
        'redline' => [
            'key'         => 'redline',
            'name'        => 'Redline Rush',
            'short'       => 'Redline',
            'color'       => '#f87171',
            'accent'      => 'var(--gs-rl, #f87171)',
            'unit'        => 'м',
            'unit_long'   => 'метров',
            'score_label' => 'Дистанция',
            'level_label' => 'Рубежи',
            // Молния — скоростной раннер.
            'icon'        => '<path d="M13 10V3L4 14h7v7l9-11h-7z"/>',
            'extras'      => [
                // Череп — заезды, оборвавшиеся столкновением.
                ['key' => 'died', 'label' => 'Разбился раз', 'icon' => '<path d="M12 2a8 8 0 0 0-5 14.3V20a1 1 0 0 0 1 1h8a1 1 0 0 0 1-1v-3.7A8 8 0 0 0 12 2z"/><circle cx="9" cy="12" r="1.4"/><circle cx="15" cy="12" r="1.4"/>'],
                // Стрелка вверх — прыжки.
                ['key' => 'jumps', 'label' => 'Прыжков', 'icon' => '<path d="M12 20V6M6 12l6-6 6 6"/>'],
            ],
        ],
    ];

    /** Whitelisted meta counter keys for a game (used to sanitise client input). */
    public static function metaKeys(?string $game): array
    {
        return array_map(fn ($e) => $e['key'], self::GAMES[$game]['extras'] ?? []);
    }

    /** @return array<string, array> */
    public static function games(): array
    {
        return self::GAMES;
    }

    /** @return list<string> */
    public static function keys(): array
    {
        return array_keys(self::GAMES);
    }

    public static function game(?string $key): ?array
    {
        return self::GAMES[$key] ?? null;
    }

    public static function exists(?string $key): bool
    {
        return $key !== null && isset(self::GAMES[$key]);
    }

    /** Accent colour for a game, falling back to a neutral grey. */
    public static function color(?string $key): string
    {
        return self::GAMES[$key]['color'] ?? '#7d8694';
    }

    // ──────────────────────────────────────────────────────────────────────
    //  Aggregations over game_plays
    // ──────────────────────────────────────────────────────────────────────

    /**
     * Per-game career summary for one user: counts, records, totals, averages
     * and timestamps in a single grouped query (+ the most recent run's score).
     *
     * @return array{game:string, plays:int, best:int, total:int, avg:int,
     *   time_ms:int, coins:int, xp:int, best_level:int, last_score:int,
     *   first_at:?Carbon, last_at:?Carbon}
     */
    public static function summary(int $userId, string $game): array
    {
        $row = GamePlay::query()
            ->where('user_id', $userId)
            ->where('game', $game)
            ->selectRaw('COUNT(*) AS plays')
            ->selectRaw('COALESCE(MAX(score), 0) AS best')
            ->selectRaw('COALESCE(SUM(score), 0) AS total')
            ->selectRaw('COALESCE(AVG(score), 0) AS avg_score')
            ->selectRaw('COALESCE(SUM(duration_ms), 0) AS time_ms')
            ->selectRaw('COALESCE(SUM(coins_earned), 0) AS coins')
            ->selectRaw('COALESCE(SUM(xp_earned), 0) AS xp')
            ->selectRaw('COALESCE(MAX(level), 0) AS best_level')
            ->selectRaw('MIN(created_at) AS first_at')
            ->selectRaw('MAX(created_at) AS last_at')
            ->first();

        $lastScore = (int) (GamePlay::query()
            ->where('user_id', $userId)
            ->where('game', $game)
            ->latest('id')
            ->value('score') ?? 0);

        return [
            'game'       => $game,
            'plays'      => (int) ($row->plays ?? 0),
            'best'       => (int) ($row->best ?? 0),
            'total'      => (int) ($row->total ?? 0),
            'avg'        => (int) round($row->avg_score ?? 0),
            'time_ms'    => (int) ($row->time_ms ?? 0),
            'coins'      => (int) ($row->coins ?? 0),
            'xp'         => (int) ($row->xp ?? 0),
            'best_level' => (int) ($row->best_level ?? 0),
            'last_score' => $lastScore,
            'first_at'   => $row->first_at ? Carbon::parse($row->first_at) : null,
            'last_at'    => $row->last_at ? Carbon::parse($row->last_at) : null,
            'extras'     => self::extraSums($userId, $game),
        ];
    }

    /**
     * Lifetime totals for a game's per-run meta counters (deaths, asteroids,
     * jumps…), summed straight out of the JSON column in one query.
     *
     * @return list<array{key:string, label:string, icon:string, value:int}>
     */
    public static function extraSums(int $userId, string $game): array
    {
        $defs = self::GAMES[$game]['extras'] ?? [];
        if (! $defs) {
            return [];
        }

        $q = GamePlay::query()->where('user_id', $userId)->where('game', $game);
        foreach ($defs as $def) {
            // JSON path is from our own whitelist, so it's safe to interpolate.
            $q->selectRaw(
                "COALESCE(SUM(CAST(JSON_EXTRACT(meta, '$.{$def['key']}') AS UNSIGNED)), 0) AS x_{$def['key']}"
            );
        }
        $row = $q->first();

        return array_map(fn ($def) => [
            'key'   => $def['key'],
            'label' => $def['label'],
            'icon'  => $def['icon'],
            'value' => (int) ($row->{'x_' . $def['key']} ?? 0),
        ], $defs);
    }

    /**
     * Runs grouped by weekday (Mon→Sun) for the donut chart. Always returns all
     * seven days so the ring is stable even with sparse data.
     *
     * @return list<array{label:string, value:int}>
     */
    public static function weekdayActivity(int $userId, string $game): array
    {
        // WEEKDAY(): 0 = Monday … 6 = Sunday.
        $counts = GamePlay::query()
            ->where('user_id', $userId)
            ->where('game', $game)
            ->groupBy(DB::raw('WEEKDAY(created_at)'))
            ->selectRaw('WEEKDAY(created_at) AS wd')
            ->selectRaw('COUNT(*) AS c')
            ->pluck('c', 'wd');

        $labels = ['Пн', 'Вт', 'Ср', 'Чт', 'Пт', 'Сб', 'Вс'];

        return array_map(
            fn ($i) => ['label' => $labels[$i], 'value' => (int) ($counts[$i] ?? 0)],
            array_keys($labels)
        );
    }

    /**
     * Cross-game headline numbers for the dashboard widget: total runs, total
     * play time, coins/XP earned from games and the favourite (most-played)
     * game. One grouped query, then folded in PHP.
     *
     * @return array{plays:int, time_ms:int, coins:int, xp:int, games:int,
     *   favorite:?string, last_at:?Carbon, per_game:array<string,array{plays:int,best:int}>}
     */
    public static function overview(int $userId): array
    {
        $rows = GamePlay::query()
            ->where('user_id', $userId)
            ->groupBy('game')
            ->selectRaw('game')
            ->selectRaw('COUNT(*) AS plays')
            ->selectRaw('COALESCE(MAX(score), 0) AS best')
            ->selectRaw('COALESCE(SUM(duration_ms), 0) AS time_ms')
            ->selectRaw('COALESCE(SUM(coins_earned), 0) AS coins')
            ->selectRaw('COALESCE(SUM(xp_earned), 0) AS xp')
            ->selectRaw('MAX(created_at) AS last_at')
            ->get();

        $plays = $time = $coins = $xp = 0;
        $favorite = null;
        $favPlays = -1;
        $lastAt = null;
        $perGame = [];

        foreach ($rows as $r) {
            $plays += (int) $r->plays;
            $time  += (int) $r->time_ms;
            $coins += (int) $r->coins;
            $xp    += (int) $r->xp;
            $perGame[$r->game] = ['plays' => (int) $r->plays, 'best' => (int) $r->best];

            if ((int) $r->plays > $favPlays) {
                $favPlays = (int) $r->plays;
                $favorite = $r->game;
            }
            $at = $r->last_at ? Carbon::parse($r->last_at) : null;
            if ($at && (! $lastAt || $at->gt($lastAt))) {
                $lastAt = $at;
            }
        }

        return [
            'plays'    => $plays,
            'time_ms'  => $time,
            'coins'    => $coins,
            'xp'       => $xp,
            'games'    => $rows->count(),
            'favorite' => $favorite,
            'last_at'  => $lastAt,
            'per_game' => $perGame,
        ];
    }

    /**
     * Daily activity for the last $days days, gap-filled with zeros so the line
     * and bar charts always have a continuous axis. Ordered oldest → newest.
     *
     * @return list<array{date:string, label:string, plays:int, best:int, total:int, avg:int}>
     */
    public static function dailySeries(int $userId, string $game, int $days = 30): array
    {
        $from = Carbon::today()->subDays($days - 1);

        $rows = GamePlay::query()
            ->where('user_id', $userId)
            ->where('game', $game)
            ->where('created_at', '>=', $from->copy()->startOfDay())
            ->groupBy(DB::raw('DATE(created_at)'))
            ->selectRaw('DATE(created_at) AS d')
            ->selectRaw('COUNT(*) AS plays')
            ->selectRaw('COALESCE(MAX(score), 0) AS best')
            ->selectRaw('COALESCE(SUM(score), 0) AS total')
            ->get()
            ->keyBy('d');

        $series = [];
        for ($i = 0; $i < $days; $i++) {
            $day = $from->copy()->addDays($i);
            $key = $day->toDateString();
            $r = $rows->get($key);
            $plays = $r ? (int) $r->plays : 0;
            $total = $r ? (int) $r->total : 0;

            $series[] = [
                'date'  => $key,
                'label' => $day->format('d.m'),
                'plays' => $plays,
                'best'  => $r ? (int) $r->best : 0,
                'total' => $total,
                'avg'   => $plays > 0 ? (int) round($total / $plays) : 0,
            ];
        }

        return $series;
    }

    /**
     * Score distribution across $buckets equal bands from 0 to the user's best.
     * Feeds the histogram. Returns empty when the player has no runs yet.
     *
     * @return list<array{from:int, to:int, label:string, count:int}>
     */
    public static function histogram(int $userId, string $game, int $buckets = 6): array
    {
        $scores = GamePlay::query()
            ->where('user_id', $userId)
            ->where('game', $game)
            ->pluck('score');

        if ($scores->isEmpty()) {
            return [];
        }

        $max = max(1, (int) $scores->max());
        // Round the band width up to a tidy step so labels read cleanly.
        $step = (int) max(1, ceil($max / $buckets));

        $bands = [];
        for ($i = 0; $i < $buckets; $i++) {
            $from = $i * $step;
            $to   = $from + $step;
            $bands[] = [
                'from'  => $from,
                'to'    => $to,
                'label' => self::compact($from) . '–' . self::compact($to),
                'count' => 0,
            ];
        }

        foreach ($scores as $s) {
            $idx = min($buckets - 1, intdiv((int) $s, $step));
            $bands[$idx]['count']++;
        }

        return $bands;
    }

    /**
     * The player's best runs for a game (default top 5), newest-first on ties.
     *
     * @return Collection<int, GamePlay>
     */
    public static function bests(int $userId, string $game, int $limit = 5): Collection
    {
        return GamePlay::query()
            ->where('user_id', $userId)
            ->where('game', $game)
            ->orderByDesc('score')
            ->orderByDesc('id')
            ->limit($limit)
            ->get();
    }

    /**
     * Where the user's personal best for a game ranks among all players who
     * have ever played it (1-based). null rank when they have no scoring run.
     *
     * @return array{rank:?int, total:int, best:int, percentile:?int}
     */
    public static function globalRank(int $userId, string $game): array
    {
        $myBest = (int) GamePlay::query()
            ->where('user_id', $userId)
            ->where('game', $game)
            ->max('score');

        $total = (int) GamePlay::query()
            ->where('game', $game)
            ->distinct()
            ->count('user_id');

        if ($myBest <= 0) {
            return ['rank' => null, 'total' => $total, 'best' => 0, 'percentile' => null];
        }

        // How many distinct players have a personal best strictly above mine.
        // Select only the grouped column so ONLY_FULL_GROUP_BY stays happy.
        $better = (int) GamePlay::query()
            ->where('game', $game)
            ->select('user_id')
            ->groupBy('user_id')
            ->havingRaw('MAX(score) > ?', [$myBest])
            ->get()
            ->count();

        $rank = $better + 1;
        $percentile = $total > 1
            ? (int) round((1 - ($rank - 1) / $total) * 100)
            : 100;

        return ['rank' => $rank, 'total' => $total, 'best' => $myBest, 'percentile' => $percentile];
    }

    /**
     * Leaderboard rows for a game: each player's best score (optionally limited
     * to the last $days days for a "this week" board), richest first. Eager
     * loads the user model onto each row for avatars/names.
     *
     * @return Collection<int, object{user:?User, best:int, best_level:int, plays:int, last_at:?Carbon}>
     */
    public static function topPlayers(string $game, ?int $days = null, int $limit = 10): Collection
    {
        $q = GamePlay::query()->where('game', $game);

        if ($days !== null) {
            $q->where('created_at', '>=', Carbon::now()->subDays($days));
        }

        $rows = $q->groupBy('user_id')
            ->selectRaw('user_id')
            ->selectRaw('MAX(score) AS best')
            ->selectRaw('MAX(level) AS best_level')
            ->selectRaw('COUNT(*) AS plays')
            ->selectRaw('MAX(created_at) AS last_at')
            ->orderByDesc('best')
            ->orderBy('last_at')
            ->limit($limit)
            ->get();

        $users = User::query()
            ->whereIn('id', $rows->pluck('user_id'))
            ->get()
            ->keyBy('id');

        return $rows->map(fn ($r) => (object) [
            'user'       => $users->get($r->user_id),
            'best'       => (int) $r->best,
            'best_level' => (int) $r->best_level,
            'plays'      => (int) $r->plays,
            'last_at'    => $r->last_at ? Carbon::parse($r->last_at) : null,
        ])->filter(fn ($r) => $r->user !== null)->values();
    }

    // ──────────────────────────────────────────────────────────────────────
    //  Formatting helpers (shared by every widget so units never drift)
    // ──────────────────────────────────────────────────────────────────────

    /** A score with its game's unit, e.g. "12 480 м" or "5 400 очк.". */
    public static function formatScore(?string $game, int $value): string
    {
        $unit = self::GAMES[$game]['unit'] ?? '';

        return trim(self::spaced($value) . ' ' . $unit);
    }

    /** Thousands-separated integer using a thin space (e.g. 12 480). */
    public static function spaced(int $value): string
    {
        return number_format($value, 0, '.', ' ');
    }

    /** Compact magnitude for tight axis labels: 1 200 → "1.2k". */
    public static function compact(int $value): string
    {
        if ($value >= 1000000) {
            return rtrim(rtrim(number_format($value / 1000000, 1, '.', ''), '0'), '.') . 'M';
        }
        if ($value >= 1000) {
            return rtrim(rtrim(number_format($value / 1000, 1, '.', ''), '0'), '.') . 'k';
        }

        return (string) $value;
    }

    /** Human play time: "1 ч 12 мин", "5 мин 30 с", "42 с". */
    public static function formatDuration(int $ms): string
    {
        $s = intdiv($ms, 1000);

        if ($s >= 3600) {
            $h = intdiv($s, 3600);
            $m = intdiv($s % 3600, 60);

            return $m > 0 ? "{$h} ч {$m} мин" : "{$h} ч";
        }

        if ($s >= 60) {
            $m = intdiv($s, 60);
            $rem = $s % 60;

            return $rem > 0 ? "{$m} мин {$rem} с" : "{$m} мин";
        }

        return "{$s} с";
    }
}
