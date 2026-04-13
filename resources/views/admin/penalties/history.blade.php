@extends('layouts.app')
@section('title', 'Riwayat Points — ' . $user->name)

@section('content')
<div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-8">
    <div>
        <h2 class="text-4xl font-extrabold tracking-tighter text-on-surface">Riwayat Poin & Kedisiplinan</h2>
        <p class="text-on-surface-variant mt-2 max-w-xl text-sm">
            Detail riwayat aktivitas harian untuk imam <span class="font-bold text-primary">{{ $user->name }}</span>.
        </p>
    </div>
    <a href="{{ route('admin.penalties.index') }}" class="bg-surface-container hover:bg-surface-container-high text-on-surface py-2.5 px-6 rounded-xl font-bold flex items-center gap-2 transition-colors">
        <span class="material-symbols-outlined text-[18px]">arrow_back</span>
        Kembali
    </a>
</div>

<!-- Summary Bento Grid -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
    <!-- Total Points Card (Primary) -->
    <div class="md:col-span-2 relative overflow-hidden rounded-xl p-8 {{ $user->penalty_points >= 0 ? 'bg-gradient-to-br from-primary-container to-primary shadow-[0_0_15px_rgba(16,185,129,0.3)]' : 'bg-gradient-to-br from-error-container to-error shadow-[0_0_15px_rgba(255,180,171,0.3)]' }} flex flex-col justify-between min-h-[160px]">
        <div class="relative z-10">
            <p class="{{ $user->penalty_points >= 0 ? 'text-on-primary/80' : 'text-on-error/80' }} text-sm font-bold tracking-wide uppercase">Total Poin Sekarang</p>
            <h3 class="text-6xl font-black {{ $user->penalty_points >= 0 ? 'text-on-primary' : 'text-on-error' }} mt-2 tracking-tighter">
                {{ $user->penalty_points >= 0 ? '+' : '' }}{{ $user->penalty_points }}
            </h3>
        </div>
        <!-- Decorative Elements -->
        <span class="material-symbols-outlined absolute -bottom-8 -right-8 text-[160px] text-white/10 rotate-12" style="font-variation-settings: 'FILL' 1;">military_tech</span>
    </div>
</div>

<!-- History List Section -->
<section class="space-y-4">
    <div class="flex justify-between items-end pb-2 border-b border-outline-variant/10">
        <div>
            <h4 class="text-lg font-bold text-on-surface tracking-tight">Log Aktivitas Terbaru</h4>
        </div>
    </div>
    
    <div class="space-y-4">
        @forelse($logs as $log)
            <div class="group bg-surface-container-low hover:bg-surface-container-high transition-all p-5 flex flex-col sm:flex-row items-start sm:items-center justify-between rounded-xl shadow-sm border border-outline-variant/5 gap-4">
                <div class="flex items-center gap-5">
                    @php
                        $isPositive = $log->points >= 0;
                        $iconBgColor = $isPositive ? 'bg-success/10' : 'bg-error/10';
                        $iconColor = $isPositive ? 'text-success' : 'text-error';
                        $icon = 'info';
                        
                        // Map event_type to icon and better readable label if needed
                        $eventLabel = $log->event_type;
                        if($log->event_type === 'attendance_ontime') {
                            $icon = 'verified';
                            $eventLabel = 'Hadir Tepat Waktu';
                        }
                        elseif($log->event_type === 'attendance_late') {
                            $icon = 'schedule';
                            $eventLabel = 'Hadir Terlambat';
                        }
                        elseif($log->event_type === 'no_show') {
                            $icon = 'cancel';
                            $eventLabel = 'Tidak Hadir (No-Show)';
                        }
                        elseif($log->event_type === 'swap_expired') {
                            $icon = 'timer_off';
                            $eventLabel = 'Swap Expired';
                        }
                        elseif($log->event_type === 'admin_reset') {
                            $icon = 'lock_open';
                            $eventLabel = 'Pemutihan Poin (Admin)';
                        }
                    @endphp

                    <div class="w-12 h-12 rounded-xl {{ $iconBgColor }} flex items-center justify-center {{ $iconColor }} shrink-0">
                        <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">{{ $icon }}</span>
                    </div>
                    <div>
                        <h5 class="text-on-surface font-bold text-sm">{{ $eventLabel }}</h5>
                        <div class="flex items-center gap-3 mt-1.5 flex-wrap">
                            <span class="text-[9px] font-black {{ $isPositive ? 'text-success bg-success/20' : 'text-error bg-error/20' }} px-2 py-0.5 rounded uppercase tracking-wider">
                                {{ $isPositive ? 'Reward' : 'Penalty' }}
                            </span>
                            <span class="text-[11px] text-on-surface-variant flex items-center gap-1 font-medium">
                                <span class="material-symbols-outlined text-[13px]">calendar_today</span>
                                {{ $log->created_at->format('d M Y • H:i') }}
                            </span>
                            @if($log->schedule)
                                <span class="text-[11px] text-on-surface-variant flex items-center gap-1 font-medium bg-surface-container-lowest px-2 py-0.5 rounded">
                                    <span class="material-symbols-outlined text-[13px]">mosque</span>
                                    {{ $log->schedule->prayerType->name ?? 'Jadwal' }} ({{ $log->schedule->date?->format('d/m') }})
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="sm:text-right w-full sm:w-auto flex flex-row sm:flex-col items-center sm:items-end justify-between sm:justify-center">
                    <span class="text-xl font-black {{ $isPositive ? 'text-success' : 'text-error' }} tracking-tight">
                        {{ $isPositive ? '+' : '' }}{{ $log->points }}
                    </span>
                    <p class="text-[10px] text-on-surface-variant mt-1 font-medium truncate max-w-[150px]" title="{{ $log->description }}">
                        {{ $log->description ?: 'Automatic System' }}
                    </p>
                </div>
            </div>
        @empty
            <div class="p-16 text-center bg-surface-container-low rounded-xl">
                <span class="material-symbols-outlined text-6xl text-on-surface-variant/30 mb-4 block">history</span>
                <h3 class="text-xl font-bold text-on-surface mb-2">Belum ada riwayat aktivitas.</h3>
            </div>
        @endforelse
    </div>

    @if($logs->hasPages())
    <div class="pt-6">
        {{ $logs->links() }}
    </div>
    @endif
</section>
@endsection
