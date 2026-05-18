@extends('layouts.app')

@section('title', $habit->name)

@section('content')
<div class="mb-8">
    <div class="flex justify-between items-start">
        <div>
            <a href="{{ route('habits.index') }}" class="btn btn-secondary btn-sm mb-4"><i class="fa-solid fa-arrow-left"></i> Back</a>
            <div class="flex items-center gap-3">
                <div class="habit-icon" style="background-color: {{ $habit->category->color }}20; color: {{ $habit->category->color }}; font-size: 2rem; width: 4rem; height: 4rem; display: flex; align-items: center; justify-content: center; border-radius: var(--radius-md);">
                    {{ $habit->category->icon }}
                </div>
                <div>
                    <h1 style="margin-bottom: 0;">{{ $habit->name }}</h1>
                    <span class="badge" style="background-color: {{ $habit->category->color }}40; color: #fff;">{{ $habit->category->name }}</span>
                    @if(!$habit->is_active)
                        <span class="badge badge-danger">Inactive</span>
                    @endif
                </div>
            </div>
        </div>
        <div class="flex gap-2">
            <form action="{{ route('habits.toggle', $habit) }}" method="POST">
                @csrf
                @php $isCompleted = $habit->isCompletedToday(); @endphp
                <button type="submit" class="btn {{ $isCompleted ? 'btn-secondary' : 'btn-success' }}">
                    <i class="fa-solid {{ $isCompleted ? 'fa-rotate-left' : 'fa-check' }}"></i>
                    {{ $isCompleted ? 'Undo Today' : 'Complete Today' }}
                </button>
            </form>
            <a href="{{ route('habits.edit', $habit) }}" class="btn btn-primary"><i class="fa-solid fa-pen"></i> Edit</a>
        </div>
    </div>
    @if($habit->description)
        <p class="mt-4 text-secondary" style="font-size: 1.1rem; max-width: 800px;">{{ $habit->description }}</p>
    @endif
</div>

<div class="grid lg:grid-cols-3 gap-8">
    <div class="lg:col-span-2">
        <!-- Stats Grid -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
            <div class="card text-center" style="padding: 1rem;">
                <div style="font-size: 1.5rem; color: var(--warning);"><i class="fa-solid fa-fire"></i></div>
                <div style="font-weight: 700; font-size: 1.5rem;">{{ $habit->current_streak }}</div>
                <div style="font-size: 0.75rem; color: var(--text-secondary);">Current Streak</div>
            </div>
            <div class="card text-center" style="padding: 1rem;">
                <div style="font-size: 1.5rem; color: #f59e0b;"><i class="fa-solid fa-crown"></i></div>
                <div style="font-weight: 700; font-size: 1.5rem;">{{ $habit->best_streak }}</div>
                <div style="font-size: 0.75rem; color: var(--text-secondary);">Best Streak</div>
            </div>
            <div class="card text-center" style="padding: 1rem;">
                <div style="font-size: 1.5rem; color: var(--success);"><i class="fa-solid fa-check-double"></i></div>
                <div style="font-weight: 700; font-size: 1.5rem;">{{ $habit->total_completions }}</div>
                <div style="font-size: 0.75rem; color: var(--text-secondary);">Total Completions</div>
            </div>
            <div class="card text-center" style="padding: 1rem;">
                <div style="font-size: 1.5rem; color: var(--info);"><i class="fa-solid fa-chart-pie"></i></div>
                <div style="font-weight: 700; font-size: 1.5rem;">{{ $habit->completionPercentage(30) }}%</div>
                <div style="font-size: 0.75rem; color: var(--text-secondary);">30-Day Rate</div>
            </div>
        </div>

        <!-- Quest Rewards Info -->
        <div class="card mb-8">
            <h3 class="card-header">Quest Rewards</h3>
            <div class="flex gap-4">
                <div class="flex-1 text-center" style="background: var(--bg-dark); padding: 1rem; border-radius: var(--radius-md);">
                    <div style="font-size: 2rem; color: var(--xp-color);"><i class="fa-solid fa-bolt"></i></div>
                    <div style="font-weight: bold; font-size: 1.2rem;">+{{ $habit->xp_reward }} XP</div>
                    <div class="text-secondary" style="font-size: 0.8rem;">per completion</div>
                </div>
                <div class="flex-1 text-center" style="background: var(--bg-dark); padding: 1rem; border-radius: var(--radius-md);">
                    <div style="font-size: 2rem; color: var(--coin-color);"><i class="fa-solid fa-coins"></i></div>
                    <div style="font-weight: bold; font-size: 1.2rem;">+{{ $habit->coin_reward }} Coins</div>
                    <div class="text-secondary" style="font-size: 0.8rem;">per completion</div>
                </div>
            </div>
            <p class="text-center text-secondary mt-4" style="font-size: 0.875rem;">Keep your streak alive for bonus multipliers!</p>
        </div>
        
        <!-- Milestones -->
        <div class="card">
            <div class="flex justify-between items-center card-header">
                <h3 style="margin: 0;">Active Milestones</h3>
                <a href="{{ route('milestones.create') }}" class="btn btn-secondary btn-sm">Add Milestone</a>
            </div>
            
            @php $activeMilestones = $habit->milestones->where('is_completed', false); @endphp
            
            @if($activeMilestones->isEmpty())
                <p class="text-secondary text-center py-4">No active milestones for this habit.</p>
            @else
                <div class="grid gap-4 mt-4">
                    @foreach($activeMilestones as $milestone)
                        <div style="background: var(--bg-dark); padding: 1rem; border-radius: var(--radius-md); border: 1px solid var(--bg-hover);">
                            <div class="flex justify-between items-center mb-2">
                                <h4 style="margin: 0;">{{ $milestone->title }}</h4>
                                <span style="font-weight: bold; color: var(--primary);">{{ $milestone->current_count }} / {{ $milestone->target_count }}</span>
                            </div>
                            <div class="progress-container mb-2">
                                <div class="progress-bar" style="width: {{ $milestone->progress }}%;"></div>
                            </div>
                            <div class="flex gap-2">
                                <span class="badge badge-warning" style="font-size: 0.7rem;"><i class="fa-solid fa-bolt"></i> {{ $milestone->xp_reward }}</span>
                                <span class="badge badge-warning" style="background: rgba(252, 211, 77, 0.2); color: #fbbf24; font-size: 0.7rem;"><i class="fa-solid fa-coins"></i> {{ $milestone->coin_reward }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    <div>
        <!-- Recent Completions Log -->
        <div class="card">
            <h3 class="card-header">Recent Activity</h3>
            @if($habit->completions->isEmpty())
                <p class="text-secondary text-center">No completions yet.</p>
            @else
                <div style="max-height: 400px; overflow-y: auto; padding-right: 0.5rem;">
                    @foreach($habit->completions as $completion)
                        <div class="flex justify-between items-center py-3 border-b" style="border-bottom: 1px solid var(--bg-hover);">
                            <div>
                                <div style="font-weight: 500;">
                                    @if($completion->completed_date->isToday())
                                        Today
                                    @elseif($completion->completed_date->isYesterday())
                                        Yesterday
                                    @else
                                        {{ $completion->completed_date->format('M j, Y') }}
                                    @endif
                                </div>
                            </div>
                            <div class="text-right">
                                <span style="color: var(--xp-color); font-size: 0.8rem; font-weight: bold;">+{{ $completion->xp_earned }} XP</span><br>
                                <span style="color: var(--coin-color); font-size: 0.8rem; font-weight: bold;">+{{ $completion->coins_earned }} <i class="fa-solid fa-coins"></i></span>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
