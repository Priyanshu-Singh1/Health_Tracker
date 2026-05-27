@extends('layouts.auth')

@section('title', 'Forgot Password')

@section('content')
    <div class="text-center mb-8">
        <div class="nav-brand" style="justify-content: center; font-size: 2rem; margin-bottom: 1rem;">
            <i class="fa-solid fa-gamepad"></i> HabitQuest
        </div>
        <h2>Lost Your Keys?</h2>
        <p>Enter your email and we'll send you a magical link to forge a new password.</p>
    </div>

    @if (session('success'))
        <div class="alert alert-success mb-6">
            {{ session('success') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-error mb-6">
            <ul style="list-style-position: inside;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('password.email') }}">
        @csrf
        <div class="form-group mb-8">
            <label class="form-label" for="email">Email Address</label>
            <input type="email" id="email" name="email" class="form-control" value="{{ old('email') }}" required autofocus>
        </div>

        <button type="submit" class="btn btn-primary btn-block btn-lg">Send Recovery Link</button>
    </form>

    <div class="text-center mt-8">
        <p>Remembered your password? <a href="{{ route('login') }}">Back to Login</a></p>
    </div>
@endsection
