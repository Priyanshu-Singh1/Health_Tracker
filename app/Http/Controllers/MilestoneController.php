<?php

namespace App\Http\Controllers;

use App\Models\Milestone;
use App\Models\Habit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MilestoneController extends Controller
{
    /**
     * Show milestones list.
     */
    public function index()
    {
        $user = Auth::user();
        $activeMilestones = $user->milestones()
            ->where('is_completed', false)
            ->with('habit')
            ->orderByDesc('created_at')
            ->get();

        $completedMilestones = $user->milestones()
            ->where('is_completed', true)
            ->with('habit')
            ->orderByDesc('completed_at')
            ->get();

        return view('milestones.index', compact('activeMilestones', 'completedMilestones'));
    }

    /**
     * Show create milestone form.
     */
    public function create()
    {
        $habits = Auth::user()->habits()->where('is_active', true)->get();
        return view('milestones.create', compact('habits'));
    }

    /**
     * Store a new milestone.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'habit_id' => 'nullable|exists:habits,id',
            'target_count' => 'required|integer|min:1|max:1000',
        ]);

        // Verify habit belongs to user
        if (!empty($validated['habit_id'])) {
            $habit = Habit::findOrFail($validated['habit_id']);
            if ($habit->user_id !== Auth::id()) {
                abort(403);
            }
        }

        $validated['user_id'] = Auth::id();
        $validated['xp_reward'] = max(50, $validated['target_count'] * 5);
        $validated['coin_reward'] = max(25, $validated['target_count'] * 2);

        Milestone::create($validated);

        return redirect()->route('milestones.index')->with('success', 'Milestone created! 🏁');
    }

    /**
     * Delete a milestone.
     */
    public function destroy(Milestone $milestone)
    {
        if ($milestone->user_id !== Auth::id()) {
            abort(403);
        }

        $milestone->delete();
        return redirect()->route('milestones.index')->with('success', 'Milestone deleted.');
    }
}
