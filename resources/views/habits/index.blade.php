@extends('layouts.app')

@section('title', 'Habit Directory')

@section('content')
<div class="flex justify-between items-center mb-8">
    <div>
        <h1>Habit Directory</h1>
        <p>Manage your quests and routines.</p>
    </div>
    <a href="{{ route('habits.create') }}" class="btn btn-primary"><i class="fa-solid fa-plus"></i> New Habit</a>
</div>

<!-- Filters -->
<div class="card mb-8">
    <form action="{{ route('habits.index') }}" method="GET" class="grid md:grid-cols-4 gap-4 items-end">
        <div>
            <label class="form-label" for="category">Category</label>
            <select name="category" id="category" class="form-control">
                <option value="">All Categories</option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                        {{ $category->icon }} {{ $category->name }}
                    </option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="form-label" for="frequency">Frequency</label>
            <select name="frequency" id="frequency" class="form-control">
                <option value="">All</option>
                <option value="daily" {{ request('frequency') == 'daily' ? 'selected' : '' }}>Daily</option>
                <option value="weekly" {{ request('frequency') == 'weekly' ? 'selected' : '' }}>Weekly</option>
                <option value="monthly" {{ request('frequency') == 'monthly' ? 'selected' : '' }}>Monthly</option>
            </select>
        </div>
        <div>
            <label class="form-label" for="search">Search</label>
            <input type="text" name="search" id="search" class="form-control" value="{{ request('search') }}" placeholder="Habit name...">
        </div>
        <div>
            <button type="submit" class="btn btn-secondary btn-block">Filter</button>
        </div>
    </form>
</div>

<!-- Habit List -->
<div class="grid md:grid-cols-2 lg:grid-cols-3 gap-4">
    @forelse($habits as $habit)
        <div class="card card-hoverable" style="{{ !$habit->is_active ? 'opacity: 0.6;' : '' }}">
            <div class="flex justify-between items-start mb-4">
                <div class="flex gap-2 items-center">
                    <div class="habit-icon" style="background-color: {{ $habit->category->color }}20; color: {{ $habit->category->color }}; width: 2.5rem; height: 2.5rem; font-size: 1.2rem;">
                        {{ $habit->category->icon }}
                    </div>
                    <div>
                        <h3 style="margin-bottom: 0; font-size: 1.1rem;">
                            <a href="{{ route('habits.show', $habit) }}" style="color: inherit;">{{ $habit->name }}</a>
                        </h3>
                        <span style="font-size: 0.75rem; color: var(--text-secondary);">{{ ucfirst($habit->frequency) }}</span>
                    </div>
                </div>
                <div class="dropdown" style="position: relative;">
                    <a href="{{ route('habits.edit', $habit) }}" class="btn btn-secondary btn-sm" title="Edit"><i class="fa-solid fa-pen"></i></a>
                </div>
            </div>
            
            <p style="font-size: 0.875rem; min-height: 2.5rem;">
                {{ \Str::limit($habit->description ?? 'No description.', 60) }}
            </p>
            
            <div class="flex justify-between items-center mt-4 pt-4 border-t" style="border-top: 1px solid var(--bg-hover);">
                <div>
                    <span class="badge badge-warning" title="XP"><i class="fa-solid fa-bolt"></i> {{ $habit->xp_reward }}</span>
                </div>
                <div style="font-size: 0.875rem; font-weight: 600;">
                    {!! str_replace('days', 'd', $habit->streak_status) !!}
                </div>
            </div>
        </div>
    @empty
        <div class="lg:col-span-3 text-center py-8">
            <p class="text-secondary mb-4">No habits found matching your criteria.</p>
            <a href="{{ route('habits.index') }}" class="btn btn-secondary">Clear Filters</a>
        </div>
    @endforelse
</div>
@endsection
