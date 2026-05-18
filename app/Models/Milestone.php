<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Milestone extends Model
{
    protected $fillable = [
        'user_id', 'habit_id', 'title', 'description',
        'target_count', 'current_count', 'xp_reward',
        'coin_reward', 'is_completed', 'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'is_completed' => 'boolean',
            'completed_at' => 'datetime',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function habit()
    {
        return $this->belongsTo(Habit::class);
    }

    /**
     * Get progress percentage.
     */
    public function getProgressAttribute(): int
    {
        if ($this->target_count <= 0) return 100;
        return min(100, (int) round(($this->current_count / $this->target_count) * 100));
    }

    /**
     * Increment progress and check for completion.
     */
    public function incrementProgress(int $amount = 1): bool
    {
        if ($this->is_completed) return false;

        $this->current_count = min($this->current_count + $amount, $this->target_count);

        if ($this->current_count >= $this->target_count) {
            $this->is_completed = true;
            $this->completed_at = now();
            $this->save();

            // Award milestone rewards
            $this->user->addXp($this->xp_reward);
            $this->user->addCoins($this->coin_reward);

            return true; // Milestone completed!
        }

        $this->save();
        return false;
    }
}
