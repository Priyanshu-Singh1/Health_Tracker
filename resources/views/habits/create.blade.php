@extends('layouts.app')

@section('title', 'Create New Habit')

@section('content')
<div class="mb-8">
    <a href="{{ route('habits.index') }}" class="btn btn-secondary btn-sm mb-4"><i class="fa-solid fa-arrow-left"></i> Back to Directory</a>
    <h1>Create New Habit</h1>
    <p>Define your new quest.</p>
</div>

<div class="card" style="max-width: 800px;">
    @if ($errors->any())
        <div class="alert alert-error">
            <ul style="list-style-position: inside;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('habits.store') }}" method="POST">
        @csrf
        
        <div class="grid md:grid-cols-2 gap-4">
            <div class="form-group md:col-span-2">
                <label class="form-label" for="name">Habit Name *</label>
                <input type="text" id="name" name="name" class="form-control" value="{{ old('name') }}" required placeholder="e.g., Drink 2L of water">
            </div>

            <div class="form-group md:col-span-2">
                <label class="form-label" for="description">Description (Optional)</label>
                <textarea id="description" name="description" class="form-control" rows="3" placeholder="Why is this habit important?">{{ old('description') }}</textarea>
            </div>

            <div class="form-group">
                <label class="form-label" for="category_id">Category *</label>
                <select id="category_id" name="category_id" class="form-control" required>
                    <option value="">Select Category</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                            {{ $category->icon }} {{ $category->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label class="form-label" for="frequency">Frequency *</label>
                <select id="frequency" name="frequency" class="form-control" required>
                    <option value="daily" {{ old('frequency') == 'daily' ? 'selected' : '' }}>Daily</option>
                    <option value="weekly" {{ old('frequency') == 'weekly' ? 'selected' : '' }}>Weekly</option>
                    <option value="monthly" {{ old('frequency') == 'monthly' ? 'selected' : '' }}>Monthly</option>
                </select>
            </div>

            <div class="form-group">
                <label class="form-label" for="priority">Difficulty / Priority *</label>
                <select id="priority" name="priority" class="form-control" required>
                    <option value="low" {{ old('priority') == 'low' ? 'selected' : '' }}>Low (Easy) - 5 XP</option>
                    <option value="medium" {{ old('priority', 'medium') == 'medium' ? 'selected' : '' }}>Medium (Normal) - 10 XP</option>
                    <option value="high" {{ old('priority') == 'high' ? 'selected' : '' }}>High (Hard) - 20 XP</option>
                </select>
            </div>

            <div class="form-group">
                <label class="form-label" for="reminder_time">Daily Reminder Time (Optional)</label>
                <input type="time" id="reminder_time" name="reminder_time" class="form-control" value="{{ old('reminder_time') }}">
            </div>
        </div>

        <div class="mt-8 pt-4 border-t" style="border-top: 1px solid var(--bg-hover); display: flex; justify-content: flex-end;">
            <button type="submit" class="btn btn-primary btn-lg"><i class="fa-solid fa-plus"></i> Create Habit</button>
        </div>
    </form>
</div>
@endsection
