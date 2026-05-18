@extends('layouts.app')

@section('title', 'Admin Dashboard')

@section('content')
<div class="mb-8">
    <h1><i class="fa-solid fa-shield text-primary"></i> Admin Control Center</h1>
    <p>Manage the HabitQuest platform.</p>
</div>

<div class="grid md:grid-cols-4 gap-4 mb-8">
    <div class="card text-center">
        <div style="font-size: 2rem; color: var(--primary);"><i class="fa-solid fa-users"></i></div>
        <div style="font-size: 1.5rem; font-weight: bold;">{{ $totalUsers }}</div>
        <div class="text-secondary text-sm">Total Players</div>
    </div>
    <div class="card text-center">
        <div style="font-size: 2rem; color: var(--success);"><i class="fa-solid fa-list-check"></i></div>
        <div style="font-size: 1.5rem; font-weight: bold;">{{ $totalHabits }}</div>
        <div class="text-secondary text-sm">Active Quests</div>
    </div>
    <div class="card text-center">
        <div style="font-size: 2rem; color: var(--warning);"><i class="fa-solid fa-check-double"></i></div>
        <div style="font-size: 1.5rem; font-weight: bold;">{{ $totalCompletions }}</div>
        <div class="text-secondary text-sm">Total Completions</div>
    </div>
    <div class="card text-center">
        <div style="font-size: 2rem; color: var(--info);"><i class="fa-solid fa-tags"></i></div>
        <div style="font-size: 1.5rem; font-weight: bold;">{{ $totalCategories }}</div>
        <div class="text-secondary text-sm">Categories</div>
    </div>
</div>

<div class="grid lg:grid-cols-2 gap-8">
    <div class="card">
        <h3 class="card-header">Recent Registrations</h3>
        <table style="width: 100%; border-collapse: collapse; text-align: left;">
            <thead>
                <tr style="border-bottom: 1px solid var(--bg-hover);">
                    <th style="padding: 0.5rem;">Player</th>
                    <th style="padding: 0.5rem;">Joined</th>
                </tr>
            </thead>
            <tbody>
                @foreach($recentUsers as $u)
                <tr style="border-bottom: 1px solid var(--bg-hover);">
                    <td style="padding: 0.5rem;">{{ $u->name }} <span class="text-secondary text-sm">({{ $u->email }})</span></td>
                    <td style="padding: 0.5rem; font-size: 0.875rem;">{{ $u->created_at->diffForHumans() }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    
    <div class="card">
        <h3 class="card-header">Top Players (Leaderboard)</h3>
        <table style="width: 100%; border-collapse: collapse; text-align: left;">
            <thead>
                <tr style="border-bottom: 1px solid var(--bg-hover);">
                    <th style="padding: 0.5rem;">Rank</th>
                    <th style="padding: 0.5rem;">Player</th>
                    <th style="padding: 0.5rem;">Level / XP</th>
                </tr>
            </thead>
            <tbody>
                @foreach($topUsers as $index => $u)
                <tr style="border-bottom: 1px solid var(--bg-hover);">
                    <td style="padding: 0.5rem; font-weight: bold; color: var(--warning);">#{{ $index + 1 }}</td>
                    <td style="padding: 0.5rem;">{{ $u->name }}</td>
                    <td style="padding: 0.5rem;"><span class="badge badge-primary">Lvl {{ $u->level }}</span> <span style="font-size: 0.8rem;" class="text-secondary">{{ $u->xp }} XP</span></td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
