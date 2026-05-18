<?php

namespace Database\Seeders;

use App\Models\Badge;
use App\Models\Category;
use App\Models\Reward;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Admin User
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'is_admin' => true,
        ]);

        // Regular User
        User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => Hash::make('password'),
        ]);

        // Categories
        $categories = [
            ['name' => 'Health', 'slug' => 'health', 'icon' => '🍎', 'color' => '#10b981', 'is_default' => true],
            ['name' => 'Productivity', 'slug' => 'productivity', 'icon' => '⚡', 'color' => '#3b82f6', 'is_default' => true],
            ['name' => 'Study', 'slug' => 'study', 'icon' => '📚', 'color' => '#8b5cf6', 'is_default' => true],
            ['name' => 'Fitness', 'slug' => 'fitness', 'icon' => '💪', 'color' => '#ef4444', 'is_default' => true],
            ['name' => 'Personal Growth', 'slug' => 'personal-growth', 'icon' => '🌱', 'color' => '#f59e0b', 'is_default' => true],
        ];

        foreach ($categories as $cat) {
            Category::create($cat);
        }

        // Badges
        $badges = [
            ['name' => 'First Step', 'description' => 'Complete your first habit.', 'icon' => '🌟', 'type' => 'completion', 'requirement' => 1, 'xp_bonus' => 50, 'coin_bonus' => 20],
            ['name' => 'Bronze Streaker', 'description' => 'Reach a 7-day streak.', 'icon' => '🥉', 'type' => 'streak', 'requirement' => 7, 'xp_bonus' => 100, 'coin_bonus' => 50],
            ['name' => 'Silver Streaker', 'description' => 'Reach a 30-day streak.', 'icon' => '🥈', 'type' => 'streak', 'requirement' => 30, 'xp_bonus' => 300, 'coin_bonus' => 150],
            ['name' => 'Gold Streaker', 'description' => 'Reach a 100-day streak.', 'icon' => '🥇', 'type' => 'streak', 'requirement' => 100, 'xp_bonus' => 1000, 'coin_bonus' => 500],
            ['name' => 'Habit Master', 'description' => 'Complete 100 total habits.', 'icon' => '👑', 'type' => 'completion', 'requirement' => 100, 'xp_bonus' => 500, 'coin_bonus' => 250],
            ['name' => 'Level 10', 'description' => 'Reach Level 10.', 'icon' => '🚀', 'type' => 'level', 'requirement' => 10, 'xp_bonus' => 200, 'coin_bonus' => 100],
        ];

        foreach ($badges as $badge) {
            Badge::create($badge);
        }

        // Rewards
        $rewards = [
            ['name' => 'Watch a Movie', 'description' => 'Treat yourself to a movie night.', 'icon' => '🎬', 'cost' => 100, 'category' => 'entertainment'],
            ['name' => 'Cheat Meal', 'description' => 'Enjoy your favorite unhealthy meal.', 'icon' => '🍔', 'cost' => 150, 'category' => 'food'],
            ['name' => 'Day Off', 'description' => 'Take a full day off from tracking.', 'icon' => '🏖️', 'cost' => 300, 'category' => 'self_care'],
            ['name' => 'New Book', 'description' => 'Buy that book you wanted.', 'icon' => '📖', 'cost' => 200, 'category' => 'entertainment'],
        ];

        foreach ($rewards as $reward) {
            Reward::create($reward);
        }
    }
}
