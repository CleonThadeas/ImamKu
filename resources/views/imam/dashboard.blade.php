@extends('layouts.app')
@section('title', 'Dashboard Imam')

@section('content')
<div class="main-header" style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:20px;">
    <div>
        <h2>Assalamu'alaikum, {{ auth()->user()->name }} <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="vertical-align:middle;color:var(--clr-accent)"><path d="M21 12.79A9 9 0 1111.21 3 7 7 0 0021 12.79z"/></svg></h2>
        <div class="breadcrumb">{{ now()->translatedFormat('l, d F Y') }}</div>
        @if($season)
            <div style="margin-top:12px; display:inline-block;">
                <span class="badge badge-gold">{{ $season->name }}</span>
            </div>
        @endif
    </div>
    <div style="background:var(--clr-surface); padding:10px 20px; border-radius:8px; border:1px solid var(--clr-border); box-shadow:0 4px 12px rgba(0,0,0,0.1); display:flex; align-items:center; gap:10px">
        <span style="font-size:1.5rem; color:var(--clr-accent)"><svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg></span>
        <div>
            <div id="realtime-clock" style="font-size:1.2rem; font-weight:700; color:var(--clr-accent); font-variant-numeric: tabular-nums;">--:--:-- WIB</div>
            <p id="locationStatus" style="font-size: 0.8rem; color: var(--clr-text-muted); margin-top: 12px; font-style: italic;">
                Mencari lokasi Anda...
            </p>
        </div>
    </div>
</div>
<!-- Penalty & Restriction Status -->
@if(auth()->user()->is_restricted)
    <div class="alert alert-danger" style="display:flex; align-items:center; gap:10px; background:var(--clr-danger); color:white; border:none; padding:15px; border-radius:8px;">
        <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><path d="M12 8v4M12 16h.01"/></svg>
        <div>
            <strong style="display:block; font-size:1.1rem;">Akun Anda Dibatasi (Restricted)</strong>
            <span>Total poin Anda ({{ auth()->user()->penalty_points }}) telah melewati batas bawah (-30). Anda tidak bisa memproses swap, mengambil jadwal, dan mengisi absensi baru. Hubungi Admin.</span>
        </div>
    </div>
@else
    <div style="margin-bottom: 20px; display:inline-flex; align-items:center; gap:8px; background:var(--clr-surface); padding:8px 16px; border-radius:50px; border:1px solid var(--clr-border);">
        <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="color:var(--clr-accent)"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
        <span style="font-size:0.9rem; color:var(--clr-text-muted);">Poin Performa:</span>
        <strong style="color: {{ auth()->user()->penalty_points < 0 ? 'var(--clr-danger)' : 'var(--clr-success)' }}; font-size:1.1rem;">
            {{ auth()->user()->penalty_points > 0 ? '+' : '' }}{{ auth()->user()->penalty_points }}
        </strong>
    </div>
@endif

<!-- Pending Swaps Alert -->
@if($pendingSwaps->count() > 0)
    <div class="alert alert-warning">
        <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="vertical-align:bottom;margin-right:4px"><path d="M16 3h5v5M4 20L21 3M21 16v5h-5M15 21l6-6M9 8l-6 6M4 14v5h5M4 19l6-6M3 3l6 6"/></svg> Anda memiliki {{ $pendingSwaps->count() }} permintaan swap yang menunggu respon.
        <a href="{{ route('imam.swaps.index') }}" style="color:var(--clr-accent);margin-left:8px">Lihat →</a>
    </div>
@endif

<div class="card">
    <div class="card-header">
        <h3 class="card-title" style="display:flex;align-items:center;gap:8px;"><svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><path d="M16 2v4M8 2v4M3 10h18"/></svg> Jadwal Saya (Mendatang)</h3>
        <a href="{{ route('imam.schedules.index') }}" class="btn btn-secondary btn-sm">Jadwal Lengkap</a>
    </div>

    @if($mySchedules->count() > 0)
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Hari</th>
                        <th>Sholat</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($mySchedules as $schedule)
                        <tr>
                            <td><strong>{{ $schedule->date->format('d/m/Y') }}</strong></td>
                            <td style="color:var(--clr-text-muted)">{{ $schedule->date->translatedFormat('l') }}</td>
                            <td><span class="badge badge-gold">{{ $schedule->prayerType->name }}</span></td>
                            <td>
                                @php
                                    $canCheckIn = false;
                                    $canSwap = false;
                                    $isCheckedIn = $schedule->attendance !== null;
                                    $isExpired = $isCheckedIn && $schedule->attendance->status === 'expired';
                                    if (!$isCheckedIn && $schedule->prayerTime && $schedule->prayerTime->effective_time) {
                                        $prayerDt = \Carbon\Carbon::parse($schedule->date->toDateString() . ' ' . $schedule->prayerTime->effective_time);
                                        $diffMins = now()->diffInMinutes($prayerDt, false);
                                        if ($diffMins <= 30 && $diffMins >= -30) $canCheckIn = true;
                                        if ($diffMins >= 120) $canSwap = true;
                                    }
                                @endphp
                                
                                <div style="display:flex;gap:5px;">
                                    @if($isExpired)
                                        <span class="badge badge-danger" style="background-color: var(--clr-danger);">Waktu Habis</span>
                                    @elseif($isCheckedIn && $schedule->attendance->status === 'approved')
                                        <span class="badge badge-success" style="background-color: var(--clr-success); padding:6px 12px; font-size:0.8rem; display:flex; align-items:center; gap:4px;"><svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M20 6L9 17l-5-5"/></svg> Selesai</span>
                                    @elseif($isCheckedIn && $schedule->attendance->status !== 'approved')
                                        <span class="badge badge-warning" style="background-color: var(--clr-warning); display:flex; align-items:center; gap:4px;"><svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg> Menunggu Validasi</span>
                                    @elseif($canCheckIn)
                                        <button type="button" class="btn btn-primary btn-xs" style="display:flex; align-items:center; gap:4px;" onclick="openAbsenModal({{ $schedule->id }}, '{{ $schedule->prayerType->name }}')"><svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z"/><circle cx="12" cy="10" r="3"/></svg> Absen Hadir</button>
                                    @endif
                                    
                                    @if(!$isCheckedIn && $canSwap)
                                        <a href="{{ route('imam.swaps.create') }}" class="btn btn-secondary btn-xs" style="display:flex; align-items:center; gap:4px;"><svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M16 3h5v5M4 20L21 3M21 16v5h-5M15 21l6-6M9 8l-6 6M4 14v5h5M4 19l6-6M3 3l6 6"/></svg> Swap</a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <div class="empty-state">
            <div class="empty-icon"><svg width="48" height="48" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" style="color:var(--clr-text-muted); opacity:0.6;"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4M7 10l5 5 5-5M12 15V3"/></svg></div>
            <p>Tidak ada jadwal mendatang.</p>
        </div>
    @endif
</div>

<!-- Absen Modal -->
<div id="absenModal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:9999; justify-content:center; align-items:center;">
    <div class="card" style="width:100%; max-width:400px; margin:20px; padding:20px; position:relative;">
        <button onclick="closeAbsenModal()" style="position:absolute; top:10px; right:15px; border:none; background:none; font-size:24px; cursor:pointer; color:var(--clr-text-muted);">&times;</button>
        <h3 style="display:flex;align-items:center;gap:8px;"><svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="color:var(--clr-accent)"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z"/><circle cx="12" cy="10" r="3"/></svg> Absen Sholat <span id="absenPrayerName"></span></h3>
        <p class="text-muted" style="font-size:14px; margin-bottom:15px;">Silakan unggah foto wajah Anda dengan latar area masjid untuk bukti kehadiran.</p>
        
        <form id="absenForm" method="POST" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="latitude" id="absenLatitude" required>
            <input type="hidden" name="longitude" id="absenLongitude" required>
            
            <div id="absenLocationStatus" style="font-size:0.85rem; margin-bottom:15px; padding:10px; border-radius:6px; background:var(--clr-surface-hover); color:var(--clr-warning);">
                Mencari lokasi Anda...
            </div>

            <div class="mb-3">
                <input type="file" name="proof_photo" class="form-control" accept="image/*" required>
            </div>
            <button type="submit" id="btnSubmitAbsen" class="btn btn-primary" style="width:100%;" disabled>Upload & Simpan Absen</button>
        </form>
    </div>
</div>

<script>
    function openAbsenModal(scheduleId, prayerName) {
        document.getElementById('absenModal').style.display = 'flex';
        document.getElementById('absenPrayerName').textContent = prayerName;
        document.getElementById('absenForm').action = "/imam/schedules/" + scheduleId + "/attendance";
        
        const locStatus = document.getElementById('absenLocationStatus');
        const btnSubmit = document.getElementById('btnSubmitAbsen');
        const latInput = document.getElementById('absenLatitude');
        const lngInput = document.getElementById('absenLongitude');
        
        locStatus.innerHTML = 'Mencari lokasi Anda...';
        locStatus.style.color = 'var(--clr-warning)';
        btnSubmit.disabled = true;

        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(
                function(position) {
                    latInput.value = position.coords.latitude;
                    lngInput.value = position.coords.longitude;
                    locStatus.innerHTML = 'Lokasi berhasil didapatkan.';
                    locStatus.style.color = 'var(--clr-success)';
                    btnSubmit.disabled = false;
                },
                function(error) {
                    locStatus.innerHTML = 'Gagal akses GPS. Pastikan izin lokasi (Location) menyala.';
                    locStatus.style.color = 'var(--clr-danger)';
                    btnSubmit.disabled = true;
                },
                { enableHighAccuracy: true, timeout: 10000, maximumAge: 0 }
            );
        } else {
            locStatus.innerHTML = 'Browser tidak mendukung GPS.';
            locStatus.style.color = 'var(--clr-danger)';
        }
    }
    
    function closeAbsenModal() {
        document.getElementById('absenModal').style.display = 'none';
        document.getElementById('absenLatitude').value = '';
        document.getElementById('absenLongitude').value = '';
    }

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
