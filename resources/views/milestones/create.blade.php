@extends('layouts.app')

@section('title', 'Set Milestone')

@section('content')
<div class="mb-8">
    <a href="{{ route('milestones.index') }}" class="btn btn-secondary btn-sm mb-4"><i class="fa-solid fa-arrow-left"></i> Back</a>
    <h1>Set New Milestone</h1>
    <p>Define a long-term goal for extra rewards.</p>
</div>

<div class="card" style="max-width: 600px;">
    @if ($errors->any())
        <div class="alert alert-error">
            <ul style="list-style-position: inside;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('milestones.store') }}" method="POST">
        @csrf
        
        <div class="form-group">
            <label class="form-label" for="title">Milestone Title *</label>
            <input type="text" id="title" name="title" class="form-control" value="{{ old('title') }}" required placeholder="e.g., Read 10 Books">
        </div>

        <div class="form-group">
            <label class="form-label" for="description">Description (Optional)</label>
            <textarea id="description" name="description" class="form-control" rows="2">{{ old('description') }}</textarea>
        </div>

        <div class="form-group">
            <label class="form-label" for="habit_id">Link to Habit (Optional)</label>
            <select id="habit_id" name="habit_id" class="form-control">
                <option value="">No Link (Manual Progress)</option>
                @foreach($habits as $habit)
                    <option value="{{ $habit->id }}" {{ old('habit_id') == $habit->id ? 'selected' : '' }}>
                        {{ $habit->name }}
                    </option>
                @endforeach
            </select>
            <p class="text-secondary mt-1" style="font-size: 0.75rem;">If linked, progress increases automatically when the habit is completed.</p>
        </div>

        <div class="form-group">
            <label class="form-label" for="target_count">Target Count *</label>
            <input type="number" id="target_count" name="target_count" class="form-control" value="{{ old('target_count', 10) }}" min="1" max="1000" required>
        </div>

        <div class="alert alert-info mt-4" style="font-size: 0.875rem;">
            <i class="fa-solid fa-gift"></i> Rewards are calculated automatically based on the target count (Min 50 XP, 25 Coins).
        </div>

        <div class="mt-8 pt-4 border-t" style="border-top: 1px solid var(--bg-hover); display: flex; justify-content: flex-end;">
            <button type="submit" class="btn btn-primary btn-lg">Set Milestone</button>
        </div>
    </form>
</div>
@endsection
