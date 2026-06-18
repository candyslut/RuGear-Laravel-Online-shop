<?php

namespace Database\Seeders;

use App\Models\Achievement;
use App\Models\FeedEvent;
use App\Models\Order;
use App\Models\User;
use App\Support\Gamification;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Backfills the Live Feed from existing data so the stream isn't empty on
 * launch (level-up / rank-up history isn't logged anywhere, so those accrue
 * going forward — here we seed achievements and orders). Idempotent: skips if
 * the feed already has rows.
 */
class FeedEventSeeder extends Seeder
{
    public function run(): void
    {
        if (FeedEvent::query()->exists()) {
            return;
        }

        $rows = [];

        // ─── Achievements (recent), with the rarity accent colour ────────────
        $awards = DB::table('achievement_user')
            ->join('users', 'users.id', '=', 'achievement_user.user_id')
            ->join('achievements', 'achievements.id', '=', 'achievement_user.achievement_id')
            ->select(
                'users.id as user_id',
                'users.name',
                'users.avatar',
                'achievements.title',
                'achievements.slug',
                'achievements.experience',
                'achievement_user.awarded_at'
            )
            ->orderByDesc('achievement_user.awarded_at')
            ->limit(60)
            ->get();

        foreach ($awards as $a) {
            if (! $a->name) {
                continue;
            }

            // rarity() only reads slug + experience off the model.
            $tmp = new Achievement(['slug' => $a->slug, 'experience' => $a->experience]);
            $tmp->slug = $a->slug;

            $when = $a->awarded_at ?? now();
            $rows[] = [
                'type'       => 'achievement',
                'user_id'    => $a->user_id,
                'name'       => $a->name,
                'avatar'     => $a->avatar,
                'title'      => $a->title,
                'color'      => Gamification::rarity($tmp)['color'],
                'created_at' => $when,
                'updated_at' => $when,
            ];
        }

        // ─── Orders (recent) — generic, no items/amount ──────────────────────
        $orders = Order::with('user')->latest()->limit(40)->get();
        foreach ($orders as $o) {
            if (! $o->user) {
                continue;
            }
            $rows[] = [
                'type'       => 'order',
                'user_id'    => $o->user_id,
                'name'       => $o->user->name,
                'avatar'     => $o->user->avatar,
                'title'      => null,
                'color'      => '#10b981',
                'created_at' => $o->created_at,
                'updated_at' => $o->created_at,
            ];
        }

        // ─── Registrations (recent players) ──────────────────────────────────
        $users = User::latest()->limit(40)->get();
        foreach ($users as $u) {
            $rows[] = [
                'type'       => 'registration',
                'user_id'    => $u->id,
                'name'       => $u->name,
                'avatar'     => $u->avatar,
                'title'      => null,
                'color'      => '#818cf8',
                'created_at' => $u->created_at,
                'updated_at' => $u->created_at,
            ];
        }

        if ($rows) {
            FeedEvent::insert($rows);
        }
    }
}
