@extends('layouts.app')
@section('title', 'Dashboard Imam')

@section('content')
<div class="flex flex-col gap-1 mb-8">
    <h2 class="text-3xl font-extrabold tracking-tight text-on-surface" style="margin-bottom:4px;">Assalamu'alaikum, {{ auth()->user()->name }}</h2>
    <p class="text-on-surface-variant text-sm font-medium">Ringkasan Aktivitas Anda - {{ now()->translatedFormat('l, d F Y') }}</p>
</div>

<!-- Extra Header Stats & Clock -->
<div class="flex flex-col md:flex-row justify-between items-center bg-surface-container p-6 rounded-[32px] mb-8 border border-primary/30 shadow-[0px_0px_30px_rgba(16,185,129,0.08)] flex-wrap gap-6 relative overflow-hidden mt-5">
    <div class="absolute -right-10 -top-10 w-40 h-40 bg-primary/20 blur-[50px] rounded-full pointer-events-none"></div>

    <div class="flex items-center gap-5 relative z-10">
        <div class="w-16 h-16 rounded-2xl bg-primary/20 border border-primary/40 flex items-center justify-center shadow-[0px_0px_15px_rgba(16,185,129,0.2)]">
            <span class="material-symbols-outlined text-primary text-3xl" style="font-variation-settings: 'FILL' 1;">schedule</span>
        </div>
        <div>
            <div id="realtime-clock" class="text-3xl md:text-4xl font-black bg-gradient-to-r from-primary to-emerald-200 bg-clip-text text-transparent tracking-tighter font-mono drop-shadow-[0_0_10px_rgba(16,185,129,0.3)]">--:--:-- WIB</div>
            <p class="text-[11px] text-on-surface-variant uppercase tracking-[0.2em] font-bold mt-1">Waktu Server Terkini</p>
        </div>
    </div>
    <div class="relative z-10 text-right">
        @if($season)
            <x-badge type="tertiary" class="shadow-sm px-4 py-2 text-xs mb-2 w-fit ml-auto items-center gap-2 border border-outline-variant/20">
                <span class="material-symbols-outlined text-sm">mosque</span>
                {{ $season->name }}
            </x-badge>
        @endif
        <p id="locationStatus" class="flex text-[10px] uppercase tracking-widest text-on-surface-variant font-bold mt-1 bg-surface-container px-3 py-1.5 rounded-full items-center gap-2 border border-outline-variant/30 text-right shadow-sm ml-auto w-fit">
            <span class="material-symbols-outlined text-[14px] animate-spin text-primary">my_location</span>
            Mencari lokasi..
        </p>
    </div>
</div>

<!-- Penalty & Restriction Status -->
@if(auth()->user()->is_restricted)
    <div class="bg-error/10 border border-error/30 p-6 rounded-3xl mb-8 flex items-start gap-4">
        <span class="material-symbols-outlined text-error text-3xl">warning</span>
        <div>
            <h3 class="text-lg font-bold text-error mb-1">Akun Anda Dibatasi (Restricted)</h3>
            <p class="text-sm text-on-surface-variant leading-relaxed">
                Total poin Anda ({{ auth()->user()->penalty_points }}) telah melewati batas bawah (-30). Anda tidak bisa memproses swap, mengambil jadwal, dan mengisi absensi baru. Hubungi Admin.
            </p>
        </div>
    </div>
@else
    <div class="inline-flex items-center gap-3 bg-surface-container px-6 py-3 rounded-full mb-8 border border-outline-variant/10 shadow-sm mt-2">
        <span class="material-symbols-outlined text-primary">trending_up</span>
        <span class="text-xs font-bold uppercase tracking-widest text-on-surface-variant">Poin Performa:</span>
        <strong class="text-xl font-black {{ auth()->user()->penalty_points < 0 ? 'text-error' : 'text-primary' }}">
            {{ auth()->user()->penalty_points > 0 ? '+' : '' }}{{ auth()->user()->penalty_points }}
        </strong>
    </div>
@endif

<!-- Pending Swaps Alert -->
@if($pendingSwaps->count() > 0)
    <div class="bg-tertiary/10 border border-tertiary/20 p-5 rounded-2xl mb-8 flex items-center justify-between">
        <div class="flex items-center gap-3">
            <span class="material-symbols-outlined text-tertiary">swap_horiz</span>
            <p class="text-sm font-medium text-on-surface">Anda memiliki <strong class="text-tertiary">{{ $pendingSwaps->count() }}</strong> permintaan swap yang menunggu respon.</p>
        </div>
        <a href="{{ route('imam.swaps.index') }}" class="px-4 py-2 bg-tertiary/20 text-tertiary text-xs font-bold rounded-lg hover:bg-tertiary/30 transition-colors uppercase tracking-widest">Lihat</a>
    </div>
@endif

<!-- Today's Schedule -->
<div class="bg-surface-container rounded-3xl border-none shadow-sm flex flex-col p-6 md:p-8 relative overflow-hidden mb-8 mt-4">
    <div class="flex justify-between items-center mb-6">
        <h3 class="text-xl font-bold flex items-center gap-3 text-on-surface">
            <div class="w-1.5 h-6 bg-primary rounded-full"></div>
            Jadwal Hari Ini (Mendatang)
        </h3>
        <a href="{{ route('imam.schedules.index') }}" class="px-5 py-2.5 bg-surface-container-highest text-on-surface text-xs font-bold rounded-xl hover:bg-surface-bright transition-all active:scale-95 flex items-center gap-2">
            Jadwal Lengkap
        </a>
    </div>

    @if($mySchedules->count() > 0)
        <!-- STRICT SCHEDULE UI RULE: DO NOT CHANGE TABLE LOGIC OR STRUCTURE -->
        <div class="w-full overflow-x-auto">
            <table class="w-full text-left border-collapse min-w-[600px]">
                <thead>
                    <tr class="bg-surface-container-low/50">
                        <th class="px-6 py-4 text-[11px] font-bold uppercase tracking-widest text-on-surface-variant text-left">Tanggal</th>
                        <th class="px-6 py-4 text-[11px] font-bold uppercase tracking-widest text-on-surface-variant text-left">Hari</th>
                        <th class="px-6 py-4 text-[11px] font-bold uppercase tracking-widest text-on-surface-variant text-left">Sholat</th>
                        <th class="px-6 py-4 text-[11px] font-bold uppercase tracking-widest text-on-surface-variant text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-outline-variant/10">
                    @foreach($mySchedules as $schedule)
                        <tr class="hover:bg-surface-container-high/40 transition-colors">
                            <td class="px-6 py-4">
                                <strong class="text-sm font-semibold text-on-surface">{{ $schedule->date->format('d/m/Y') }}</strong>
                            </td>
                            <td class="px-6 py-4 text-sm text-on-surface-variant">
                                {{ $schedule->date->translatedFormat('l') }}
                            </td>
                            <td class="px-6 py-4">
                                <x-badge type="tertiary">{{ $schedule->prayerType->name }}</x-badge>
                            </td>
                            <td class="px-6 py-4 text-right">
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
                                
                                <div class="flex justify-end gap-2">
                                    @if($isExpired)
                                        <x-badge type="error">Waktu Habis</x-badge>
                                    @elseif($isCheckedIn && $schedule->attendance->status === 'approved')
                                        <x-badge type="success">
                                            <span class="material-symbols-outlined text-[12px]">done_all</span> Selesai
                                        </x-badge>
                                    @elseif($isCheckedIn && $schedule->attendance->status !== 'approved')
                                        <x-badge type="warning">
                                            <span class="material-symbols-outlined text-[12px]">pending</span> Menunggu Validasi
                                        </x-badge>
                                    @elseif($canCheckIn)
                                        <!-- PRESERVE JS onClick AND ID -->
                                        <button type="button" class="px-4 py-2 bg-gradient-to-br from-primary-container to-primary text-on-primary-container text-xs font-bold rounded-lg shadow-[0px_0px_10px_rgba(16,185,129,0.3)] hover:scale-[1.02] transition-transform flex items-center gap-2 border-none cursor-pointer" onclick="openAbsenModal({{ $schedule->id }}, '{{ $schedule->prayerType->name }}')">
                                            <span class="material-symbols-outlined text-sm">location_on</span> Absen Hadir
                                        </button>
                                    @endif
                                    
                                    @if(!$isCheckedIn && $canSwap)
                                        <a href="{{ route('imam.swaps.create') }}" class="px-4 py-2 bg-surface-container-highest text-on-surface hover:text-primary text-xs font-bold rounded-lg transition-colors flex items-center gap-2">
                                            <span class="material-symbols-outlined text-sm">swap_horiz</span> Swap
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <div class="py-12 flex flex-col items-center justify-center text-center">
            <div class="w-16 h-16 rounded-2xl bg-surface-container-low flex items-center justify-center mb-4 border border-outline-variant/10">
                <span class="material-symbols-outlined text-3xl text-on-surface-variant/50">event_busy</span>
            </div>
            <p class="text-on-surface-variant text-sm font-medium">Tidak ada jadwal mendatang.</p>
        </div>
    @endif
</div>

<!-- Absen Modal (Strictly Preseving JS Bindings) -->
<div id="absenModal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.6); backdrop-filter:blur(8px); z-index:9999; justify-content:center; align-items:center;">
    <div class="bg-surface-container p-8 rounded-3xl w-full max-w-[450px] relative m-4 border border-outline-variant/20 shadow-2xl">
        <button onclick="closeAbsenModal()" class="absolute top-6 right-6 text-on-surface-variant hover:text-error transition-colors" style="border:none;background:none;cursor:pointer;">
            <span class="material-symbols-outlined text-2xl">close</span>
        </button>
        
        <h3 class="text-2xl font-bold text-on-surface mb-2 flex items-center gap-3">
            <div class="p-2 bg-primary/10 rounded-lg text-primary"><span class="material-symbols-outlined">how_to_reg</span></div> 
            Absen Sholat <span id="absenPrayerName" class="text-primary ml-1"></span>
        </h3>
        <p class="text-sm text-on-surface-variant mb-6 pb-6 border-b border-outline-variant/10">Silakan unggah foto wajah Anda dengan latar area masjid untuk bukti kehadiran.</p>
        
        <form id="absenForm" method="POST" enctype="multipart/form-data">
            @csrf
            <!-- PRESERVE INPUT NAMES & IDs -->
            <input type="hidden" name="latitude" id="absenLatitude" required>
            <input type="hidden" name="longitude" id="absenLongitude" required>
            
            <div id="absenLocationStatus" class="text-[11px] font-bold uppercase tracking-widest p-4 rounded-xl bg-surface-container-low text-tertiary border border-tertiary/20 mb-6 flex items-center gap-2">
                <span class="material-symbols-outlined text-base animate-spin">my_location</span>
                Mencari lokasi Anda...
            </div>

            <div class="mb-6">
                <!-- PRESERVE INPUT NAME proof_photo -->
                <label class="block text-xs font-bold uppercase tracking-widest text-on-surface-variant mb-2">Unggah Foto Bukti</label>
                <input type="file" name="proof_photo" class="block w-full text-sm text-on-surface file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-surface-container-highest file:text-on-surface file:cursor-pointer hover:file:bg-primary/20 hover:file:text-primary transition-all bg-surface-container-lowest border border-outline-variant/20 rounded-xl p-1 file:mr-3 focus:outline-none" accept="image/*" required>
            </div>
            
            <button type="submit" id="btnSubmitAbsen" class="w-full py-4 bg-gradient-to-br from-primary-container to-primary text-on-primary-container text-sm font-bold uppercase tracking-widest rounded-xl disabled:opacity-50 disabled:cursor-not-allowed hover:shadow-[0px_0px_15px_rgba(16,185,129,0.3)] transition-all cursor-pointer border-none" disabled>
                Upload & Simpan Absen
            </button>
        </form>
    </div>
</div>

<script>
    // JS Strictly Preserved
    function openAbsenModal(scheduleId, prayerName) {
        document.getElementById('absenModal').style.display = 'flex';
        document.getElementById('absenPrayerName').textContent = prayerName;
        document.getElementById('absenForm').action = "/imam/schedules/" + scheduleId + "/attendance";
        
        const locStatus = document.getElementById('absenLocationStatus');
        const btnSubmit = document.getElementById('btnSubmitAbsen');
        const latInput = document.getElementById('absenLatitude');
        const lngInput = document.getElementById('absenLongitude');
        
        locStatus.innerHTML = '<span class="material-symbols-outlined text-base animate-spin">my_location</span> Mencari lokasi Anda...';
        locStatus.className = 'text-[11px] font-bold uppercase tracking-widest p-4 rounded-xl bg-surface-container-low text-tertiary border border-tertiary/20 mb-6 flex items-center gap-2';
        btnSubmit.disabled = true;

        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(
                function(position) {
                    latInput.value = position.coords.latitude;
                    lngInput.value = position.coords.longitude;
                    locStatus.innerHTML = '<span class="material-symbols-outlined text-base">check_circle</span> Lokasi berhasil didapatkan.';
                    locStatus.className = 'text-[11px] font-bold uppercase tracking-widest p-4 rounded-xl bg-primary/10 text-primary border border-primary/20 mb-6 flex items-center gap-2';
                    btnSubmit.disabled = false;
                },
                function(error) {
                    locStatus.innerHTML = '<span class="material-symbols-outlined text-base">error</span> Gagal akses GPS. Pastikan izin menyala.';
                    locStatus.className = 'text-[11px] font-bold uppercase tracking-widest p-4 rounded-xl bg-error/10 text-error border border-error/20 mb-6 flex items-center gap-2';
                    btnSubmit.disabled = true;
                },
                { enableHighAccuracy: true, timeout: 10000, maximumAge: 0 }
            );
        } else {
            locStatus.innerHTML = '<span class="material-symbols-outlined text-base">error</span> Browser tidak mendukung GPS.';
            locStatus.className = 'text-[11px] font-bold uppercase tracking-widest p-4 rounded-xl bg-error/10 text-error border border-error/20 mb-6 flex items-center gap-2';
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
