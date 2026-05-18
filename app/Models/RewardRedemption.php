<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RewardRedemption extends Model
{
    protected $fillable = ['reward_id', 'user_id', 'coins_spent'];

    public function reward()
    {
        return $this->belongsTo(Reward::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
