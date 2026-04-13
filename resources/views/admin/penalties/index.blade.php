@extends('layouts.app')
@section('title', 'Penalty & Ranking Imam')

@section('content')
<div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-8">
    <div>
        <h2 class="text-4xl font-extrabold tracking-tighter text-on-surface">Penalty & Ranking Imam</h2>
        <p class="text-on-surface-variant mt-2 max-w-xl text-sm">
            Monitoring performa dan sistem penalti imam secara real-time berdasarkan kehadiran dan kedisiplinan.
        </p>
    </div>
</div>

<!-- Penalty Config Summary -->
<div class="bg-surface-container-low p-4 rounded-xl mb-8 border-l-4 border-primary shadow-lg flex flex-wrap gap-4 items-center">
    <span class="text-[10px] font-bold uppercase tracking-widest text-on-surface-variant bg-surface-container px-3 py-1.5 rounded-lg mr-2">Aturan Poin</span>
    <x-badge type="success"><span class="material-symbols-outlined text-[12px]">check</span> Hadir: +{{ $penaltyConfig['attendance_ontime'] }}</x-badge>
    <x-badge type="warning"><span class="material-symbols-outlined text-[12px]">schedule</span> Terlambat: {{ $penaltyConfig['attendance_late'] }}</x-badge>
    <x-badge type="error"><span class="material-symbols-outlined text-[12px]">cancel</span> No-Show: {{ $penaltyConfig['no_show'] }}</x-badge>
    <x-badge type="error"><span class="material-symbols-outlined text-[12px]">timer_off</span> Swap Expired: {{ $penaltyConfig['swap_expired'] }}</x-badge>
    <x-badge type="tertiary"><span class="material-symbols-outlined text-[12px]">lock</span> Restriction: &le; {{ $penaltyConfig['restriction_threshold'] }} poin</x-badge>
</div>

<!-- Imam Ranking Table -->
<div class="bg-surface-container-low rounded-xl overflow-hidden shadow-[0px_4px_12px_rgba(0,0,0,0.1),0px_12px_32px_rgba(0,0,0,0.2),0px_0px_8px_rgba(78,222,163,0.05)]">
    <div class="px-6 py-5 flex justify-between items-center bg-surface-container border-b border-outline-variant/10">
        <h3 class="font-bold text-on-surface flex items-center gap-2">
            <span class="material-symbols-outlined text-primary-fixed-dim">leaderboard</span>
            Ranking Imam
        </h3>
    </div>

    @if($imams->count() > 0)
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-surface-container/50">
                    <th class="px-6 py-4 text-[11px] font-black uppercase tracking-[0.1em] text-on-surface-variant/70 border-b border-outline-variant/10 w-12">#</th>
                    <th class="px-6 py-4 text-[11px] font-black uppercase tracking-[0.1em] text-on-surface-variant/70 border-b border-outline-variant/10">Imam</th>
                    <th class="px-6 py-4 text-[11px] font-black uppercase tracking-[0.1em] text-on-surface-variant/70 border-b border-outline-variant/10 text-center">Total Poin</th>
                    <th class="px-6 py-4 text-[11px] font-black uppercase tracking-[0.1em] text-on-surface-variant/70 border-b border-outline-variant/10 text-center">Hadir</th>
                    <th class="px-6 py-4 text-[11px] font-black uppercase tracking-[0.1em] text-on-surface-variant/70 border-b border-outline-variant/10 text-center">Terlambat</th>
                    <th class="px-6 py-4 text-[11px] font-black uppercase tracking-[0.1em] text-on-surface-variant/70 border-b border-outline-variant/10 text-center">No-Show</th>
                    <th class="px-6 py-4 text-[11px] font-black uppercase tracking-[0.1em] text-on-surface-variant/70 border-b border-outline-variant/10 text-center">Swap Exp.</th>
                    <th class="px-6 py-4 text-[11px] font-black uppercase tracking-[0.1em] text-on-surface-variant/70 border-b border-outline-variant/10">Status</th>
                    <th class="px-6 py-4 text-right border-b border-outline-variant/10">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-outline-variant/10">
                @foreach($imams as $i => $imam)
                    @php
                        $bd = $imam->penalty_breakdown;
                        $ontime = $bd->get('attendance_ontime');
                        $late = $bd->get('attendance_late');
                        $noshow = $bd->get('no_show');
                        $swapExp = $bd->get('swap_expired');
                    @endphp
                    <tr class="hover:bg-surface-container-high/40 transition-colors group {{ $imam->is_restricted ? 'bg-error/5 relative' : '' }}">
                        @if($imam->is_restricted)
                        <div class="absolute left-0 top-0 bottom-0 w-1 bg-error block"></div>
                        @endif
                        <td class="px-6 py-5">
                            <span class="text-on-surface-variant font-bold text-sm">{{ $i + 1 }}</span>
                        </td>
                        <td class="px-6 py-5">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl bg-primary/20 flex items-center justify-center text-lg font-bold text-primary">{{ substr($imam->name ?? 'I', 0, 1) }}</div>
                                <div>
                                    <p class="font-bold text-on-surface group-hover:text-primary transition-colors flex items-center gap-2">
                                        {{ $imam->name }}
                                        @if(!$imam->is_active)
                                            <span class="px-2 py-0.5 rounded-full text-[9px] font-black uppercase bg-warning/20 text-warning">Nonaktif</span>
                                        @endif
                                    </p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-5 text-center">
                            <div class="text-xl font-black {{ $imam->penalty_points >= 0 ? 'text-success' : 'text-error' }}">
                                {{ $imam->penalty_points >= 0 ? '+' : '' }}{{ $imam->penalty_points }}
                            </div>
                        </td>
                        <td class="px-6 py-5 text-center"><div class="text-on-surface-variant font-medium text-sm">{{ $ontime?->count ?? 0 }}</div></td>
                        <td class="px-6 py-5 text-center"><div class="text-on-surface-variant font-medium text-sm">{{ $late?->count ?? 0 }}</div></td>
                        <td class="px-6 py-5 text-center"><div class="text-on-surface-variant font-medium text-sm">{{ $noshow?->count ?? 0 }}</div></td>
                        <td class="px-6 py-5 text-center"><div class="text-on-surface-variant font-medium text-sm">{{ $swapExp?->count ?? 0 }}</div></td>
                        <td class="px-6 py-5">
                            @if($imam->is_restricted)
                                <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase bg-error/20 text-error inline-flex items-center gap-1">
                                    <span class="material-symbols-outlined text-[12px]">lock</span> Dibatasi
                                </span>
                            @else
                                <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase bg-primary/20 text-primary-fixed-dim">Aktif</span>
                            @endif
                        </td>
                        <td class="px-6 py-5 text-right">
                            <div class="flex items-center justify-end gap-2 opacity-100 lg:opacity-0 lg:group-hover:opacity-100 transition-opacity">
                                <a href="{{ route('admin.penalties.history', $imam) }}" class="px-3 py-1.5 bg-secondary/10 text-secondary border border-secondary/20 hover:bg-secondary/20 rounded-lg text-xs font-bold transition-colors">Riwayat</a>
                                @if($imam->is_restricted)
                                    <form method="POST" action="{{ route('admin.penalties.lift', $imam) }}" onsubmit="return confirm('Angkat pembatasan untuk {{ $imam->name }}?')">
                                        @csrf
                                        <button type="submit" class="px-3 py-1.5 bg-primary/10 text-primary border border-primary/20 hover:bg-primary/20 rounded-lg text-xs font-bold transition-colors flex items-center gap-1">
                                            <span class="material-symbols-outlined text-[14px]">lock_open</span> Angkat
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @else
    <div class="p-16 text-center">
        <span class="material-symbols-outlined text-6xl text-on-surface-variant/30 mb-4 block">military_tech</span>
        <h3 class="text-xl font-bold text-on-surface mb-2">Belum ada data imam.</h3>
    </div>
    @endif
</div>
@endsection
