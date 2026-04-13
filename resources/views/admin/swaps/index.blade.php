@extends('layouts.app')
@section('title', 'Monitoring Swap Jadwal')

@section('content')
<div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-8">
    <div>
        <h2 class="text-2xl font-bold flex items-center gap-3 text-on-surface">
            <div class="w-1.5 h-6 bg-primary rounded-full"></div>
            Monitoring Swap Jadwal
        </h2>
        <div class="text-sm text-on-surface-variant font-medium mt-1 flex items-center gap-2 tracking-wide">
            <span class="material-symbols-outlined text-[16px]">admin_panel_settings</span>
            Admin — Pantau transaksi pertukaran jadwal antar Imam secara menyeluruh
        </div>
    </div>
</div>

<div class="card p-0 overflow-hidden">
    <div class="table-wrapper border-0 rounded-none bg-surface-container-low mt-4">
        <table>
            <thead>
                <tr>
                    <th class="bg-surface-container-low !px-6">Waktu Dibuat</th>
                    <th class="bg-surface-container-low">Imam Pemohon</th>
                    <th class="bg-surface-container-low">Jadwal Terdaftar</th>
                    <th class="bg-surface-container-low">Imam Penerima & Jadwal Pengganti</th>
                    <th class="bg-surface-container-low">Status Akhir</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-outline-variant/10">
                @forelse($swaps as $swap)
                    <tr class="hover:bg-surface-container/50 transition-colors">
                        <td class="!px-6">
                            <div class="font-bold text-on-surface">{{ $swap->created_at->format('d/m/Y') }}</div>
                            <div class="text-xs text-on-surface-variant flex items-center gap-1 mt-1">
                                <span class="material-symbols-outlined text-[12px]">schedule</span> 
                                {{ $swap->created_at->format('H:i') }} WIB
                            </div>
                        </td>
                        <td>
                            <div class="font-bold text-primary flex items-center gap-2">
                                <div class="w-7 h-7 rounded-full bg-primary/20 flex items-center justify-center text-[10px] font-bold text-primary">{{ substr($swap->requester->name ?? 'A', 0, 1) }}</div>
                                {{ $swap->requester->name ?? '-' }}
                            </div>
                        </td>
                        <td>
                            <x-badge type="tertiary" class="!text-[10px] !py-0.5 !px-2 mb-1">{{ $swap->schedule->prayerType->name ?? '-' }}</x-badge>
                            <div class="text-xs text-on-surface-variant">{{ $swap->schedule->date ? \Carbon\Carbon::parse($swap->schedule->date)->format('d/m/Y') : '-' }}</div>
                        </td>
                        <td>
                            @if($swap->targetSchedule)
                                <div class="font-bold text-secondary flex items-center gap-2 mb-1">
                                    <div class="w-5 h-5 rounded-full bg-secondary/20 flex items-center justify-center text-[8px] font-bold text-secondary">{{ substr($swap->targetSchedule->user->name ?? 'A', 0, 1) }}</div>
                                    {{ $swap->targetSchedule->user->name ?? '-' }}
                                </div>
                                <x-badge type="info" class="!text-[10px] !py-0.5 !px-2 mb-1">{{ $swap->targetSchedule->prayerType->name ?? '-' }}</x-badge>
                                <div class="text-xs text-on-surface-variant">{{ $swap->targetSchedule->date ? \Carbon\Carbon::parse($swap->targetSchedule->date)->format('d/m/Y') : '' }}</div>
                            @else
                                <span class="text-xs text-on-surface-variant/60 italic inline-flex items-center gap-1">
                                    <span class="material-symbols-outlined text-[12px]">pending</span> Tawaran belum diambil
                                </span>
                            @endif
                        </td>
                        <td>
                            @if($swap->status === 'pending')
                                <x-badge type="warning" class="shadow-sm">
                                    <span class="material-symbols-outlined text-[12px] animate-pulse">sync</span> Menunggu
                                </x-badge>
                            @elseif($swap->status === 'accepted')
                                <x-badge type="success" class="shadow-sm">
                                    <span class="material-symbols-outlined text-[12px]">check_circle</span> Berhasil
                                </x-badge>
                            @elseif($swap->status === 'rejected')
                                <x-badge type="error" class="shadow-sm">
                                    <span class="material-symbols-outlined text-[12px]">cancel</span> Dibatalkan
                                </x-badge>
                            @elseif($swap->status === 'expired')
                                <x-badge type="tertiary" class="shadow-sm opacity-60">
                                    <span class="material-symbols-outlined text-[12px]">timer_off</span> Kedaluwarsa
                                </x-badge>
                            @else
                                <x-badge type="tertiary" class="shadow-sm">
                                    <span class="material-symbols-outlined text-[12px]">info</span> {{ ucfirst($swap->status) }}
                                </x-badge>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="p-12 text-center">
                            <div class="flex flex-col items-center justify-center text-on-surface-variant/50">
                                <span class="material-symbols-outlined text-5xl mb-3">swap_horiz</span>
                                <p class="text-sm font-medium">Kosong. Belum ada riwayat permohonan Swap Jadwal oleh para Imam.</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    @if($swaps->hasPages())
    <div class="pagination-wrapper p-4 border-t border-outline-variant/10 bg-surface-container">
        {{ $swaps->links('pagination::bootstrap-5') }}
    </div>
    @endif
</div>
@endsection
