@extends('layouts.app')
@section('title', 'Admin Dashboard')

@section('content')
<div class="main-header" style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:20px;">
    <div>
        <h2>Dashboard Admin</h2>
        <div class="breadcrumb">Ringkasan Sistem ImamKu - {{ now()->translatedFormat('l, d F Y') }}</div>
    </div>
    <div style="background:var(--clr-surface); padding:10px 20px; border-radius:8px; border:1px solid var(--clr-border); box-shadow:0 4px 12px rgba(0,0,0,0.1); display:flex; align-items:center; gap:10px">
        <span style="font-size:1.5rem; color:var(--clr-accent)"><svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg></span>
        <div>
            <div id="realtime-clock" style="font-size:1.2rem; font-weight:700; color:var(--clr-accent); font-variant-numeric: tabular-nums;">--:--:-- WIB</div>
            <div style="font-size:0.7rem; color:var(--clr-text-muted); text-transform:uppercase; letter-spacing:1px">Waktu Server Terkini</div>
        </div>
    </div>
    <div style="text-align:right">
        @if($season)
            <span class="badge badge-gold">{{ $season->name }}</span>
        @endif
    </div>
</div>

<!-- Stats -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon green"><svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/></svg></div>
        <div>
            <div class="stat-value">{{ $stats['total_imams'] }}</div>
            <div class="stat-label">Total Imam Aktif</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon gold"><svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><path d="M16 2v4M8 2v4M3 10h18"/></svg></div>
        <div>
            <div class="stat-value">{{ $stats['total_schedules'] }}</div>
            <div class="stat-label">Jadwal Terisi</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon red"><svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0zM12 9v4M12 17h.01"/></svg></div>
        <div>
            <div class="stat-value">{{ $stats['empty_slots'] }}</div>
            <div class="stat-label">Slot Kosong</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon blue"><svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M18 8A6 6 0 006 8c0 7-3 9-3 9h18s-3-2-3-9M13.73 21a2 2 0 01-3.46 0"/></svg></div>
        <div>
            <div class="stat-value">{{ $stats['notifications_sent'] }}</div>
            <div class="stat-label">Notifikasi Terkirim</div>
        </div>
    </div>
</div>

<!-- Today's Schedule -->
<div class="card">
    <div class="card-header">
        <h3 class="card-title" style="display:flex;align-items:center;gap:8px"><svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><path d="M16 2v4M8 2v4M3 10h18"/></svg> Jadwal Hari Ini</h3>
        <a href="{{ route('admin.schedules.index') }}" class="btn btn-secondary btn-sm">Lihat Semua</a>
    </div>

    @if($stats['today_schedules']->count() > 0)
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>Waktu</th>
                        <th>Sholat</th>
                        <th>Imam</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($stats['today_schedules'] as $schedule)
                        <tr>
                            <td>
                                @php
                                    $pt = \App\Models\PrayerTime::where('season_id', $season->id)
                                        ->where('date', now()->toDateString())
                                        ->where('prayer_type_id', $schedule->prayer_type_id)
                                        ->first();
                                @endphp
                                {{ $pt ? $pt->effective_time : '-' }}
                            </td>
                            <td><strong>{{ $schedule->prayerType->name }}</strong></td>
                            <td>
                                @if($schedule->user)
                                    <span class="badge badge-info">{{ $schedule->user->name }}</span>
                                @else
                                    <span class="badge badge-danger">Belum diisi</span>
                                @endif
                            </td>
                            <td>
                                @if($schedule->user)
                                    <span class="badge badge-success">Terisi</span>
                                @else
                                    <span class="badge badge-warning">Kosong</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <div class="empty-state">
            <div class="empty-icon"><svg width="48" height="48" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" style="opacity:0.5"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><path d="M16 2v4M8 2v4M3 10h18"/></svg></div>
            <p>Tidak ada jadwal untuk hari ini.</p>
            @if(!$season)
                <p style="margin-top:8px"><a href="{{ route('admin.seasons.create') }}" class="btn btn-primary btn-sm" style="margin-top:12px">Buat Season Ramadan</a></p>
            @endif
        </div>
    @endif
</div>

<script>
    function updateClock() {
        const now = new Date();
        const options = { timeZone: 'Asia/Jakarta', hour12: false, hour: '2-digit', minute:'2-digit', second:'2-digit' };
        const timeString = now.toLocaleTimeString('id-ID', options);
        const clockTarget = document.getElementById('realtime-clock');
        if (clockTarget) {
            clockTarget.innerText = timeString.replace(/\./g, ':') + ' WIB';
        }
    }
    setInterval(updateClock, 1000);
    updateClock();
</script>
@endsection
