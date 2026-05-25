<?php

namespace App\Http\Controllers;

use App\Models\Habit;
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

    /**
     * Show habits for a specific date.
     */
    public function showDay(Request $request, $date)
    {
        $user = Auth::user();
        $dateObj = Carbon::parse($date);
        
        // Prevent tracking for future dates
        if ($dateObj->isFuture()) {
            if ($request->ajax()) {
                return response()->json(['error' => 'Cannot track habits for future dates.'], 400);
            }
            return redirect()->route('calendar')->with('error', 'Cannot track habits for future dates.');
        }

        $habits = $user->habits()->where('is_active', true)->get();
        $dateFormatted = $dateObj->format('F j, Y');

        if ($request->ajax()) {
            return view('calendar.partials.day_habits', compact('habits', 'date', 'dateFormatted'));
        }

        return view('calendar.show', compact('habits', 'date', 'dateFormatted'));
    }

    /**
     * Toggle habit completion for a specific date.
     */
    public function toggleHabitForDay(Request $request, $date, Habit $habit)
    {
        if ($habit->user_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        $dateObj = Carbon::parse($date);
        if ($dateObj->isFuture()) {
            if ($request->ajax()) {
                return response()->json(['error' => 'Cannot track habits for future dates.'], 400);
            }
            return back()->with('error', 'Cannot track habits for future dates.');
        }

        if ($habit->isCompletedOn($date)) {
            $habit->markIncompleteForDate($date);
            if ($request->ajax()) return response()->json(['success' => true]);
            return back()->with('info', 'Habit marked as incomplete for ' . $dateObj->format('M j') . '.');
        }

        $habit->markCompleteForDate($date);
        if ($request->ajax()) return response()->json(['success' => true]);
        return back()->with('success', 'Habit completed for ' . $dateObj->format('M j') . '! +'.$habit->xp_reward.' XP 🎉');
    }
}
