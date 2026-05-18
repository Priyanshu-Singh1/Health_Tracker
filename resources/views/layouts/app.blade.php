<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Habit Tracker') - Level Up Your Life</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    @stack('styles')
</head>
<body>
    
    @auth
    <nav class="navbar">
        <div class="container">
            <a href="{{ route('dashboard') }}" class="nav-brand">
                <i class="fa-solid fa-gamepad"></i> HabitQuest
            </a>
            
            <div class="nav-links">
                <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">Dashboard</a>
                <a href="{{ route('habits.index') }}" class="nav-link {{ request()->routeIs('habits.*') ? 'active' : '' }}">Habits</a>
                <a href="{{ route('calendar') }}" class="nav-link {{ request()->routeIs('calendar') ? 'active' : '' }}">Calendar</a>
                <a href="{{ route('milestones.index') }}" class="nav-link {{ request()->routeIs('milestones.*') ? 'active' : '' }}">Milestones</a>
                <a href="{{ route('rewards.index') }}" class="nav-link {{ request()->routeIs('rewards.*') ? 'active' : '' }}">Rewards</a>
                @if(Auth::user()->is_admin)
                    <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.*') ? 'active' : '' }}"><i class="fa-solid fa-shield"></i></a>
                @endif
            </div>

            <div class="flex items-center gap-4">
                <div class="user-stats-bar">
                    <span class="stat-item stat-level" title="Level"><i class="fa-solid fa-star"></i> {{ Auth::user()->level }}</span>
                    <span class="stat-item stat-xp" title="XP"><i class="fa-solid fa-bolt"></i> {{ Auth::user()->xp }}</span>
                    <span class="stat-item stat-coin" title="Coins"><i class="fa-solid fa-coins"></i> {{ Auth::user()->coins }}</span>
                </div>
                
                <div style="position: relative;" id="userMenuBtn">
                    <button class="btn btn-secondary btn-sm" style="border-radius: var(--radius-full); width: 40px; height: 40px; padding: 0;">
                        {{ substr(Auth::user()->name, 0, 1) }}
                    </button>
                    <div id="userMenu" class="card" style="display: none; position: absolute; right: 0; top: 120%; min-width: 200px; z-index: 100; padding: 0.5rem;">
                        <div style="padding: 0.5rem; border-bottom: 1px solid var(--bg-hover); margin-bottom: 0.5rem;">
                            <strong>{{ Auth::user()->name }}</strong><br>
                            <small class="text-secondary">{{ Auth::user()->rank }}</small>
                        </div>
                        <a href="{{ route('profile') }}" class="nav-link" style="display: block; margin-bottom: 0.25rem;">Profile</a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="nav-link" style="width: 100%; text-align: left; background: none; border: none; cursor: pointer;">Logout</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </nav>
    @endauth

    <main class="container" style="padding-top: 2rem; padding-bottom: 4rem;">
        @if(session('success'))
            <div class="alert alert-success">
                <i class="fa-solid fa-check-circle mr-2"></i> {{ session('success') }}
            </div>
        @endif
        
        @if(session('error'))
            <div class="alert alert-error">
                <i class="fa-solid fa-triangle-exclamation mr-2"></i> {{ session('error') }}
            </div>
        @endif
        
        @if(session('info'))
            <div class="alert alert-info">
                <i class="fa-solid fa-circle-info mr-2"></i> {{ session('info') }}
            </div>
        @endif

        @yield('content')
    </main>

    <script>
        // Simple dropdown toggle
        const menuBtn = document.getElementById('userMenuBtn');
        const menu = document.getElementById('userMenu');
        if(menuBtn && menu) {
            menuBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                menu.style.display = menu.style.display === 'none' ? 'block' : 'none';
            });
            document.addEventListener('click', () => {
                menu.style.display = 'none';
            });
        }
    </script>
    @stack('scripts')
</body>
</html>
