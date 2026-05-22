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

#[Fillable(['name', 'email', 'password', 'role'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
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
        $this->experience = $this->experience + $amount;
        $this->level = max(1, intdiv($this->experience, 100) + 1);
        $this->save();
    }

    public function awardAchievement(Achievement $achievement): bool
    {
        if ($this->achievements()->where('achievement_id', $achievement->id)->exists()) {
            return false;
        }

        $this->achievements()->attach($achievement->id, ['awarded_at' => now()]);
        $this->addExperience($achievement->experience);

        return true;
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
