@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="mb-8">
    <h1>Dashboard</h1>
    <p>Welcome back, <strong>{{ $user->name }}</strong>! Ready to level up?</p>
</div>

<!-- Badges Alert -->
@if(isset($newBadges) && count($newBadges) > 0)
<div class="alert alert-success" style="background: linear-gradient(135deg, rgba(16, 185, 129, 0.1), rgba(59, 130, 246, 0.1)); border-color: var(--primary);">
    <h3 style="color: var(--primary);"><i class="fa-solid fa-trophy"></i> New Badges Unlocked!</h3>
    <div class="flex gap-4 mt-2">
        @foreach($newBadges as $badge)
        <div class="text-center" style="background: rgba(255,255,255,0.05); padding: 0.5rem; border-radius: var(--radius-md);">
            <div style="font-size: 2rem;">{{ $badge->icon }}</div>
            <div style="font-weight: bold; font-size: 0.875rem;">{{ $badge->name }}</div>
            <div style="font-size: 0.75rem; color: var(--text-secondary);">+{{ $badge->xp_bonus }} XP | +{{ $badge->coin_bonus }} Coins</div>
        </div>
        @endforeach
    </div>
</div>
@endif

<!-- Level Progress -->
<div class="card mb-8">
    <div class="flex justify-between items-center mb-2">
        <h3 style="margin: 0;">Level {{ $user->level }} - {{ $user->rank }}</h3>
        <span class="badge badge-primary"><i class="fa-solid fa-bolt"></i> {{ $user->xp }} / {{ $user->xpForNextLevel() }} XP</span>
    </div>
    <div class="progress-container">
        <div class="progress-bar xp" style="width: {{ $user->xpProgress() }}%;"></div>
    </div>
    <p class="text-right mt-1" style="font-size: 0.875rem;">{{ $user->xpForNextLevel() - $user->xp }} XP to next level</p>
</div>

<div class="grid lg:grid-cols-3 gap-8">
    
    <!-- Left Column: Daily Habits -->
    <div class="lg:col-span-2">
        <div class="flex justify-between items-center mb-4">
            <h2>Today's Quests</h2>
            <a href="{{ route('habits.create') }}" class="btn btn-primary btn-sm"><i class="fa-solid fa-plus"></i> New Quest</a>
        </div>
        
        <div class="card mb-8">
            <div class="flex justify-between items-center mb-4">
                <div>
                    <strong style="font-size: 1.25rem;">{{ $completedToday }} / {{ $totalHabits }}</strong>
                    <span class="text-secondary">Completed</span>
                </div>
                <div style="text-align: right;">
                    <div style="font-weight: bold; color: {{ $completionRate == 100 ? 'var(--success)' : 'var(--primary)' }}">{{ $completionRate }}%</div>
                </div>
            </div>
            <div class="progress-container">
                <div class="progress-bar health" style="width: {{ $completionRate }}%;"></div>
            </div>
        </div>

        @if($habits->isEmpty())
            <div class="card text-center" style="padding: 3rem 1rem;">
                <div style="font-size: 3rem; color: var(--text-secondary); margin-bottom: 1rem;"><i class="fa-solid fa-scroll"></i></div>
                <h3>Your quest log is empty!</h3>
                <p>Start tracking habits to earn XP and rewards.</p>
                <a href="{{ route('habits.create') }}" class="btn btn-primary mt-4">Create First Habit</a>
            </div>
        @else
            @foreach($habits as $habit)
                @php $isCompleted = $habit->isCompletedToday(); @endphp
                <div class="habit-item {{ $isCompleted ? 'completed' : '' }}" style="{{ $isCompleted ? 'opacity: 0.7; border-color: var(--success);' : '' }}">
                    <div class="habit-info">
                        <div class="habit-icon" style="background-color: {{ $habit->category->color }}20; color: {{ $habit->category->color }};">
                            {{ $habit->category->icon }}
                        </div>
                        <div class="habit-details">
                            <h4 style="{{ $isCompleted ? 'text-decoration: line-through; color: var(--text-secondary);' : '' }}">
                                <a href="{{ route('habits.show', $habit) }}" style="color: inherit;">{{ $habit->name }}</a>
                            </h4>
                            <p>
                                <span class="badge badge-warning" title="XP Reward"><i class="fa-solid fa-bolt"></i> {{ $habit->xp_reward }}</span>
                                <span class="badge badge-warning" title="Coin Reward" style="background: rgba(252, 211, 77, 0.2); color: #fbbf24;"><i class="fa-solid fa-coins"></i> {{ $habit->coin_reward }}</span>
                                <span style="margin-left: 0.5rem; font-size: 0.8rem;">{{ $habit->streak_status }}</span>
                            </p>
                        </div>
                    </div>
                    <div class="habit-actions">
                        <form action="{{ route('habits.toggle', $habit) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn {{ $isCompleted ? 'btn-secondary' : 'btn-success' }}" style="border-radius: var(--radius-full); width: 40px; height: 40px; padding: 0;" title="{{ $isCompleted ? 'Undo' : 'Complete' }}">
                                <i class="fa-solid {{ $isCompleted ? 'fa-rotate-left' : 'fa-check' }}"></i>
                            </button>
                        </form>
                    </div>
                </div>
            @endforeach
        @endif
    </div>
    
    <!-- Right Column: Stats & Top Habits -->
    <div>
        <!-- Stats Mini Cards -->
        <div class="grid grid-cols-2 gap-4 mb-8">
            <div class="card text-center" style="padding: 1rem;">
                <div style="font-size: 2rem; color: var(--primary); margin-bottom: 0.5rem;"><i class="fa-solid fa-calendar-check"></i></div>
                <div style="font-weight: 700; font-size: 1.25rem;">{{ $monthlyCompletions }}</div>
                <div style="font-size: 0.75rem; color: var(--text-secondary);">Monthly Completions</div>
            </div>
            <div class="card text-center" style="padding: 1rem;">
                <div style="font-size: 2rem; color: var(--warning); margin-bottom: 0.5rem;"><i class="fa-solid fa-fire"></i></div>
                <div style="font-weight: 700; font-size: 1.25rem;">{{ $user->longest_streak }}</div>
                <div style="font-size: 0.75rem; color: var(--text-secondary);">Longest Streak</div>
            </div>
        </div>

        <!-- Weekly Graph (Simplified CSS representation) -->
        <div class="card mb-8">
            <h3 class="card-header">Weekly Progress</h3>
            <div class="flex justify-between items-end" style="height: 120px; padding-top: 1rem;">
                @foreach($weeklyData as $day)
                    @php $height = $day['total'] > 0 ? ($day['completed'] / $day['total']) * 100 : 0; @endphp
                    <div class="flex" style="flex-direction: column; align-items: center; width: 14%;">
                        <div style="width: 100%; height: 100px; display: flex; align-items: flex-end; background: var(--bg-hover); border-radius: var(--radius-sm); overflow: hidden;">
                            <div style="width: 100%; height: {{ $height }}%; background: linear-gradient(0deg, var(--primary), var(--secondary)); border-radius: var(--radius-sm); transition: height 0.5s;"></div>
                        </div>
                        <div style="font-size: 0.7rem; margin-top: 0.5rem; color: var(--text-secondary);">{{ substr($day['day'], 0, 1) }}</div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Top Habits -->
        <div class="card mb-8">
            <h3 class="card-header">Top Streaks</h3>
            @if($topHabits->isEmpty())
                <p class="text-secondary text-center">No streaks yet.</p>
            @else
                @foreach($topHabits as $habit)
                    <div class="flex justify-between items-center mb-2" style="font-size: 0.875rem;">
                        <span style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 60%;">{{ $habit->category->icon }} {{ $habit->name }}</span>
                        <span class="badge badge-warning" style="background: rgba(245, 158, 11, 0.1);"><i class="fa-solid fa-fire" style="color: var(--warning);"></i> {{ $habit->current_streak }}</span>
                    </div>
                @endforeach
            @endif
        </div>

        <!-- Recent Badges -->
        <div class="card">
            <h3 class="card-header">Recent Badges</h3>
            @if($recentBadges->isEmpty())
                <p class="text-secondary text-center">Complete habits to earn badges!</p>
            @else
                <div class="flex gap-2" style="flex-wrap: wrap;">
                    @foreach($recentBadges as $badge)
                        <div title="{{ $badge->name }}: {{ $badge->description }}" style="background: var(--bg-hover); padding: 0.5rem; border-radius: var(--radius-md); font-size: 1.5rem; cursor: help; border: 1px solid rgba(255,255,255,0.05);">
                            {{ $badge->icon }}
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
