@if($habits->isEmpty())
    <div class="text-center py-8" style="color: var(--text-secondary);">
        <i class="fa-solid fa-ghost fa-3x mb-4" style="opacity: 0.5;"></i>
        <p style="font-size: 1.1rem;">No active habits found.</p>
    </div>
@else
    <div class="grid gap-4">
        @foreach($habits as $habit)
            @php
                $isCompleted = $habit->isCompletedOn($date);
            @endphp
            <div class="card" style="display: flex; justify-content: space-between; align-items: center; border-left: 4px solid {{ $habit->category->color ?? '#6366f1' }}; {{ $isCompleted ? 'opacity: 0.7;' : '' }} padding: 1rem;">
                <div>
                    <h4 style="margin: 0; font-size: 1.1rem; {{ $isCompleted ? 'text-decoration: line-through; color: var(--text-secondary);' : '' }}">
                        <span style="margin-right: 0.5rem;">{{ $habit->category->icon ?? '🎯' }}</span>
                        {{ $habit->name }}
                    </h4>
                    <p style="margin: 0.25rem 0 0 0; color: var(--text-secondary); font-size: 0.8rem;">
                        {{ ucfirst($habit->frequency) }} 
                        @if($isCompleted)
                            • <span style="color: var(--success); font-weight: 600;"><i class="fa-solid fa-check"></i> Completed</span>
                        @endif
                    </p>
                </div>
                
                <form class="toggle-habit-form" action="{{ route('calendar.toggle', ['date' => $date, 'habit' => $habit->id]) }}" method="POST">
                    @csrf
                    <button type="submit" class="btn {{ $isCompleted ? 'btn-secondary' : 'btn-primary' }}" style="border-radius: 50%; width: 2.5rem; height: 2.5rem; padding: 0; display: flex; align-items: center; justify-content: center;" title="{{ $isCompleted ? 'Mark as incomplete' : 'Mark as complete' }}">
                        @if($isCompleted)
                            <i class="fa-solid fa-rotate-left"></i>
                        @else
                            <i class="fa-solid fa-check"></i>
                        @endif
                    </button>
                </form>
            </div>
        @endforeach
    </div>
@endif
