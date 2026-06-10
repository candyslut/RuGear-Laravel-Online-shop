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

#[Fillable(['name', 'email', 'password', 'role', 'phone', 'gender', 'about', 'avatar', 'cosmetic_border', 'cosmetic_font', 'cosmetic_nickname_color', 'cosmetic_theme'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    public int $lastLevelUp = 0;
    public int $lastLevelCoins = 0;

    /**
     * Rank-up summary set by addExperience() when the most recent XP gain
     * pushed the user into a higher rank tier, otherwise null. Controllers
     * read it to flash the bottom-right rank-up toast on a full page load.
     *
     * @var array{code:string, name:string, color:string, coins:int, level:int}|null
     */
    public ?array $lastRankUp = null;

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'last_active_date' => 'date',
        ];
    }

    public function cartItems() {
        return $this->hasMany(CartItems::class);
    }

    public function commentaries() {
        return $this->hasMany(Commentary::class);
    }

    /**
     * Site notifications shown in the header bell.
     * Overrides the Notifiable trait's morphMany (database channel is unused here).
     */
    public function notifications(): HasMany
    {
        return $this->hasMany(\App\Models\Notification::class);
    }

    public function unreadNotificationsCount(): int
    {
        return $this->notifications()->whereNull('read_at')->count();
    }

    public function tickets() {
        return $this->hasMany(Ticket::class);
    }

    public function dailyQuests(): HasMany
    {
        return $this->hasMany(DailyQuest::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function shopItems(): BelongsToMany
    {
        return $this->belongsToMany(\App\Models\ShopItem::class, 'user_shop_items')
            ->withPivot('purchased_at');
    }

    public function achievements(): BelongsToMany
    {
        return $this->belongsToMany(Achievement::class)
            ->withPivot('awarded_at');
    }

    public function addExperience(int $amount): void
    {
        // Freshly created models (factories/seeders) may not have the DB
        // defaults hydrated yet, so level/experience can be null here.
        $oldLevel = max(1, (int) $this->level);
        $this->experience = (int) $this->experience + $amount;
        $this->level = max(1, intdiv($this->experience, 100) + 1);
        $this->save();

        $this->lastLevelUp = $this->level - $oldLevel;
        $this->lastRankUp = null;
        if ($this->lastLevelUp > 0) {
            $this->lastLevelCoins = $this->lastLevelUp * 10;
            $this->addCoins($this->lastLevelCoins);
            app(\App\Services\NotificationService::class)->levelUp($this, $this->level);
            $this->handleRankUp($oldLevel);
        } else {
            $this->lastLevelCoins = 0;
        }
    }

    /**
     * Award the one-time coin bonus and bell notification when a level gain
     * crosses into a higher rank tier (Бронза → … → Алмаз). Called from
     * addExperience after a level-up; records the result in $lastRankUp so the
     * UI can also surface a toast.
     */
    protected function handleRankUp(int $oldLevel): void
    {
        $oldTier = \App\Support\Gamification::rankTier($oldLevel);
        $newTier = \App\Support\Gamification::rankTier($this->level);

        if ($newTier['index'] <= $oldTier['index']) {
            return;
        }

        $reward = \App\Support\Gamification::rankRewardBetween($oldLevel, $this->level);
        if ($reward > 0) {
            $this->addCoins($reward);
        }

        $this->lastRankUp = [
            'code'  => $newTier['code'],
            'name'  => $newTier['name'],
            'color' => $newTier['color'],
            'coins' => $reward,
            'level' => $this->level,
        ];

        app(\App\Services\NotificationService::class)->rankUp($this, $newTier['name'], $reward);
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
        app(\App\Services\NotificationService::class)->achievement($this, $achievement);
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
