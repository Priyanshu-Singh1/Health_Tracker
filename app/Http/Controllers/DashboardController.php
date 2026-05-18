<?php

namespace App\Http\Controllers;

use App\Models\Badge;
use App\Models\Habit;
use App\Models\HabitCompletion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Show main dashboard with analytics.
     */
    public function index()
    {
        $user = Auth::user();
        $user->load(['badges']);

        $today = Carbon::today();

        // Get user's habits with today's completion status
        $habits = $user->habits()
            ->where('is_active', true)
            ->with('category')
            ->get();

        $totalHabits = $habits->count();
        $completedToday = $habits->filter(fn($h) => $h->isCompletedToday())->count();
        $completionRate = $totalHabits > 0 ? round(($completedToday / $totalHabits) * 100) : 0;

        // Weekly progress data (last 7 days)
        $weeklyData = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = $today->copy()->subDays($i);
            $completed = HabitCompletion::where('user_id', $user->id)
                ->where('completed_date', $date->toDateString())
                ->count();
            $weeklyData[] = [
                'day' => $date->format('D'),
                'date' => $date->toDateString(),
                'completed' => $completed,
                'total' => $totalHabits,
            ];
        }

        // Monthly stats
        $monthStart = $today->copy()->startOfMonth();
        $monthlyCompletions = HabitCompletion::where('user_id', $user->id)
            ->where('completed_date', '>=', $monthStart->toDateString())
            ->count();

        $daysInMonth = $today->daysInMonth;
        $daysPassed = $today->day;
        $monthlyPossible = $totalHabits * $daysPassed;
        $monthlyRate = $monthlyPossible > 0 ? round(($monthlyCompletions / $monthlyPossible) * 100) : 0;

        // Top performing habits
        $topHabits = $user->habits()
            ->where('is_active', true)
            ->orderByDesc('current_streak')
            ->take(5)
            ->get();

        // Recent badge achievements
        $recentBadges = $user->badges()
            ->orderByDesc('badge_user.earned_at')
            ->take(5)
            ->get();

        // Check for new badges
        $newBadges = Badge::checkAndAward($user);

        return view('dashboard', compact(
            'user', 'habits', 'totalHabits', 'completedToday',
            'completionRate', 'weeklyData', 'monthlyCompletions',
            'monthlyRate', 'topHabits', 'recentBadges', 'newBadges'
        ));
    }
}
