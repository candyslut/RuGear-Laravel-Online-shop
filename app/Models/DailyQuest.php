<?php

namespace App\Models;

use App\Support\Quests;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DailyQuest extends Model
{
    protected $fillable = [
        'user_id',
        'quest_date',
        'slug',
        'progress',
        'goal',
        'reward_coins',
        'reward_xp',
        'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'quest_date'   => 'date',
            'completed_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** The catalog template backing this quest (title/description/type/icon). */
    public function template(): ?array
    {
        return Quests::get($this->slug);
    }

    public function getIsCompletedAttribute(): bool
    {
        return $this->completed_at !== null;
    }

    /** Progress as a 0–100 integer for the meter. */
    public function getPercentAttribute(): int
    {
        if ($this->goal <= 0) {
            return 100;
        }

        return max(0, min(100, (int) round($this->progress / $this->goal * 100)));
    }
}
