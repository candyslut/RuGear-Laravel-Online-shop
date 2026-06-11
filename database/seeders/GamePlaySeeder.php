<?php

namespace Database\Seeders;

use App\Models\GamePlay;
use App\Models\User;
use App\Support\GameStats;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

/**
 * Seeds the game_plays table so the dashboard Игротека, the statistics modal
 * and the game leaderboards have something to show out of the box. Every user
 * gets a believable career across both mini-games: dozens of runs scattered
 * over the last 30 days, each with a per-game skill so leaderboards rank
 * meaningfully.
 *
 * Beyond the raw rows, this seeder also keeps the users table's legendary-gate
 * columns (buzzword_levels, redline_best_distance) consistent with the runs it
 * writes — otherwise a seeded "pro" who cleared 10+ Buzzword levels and ran
 * 10 000 m in Redline would still be locked out of legendary cosmetics, since
 * those columns would sit at their default 0 (see User::legendaryUnlocked()).
 *
 * The admin gets a guaranteed top-tier career: they should have game stats too
 * and be able to buy legendary cosmetics/sticker packs straight away.
 */
class GamePlaySeeder extends Seeder
{
    public function run(): void
    {
        $rows = [];

        // Plain users + the Test User: a varied career, skill drives who clears
        // the legendary gate (high-skill Redline runs reach 10 000 m).
        foreach (User::query()->where('role', '!=', 'admin')->get() as $user) {
            $skill = [
                'buzzword' => mt_rand(45, 130) / 100,
                'redline'  => mt_rand(45, 130) / 100,
            ];

            $this->seedCareer($user, $skill, $rows);
        }

        // Admin: a guaranteed pro so the dashboards have an example power-player
        // and the admin clears the legendary gate out of the box.
        $admin = User::query()->where('role', 'admin')->first();
        if ($admin) {
            $this->seedCareer($admin, ['buzzword' => 1.3, 'redline' => 1.45], $rows, pro: true);
        }

        // Chunked insert so a few thousand rows go in without exhausting the
        // placeholder limit. created_at/updated_at are set explicitly because
        // we backdate runs across the last month.
        foreach (array_chunk($rows, 200) as $chunk) {
            GamePlay::insert($chunk);
        }
    }

    /**
     * Build a full career for one user, append its rows to $rows (by reference)
     * and persist the derived legendary-gate progress onto the user:
     *   - buzzword_levels       = lifetime cleared levels (sum of per-run level)
     *   - redline_best_distance = best Redline distance (max score)
     * mirroring how /game/buzz-claim and /game/runner-distance maintain them in
     * live play, so legendaryUnlocked() agrees with the seeded stats.
     *
     * @param  list<array>  $rows
     */
    private function seedCareer(User $user, array $skill, array &$rows, bool $pro = false): void
    {
        $buzzwordLevels = 0;
        $redlineBest = 0;

        foreach (GameStats::keys() as $game) {
            // A pro plays a lot of both games; regular players favour one and a
            // few barely touch the other.
            $runs = $pro
                ? mt_rand(25, 45)
                : (mt_rand(0, 10) === 0 ? mt_rand(0, 3) : mt_rand(8, 40));

            for ($i = 0; $i < $runs; $i++) {
                $run = $this->makeRun($user->id, $game, $skill[$game], $pro);
                $rows[] = $run;

                if ($game === 'buzzword') {
                    $buzzwordLevels += $run['level'];
                } else {
                    $redlineBest = max($redlineBest, $run['score']);
                }
            }
        }

        $user->forceFill([
            'buzzword_levels'       => $buzzwordLevels,
            'redline_best_distance' => $redlineBest,
        ])->save();
    }

    /** Build one believable run row for a game, backdated within 30 days. */
    private function makeRun(int $userId, string $game, float $skill, bool $pro = false): array
    {
        // Spread runs over the last 30 days, weighted toward recent days so the
        // activity chart shows a lively, slightly rising trend.
        $daysAgo = (int) floor(abs(30 - sqrt(mt_rand(0, 900))));
        $when = Carbon::now()
            ->subDays(min(29, $daysAgo))
            ->setTime(mt_rand(8, 23), mt_rand(0, 59), mt_rand(0, 59));

        if ($game === 'buzzword') {
            $level = max(1, (int) round($skill * mt_rand(1, 14)));
            $score = (int) round($skill * mt_rand(150, 6500));
            $duration = mt_rand(25, 240) * 1000;
            $coins = $level;
            $xp = $level * 5;
            $meta = [
                'asteroids' => (int) round($score / mt_rand(40, 70)),
                'deaths'    => mt_rand(0, 3),
            ];
        } else { // redline
            $level = (int) round($skill * mt_rand(0, 11));
            // Distance band is wide enough that high-skill players (and the
            // admin pro) reach the 10 000 m legendary gate; low-skill players
            // stay well short of it.
            $score = $pro
                ? mt_rand(10000, 16000)
                : (int) round($skill * mt_rand(120, 9500));
            $duration = mt_rand(15, 180) * 1000;
            $coins = $level;
            $xp = $level * 5;
            $meta = [
                'died' => mt_rand(0, 1),
                'jumps' => (int) round($score / mt_rand(35, 55)),
            ];
        }

        return [
            'user_id'      => $userId,
            'game'         => $game,
            'score'        => $score,
            'level'        => $level,
            'duration_ms'  => $duration,
            'coins_earned' => $coins,
            'xp_earned'    => $xp,
            'meta'         => json_encode($meta),
            'created_at'   => $when,
            'updated_at'   => $when,
        ];
    }
}
