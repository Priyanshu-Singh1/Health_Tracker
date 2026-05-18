<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Badge extends Model
{
    protected $fillable = ['name', 'description', 'icon', 'type', 'requirement', 'xp_bonus', 'coin_bonus'];

    public function users()
    {
        return $this->belongsToMany(User::class)->withPivot('earned_at')->withTimestamps();
    }

    /**
     * Check and award badges to a user based on their stats.
     */
    public static function checkAndAward(User $user): array
    {
        $awarded = [];
        $badges = self::all();

        foreach ($badges as $badge) {
            // Skip if user already has this badge
            if ($user->badges->contains($badge->id)) {
                continue;
            }

            $earned = false;

            switch ($badge->type) {
                case 'streak':
                    $earned = $user->longest_streak >= $badge->requirement;
                    break;
                case 'completion':
                    $earned = $user->total_habits_completed >= $badge->requirement;
                    break;
                case 'level':
                    $earned = $user->level >= $badge->requirement;
                    break;
            }

            if ($earned) {
                $user->badges()->attach($badge->id, ['earned_at' => now()]);
                $user->addXp($badge->xp_bonus);
                $user->addCoins($badge->coin_bonus);
                $awarded[] = $badge;
            }
        }

        return $awarded;
    }
}
