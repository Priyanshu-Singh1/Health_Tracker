@extends('layouts.auth')

@section('title', 'Login')

@section('content')
    <div class="text-center mb-8">
        <div class="nav-brand" style="justify-content: center; font-size: 2rem; margin-bottom: 1rem;">
            <i class="fa-solid fa-gamepad"></i> HabitQuest
        </div>
        <h2>Welcome Back, Player!</h2>
        <p>Login to continue your journey.</p>
    </div>

    @if ($errors->any())
        <div class="alert alert-error">
            <ul style="list-style-position: inside;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}">
        @csrf
        <div class="form-group">
            <label class="form-label" for="email">Email Address</label>
            <input type="email" id="email" name="email" class="form-control" value="{{ old('email') }}" required autofocus>
        </div>

        <div class="form-group">
            <div class="flex justify-between items-center mb-1">
                <label class="form-label" for="password" style="margin: 0;">Password</label>
                <a href="{{ route('password.request') }}" style="font-size: 0.85rem; color: var(--primary);">Forgot Password?</a>
            </div>
            <input type="password" id="password" name="password" class="form-control" required>
        </div>

        <div class="form-group flex items-center gap-2 mb-8">
            <input type="checkbox" id="remember" name="remember" class="form-control" style="width: auto;">
            <label for="remember" style="margin: 0; color: var(--text-secondary);">Remember my login</label>
        </div>

        <button type="submit" class="btn btn-primary btn-block btn-lg">Login to Start Playing</button>
    </form>

    <div class="text-center mt-8">
        <p>Don't have an account? <a href="{{ route('register') }}">Create Character</a></p>
    </div>
@endsection
