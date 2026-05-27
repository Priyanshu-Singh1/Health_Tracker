@extends('layouts.auth')

@section('title', 'Reset Password')

@section('content')
    <div class="text-center mb-8">
        <div class="nav-brand" style="justify-content: center; font-size: 2rem; margin-bottom: 1rem;">
            <i class="fa-solid fa-gamepad"></i> HabitQuest
        </div>
        <h2>Forge New Keys</h2>
        <p>Enter your new password below.</p>
    </div>

    @if ($errors->any())
        <div class="alert alert-error mb-6">
            <ul style="list-style-position: inside;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('password.update') }}">
        @csrf
        <input type="hidden" name="token" value="{{ $token }}">

        <div class="form-group">
            <label class="form-label" for="email">Email Address</label>
            <input type="email" id="email" name="email" class="form-control" value="{{ $email ?? old('email') }}" required autofocus readonly style="opacity: 0.7;">
        </div>

        <div class="form-group">
            <label class="form-label" for="password">New Password</label>
            <input type="password" id="password" name="password" class="form-control" required>
        </div>

        <div class="form-group mb-8">
            <label class="form-label" for="password_confirmation">Confirm Password</label>
            <input type="password" id="password_confirmation" name="password_confirmation" class="form-control" required>
        </div>

        <button type="submit" class="btn btn-primary btn-block btn-lg">Reset Password</button>
    </form>
@endsection
