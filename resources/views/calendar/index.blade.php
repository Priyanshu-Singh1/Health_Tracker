@extends('layouts.app')

@section('title', 'Calendar')

@push('styles')
<style>
    .calendar-container {
        background: rgba(30, 41, 59, 0.4);
        backdrop-filter: blur(16px);
        -webkit-backdrop-filter: blur(16px);
        border: 1px solid rgba(255, 255, 255, 0.05);
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5), 0 0 40px rgba(99, 102, 241, 0.1);
        border-radius: 1.5rem;
        padding: 2.5rem;
    }
    
    .day-header {
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        font-size: 0.8rem;
        color: var(--text-secondary);
        margin-bottom: 1rem;
    }
    
    .day-header.weekend { color: var(--primary); opacity: 0.8; }
    
    .calendar-day {
        position: relative;
        aspect-ratio: 1;
        border-radius: 1rem;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        border: 1px solid rgba(255, 255, 255, 0.03);
        background: rgba(255, 255, 255, 0.02);
    }
    
    .calendar-day:hover {
        transform: translateY(-5px) scale(1.05);
        z-index: 10;
        box-shadow: 0 15px 30px rgba(0,0,0,0.4);
    }
    
    .calendar-day:hover .track-btn {
        opacity: 1 !important;
    }
    
    .track-btn:hover {
        color: var(--primary) !important;
        transform: scale(1.2);
    }
    
    .day-number {
        font-weight: 700;
        font-size: 1.25rem;
        z-index: 2;
        transition: color 0.3s;
    }
    
    .day-status {
        font-size: 0.7rem;
        font-weight: 600;
        margin-top: 0.2rem;
        padding: 0.1rem 0.5rem;
        border-radius: 1rem;
        background: rgba(0,0,0,0.3);
        z-index: 2;
    }
    
    /* Perfect Day */
    .day-100 {
        background: linear-gradient(135deg, rgba(16, 185, 129, 0.2), rgba(16, 185, 129, 0.4));
        border-color: rgba(16, 185, 129, 0.5);
        box-shadow: inset 0 0 20px rgba(16, 185, 129, 0.1);
    }
    .day-100:hover {
        box-shadow: 0 15px 30px rgba(16, 185, 129, 0.3), inset 0 0 20px rgba(16, 185, 129, 0.4);
        border-color: var(--success);
    }
    
    /* Good Day */
    .day-75 {
        background: linear-gradient(135deg, rgba(16, 185, 129, 0.1), rgba(16, 185, 129, 0.2));
        border-color: rgba(16, 185, 129, 0.2);
    }
    .day-75:hover { box-shadow: 0 10px 25px rgba(16, 185, 129, 0.2); border-color: rgba(16, 185, 129, 0.5); }
    
    /* Okay Day */
    .day-50 {
        background: linear-gradient(135deg, rgba(245, 158, 11, 0.1), rgba(245, 158, 11, 0.2));
        border-color: rgba(245, 158, 11, 0.2);
    }
    .day-50:hover { box-shadow: 0 10px 25px rgba(245, 158, 11, 0.2); border-color: rgba(245, 158, 11, 0.5); }
    
    /* Needs Work */
    .day-1 {
        background: linear-gradient(135deg, rgba(239, 68, 68, 0.1), rgba(239, 68, 68, 0.2));
        border-color: rgba(239, 68, 68, 0.2);
    }
    .day-1:hover { box-shadow: 0 10px 25px rgba(239, 68, 68, 0.2); border-color: rgba(239, 68, 68, 0.5); }
    
    .day-today {
        border: 2px solid var(--primary);
        box-shadow: 0 0 20px rgba(99, 102, 241, 0.4);
    }
    .day-today .day-number { color: var(--primary); }
    
    .day-future { opacity: 0.3; pointer-events: none; filter: grayscale(1); }
    .day-empty { opacity: 0.1; pointer-events: none; }
    
    /* Custom Tooltip */
    .calendar-day .day-tooltip {
        position: absolute;
        bottom: 110%;
        left: 50%;
        transform: translateX(-50%) translateY(10px);
        background: rgba(15, 23, 42, 0.95);
        border: 1px solid rgba(255, 255, 255, 0.1);
        padding: 0.75rem 1rem;
        border-radius: 0.75rem;
        width: max-content;
        opacity: 0;
        visibility: hidden;
        transition: all 0.3s;
        box-shadow: 0 15px 30px rgba(0,0,0,0.5);
        pointer-events: none;
        z-index: 100;
    }
    .calendar-day:hover .day-tooltip {
        opacity: 1;
        visibility: visible;
        transform: translateX(-50%) translateY(0);
    }
    
    /* Streak Card Glow */
    .streak-card {
        position: relative;
        overflow: hidden;
        background: linear-gradient(135deg, #1e293b, #0f172a);
        border: 1px solid rgba(99, 102, 241, 0.3);
    }
    .streak-glow {
        position: absolute;
        top: -50%;
        right: -50%;
        width: 200px;
        height: 200px;
        background: radial-gradient(circle, rgba(99, 102, 241, 0.4) 0%, transparent 70%);
        opacity: 0.5;
        transition: all 0.5s;
    }
    .streak-card:hover .streak-glow {
        transform: scale(1.5);
        opacity: 0.8;
    }
    
    /* Modal Styles */
    .custom-modal {
        display: none;
        position: fixed;
        inset: 0;
        z-index: 9999;
        background: rgba(15, 23, 42, 0.8);
        backdrop-filter: blur(8px);
        -webkit-backdrop-filter: blur(8px);
        align-items: center;
        justify-content: center;
        opacity: 0;
        transition: opacity 0.3s ease;
    }
    .custom-modal.show {
        display: flex;
        opacity: 1;
    }
    .modal-box {
        background: var(--bg-color);
        border: 1px solid rgba(255, 255, 255, 0.1);
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5), 0 0 40px rgba(99, 102, 241, 0.2);
        border-radius: 1.5rem;
        width: 90%;
        max-width: 500px;
        max-height: 80vh;
        overflow-y: auto;
        transform: scale(0.95) translateY(20px);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .custom-modal.show .modal-box {
        transform: scale(1) translateY(0);
    }
</style>
@endpush

@section('content')
<div class="flex md:flex-row justify-between items-center mb-8 gap-4" style="flex-wrap: wrap;">
    <div>
        <h1 style="background: linear-gradient(135deg, #fff, #94a3b8); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">Consistency Calendar</h1>
        <p style="font-size: 1.1rem;">Track your daily performance and maintain your monthly streak.</p>
    </div>
    
    <div class="flex items-center gap-4" style="background: rgba(30,41,59,0.6); padding: 0.5rem; border-radius: 1rem; border: 1px solid rgba(255,255,255,0.05);">
        <a href="{{ route('calendar', ['month' => $prevMonth->month, 'year' => $prevMonth->year]) }}" class="btn btn-secondary" style="border-radius: 0.75rem;"><i class="fa-solid fa-chevron-left"></i></a>
        <h2 style="margin: 0; min-width: 180px; text-align: center; font-size: 1.5rem;">{{ $monthName }}</h2>
        <a href="{{ route('calendar', ['month' => $nextMonth->month, 'year' => $nextMonth->year]) }}" class="btn btn-secondary" style="border-radius: 0.75rem;"><i class="fa-solid fa-chevron-right"></i></a>
    </div>
</div>

<div class="grid lg:grid-cols-4 gap-8">
    <div class="lg:col-span-3">
        <div class="calendar-container">
            <!-- Days of Week Header -->
            <div class="grid grid-cols-7 gap-2 text-center day-header">
                <div class="weekend">Sun</div><div>Mon</div><div>Tue</div><div>Wed</div><div>Thu</div><div>Fri</div><div class="weekend">Sat</div>
            </div>
            
            <!-- Calendar Grid -->
            <div class="grid grid-cols-7 gap-2">
                @php
                    $firstDayOfWeek = \Carbon\Carbon::createFromDate($year, $month, 1)->dayOfWeek;
                @endphp
                
                @for($i = 0; $i < $firstDayOfWeek; $i++)
                    <div class="calendar-day day-empty"></div>
                @endfor

                @foreach($calendarData as $day)
                    @php
                        $dayClass = '';
                        $statusText = 'No Activity';
                        $statusColor = 'var(--text-secondary)';
                        
                        if($day['isFuture']) {
                            $dayClass = 'day-future';
                        } elseif ($day['total'] > 0) {
                            if ($day['percentage'] == 100) {
                                $dayClass = 'day-100';
                                $statusText = 'Perfect Day';
                                $statusColor = 'var(--success)';
                            } elseif ($day['percentage'] >= 75) {
                                $dayClass = 'day-75';
                                $statusText = 'Good Day';
                                $statusColor = 'rgba(16, 185, 129, 0.8)';
                            } elseif ($day['percentage'] >= 50) {
                                $dayClass = 'day-50';
                                $statusText = 'Okay Day';
                                $statusColor = 'var(--warning)';
                            } elseif ($day['percentage'] > 0) {
                                $dayClass = 'day-1';
                                $statusText = 'Needs Work';
                                $statusColor = 'var(--danger)';
                            }
                        }

                        if ($day['isToday']) {
                            $dayClass .= ' day-today';
                        }
                    @endphp
                    
                    <div class="calendar-day {{ $dayClass }}" @if(!$day['isFuture']) onclick="openTrackModal('{{ $day['date'] }}', '{{ \Carbon\Carbon::createFromDate($year, $month, $day['day'])->format('F j, Y') }}')" @endif>
                        @if(!$day['isFuture'])
                            <div class="track-btn" style="position: absolute; top: 0.25rem; right: 0.25rem; color: var(--text-secondary); opacity: 0; transition: opacity 0.2s; font-size: 0.8rem; padding: 0.25rem;" title="Track habits for this day">
                                <i class="fa-solid fa-pen-to-square"></i>
                            </div>
                        @endif

                        <span class="day-number">{{ $day['day'] }}</span>
                        
                        @if($day['total'] > 0 && !$day['isFuture'])
                            <div class="day-status" style="color: {{ $statusColor }};">
                                {{ $day['percentage'] }}%
                            </div>
                        @endif

                        @if(!$day['isFuture'])
                        <div class="day-tooltip">
                            <div style="font-weight: 700; margin-bottom: 0.25rem;">{{ \Carbon\Carbon::createFromDate($year, $month, $day['day'])->format('F j, Y') }}</div>
                            <div style="color: {{ $statusColor }}; font-size: 0.85rem; display: flex; align-items: center; gap: 0.5rem;">
                                <i class="fa-solid fa-circle" style="font-size: 0.5rem;"></i> {{ $statusText }}
                            </div>
                            <div style="color: var(--text-secondary); font-size: 0.8rem; margin-top: 0.5rem; border-top: 1px solid rgba(255,255,255,0.1); padding-top: 0.5rem;">
                                {{ $day['completed'] }} of {{ $day['total'] }} habits completed
                            </div>
                        </div>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    </div>
    
    <div style="display: flex; flex-direction: column; gap: 2rem;">
        <div class="card streak-card" style="text-align: center; padding: 2.5rem 1.5rem;">
            <div class="streak-glow"></div>
            <div style="position: relative; z-index: 1;">
                <div style="font-size: 3.5rem; color: var(--primary); text-shadow: 0 0 20px rgba(99,102,241,0.5); margin-bottom: 1rem; transform: scale(1); transition: transform 0.3s;" onmouseover="this.style.transform='scale(1.1)'" onmouseout="this.style.transform='scale(1)'">
                    <i class="fa-solid fa-fire-flame-curved"></i>
                </div>
                <h3 style="font-size: 3rem; font-weight: 800; margin: 0; background: linear-gradient(135deg, #fff, #a5b4fc); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">{{ $monthlyStreak }}</h3>
                <p style="color: var(--text-secondary); font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; font-size: 0.85rem; margin-top: 0.5rem;">Max Monthly Streak</p>
                <div style="font-size: 0.75rem; color: rgba(255,255,255,0.3); margin-top: 0.5rem;">(Days >80% Completion)</div>
            </div>
        </div>

        <div class="card" style="background: rgba(30, 41, 59, 0.4); backdrop-filter: blur(10px); border: 1px solid rgba(255, 255, 255, 0.05);">
            <h3 class="card-header" style="font-size: 1.25rem;"><i class="fa-solid fa-list-ul" style="color: var(--primary); margin-right: 0.5rem;"></i> Legend</h3>
            <div style="display: flex; flex-direction: column; gap: 1rem;">
                <div style="display: flex; align-items: center; gap: 1rem;">
                    <div class="calendar-day day-100" style="width: 2.5rem; height: 2.5rem; border-radius: 0.5rem;"></div>
                    <div>
                        <div style="font-weight: 600; font-size: 0.9rem;">Perfect Day</div>
                        <div style="font-size: 0.75rem; color: var(--text-secondary);">100% Complete</div>
                    </div>
                </div>
                <div style="display: flex; align-items: center; gap: 1rem;">
                    <div class="calendar-day day-75" style="width: 2.5rem; height: 2.5rem; border-radius: 0.5rem;"></div>
                    <div>
                        <div style="font-weight: 600; font-size: 0.9rem;">Good Day</div>
                        <div style="font-size: 0.75rem; color: var(--text-secondary);">75% - 99% Complete</div>
                    </div>
                </div>
                <div style="display: flex; align-items: center; gap: 1rem;">
                    <div class="calendar-day day-50" style="width: 2.5rem; height: 2.5rem; border-radius: 0.5rem;"></div>
                    <div>
                        <div style="font-weight: 600; font-size: 0.9rem;">Okay Day</div>
                        <div style="font-size: 0.75rem; color: var(--text-secondary);">50% - 74% Complete</div>
                    </div>
                </div>
                <div style="display: flex; align-items: center; gap: 1rem;">
                    <div class="calendar-day day-1" style="width: 2.5rem; height: 2.5rem; border-radius: 0.5rem;"></div>
                    <div>
                        <div style="font-weight: 600; font-size: 0.9rem;">Needs Work</div>
                        <div style="font-size: 0.75rem; color: var(--text-secondary);">1% - 49% Complete</div>
                    </div>
                </div>
                <div style="display: flex; align-items: center; gap: 1rem;">
                    <div class="calendar-day" style="width: 2.5rem; height: 2.5rem; border-radius: 0.5rem;"></div>
                    <div>
                        <div style="font-weight: 600; font-size: 0.9rem;">No Activity</div>
                        <div style="font-size: 0.75rem; color: var(--text-secondary);">0% / No Habits</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Tracking Modal -->
<div id="trackModal" class="custom-modal">
    <div class="modal-box">
        <div style="display: flex; justify-content: space-between; align-items: center; padding: 1.5rem; border-bottom: 1px solid rgba(255,255,255,0.05); background: rgba(30,41,59,0.5);">
            <h3 id="modalDateTitle" style="margin: 0; font-size: 1.25rem;">Track Habits</h3>
            <button onclick="closeTrackModal()" style="background: none; border: none; color: var(--text-secondary); cursor: pointer; font-size: 1.25rem; transition: color 0.2s;"><i class="fa-solid fa-times"></i></button>
        </div>
        <div id="modalContent" style="padding: 1.5rem;">
            <!-- Content will be injected here via AJAX -->
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    let currentTrackDate = null;
    
    function openTrackModal(dateStr, formattedDate) {
        document.getElementById('modalDateTitle').innerText = formattedDate;
        const modal = document.getElementById('trackModal');
        modal.style.display = 'flex';
        // Trigger reflow for animation
        void modal.offsetWidth;
        modal.classList.add('show');
        currentTrackDate = dateStr;
        loadHabitsForDate(dateStr);
    }

    function closeTrackModal() {
        const modal = document.getElementById('trackModal');
        modal.classList.remove('show');
        setTimeout(() => {
            modal.style.display = 'none';
            // Reload page to reflect new consistency %
            window.location.reload();
        }, 300);
    }

    async function loadHabitsForDate(dateStr) {
        const contentDiv = document.getElementById('modalContent');
        contentDiv.innerHTML = '<div style="text-align: center; color: var(--text-secondary); padding: 2rem 0;"><i class="fa-solid fa-circle-notch fa-spin fa-2x"></i></div>';
        
        try {
            const response = await fetch(`/calendar/${dateStr}`, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });
            const html = await response.text();
            contentDiv.innerHTML = html;
            
            // Attach AJAX submit handlers to the forms inside modal
            attachFormHandlers();
        } catch (e) {
            contentDiv.innerHTML = '<div class="alert alert-error">Failed to load habits.</div>';
        }
    }

    function attachFormHandlers() {
        const forms = document.querySelectorAll('#modalContent .toggle-habit-form');
        forms.forEach(form => {
            form.addEventListener('submit', async (e) => {
                e.preventDefault();
                const btn = form.querySelector('button');
                const originalIcon = btn.innerHTML;
                btn.innerHTML = '<i class="fa-solid fa-circle-notch fa-spin"></i>';
                btn.disabled = true;
                
                try {
                    const response = await fetch(form.action, {
                        method: 'POST',
                        body: new FormData(form),
                        headers: { 'X-Requested-With': 'XMLHttpRequest' }
                    });
                    const data = await response.json();
                    if(data.success) {
                        // Reload the partial to get updated states
                        loadHabitsForDate(currentTrackDate);
                    }
                } catch(err) {
                    console.error(err);
                    btn.innerHTML = originalIcon;
                    btn.disabled = false;
                }
            });
        });
    }

    // Close on click outside
    document.getElementById('trackModal').addEventListener('click', function(e) {
        if(e.target === this) closeTrackModal();
    });
</script>
@endpush
