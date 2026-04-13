@extends('layouts.app')
@section('title', 'Lihat Jadwal')

@section('content')
<div class="flex flex-col md:flex-row md:justify-between md:items-end gap-4 mb-8">
    <div>
        <h2 class="text-3xl font-extrabold tracking-tight text-on-surface mb-1 flex items-center gap-3">
            Jadwal Lengkap
        </h2>
        <p class="text-on-surface-variant text-sm font-medium">
            {{ $season ? $season->name : 'Tidak ada season aktif' }} — <span class="text-primary font-bold">Jadwal Anda ditandai dengan highlight penuh.</span>
        </p>
    </div>
</div>

@if($season && $schedules->count() > 0)
    @php
        $imamsList = \App\Models\User::where('role', 'imam')->where('is_active', true)->get();
    @endphp

    <!-- Legend -->
    <div class="bg-surface-container-low p-4 rounded-2xl mb-8 flex flex-wrap gap-3 items-center border border-outline-variant/10">
        <span class="text-[10px] font-bold uppercase tracking-widest text-on-surface-variant bg-surface-container px-3 py-1.5 rounded-lg mr-2">Legenda Imam</span>
        @foreach($imamsList as $i => $imam)
            @php
                $isMyImam = $imam->id === $user->id;
                // Use distinct Emerald Slate colors
                $colors = ['text-primary bg-primary/10 border border-primary/20', 'text-tertiary bg-tertiary/10 border border-tertiary/20', 'text-secondary bg-secondary/10 border border-secondary/20', 'text-[var(--clr-accent)] bg-[var(--clr-accent)]/10 border border-[var(--clr-accent)]/20', 'text-on-surface bg-surface-container-highest border border-outline-variant/30'];
                if($isMyImam) {
                    $c = "text-on-primary-container bg-primary border-primary font-black shadow-[0_0_15px_rgba(16,185,129,0.3)]";
                } else {
                    $c = $colors[$i % count($colors)];
                }
            @endphp
            <span class="px-3 py-1.5 rounded-xl text-xs font-bold {{ $c }} flex items-center gap-1">
                @if($isMyImam) <span class="material-symbols-outlined text-[14px]">person</span> @endif
                {{ $imam->name }} {{ $isMyImam ? '(Anda)' : '' }}
            </span>
        @endforeach
    </div>

    <!-- STRICT SCHEDULE UI GRID: Preserving existing logic -->
    <div class="bg-surface-container rounded-3xl p-6 overflow-x-auto shadow-sm border border-outline-variant/10 mb-12 custom-scrollbar">
        <style>
            .schedule-grid { border-color: rgba(60, 74, 66, 0.2); border-radius: 16px; background: #0b1326; }
            .schedule-cell { border-color: rgba(60, 74, 66, 0.15); padding: 16px; }
            .schedule-cell.header { background: #131b2e; color: #afb9cb; font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.1em; }
            .schedule-cell.date-cell span:last-child { color: #86948a; }
            .schedule-cell:hover:not(.header):not(.date-cell) { background: rgba(78, 222, 163, 0.05); }
        </style>

        <div class="schedule-grid" style="grid-template-columns: 120px repeat({{ $prayerTypes->count() }}, 1fr)">
            <!-- Header Row -->
            <div class="schedule-cell header flex items-center gap-2" style="display:flex;"><span class="material-symbols-outlined text-[16px]">calendar_month</span> Tanggal</div>
            @foreach($prayerTypes as $pt)
                <div class="schedule-cell header text-center">{{ $pt->name }}</div>
            @endforeach

            <!-- Data Rows -->
            @foreach($schedules as $date => $dateSchedules)
                <div class="schedule-cell date-cell flex flex-col justify-center items-center h-full" style="display:flex; flex-direction:column; justify-content:center; align-items:center;">
                    <span class="text-on-surface font-bold text-sm">{{ \Carbon\Carbon::parse($date)->format('d/m') }}</span>
                    <span class="text-[10px] font-medium tracking-wide uppercase mt-1">{{ \Carbon\Carbon::parse($date)->translatedFormat('l') }}</span>
                </div>
                @foreach($prayerTypes as $pt)
                    @php
                        $schedule = $dateSchedules->firstWhere('prayer_type_id', $pt->id);
                        $isMySchedule = $schedule && $schedule->user_id === $user->id;
                        $imamIndex = $schedule && $schedule->user ? $imamsList->search(fn($im) => $im->id === $schedule->user_id) : false;
                        
                        $statusClass = '';
                        $bgColor = 'transparent';
                        $textColor = 'rgba(218, 226, 253, 0.4)';
                        
                        if ($imamIndex !== false) {
                            if($isMySchedule) {
                                $bgColor = 'var(--clr-primary, #10b981)';
                                $textColor = '#002113'; // on-primary
                            } else {
                                $colors = ['rgba(78,222,163,0.1)', 'rgba(255,185,95,0.1)', 'rgba(217,227,246,0.1)', 'rgba(16,185,129,0.1)', 'rgba(64,74,89,0.5)'];
                                $textColors = ['#4edea3', '#ffb95f', '#dae2fd', '#10b981', '#dae2fd'];
                                $bgColor = $colors[$imamIndex % count($colors)];
                                $textColor = $textColors[$imamIndex % count($textColors)];
                            }
                            $statusClass = 'has-imam';
                        }

                        $statusBadge = '';
                        $isError = false;
                        if ($schedule && $schedule->user) {
                            if ($schedule->attendance) {
                                if (in_array($schedule->attendance->status, ['approved', 'pending'])) {
                                    $bColor = $isMySchedule ? 'bg-[#003824] text-[#4edea3]' : 'bg-primary/20 text-primary';
                                    $statusBadge = '<span class="text-[9px] uppercase tracking-widest font-bold px-1.5 py-0.5 rounded ' . $bColor . ' mt-1 w-full text-center block">Selesai</span>';
                                } else {
                                    $bColor = $isMySchedule ? 'bg-[#690005] text-[#ffdad6]' : 'bg-error/20 text-error';
                                    $statusBadge = '<span class="text-[9px] uppercase tracking-widest font-bold px-1.5 py-0.5 rounded ' . $bColor . ' mt-1 w-full text-center block">Expired</span>';
                                    $isError = true;
                                }
                            } else {
                                $schDate = \Carbon\Carbon::parse($schedule->date)->toDateString();
                                if ($schDate < now()->toDateString() || ($schDate == now()->toDateString() && $schedule->prayerTime && now()->format('H:i:s') > $schedule->prayerTime->effective_time)) {
                                    $bColor = $isMySchedule ? 'bg-black/40 text-[#ffb4ab]' : 'bg-error/20 text-error';
                                    $statusBadge = '<span class="text-[9px] uppercase tracking-widest font-bold px-1.5 py-0.5 rounded ' . $bColor . ' mt-1 w-full text-center block opacity-60">Terlewat</span>';
                                    $isError = true;
                                }
                            }
                        }
                    @endphp
                    <div class="schedule-cell flex flex-col justify-center items-center text-center {{ $statusClass }} {{ $isMySchedule ? 'my-schedule' : '' }}" style="display:flex; justify-content:center; align-items:center; {{ $isError && !$isMySchedule ? 'opacity: 0.7;' : '' }}">
                        @if($schedule && $schedule->user)
                            <div class="flex flex-col items-center justify-center w-full p-2.5 rounded-xl transition-all" style="background: {{ $bgColor }}; color: {{ $textColor }}; border: 1px solid {{ $isMySchedule ? 'transparent' : str_replace('0.1)', '0.3)', $bgColor) }}; width:100%; {{ $isMySchedule ? 'box-shadow: 0px 4px 15px rgba(16,185,129,0.2); transform:scale(1.02); z-index:10;' : '' }}">
                                <span class="text-xs font-bold leading-tight">{{ $isMySchedule ? '● ' : '' }}{{ $schedule->user->name }}</span>
                                {!! $statusBadge !!}
                            </div>
                        @else
                            <span class="text-on-surface-variant/30 font-bold opacity-30">—</span>
                        @endif
                    </div>
                @endforeach
            @endforeach
        </div>
    </div>
@else
    <div class="py-16 bg-surface-container rounded-3xl flex flex-col items-center justify-center text-center border border-outline-variant/10">
        <div class="w-20 h-20 rounded-2xl bg-surface-container-low flex items-center justify-center mb-6">
            <span class="material-symbols-outlined text-5xl text-on-surface-variant/30">event_busy</span>
        </div>
        <h3 class="text-xl font-bold text-on-surface mb-2">Slot Jadwal Kosong</h3>
        <p class="text-on-surface-variant text-sm mb-8">Belum ada jadwal tersedia dari Admin.</p>
    </div>
@endif

@endsection
