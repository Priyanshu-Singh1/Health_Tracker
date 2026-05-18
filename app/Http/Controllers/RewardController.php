<?php

namespace App\Http\Controllers;

use App\Models\Reward;
use App\Models\RewardRedemption;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RewardController extends Controller
{
    /**
     * Show reward store.
     */
    public function index()
    {
        $user = Auth::user();
        $rewards = Reward::where('is_active', true)->orderBy('cost')->get();
        $recentRedemptions = $user->rewardRedemptions()
            ->with('reward')
            ->orderByDesc('created_at')
            ->take(10)
            ->get();

        return view('rewards.store', compact('rewards', 'user', 'recentRedemptions'));
    }

    /**
     * Redeem a reward.
     */
    public function redeem(Reward $reward)
    {
        $user = Auth::user();

        if (!$reward->is_active) {
            return back()->with('error', 'This reward is no longer available.');
        }

        $redemption = $reward->redeem($user);

        if (!$redemption) {
            return back()->with('error', 'Insufficient coins! You need ' . $reward->cost . ' coins but have ' . $user->coins . '.');
        }

        return back()->with('success', "🎁 Redeemed '{$reward->name}' for {$reward->cost} coins! Enjoy your reward!");
    }

    /**
     * Show redemption history.
     */
    public function history()
    {
        $user = Auth::user();
        $redemptions = $user->rewardRedemptions()
            ->with('reward')
            ->orderByDesc('created_at')
            ->paginate(20);

        return view('rewards.history', compact('redemptions', 'user'));
    }
}
