<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'xp',
        'level',
        'coins',
        'avatar',
        'is_admin',
        'bio',
        'total_habits_completed',
        'longest_streak',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

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
            'is_admin' => 'boolean',
        ];
    }

    // ---- Relationships ----

    public function habits()
    {
        return $this->hasMany(Habit::class);
    }

    public function habitCompletions()
    {
        return $this->hasMany(HabitCompletion::class);
    }

    public function badges()
    {
        return $this->belongsToMany(Badge::class)->withPivot('earned_at')->withTimestamps();
    }

    public function rewardRedemptions()
    {
        return $this->hasMany(RewardRedemption::class);
    }

    public function milestones()
    {
        return $this->hasMany(Milestone::class);
    }

    // ---- Gamification Helpers ----

    /**
     * Calculate XP needed for next level.
     */
    public function xpForNextLevel(): int
    {
        return $this->level * 100;
    }

    /**
     * Get XP progress percentage toward next level.
     */
    public function xpProgress(): int
    {
        $xpForLevel = $this->xpForNextLevel();
        $xpInCurrentLevel = $this->xp % $xpForLevel;
        return $xpForLevel > 0 ? (int) round(($xpInCurrentLevel / $xpForLevel) * 100) : 0;
    }

    /**
     * Add XP and handle level-ups.
     */
    public function addXp(int $amount): bool
    {
        $this->xp += $amount;
        $leveledUp = false;

        while ($this->xp >= $this->xpForNextLevel()) {
            $this->xp -= $this->xpForNextLevel();
            $this->level++;
            $this->coins += 20; // Bonus coins on level up
            $leveledUp = true;
        }

        $this->save();
        return $leveledUp;
    }

    /**
     * Add coins.
     */
    public function addCoins(int $amount): void
    {
        $this->increment('coins', $amount);
    }

    /**
     * Spend coins. Returns false if insufficient balance.
     */
    public function spendCoins(int $amount): bool
    {
        if ($this->coins < $amount) {
            return false;
        }
        $this->decrement('coins', $amount);
        return true;
    }

    /**
     * Get the user's rank title based on level.
     */
    public function getRankAttribute(): string
    {
        return match(true) {
            $this->level >= 50 => 'Legend',
            $this->level >= 40 => 'Master',
            $this->level >= 30 => 'Expert',
            $this->level >= 20 => 'Veteran',
            $this->level >= 15 => 'Skilled',
            $this->level >= 10 => 'Adept',
            $this->level >= 5 => 'Apprentice',
            default => 'Beginner',
        };
    }
}
