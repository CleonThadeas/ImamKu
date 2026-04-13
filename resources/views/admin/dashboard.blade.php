@extends('layouts.app')
@section('title', 'Admin Dashboard')

@section('content')
<div class="flex flex-col gap-1 mb-8">
    <h2 class="text-3xl font-extrabold tracking-tight text-on-surface" style="margin-bottom:4px;">Dashboard Admin</h2>
    <p class="text-on-surface-variant text-sm font-medium">Ringkasan Sistem ImamKu - {{ now()->translatedFormat('l, d F Y') }}</p>
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
            <x-badge type="tertiary" class="shadow-sm px-4 py-2 text-xs mb-2 flex items-center gap-2 border border-outline-variant/20">
                <span class="material-symbols-outlined text-sm">mosque</span>
                {{ $season->name }}
            </x-badge>
        @else
            <x-badge type="error" class="shadow-sm px-4 py-2 text-xs mb-2 flex items-center gap-2 border border-outline-variant/20">
                <span class="material-symbols-outlined text-sm">warning</span>
                Tidak ada Season Aktif
            </x-badge>
        @endif
    </div>
</div>

<!-- Stats -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8 mt-5">
    <x-stat-card title="Total Imam Aktif" :value="$stats['total_imams']" icon="person_book" color="primary" />
    <x-stat-card title="Jadwal Terisi" :value="$stats['total_schedules']" icon="event_available" color="primary" />
    <x-stat-card title="Slot Kosong" :value="$stats['empty_slots']" icon="event_busy" color="error" />
    <x-stat-card title="Notifikasi Terkirim" :value="$stats['notifications_sent']" icon="notifications_active" color="tertiary" />
</div>

<!-- Today's Schedule -->
<div class="bg-surface-container rounded-3xl border-none shadow-sm flex flex-col p-6 md:p-8 relative overflow-hidden mb-8 mt-5">
    <div class="flex justify-between items-center mb-6">
        <h3 class="text-xl font-bold flex items-center gap-3 text-on-surface">
            <div class="w-1.5 h-6 bg-primary rounded-full"></div>
            Jadwal Hari Ini
        </h3>
        <a href="{{ route('admin.schedules.index') }}" class="px-5 py-2.5 bg-surface-container-highest text-on-surface text-xs font-bold rounded-xl hover:bg-surface-bright transition-all active:scale-95 flex items-center gap-2">
            Lihat Semua
        </a>
    </div>

    @if($stats['today_schedules']->count() > 0)
        <!-- DO NOT CHANGE THIS TABLE STRUCTURE (Schedule UI Rule) -->
        <div class="w-full overflow-x-auto">
            <table class="w-full text-left border-collapse min-w-[600px]">
                <thead>
                    <tr class="bg-surface-container-low/50">
                        <th class="px-6 py-4 text-[11px] font-bold uppercase tracking-widest text-on-surface-variant text-left">Waktu</th>
                        <th class="px-6 py-4 text-[11px] font-bold uppercase tracking-widest text-on-surface-variant text-left">Sholat</th>
                        <th class="px-6 py-4 text-[11px] font-bold uppercase tracking-widest text-on-surface-variant text-left">Imam</th>
                        <th class="px-6 py-4 text-[11px] font-bold uppercase tracking-widest text-on-surface-variant text-left">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-outline-variant/10">
                    @foreach($stats['today_schedules'] as $schedule)
                        <tr class="hover:bg-surface-container-high/40 transition-colors">
                            <td class="px-6 py-4">
                                @php
                                    $pt = \App\Models\PrayerTime::where('season_id', $season ? $season->id : 0)
                                        ->where('date', now()->toDateString())
                                        ->where('prayer_type_id', $schedule->prayer_type_id)
                                        ->first();
                                @endphp
                                <span class="font-mono text-xs text-on-surface-variant">{{ $pt ? $pt->effective_time : '-' }} WIB</span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-sm font-semibold text-on-surface">{{ $schedule->prayerType->name }}</span>
                            </td>
                            <td class="px-6 py-4">
                                @if($schedule->user)
                                    <div class="flex items-center gap-2.5">
                                        <div class="w-7 h-7 rounded-full bg-primary/20 flex items-center justify-center text-primary font-bold text-[10px]">{{ substr($schedule->user->name, 0, 1) }}</div>
                                        <span class="text-sm font-medium text-on-surface">{{ $schedule->user->name }}</span>
                                    </div>
                                @else
                                    <span class="text-sm font-medium text-error">Belum Ditentukan</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                @if($schedule->user)
                                    <x-badge type="success">
                                        <span class="w-1.5 h-1.5 rounded-full bg-primary"></span>
                                        Terisi
                                    </x-badge>
                                @else
                                    <x-badge type="error">
                                        <span class="w-1.5 h-1.5 rounded-full bg-error"></span>
                                        Kosong
                                    </x-badge>
                                @endif
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
            <p class="text-on-surface-variant text-sm font-medium">Tidak ada jadwal untuk hari ini.</p>
            @if(!$season)
                <a href="{{ route('admin.seasons.create') }}" class="mt-4 px-6 py-3 bg-gradient-to-br from-primary-container to-primary text-on-primary-container text-xs font-bold rounded-xl shadow-[0px_0px_15px_rgba(16,185,129,0.3)] hover:scale-[1.02] transition-transform">
                    Buat Season Ramadan
                </a>
            @endif
        </div>
    @endif
</div>

<!-- Bottom Extra Section: Broadcast (UI Only) -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-8 pb-8 mt-5">
    <div class="lg:col-span-2 bg-surface-container p-8 rounded-3xl relative overflow-hidden group border-none shadow-sm">
        <div class="absolute right-0 top-0 bottom-0 w-1/3 bg-gradient-to-l from-primary/5 to-transparent pointer-events-none"></div>
        <div class="relative z-10 flex flex-col h-full">
            <div class="flex flex-wrap items-center gap-3 mb-2">
                <h4 class="text-xl font-bold tracking-tight text-on-surface" style="margin-bottom:0px;">Quick Broadcast</h4>
                <x-badge type="tertiary" class="text-[9px]">UI Mockup / Pending Backend</x-badge>
            </div>
            <p class="text-on-surface-variant text-sm max-w-md mt-1">Kirimkan notifikasi darurat segera kepada seluruh Imam (Preview Fitur Baru).</p>
            <div class="mt-8 flex gap-3 flex-wrap sm:flex-nowrap">
                <input type="text" disabled class="flex-1 w-full sm:w-auto bg-surface-container-lowest border border-outline-variant/10 rounded-xl px-4 py-3 text-sm focus:ring-1 focus:ring-primary outline-none text-on-surface opacity-60 cursor-not-allowed" placeholder="Tulis pesan pengumuman..." />
                <button disabled title="Feature is UI only currently" class="w-full sm:w-auto px-6 py-3 bg-primary/10 text-primary font-bold text-xs rounded-xl hover:bg-primary/20 opacity-60 cursor-not-allowed transition-all uppercase tracking-widest text-nowrap">KIRIM BROADCAST</button>
            </div>
        </div>
    </div>
    
    <div class="bg-surface-container-high p-8 rounded-3xl flex flex-col justify-center items-center text-center shadow-sm border border-outline-variant/5">
        <div class="w-16 h-16 rounded-2xl bg-primary/10 flex items-center justify-center mb-4">
            <span class="material-symbols-outlined text-3xl text-primary" style="font-variation-settings: 'FILL' 1;">cloud_download</span>
        </div>
        <h4 class="text-lg font-bold text-on-surface" style="margin-bottom:5px;">Laporan Bulanan</h4>
        <p class="text-on-surface-variant text-xs mt-2 px-4 max-w-[200px]">Download ringkasan fee dan performa imam.</p>
        <a href="{{ route('admin.fees.report') }}" class="mt-6 w-full py-3 bg-surface-container text-on-surface font-bold text-[10px] uppercase tracking-[0.2em] rounded-xl hover:bg-primary hover:text-on-primary-container transition-colors inline-block text-center border border-outline-variant/10">UNDUH PDF</a>
    </div>
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
