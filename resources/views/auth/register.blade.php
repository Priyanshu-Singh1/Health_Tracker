@extends('layouts.auth')

@section('title', 'Create Character')

@section('content')
    <div class="text-center mb-8">
        <div class="nav-brand" style="justify-content: center; font-size: 2rem; margin-bottom: 1rem;">
            <i class="fa-solid fa-gamepad"></i> HabitQuest
        </div>
        <h2>Create Character</h2>
        <p>Start tracking habits and earning rewards.</p>
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

    <form method="POST" action="{{ route('register') }}">
        @csrf
        <div class="form-group">
            <label class="form-label" for="name">Character Name</label>
            <input type="text" id="name" name="name" class="form-control" value="{{ old('name') }}" required autofocus>
        </div>

        <div class="form-group">
            <label class="form-label" for="email">Email Address</label>
            <input type="email" id="email" name="email" class="form-control" value="{{ old('email') }}" required>
        </div>

        <div class="form-group">
            <label class="form-label" for="password">Password</label>
            <input type="password" id="password" name="password" class="form-control" required>
        </div>

        <div class="form-group mb-8">
            <label class="form-label" for="password_confirmation">Confirm Password</label>
            <input type="password" id="password_confirmation" name="password_confirmation" class="form-control" required>
        </div>

        <button type="submit" class="btn btn-primary btn-block btn-lg">Begin Adventure</button>
    </form>

    <div class="text-center mt-8">
        <p>Already have an account? <a href="{{ route('login') }}">Login</a></p>
    </div>
@endsection
