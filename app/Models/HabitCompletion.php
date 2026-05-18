<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HabitCompletion extends Model
{
    protected $fillable = ['habit_id', 'user_id', 'completed_date', 'xp_earned', 'coins_earned'];

    protected function casts(): array
    {
        return [
            'completed_date' => 'date',
        ];
    }

    public function habit()
    {
        return $this->belongsTo(Habit::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
