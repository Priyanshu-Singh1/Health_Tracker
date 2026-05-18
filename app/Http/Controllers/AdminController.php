<?php

namespace App\Http\Controllers;

use App\Models\Badge;
use App\Models\Category;
use App\Models\Habit;
use App\Models\HabitCompletion;
use App\Models\Reward;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    public function index()
    {
        $totalUsers = User::count();
        $totalHabits = Habit::count();
        $totalCompletions = HabitCompletion::count();
        $totalCategories = Category::count();
        $recentUsers = User::orderByDesc('created_at')->take(10)->get();
        $topUsers = User::orderByDesc('xp')->take(10)->get();

        return view('admin.dashboard', compact(
            'totalUsers', 'totalHabits', 'totalCompletions',
            'totalCategories', 'recentUsers', 'topUsers'
        ));
    }

    public function users()
    {
        $users = User::withCount('habits')->orderByDesc('created_at')->paginate(20);
        return view('admin.users', compact('users'));
    }

    public function toggleAdmin(User $user)
    {
        if ($user->id === Auth::id()) {
            return back()->with('error', 'Cannot modify your own admin status.');
        }
        $user->update(['is_admin' => !$user->is_admin]);
        return back()->with('success', 'User admin status updated.');
    }

    public function categories()
    {
        $categories = Category::withCount('habits')->get();
        return view('admin.categories', compact('categories'));
    }

    public function storeCategory(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'icon' => 'required|string|max:10',
            'color' => 'required|string|max:7',
        ]);
        $validated['slug'] = \Str::slug($validated['name']);
        Category::create($validated);
        return back()->with('success', 'Category created!');
    }

    public function destroyCategory(Category $category)
    {
        if ($category->is_default) {
            return back()->with('error', 'Cannot delete default categories.');
        }
        $category->delete();
        return back()->with('success', 'Category deleted.');
    }

    public function rewards()
    {
        $rewards = Reward::withCount('redemptions')->get();
        return view('admin.rewards', compact('rewards'));
    }

    public function storeReward(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string|max:1000',
            'icon' => 'required|string|max:10',
            'cost' => 'required|integer|min:1',
            'category' => 'required|in:entertainment,food,self_care,social,custom',
        ]);
        Reward::create($validated);
        return back()->with('success', 'Reward created!');
    }

    public function toggleReward(Reward $reward)
    {
        $reward->update(['is_active' => !$reward->is_active]);
        return back()->with('success', 'Reward status updated.');
    }

    public function destroyReward(Reward $reward)
    {
        $reward->delete();
        return back()->with('success', 'Reward deleted.');
    }
}
