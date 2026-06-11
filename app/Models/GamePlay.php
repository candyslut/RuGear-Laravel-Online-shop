<?php

namespace App\Models;

use App\Support\GameStats;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * A single finished mini-game run. One row is written when a run ends (death or
 * abandon in Redline Rush, game over in Buzzword Blast). The whole game-stats
 * surface (charts, records, averages, trends) is derived from this table — see
 * App\Support\GameStats for the aggregation helpers.
 */
class GamePlay extends Model
{
    protected $fillable = [
        'user_id',
        'game',
        'score',
        'level',
        'duration_ms',
        'coins_earned',
        'xp_earned',
        'meta',
    ];

    protected function casts(): array
    {
        return [
            'meta' => 'array',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** Registry entry (name/color/unit/icon) for this play's game. */
    public function gameMeta(): ?array
    {
        return GameStats::game($this->game);
    }
}
