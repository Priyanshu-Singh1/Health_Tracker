<?php

namespace App\Http\Controllers;

use App\Models\Badge;
use App\Models\Category;
use App\Models\Habit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HabitController extends Controller
{
    /**
     * Display habits with filtering and search.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = $user->habits()->with('category');

        // Filter by category
        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        // Filter by frequency
        if ($request->filled('frequency')) {
            $query->where('frequency', $request->frequency);
        }

        // Filter by priority
        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        // Filter by completion status
        if ($request->filled('status')) {
            if ($request->status === 'completed') {
                $query->whereHas('completions', function ($q) {
                    $q->where('completed_date', today()->toDateString());
                });
            } elseif ($request->status === 'pending') {
                $query->whereDoesntHave('completions', function ($q) {
                    $q->where('completed_date', today()->toDateString());
                });
            }
        }

        // Filter by active status
        if ($request->filled('active')) {
            $query->where('is_active', $request->active === 'yes');
        }

        // Search by name
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $habits = $query->orderByDesc('created_at')->get();
        $categories = Category::all();

        return view('habits.index', compact('habits', 'categories'));
    }

    /**
     * Show create habit form.
     */
    public function create()
    {
        $categories = Category::all();
        return view('habits.create', compact('categories'));
    }

    /**
     * Store a new habit.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'category_id' => 'required|exists:categories,id',
            'frequency' => 'required|in:daily,weekly,monthly',
            'priority' => 'required|in:low,medium,high',
            'reminder_time' => 'nullable|date_format:H:i',
        ]);

        $validated['user_id'] = Auth::id();

        // Set rewards based on priority
        $validated['xp_reward'] = match($validated['priority']) {
            'high' => 20,
            'medium' => 10,
            'low' => 5,
        };
        $validated['coin_reward'] = match($validated['priority']) {
            'high' => 10,
            'medium' => 5,
            'low' => 3,
        };

        Habit::create($validated);

        return redirect()->route('habits.index')->with('success', 'Habit created successfully! 🎯');
    }

    /**
     * Show edit habit form.
     */
    public function edit(Habit $habit)
    {
        $this->authorizeHabit($habit);
        $categories = Category::all();
        return view('habits.edit', compact('habit', 'categories'));
    }

    /**
     * Update habit.
     */
    public function update(Request $request, Habit $habit)
    {
        $this->authorizeHabit($habit);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'category_id' => 'required|exists:categories,id',
            'frequency' => 'required|in:daily,weekly,monthly',
            'priority' => 'required|in:low,medium,high',
            'reminder_time' => 'nullable|date_format:H:i',
            'is_active' => 'boolean',
        ]);

        $validated['xp_reward'] = match($validated['priority']) {
            'high' => 20,
            'medium' => 10,
            'low' => 5,
        };
        $validated['coin_reward'] = match($validated['priority']) {
            'high' => 10,
            'medium' => 5,
            'low' => 3,
        };

        $habit->update($validated);

        return redirect()->route('habits.index')->with('success', 'Habit updated successfully! ✅');
    }

    /**
     * Delete habit.
     */
    public function destroy(Habit $habit)
    {
        $this->authorizeHabit($habit);
        $habit->delete();

        return redirect()->route('habits.index')->with('success', 'Habit deleted.');
    }

    /**
     * Toggle habit completion for today.
     */
    public function toggleComplete(Habit $habit)
    {
        $this->authorizeHabit($habit);

        if ($habit->isCompletedToday()) {
            $habit->markIncomplete();
            return back()->with('info', 'Habit marked as incomplete.');
        }

        $completion = $habit->markComplete();

        if ($completion) {
            // Check for new badges
            $user = Auth::user()->fresh();
            $newBadges = Badge::checkAndAward($user);

            // Update related milestones
            $milestones = $user->milestones()
                ->where('habit_id', $habit->id)
                ->where('is_completed', false)
                ->get();

            $completedMilestones = [];
            foreach ($milestones as $milestone) {
                if ($milestone->incrementProgress()) {
                    $completedMilestones[] = $milestone;
                }
            }

            $message = "Habit completed! +{$completion->xp_earned} XP, +{$completion->coins_earned} coins 🎉";

            if (count($newBadges) > 0) {
                $badgeNames = implode(', ', array_map(fn($b) => $b->icon . ' ' . $b->name, $newBadges));
                $message .= " | New badge: {$badgeNames}";
            }

            if (count($completedMilestones) > 0) {
                $message .= " | 🏁 Milestone completed!";
            }

            return back()->with('success', $message);
        }

        return back()->with('info', 'Already completed today.');
    }

    /**
     * Show habit details.
     */
    public function show(Habit $habit)
    {
        $this->authorizeHabit($habit);
        $habit->load(['category', 'completions' => function ($q) {
            $q->orderByDesc('completed_date')->take(30);
        }, 'milestones']);

        return view('habits.show', compact('habit'));
    }

    /**
     * Ensure user owns the habit.
     */
    private function authorizeHabit(Habit $habit): void
    {
        if ($habit->user_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }
    }
}
