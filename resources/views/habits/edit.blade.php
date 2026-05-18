@extends('layouts.app')

@section('title', 'Edit Habit')

@section('content')
<div class="mb-8">
    <div class="flex justify-between items-center">
        <div>
            <a href="{{ route('habits.index') }}" class="btn btn-secondary btn-sm mb-4"><i class="fa-solid fa-arrow-left"></i> Back</a>
            <h1>Edit Habit</h1>
        </div>
        <form action="{{ route('habits.destroy', $habit) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this habit? All history will be lost.');">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-danger"><i class="fa-solid fa-trash"></i> Delete Habit</button>
        </form>
    </div>
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

    <form action="{{ route('habits.update', $habit) }}" method="POST">
        @csrf
        @method('PUT')
        
        <div class="grid md:grid-cols-2 gap-4">
            <div class="form-group md:col-span-2">
                <label class="form-label" for="name">Habit Name *</label>
                <input type="text" id="name" name="name" class="form-control" value="{{ old('name', $habit->name) }}" required>
            </div>

            <div class="form-group md:col-span-2">
                <label class="form-label" for="description">Description</label>
                <textarea id="description" name="description" class="form-control" rows="3">{{ old('description', $habit->description) }}</textarea>
            </div>

            <div class="form-group">
                <label class="form-label" for="category_id">Category *</label>
                <select id="category_id" name="category_id" class="form-control" required>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ old('category_id', $habit->category_id) == $category->id ? 'selected' : '' }}>
                            {{ $category->icon }} {{ $category->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label class="form-label" for="frequency">Frequency *</label>
                <select id="frequency" name="frequency" class="form-control" required>
                    <option value="daily" {{ old('frequency', $habit->frequency) == 'daily' ? 'selected' : '' }}>Daily</option>
                    <option value="weekly" {{ old('frequency', $habit->frequency) == 'weekly' ? 'selected' : '' }}>Weekly</option>
                    <option value="monthly" {{ old('frequency', $habit->frequency) == 'monthly' ? 'selected' : '' }}>Monthly</option>
                </select>
            </div>

            <div class="form-group">
                <label class="form-label" for="priority">Difficulty / Priority *</label>
                <select id="priority" name="priority" class="form-control" required>
                    <option value="low" {{ old('priority', $habit->priority) == 'low' ? 'selected' : '' }}>Low (Easy) - 5 XP</option>
                    <option value="medium" {{ old('priority', $habit->priority) == 'medium' ? 'selected' : '' }}>Medium (Normal) - 10 XP</option>
                    <option value="high" {{ old('priority', $habit->priority) == 'high' ? 'selected' : '' }}>High (Hard) - 20 XP</option>
                </select>
            </div>

            <div class="form-group">
                <label class="form-label" for="reminder_time">Daily Reminder Time</label>
                <input type="time" id="reminder_time" name="reminder_time" class="form-control" value="{{ old('reminder_time', $habit->reminder_time ? substr($habit->reminder_time, 0, 5) : '') }}">
            </div>

            <div class="form-group md:col-span-2 mt-4">
                <label class="flex items-center gap-2">
                    <input type="hidden" name="is_active" value="0">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', $habit->is_active) ? 'checked' : '' }} style="width: auto;">
                    Habit is Active
                </label>
                <p class="text-secondary mt-1" style="font-size: 0.875rem;">Inactive habits won't appear on your daily dashboard.</p>
            </div>
        </div>

        <div class="mt-8 pt-4 border-t" style="border-top: 1px solid var(--bg-hover); display: flex; justify-content: flex-end;">
            <button type="submit" class="btn btn-primary btn-lg"><i class="fa-solid fa-save"></i> Save Changes</button>
        </div>
    </form>
</div>
@endsection
