<?php

namespace App\Http\Controllers;

use App\Models\FeedEvent;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class FeedController extends Controller
{
    /**
     * The public Live Feed: the newest ~40 events for the under-header strip,
     * plus "event_of_day" — the day's single most powerful event (it stays
     * pinned all day). Global, so one shared 20s cache key keeps polling cheap.
     */
    public function index()
    {
        // Short TTL; also explicitly busted in FeedService::record on every new
        // event, so the next poll always sees fresh data.
        $payload = Cache::remember('feed:recent', 10, function () {
            $recent = FeedEvent::latest()->limit(40)->get();

            // Most powerful event of the day, picked by "weight" (rank > rarity >
            // level > order). Only genuinely notable events qualify (see
            // eodQualifies) so a beginner achievement never wins. If today had
            // nothing notable, fall back to the best of the last 30 days so the
            // plate still shows something worthwhile.
            $eod = $this->pickEod(now()->startOfDay())
                ?? $this->pickEod(now()->subDays(30));

            return [
                'events'       => $recent->map(fn (FeedEvent $e) => $this->toArray($e))->all(),
                'event_of_day' => $eod ? $this->toArray($eod) : null,
            ];
        });

        return response()->json($payload);
    }

    /** Highest-weight qualifying event since $since, newest winning ties (or null). */
    private function pickEod(\Carbon\CarbonInterface $since): ?FeedEvent
    {
        return FeedEvent::where('created_at', '>=', $since)
            ->orderByDesc('id')
            ->limit(500)
            ->get()
            ->filter(fn (FeedEvent $e) => $this->eodQualifies($e))
            ->reduce(function (?FeedEvent $best, FeedEvent $e) {
                if (! $best) {
                    return $e;
                }
                $we = $this->weight($e);
                $wb = $this->weight($best);

                return ($we > $wb || ($we === $wb && $e->id > $best->id)) ? $e : $best;
            });
    }

    private function toArray(FeedEvent $e): array
    {
        return [
            'id'     => $e->id,
            'type'   => $e->type,
            'name'   => $e->name,
            'avatar' => $e->avatar ? Storage::url($e->avatar) : null,
            'value'  => $this->value($e),
            'color'  => $e->color,
            'ago'    => $this->ago($e),
        ];
    }

    /** The variable part shown in a block (the type label is rendered client-side). */
    private function value(FeedEvent $e): string
    {
        return match ($e->type) {
            'achievement' => '«' . $e->title . '»',
            'level_up'    => (string) $e->title,
            'rank_up'     => $e->title,
            'order'       => '',
            default       => '',
        };
    }

    /** Relative time for the hover tooltip; older than ~6h collapses to "недавно". */
    private function ago(FeedEvent $e): string
    {
        if (! $e->created_at) {
            return 'недавно';
        }

        return $e->created_at->gt(now()->subHours(6))
            ? $e->created_at->locale('ru')->diffForHumans()
            : 'недавно';
    }

    /**
     * Only genuinely notable events may become the Event of the Day: any rank-up,
     * a rare-or-better achievement, or a level milestone (10+). Orders and common
     * "first step" achievements never qualify.
     */
    private function eodQualifies(FeedEvent $e): bool
    {
        return match ($e->type) {
            'rank_up'     => true,
            'achievement' => $this->rarityWeight($e->color) >= 400,
            'level_up'    => (int) $e->title >= 10,
            default       => false,
        };
    }

    /** Significance score used to pick the Event of the Day. */
    private function weight(FeedEvent $e): int
    {
        return match ($e->type) {
            'rank_up'     => 1000 + $this->rankIndex($e->title) * 100,
            'achievement' => $this->rarityWeight($e->color),
            'level_up'    => 100 + (int) $e->title,
            'order'       => 50,
            default       => 0,
        };
    }

    private function rankIndex(?string $name): int
    {
        return ['Бронза' => 0, 'Серебро' => 1, 'Золото' => 2, 'Платина' => 3, 'Алмаз' => 4][$name] ?? 0;
    }

    private function rarityWeight(?string $color): int
    {
        // Mirrors App\Support\Gamification rarity colours: legendary→epic→rare→common.
        return ['#f5a623' => 800, '#a855f7' => 600, '#3b82f6' => 400, '#7d8694' => 200][$color] ?? 200;
    }
}
