<?php

namespace App\Services;

use App\Models\DailyQuest;
use App\Models\User;
use App\Support\Quests;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

/**
 * Daily quests: three per user per day, drawn from App\Support\Quests. The set
 * is created lazily the first time it's needed each day and never reshuffles
 * (the draw is deterministic per user+day). progress() advances any of today's
 * active quests that listen for a given event type, paying out coins + XP and
 * dropping a bell notification the moment one is completed.
 */
class DailyQuestService
{
    /**
     * Today's three quests for the user, creating them if this is the first
     * call of the day. Returned newest-relevant first: active before completed.
     *
     * @return Collection<int, DailyQuest>
     */
    public function todayFor(User $user): Collection
    {
        $today = Carbon::today();

        $existing = $user->dailyQuests()
            ->whereDate('quest_date', $today)
            ->get();

        if ($existing->isEmpty()) {
            $existing = $this->generate($user, $today);
        }

        return $existing
            ->sortBy(fn (DailyQuest $q) => [$q->is_completed ? 1 : 0, $q->id])
            ->values();
    }

    /**
     * Materialise the day's three quests. firstOrCreate keeps it safe against a
     * double request racing to create the same set.
     *
     * @return Collection<int, DailyQuest>
     */
    protected function generate(User $user, Carbon $date): Collection
    {
        $quests = collect(Quests::pickFor($user, $date))->map(function (string $slug) use ($user, $date) {
            $tpl = Quests::get($slug);

            return DailyQuest::firstOrCreate(
                ['user_id' => $user->id, 'quest_date' => $date->toDateString(), 'slug' => $slug],
                [
                    'progress'     => 0,
                    'goal'         => $tpl['goal'],
                    'reward_coins' => $tpl['coins'],
                    'reward_xp'    => $tpl['xp'],
                ],
            );
        });

        return $quests;
    }

    /**
     * Advance every active quest of the given event type by $amount, completing
     * and rewarding any that reach their goal.
     *
     * @return list<array{title:string, coins:int, xp:int}>  Quests just completed
     *         (for surfacing a toast); empty when nothing finished.
     */
    public function progress(User $user, string $type, int $amount = 1): array
    {
        // Slugs in the catalog that react to this event type.
        $typeSlugs = collect(Quests::all())
            ->filter(fn ($tpl) => $tpl['type'] === $type)
            ->keys();

        if ($typeSlugs->isEmpty()) {
            return [];
        }

        // Only today's still-active quests of a matching type are affected.
        $quests = $this->todayFor($user)
            ->whereIn('slug', $typeSlugs->all())
            ->where('is_completed', false);

        $completed = [];

        foreach ($quests as $quest) {
            $quest->progress = min($quest->goal, $quest->progress + $amount);

            if ($quest->progress >= $quest->goal && ! $quest->is_completed) {
                $quest->completed_at = now();
                $quest->save();

                $user->addCoins($quest->reward_coins);
                $user->addExperience($quest->reward_xp); // handles its own level/rank notifications

                $title = $quest->template()['title'] ?? 'Задание';
                app(NotificationService::class)->questCompleted($user, $title, $quest->reward_coins, $quest->reward_xp);

                $completed[] = [
                    'title' => $title,
                    'coins' => $quest->reward_coins,
                    'xp'    => $quest->reward_xp,
                ];
            } else {
                $quest->save();
            }
        }

        return $completed;
    }

    /**
     * Swap ALL of today's quests — completed ones included — for random
     * templates that aren't already in the day's set, charging
     * Quests::REROLL_COST coins. Available ONCE per day
     * (users.last_quest_reroll_date). Old rows are deleted and replaced so
     * the deterministic daily draw is untouched (generate() only runs when
     * the day has no rows at all).
     *
     * @return string 'ok' | 'used' (already rerolled today) | 'invalid'
     *                (no quests today) | 'coins' (balance too low) |
     *                'pool' (not enough unused templates)
     */
    public function rerollAll(User $user): string
    {
        $today = $this->todayFor($user);

        if ($today->isEmpty()) {
            return 'invalid';
        }

        if ($user->last_quest_reroll_date?->isToday()) {
            return 'used';
        }

        if ((int) $user->coins < Quests::REROLL_COST) {
            return 'coins';
        }

        $candidates = array_values(array_diff(array_keys(Quests::all()), $today->pluck('slug')->all()));
        if (count($candidates) < $today->count()) {
            return 'pool';
        }
        shuffle($candidates);

        \Illuminate\Support\Facades\DB::transaction(function () use ($user, $today, $candidates) {
            $user->decrement('coins', Quests::REROLL_COST);
            $user->last_quest_reroll_date = Carbon::today();
            $user->save();

            foreach ($today as $i => $quest) {
                $slug = $candidates[$i];
                $tpl  = Quests::get($slug);
                $date = $quest->quest_date->toDateString();
                $quest->delete();

                DailyQuest::create([
                    'user_id'      => $user->id,
                    'quest_date'   => $date,
                    'slug'         => $slug,
                    'progress'     => 0,
                    'goal'         => $tpl['goal'],
                    'reward_coins' => $tpl['coins'],
                    'reward_xp'    => $tpl['xp'],
                ]);
            }
        });

        return 'ok';
    }

    /** Count of today's quests already completed (for menu badges). */
    public function completedCountToday(User $user): int
    {
        return $this->todayFor($user)->where('is_completed', true)->count();
    }
}
