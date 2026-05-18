<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reward extends Model
{
    protected $fillable = ['name', 'description', 'icon', 'cost', 'category', 'is_active'];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function redemptions()
    {
        return $this->hasMany(RewardRedemption::class);
    }

    /**
     * Redeem this reward for a user.
     */
    public function redeem(User $user): ?RewardRedemption
    {
        if (!$user->spendCoins($this->cost)) {
            return null;
        }

        return $this->redemptions()->create([
            'user_id' => $user->id,
            'coins_spent' => $this->cost,
        ]);
    }
}
