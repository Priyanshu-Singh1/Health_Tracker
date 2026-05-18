@extends('layouts.app')

@section('title', 'Milestones')

@section('content')
<div class="flex justify-between items-center mb-8">
    <div>
        <h1>Milestones</h1>
        <p>Set long-term goals and earn massive rewards.</p>
    </div>
    <a href="{{ route('milestones.create') }}" class="btn btn-primary"><i class="fa-solid fa-plus"></i> New Milestone</a>
</div>

<div class="grid lg:grid-cols-2 gap-8">
    <!-- Active Milestones -->
    <div>
        <h2 class="mb-4">Active Quests</h2>
        @if($activeMilestones->isEmpty())
            <div class="card text-center py-8">
                <p class="text-secondary">No active milestones. Set a new goal!</p>
                <a href="{{ route('milestones.create') }}" class="btn btn-secondary mt-2">Create Milestone</a>
            </div>
        @else
            <div class="grid gap-4">
                @foreach($activeMilestones as $milestone)
                    <div class="card relative overflow-hidden" style="padding-bottom: 3rem;">
                        <div class="flex justify-between items-start mb-2">
                            <div>
                                <h3 style="margin-bottom: 0;">{{ $milestone->title }}</h3>
                                @if($milestone->habit)
                                    <span class="text-secondary" style="font-size: 0.875rem;"><i class="fa-solid fa-link"></i> Linked to: {{ $milestone->habit->name }}</span>
                                @endif
                            </div>
                            <form action="{{ route('milestones.destroy', $milestone) }}" method="POST" onsubmit="return confirm('Delete this milestone?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger" style="background: transparent; color: var(--danger);"><i class="fa-solid fa-trash"></i></button>
                            </form>
                        </div>
                        <p style="font-size: 0.875rem;">{{ $milestone->description }}</p>
                        
                        <div class="flex gap-2 mb-4">
                            <span class="badge badge-warning" title="Reward"><i class="fa-solid fa-bolt"></i> {{ $milestone->xp_reward }}</span>
                            <span class="badge badge-warning" style="background: rgba(252, 211, 77, 0.2); color: #fbbf24;"><i class="fa-solid fa-coins"></i> {{ $milestone->coin_reward }}</span>
                        </div>

                        <div style="position: absolute; bottom: 0; left: 0; width: 100%; background: var(--bg-dark); padding: 0.5rem 1rem; border-top: 1px solid var(--bg-hover);">
                            <div class="flex justify-between items-center" style="font-size: 0.75rem; font-weight: bold;">
                                <span>Progress</span>
                                <span>{{ $milestone->current_count }} / {{ $milestone->target_count }}</span>
                            </div>
                            <div class="progress-container mt-1" style="height: 0.5rem;">
                                <div class="progress-bar" style="width: {{ $milestone->progress }}%;"></div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <!-- Completed Milestones -->
    <div>
        <h2 class="mb-4">Completed Glories</h2>
        @if($completedMilestones->isEmpty())
            <div class="card text-center py-8" style="opacity: 0.7;">
                <p class="text-secondary">No completed milestones yet.</p>
            </div>
        @else
            <div class="grid gap-4">
                @foreach($completedMilestones as $milestone)
                    <div class="card" style="border-color: var(--success); background: linear-gradient(135deg, var(--bg-card), rgba(16, 185, 129, 0.05));">
                        <div class="flex items-center gap-4">
                            <div style="font-size: 2.5rem; color: var(--success);"><i class="fa-solid fa-medal"></i></div>
                            <div>
                                <h3 style="margin-bottom: 0;">{{ $milestone->title }}</h3>
                                <p class="text-secondary mb-0" style="font-size: 0.875rem;">Completed on {{ $milestone->completed_at->format('M j, Y') }}</p>
                                <div class="flex gap-2 mt-2">
                                    <span class="badge badge-success" style="font-size: 0.7rem;">+{{ $milestone->xp_reward }} XP</span>
                                    <span class="badge badge-success" style="font-size: 0.7rem;">+{{ $milestone->coin_reward }} Coins</span>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
@endsection
