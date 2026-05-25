@extends('layouts.app')

@section('title', 'Habits for ' . $dateFormatted)

@section('content')
<div class="flex justify-between items-center mb-8">
    <div>
        <a href="{{ route('calendar') }}" class="btn btn-secondary mb-4" style="display: inline-block;">
            <i class="fa-solid fa-arrow-left"></i> Back to Calendar
        </a>
        <h1>{{ $dateFormatted }}</h1>
        <p>Track your habits for this specific day.</p>
    </div>
</div>

@if($habits->isEmpty())
    <div class="card text-center py-12" style="color: var(--text-secondary);">
        <i class="fa-solid fa-ghost fa-3x mb-4" style="opacity: 0.5;"></i>
        <p style="font-size: 1.25rem;">No active habits found.</p>
        <p style="margin-top: 0.5rem;">Go to your Habits page to create a new one.</p>
    </div>
@else
    <div class="grid md:grid-cols-2 gap-6">
        @foreach($habits as $habit)
            @php
                $isCompleted = $habit->isCompletedOn($date);
            @endphp
            <div class="card" style="display: flex; justify-content: space-between; align-items: center; border-left: 4px solid {{ $habit->category->color ?? '#6366f1' }}; {{ $isCompleted ? 'opacity: 0.7;' : '' }}">
                <div>
                    <h3 style="margin: 0; font-size: 1.25rem; {{ $isCompleted ? 'text-decoration: line-through; color: var(--text-secondary);' : '' }}">
                        <span style="margin-right: 0.5rem;">{{ $habit->category->icon ?? '🎯' }}</span>
                        {{ $habit->name }}
                    </h3>
                    <p style="margin: 0.5rem 0 0 0; color: var(--text-secondary); font-size: 0.9rem;">
                        {{ ucfirst($habit->frequency) }} • Priority: {{ ucfirst($habit->priority) }}
                        @if($isCompleted)
                            • <span style="color: var(--success); font-weight: 600;"><i class="fa-solid fa-check"></i> Completed</span>
                        @endif
                    </p>
                </div>
                
                <form action="{{ route('calendar.toggle', ['date' => $date, 'habit' => $habit->id]) }}" method="POST">
                    @csrf
                    <button type="submit" class="btn {{ $isCompleted ? 'btn-secondary' : 'btn-primary' }}" style="border-radius: 50%; width: 3rem; height: 3rem; padding: 0; display: flex; align-items: center; justify-content: center;" title="{{ $isCompleted ? 'Mark as incomplete' : 'Mark as complete' }}">
                        @if($isCompleted)
                            <i class="fa-solid fa-rotate-left"></i>
                        @else
                            <i class="fa-solid fa-check" style="font-size: 1.25rem;"></i>
                        @endif
                    </button>
                </form>
            </div>
        @endforeach
    </div>
@endif
@endsection
