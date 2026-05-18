<?php

namespace App\Http\Controllers;

use App\Models\HabitCompletion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class CalendarController extends Controller
{
    /**
     * Show calendar view.
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        $month = $request->input('month', Carbon::today()->month);
        $year = $request->input('year', Carbon::today()->year);

        $startOfMonth = Carbon::createFromDate($year, $month, 1)->startOfMonth();
        $endOfMonth = $startOfMonth->copy()->endOfMonth();

        // Get all completions for this month
        $completions = HabitCompletion::where('user_id', $user->id)
            ->whereBetween('completed_date', [$startOfMonth->toDateString(), $endOfMonth->toDateString()])
            ->get()
            ->groupBy(function ($item) {
                return $item->completed_date->format('Y-m-d');
            });

        // Get total active habits count
        $totalHabits = $user->habits()->where('is_active', true)->count();

        // Build calendar data
        $calendarData = [];
        $currentDate = $startOfMonth->copy();
        while ($currentDate->lte($endOfMonth)) {
            $dateStr = $currentDate->toDateString();
            $dayCompletions = $completions->get($dateStr, collect());
            $completedCount = $dayCompletions->count();

            $calendarData[] = [
                'date' => $dateStr,
                'day' => $currentDate->day,
                'dayOfWeek' => $currentDate->dayOfWeek,
                'completed' => $completedCount,
                'total' => $totalHabits,
                'percentage' => $totalHabits > 0 ? round(($completedCount / $totalHabits) * 100) : 0,
                'isToday' => $currentDate->isToday(),
                'isPast' => $currentDate->isPast() && !$currentDate->isToday(),
                'isFuture' => $currentDate->isFuture(),
            ];

            $currentDate->addDay();
        }

        // Calculate monthly streak
        $monthlyStreak = 0;
        $tempStreak = 0;
        foreach ($calendarData as $day) {
            if ($day['isPast'] || $day['isToday']) {
                if ($day['percentage'] >= 80) {
                    $tempStreak++;
                    $monthlyStreak = max($monthlyStreak, $tempStreak);
                } else {
                    $tempStreak = 0;
                }
            }
        }

        $prevMonth = $startOfMonth->copy()->subMonth();
        $nextMonth = $startOfMonth->copy()->addMonth();
        $monthName = $startOfMonth->format('F Y');

        return view('calendar.index', compact(
            'calendarData', 'monthName', 'month', 'year',
            'prevMonth', 'nextMonth', 'totalHabits', 'monthlyStreak'
        ));
    }
}
