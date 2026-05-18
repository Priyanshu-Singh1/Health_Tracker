@extends('layouts.app')

@section('title', 'Reward Store')

@section('content')
<div class="flex justify-between items-center mb-8">
    <div>
        <h1>Reward Store</h1>
        <p>Spend your hard-earned coins to treat yourself!</p>
    </div>
    <div class="card text-center" style="padding: 1rem 2rem; background: linear-gradient(135deg, rgba(245, 158, 11, 0.1), rgba(252, 211, 77, 0.1)); border-color: var(--warning);">
        <div style="font-size: 0.875rem; color: var(--text-secondary); text-transform: uppercase; letter-spacing: 1px;">Your Balance</div>
        <div style="font-size: 2.5rem; font-weight: 800; color: var(--coin-color); text-shadow: 0 0 10px rgba(252, 211, 77, 0.5);">
            <i class="fa-solid fa-coins"></i> {{ $user->coins }}
        </div>
    </div>
</div>

<div class="grid md:grid-cols-2 lg:grid-cols-4 gap-6 mb-12">
    @foreach($rewards as $reward)
        @php $canAfford = $user->coins >= $reward->cost; @endphp
        <div class="card {{ $canAfford ? 'card-hoverable' : '' }}" style="display: flex; flex-direction: column; {{ !$canAfford ? 'opacity: 0.7; filter: grayscale(50%);' : '' }}">
            <div class="text-center mb-4 pt-4">
                <div style="font-size: 4rem; margin-bottom: 1rem; filter: drop-shadow(0 10px 15px rgba(0,0,0,0.2));">{{ $reward->icon }}</div>
                <h3 style="font-size: 1.25rem;">{{ $reward->name }}</h3>
                <span class="badge badge-primary">{{ ucfirst(str_replace('_', ' ', $reward->category)) }}</span>
            </div>
            
            <p class="text-center text-secondary flex-1" style="font-size: 0.875rem;">{{ $reward->description }}</p>
            
            <div class="mt-4 pt-4 border-t" style="border-top: 1px solid var(--bg-hover);">
                <form action="{{ route('rewards.redeem', $reward) }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-block" style="background-color: {{ $canAfford ? 'var(--warning)' : 'var(--bg-hover)' }}; color: {{ $canAfford ? '#000' : 'var(--text-secondary)' }}; font-weight: bold;" {{ !$canAfford ? 'disabled' : '' }}>
                        <i class="fa-solid fa-coins"></i> {{ $reward->cost }} 
                        {{ $canAfford ? 'Redeem' : '(Need ' . ($reward->cost - $user->coins) . ' more)' }}
                    </button>
                </form>
            </div>
        </div>
    @endforeach
</div>

<!-- Recent Redemptions -->
<div class="card">
    <div class="flex justify-between items-center card-header">
        <h3 style="margin: 0;">Your Recent Treats</h3>
        <a href="#" class="btn btn-secondary btn-sm">View Full History</a>
    </div>
    
    @if($recentRedemptions->isEmpty())
        <p class="text-secondary text-center py-4">You haven't redeemed any rewards yet. Keep completing habits to earn coins!</p>
    @else
        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($recentRedemptions as $redemption)
                <div class="flex items-center gap-4 p-4 rounded" style="background: var(--bg-dark); border: 1px solid var(--bg-hover);">
                    <div style="font-size: 2rem;">{{ $redemption->reward->icon }}</div>
                    <div>
                        <div style="font-weight: 600;">{{ $redemption->reward->name }}</div>
                        <div style="font-size: 0.75rem; color: var(--text-secondary);">
                            {{ $redemption->created_at->diffForHumans() }} &bull; 
                            <span style="color: var(--danger);">-{{ $redemption->coins_spent }} <i class="fa-solid fa-coins"></i></span>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection
