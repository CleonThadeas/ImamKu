@extends('layouts.app')
@section('title', 'Lihat Jadwal')

@section('content')
<div class="main-header">
    <div>
        <h2 style="display:flex;align-items:center;gap:10px"><svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><path d="M16 2v4M8 2v4M3 10h18"/></svg> Jadwal Lengkap</h2>
        <div class="breadcrumb">{{ $season ? $season->name : 'Tidak ada season aktif' }} — Jadwal Anda ditandai dengan highlight</div>
    </div>
</div>

@if($season && $schedules->count() > 0)
    @php
        $imamsList = \App\Models\User::where('role', 'imam')->where('is_active', true)->get();
    @endphp

    <!-- Legend -->
    <div class="card" style="margin-bottom:20px;padding:14px">
        <div style="display:flex;flex-wrap:wrap;gap:12px;align-items:center">
            <span style="font-size:0.75rem;color:var(--clr-text-muted);font-weight:600">LEGENDA:</span>
            @foreach($imamsList as $i => $imam)
                <span class="imam-tag imam-color-{{ ($i % 5) + 1 }}" style="padding:4px 10px;border-radius:6px;font-size:0.7rem;font-weight:600;border:1px solid">
                    {{ $imam->name }} {{ $imam->id === $user->id ? '(Anda)' : '' }}
                </span>
            @endforeach
        </div>
    </div>

    <!-- Schedule Grid -->
    <div class="card" style="padding:12px;overflow-x:auto">
        <div class="schedule-grid" style="grid-template-columns: 100px repeat({{ $prayerTypes->count() }}, 1fr)">
            <!-- Header Row -->
            <div class="schedule-cell header">Tanggal</div>
            @foreach($prayerTypes as $pt)
                <div class="schedule-cell header">{{ $pt->name }}</div>
            @endforeach

            <!-- Data Rows -->
            @foreach($schedules as $date => $dateSchedules)
                <div class="schedule-cell date-cell">
                    <span>{{ \Carbon\Carbon::parse($date)->format('d/m') }}</span>
                    <span style="font-size:0.55rem;color:var(--clr-text-muted)">{{ \Carbon\Carbon::parse($date)->translatedFormat('D') }}</span>
                </div>
                @foreach($prayerTypes as $pt)
                    @php
                        $schedule = $dateSchedules->firstWhere('prayer_type_id', $pt->id);
                        $isMySchedule = $schedule && $schedule->user_id === $user->id;
                        $imamIndex = $schedule && $schedule->user ? $imamsList->search(fn($im) => $im->id === $schedule->user_id) : false;
                        
                        $statusClass = $imamIndex !== false ? 'imam-color-' . (($imamIndex % 5) + 1) : '';
                        $statusBadge = '';
                        if ($schedule && $schedule->user) {
                            if ($schedule->attendance) {
                                if (in_array($schedule->attendance->status, ['approved', 'pending'])) {
                                    $statusClass = 'bg-success text-white border-0 opacity-75';
                                    $statusBadge = '<span style="font-size:0.5rem;display:block;margin-top:2px;">Selesai</span>';
                                } else {
                                    $statusClass = 'bg-danger text-white border-0';
                                    $statusBadge = '<span style="font-size:0.5rem;display:block;margin-top:2px;">Expired</span>';
                                }
                            } else {
                                $schDate = \Carbon\Carbon::parse($schedule->date)->toDateString();
                                if ($schDate < now()->toDateString() || ($schDate == now()->toDateString() && $schedule->prayerTime && now()->format('H:i') > $schedule->prayerTime->effective_time)) {
                                    $statusClass = 'bg-danger text-white border-0 opacity-50';
                                    $statusBadge = '<span style="font-size:0.5rem;display:block;margin-top:2px;">Terlewat</span>';
                                }
                            }
                        }
                    @endphp
                    <div class="schedule-cell {{ $isMySchedule ? 'my-schedule' : '' }}">
                        @if($schedule && $schedule->user)
                            <span class="imam-tag {{ $statusClass }}" style="display:inline-block; line-height:1.2;">
                                {{ $isMySchedule ? '● ' : '' }}{{ $schedule->user->name }}
                                {!! $statusBadge !!}
                            </span>
                        @else
                            <span class="empty-slot">—</span>
                        @endif
                    </div>
                @endforeach
            @endforeach
        </div>
    </div>
@else
    <div class="card">
        <div class="empty-state">
            <div class="empty-icon"><svg width="48" height="48" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" style="opacity:0.5"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><path d="M16 2v4M8 2v4M3 10h18"/></svg></div>
            <p>Belum ada jadwal tersedia.</p>
        </div>
    </div>
@endif


@endsection
