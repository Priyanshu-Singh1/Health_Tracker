<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Habit extends Model
{
    protected $fillable = [
        'user_id', 'category_id', 'name', 'description', 'frequency',
        'priority', 'xp_reward', 'coin_reward', 'current_streak',
        'best_streak', 'total_completions', 'last_completed_at',
        'is_active', 'reminder_time',
    ];

    protected function casts(): array
    {
        return [
            'last_completed_at' => 'date',
            'is_active' => 'boolean',
        ];
    }

    // ---- Relationships ----

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function completions()
    {
        return $this->hasMany(HabitCompletion::class);
    }

    public function milestones()
    {
        return $this->hasMany(Milestone::class);
    }

    // ---- Helper Methods ----

    /**
     * Check if this habit was completed today.
     */
    public function isCompletedToday(): bool
    {
        return $this->completions()
            ->where('completed_date', Carbon::today()->toDateString())
            ->exists();
    }

    /**
     * Check if this habit was completed on a specific date.
     */
    public function isCompletedOn(string $date): bool
    {
        return $this->completions()
            ->where('completed_date', $date)
            ->exists();
    }

    /**
     * Get completion percentage for the last N days.
     */
    public function completionPercentage(int $days = 30): float
    {
        $startDate = Carbon::today()->subDays($days);
        $completedCount = $this->completions()
            ->where('completed_date', '>=', $startDate->toDateString())
            ->count();

        return $days > 0 ? round(($completedCount / $days) * 100, 1) : 0;
    }

    /**
     * Mark habit as completed for today.
     */
    public function markComplete(): ?HabitCompletion
    {
        if ($this->isCompletedToday()) {
            return null;
        }

        $today = Carbon::today();
        $yesterday = Carbon::yesterday();

        // Update streak
        if ($this->last_completed_at && $this->last_completed_at->eq($yesterday)) {
            $this->current_streak++;
        } elseif (!$this->last_completed_at || !$this->last_completed_at->eq($today)) {
            $this->current_streak = 1;
        }

        if ($this->current_streak > $this->best_streak) {
            $this->best_streak = $this->current_streak;
        }

        $this->total_completions++;
        $this->last_completed_at = $today;
        $this->save();

        // Calculate bonus XP for streaks
        $streakBonus = 0;
        if ($this->current_streak >= 100) $streakBonus = 50;
        elseif ($this->current_streak >= 30) $streakBonus = 25;
        elseif ($this->current_streak >= 7) $streakBonus = 10;

        $xpEarned = $this->xp_reward + $streakBonus;
        $coinsEarned = $this->coin_reward + (int)($streakBonus / 2);

        // Create completion record
        $completion = $this->completions()->create([
            'user_id' => $this->user_id,
            'completed_date' => $today->toDateString(),
            'xp_earned' => $xpEarned,
            'coins_earned' => $coinsEarned,
        ]);

        // Update user stats
        $user = $this->user;
        $user->addXp($xpEarned);
        $user->addCoins($coinsEarned);
        $user->increment('total_habits_completed');

        if ($this->current_streak > $user->longest_streak) {
            $user->update(['longest_streak' => $this->current_streak]);
        }

        return $completion;
    }

    /**
     * Undo today's completion.
     */
    public function markIncomplete(): bool
    {
        $completion = $this->completions()
            ->where('completed_date', Carbon::today()->toDateString())
            ->first();

        if (!$completion) {
            return false;
        }

        // Reverse rewards
        $user = $this->user;
        $user->xp = max(0, $user->xp - $completion->xp_earned);
        $user->coins = max(0, $user->coins - $completion->coins_earned);
        $user->total_habits_completed = max(0, $user->total_habits_completed - 1);
        $user->save();

        // Reset streak
        $this->current_streak = max(0, $this->current_streak - 1);
        $this->total_completions = max(0, $this->total_completions - 1);
        $this->save();

        $completion->delete();
        return true;
    }

    /**
     * Get streak status with emoji.
     */
    public function getStreakStatusAttribute(): string
    {
        return match(true) {
            $this->current_streak >= 100 => '🔥🏆 ' . $this->current_streak . ' days!',
            $this->current_streak >= 30 => '🔥🥈 ' . $this->current_streak . ' days!',
            $this->current_streak >= 7 => '🔥🥉 ' . $this->current_streak . ' days!',
            $this->current_streak >= 3 => '🔥 ' . $this->current_streak . ' days',
            $this->current_streak >= 1 => '✨ ' . $this->current_streak . ' day(s)',
            default => '💤 No streak',
        };
    }
}
