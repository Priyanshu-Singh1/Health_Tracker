@extends('layouts.app')

@section('title', 'Player Profile')

@section('content')
<div class="mb-8 text-center">
    <div style="width: 100px; height: 100px; border-radius: 50%; background: linear-gradient(135deg, var(--primary), var(--secondary)); display: flex; align-items: center; justify-content: center; font-size: 3rem; margin: 0 auto 1rem; border: 4px solid var(--bg-dark); box-shadow: 0 0 0 2px var(--primary);">
        {{ substr($user->name, 0, 1) }}
    </div>
    <h1>{{ $user->name }}</h1>
    <p class="text-secondary">{{ $user->email }}</p>
    <div class="badge badge-primary mt-2" style="font-size: 1rem;">{{ $user->rank }} - Level {{ $user->level }}</div>
</div>

<div class="grid lg:grid-cols-3 gap-8">
    <div class="lg:col-span-1">
        <div class="card mb-4">
            <h3 class="card-header">Edit Profile</h3>
            <form action="{{ route('profile.update') }}" method="POST">
                @csrf
                @method('PUT')
                <div class="form-group">
                    <label class="form-label">Name</label>
                    <input type="text" name="name" class="form-control" value="{{ old('name', $user->name) }}" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" value="{{ old('email', $user->email) }}" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Bio</label>
                    <textarea name="bio" class="form-control" rows="3">{{ old('bio', $user->bio) }}</textarea>
                </div>
                <button type="submit" class="btn btn-primary btn-block">Save Profile</button>
            </form>
        </div>

        <div class="card">
            <h3 class="card-header">Change Password</h3>
            <form action="{{ route('profile.password') }}" method="POST">
                @csrf
                @method('PUT')
                <div class="form-group">
                    <label class="form-label">Current Password</label>
                    <input type="password" name="current_password" class="form-control" required>
                </div>
                <div class="form-group">
                    <label class="form-label">New Password</label>
                    <input type="password" name="password" class="form-control" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Confirm Password</label>
                    <input type="password" name="password_confirmation" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-secondary btn-block">Update Password</button>
            </form>
        </div>
    </div>

    <div class="lg:col-span-2">
        <div class="card mb-8">
            <h3 class="card-header">Lifetime Statistics</h3>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div class="text-center p-4" style="background: var(--bg-dark); border-radius: var(--radius-md);">
                    <div style="font-size: 1.5rem; color: var(--xp-color);"><i class="fa-solid fa-bolt"></i></div>
                    <div style="font-weight: bold; font-size: 1.25rem;">{{ $user->xp }}</div>
                    <div style="font-size: 0.75rem;" class="text-secondary">Total XP</div>
                </div>
                <div class="text-center p-4" style="background: var(--bg-dark); border-radius: var(--radius-md);">
                    <div style="font-size: 1.5rem; color: var(--success);"><i class="fa-solid fa-check-double"></i></div>
                    <div style="font-weight: bold; font-size: 1.25rem;">{{ $user->total_habits_completed }}</div>
                    <div style="font-size: 0.75rem;" class="text-secondary">Habits Done</div>
                </div>
                <div class="text-center p-4" style="background: var(--bg-dark); border-radius: var(--radius-md);">
                    <div style="font-size: 1.5rem; color: var(--warning);"><i class="fa-solid fa-fire"></i></div>
                    <div style="font-weight: bold; font-size: 1.25rem;">{{ $user->longest_streak }}</div>
                    <div style="font-size: 0.75rem;" class="text-secondary">Max Streak</div>
                </div>
                <div class="text-center p-4" style="background: var(--bg-dark); border-radius: var(--radius-md);">
                    <div style="font-size: 1.5rem; color: var(--info);"><i class="fa-solid fa-trophy"></i></div>
                    <div style="font-weight: bold; font-size: 1.25rem;">{{ $user->badges->count() }}</div>
                    <div style="font-size: 0.75rem;" class="text-secondary">Badges Earned</div>
                </div>
            </div>
        </div>

        <div class="card">
            <h3 class="card-header">Badge Cabinet</h3>
            @if($user->badges->isEmpty())
                <p class="text-secondary text-center py-4">No badges earned yet. Keep going!</p>
            @else
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    @foreach($user->badges as $badge)
                        <div class="text-center p-4" style="background: var(--bg-dark); border-radius: var(--radius-md); border: 1px solid var(--bg-hover);" title="{{ $badge->description }}">
                            <div style="font-size: 2.5rem; margin-bottom: 0.5rem; filter: drop-shadow(0 0 5px rgba(255,255,255,0.2));">{{ $badge->icon }}</div>
                            <div style="font-weight: 600; font-size: 0.875rem; line-height: 1.2;">{{ $badge->name }}</div>
                            <div style="font-size: 0.7rem; color: var(--text-secondary); margin-top: 0.25rem;">{{ \Carbon\Carbon::parse($badge->pivot->earned_at)->format('M d, Y') }}</div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
