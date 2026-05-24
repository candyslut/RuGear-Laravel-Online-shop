<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password', 'role', 'phone', 'gender', 'about', 'avatar'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    public int $lastLevelUp = 0;
    public int $lastLevelCoins = 0;

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function cartItems() {
        return $this->hasMany(CartItems::class);
    }

    public function commentaries() {
        return $this->hasMany(Commentary::class);
    }

    public function tickets() {
        return $this->hasMany(Ticket::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function achievements(): BelongsToMany
    {
        return $this->belongsToMany(Achievement::class)
            ->withPivot('awarded_at');
    }

    public function addExperience(int $amount): void
    {
        $oldLevel = $this->level;
        $this->experience = $this->experience + $amount;
        $this->level = max(1, intdiv($this->experience, 100) + 1);
        $this->save();

        $this->lastLevelUp = $this->level - $oldLevel;
        if ($this->lastLevelUp > 0) {
            $this->lastLevelCoins = $this->lastLevelUp * 10;
            $this->addCoins($this->lastLevelCoins);
        } else {
            $this->lastLevelCoins = 0;
        }
    }

    public function addCoins(int $amount): void
    {
        $this->increment('coins', $amount);
    }

    /**
     * @return array{leveled_up: bool, new_level: int, level_coins: int}|false
     */
    public function awardAchievement(Achievement $achievement): array|false
    {
        if ($this->achievements()->where('achievement_id', $achievement->id)->exists()) {
            return false;
        }

        $this->achievements()->attach($achievement->id, ['awarded_at' => now()]);
        $this->addExperience($achievement->experience);
        $this->addCoins($achievement->coins);

        return [
            'leveled_up'  => $this->lastLevelUp > 0,
            'new_level'   => $this->level,
            'level_coins' => $this->lastLevelCoins,
        ];
    }

    public function getNextLevelExperienceAttribute(): int
    {
        return $this->level * 100;
    }

    public function getExperienceProgressAttribute(): int
    {
        return max(0, min($this->experience, $this->nextLevelExperience - 1) - ($this->level - 1) * 100);
    }
}
