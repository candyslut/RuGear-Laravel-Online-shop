<?php

namespace App\Services;

use App\Models\FeedEvent;
use App\Models\User;
use Illuminate\Support\Facades\Cache;

/**
 * Records public platform activity into the `feed_events` stream that powers
 * the site-wide "Live Feed". Called from the same central choke points that
 * already drive NotificationService (User::awardAchievement / addExperience /
 * handleRankUp and OrderController::store), so wiring stays minimal.
 *
 * Recording never throws into the originating action — a broken feed must not
 * block an order or an achievement.
 */
class FeedService
{
    /** Fallback accent colour per event type (overridable via $extra['color']). */
    private const TYPE_COLORS = [
        'achievement'  => '#fb923c', // warm orange — matches the achievement toast
        'level_up'     => '#22d3ee', // cyan — matches the level-up toast
        'rank_up'      => '#fbbf24', // gold — overridden by the tier colour
        'order'        => '#10b981', // green — matches the success/quest toast
        'registration' => '#818cf8', // indigo — a fresh "new player" accent
    ];

    /**
     * @param array{title?:string|null, color?:string|null} $extra
     */
    public function record(string $type, User $user, array $extra = []): void
    {
        try {
            $title = $extra['title'] ?? null;

            // A level/rank/achievement milestone is only reached once, so guard
            // against duplicates: the same XP boundary can be applied twice in a
            // request via two User instances (or a double-submit), which would
            // otherwise record the same "Уровень 3" / rank / achievement twice.
            // Orders are intentionally repeatable, so they skip this check.
            if ($type !== 'order'
                && FeedEvent::where('user_id', $user->id)
                    ->where('type', $type)
                    ->where('title', $title)
                    ->exists()) {
                return;
            }

            FeedEvent::create([
                'type'    => $type,
                'user_id' => $user->id,
                'name'    => $user->name,
                'avatar'  => $user->avatar,
                'title'   => $title,
                'color'   => $extra['color'] ?? self::TYPE_COLORS[$type] ?? null,
            ]);

            // Freshly recorded activity should appear within the poll window.
            Cache::forget('feed:recent');

            $this->pruneOccasionally();
        } catch (\Throwable $e) {
            // Swallow — the feed is a non-critical, best-effort side effect.
            report($e);
        }
    }

    /**
     * Keep the table bounded without a scheduler: roughly 1 call in 50 trims
     * everything older than 30 days. Cheap on a low-traffic site, and the feed
     * only ever reads the newest ~40 rows anyway.
     */
    private function pruneOccasionally(): void
    {
        if (random_int(1, 50) !== 1) {
            return;
        }

        FeedEvent::where('created_at', '<', now()->subDays(30))->delete();
    }
}
