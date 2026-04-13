@extends('layouts.app')
@section('title', 'Validasi Absensi Imam')

@section('content')
<div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-8">
    <div>
        <h2 class="text-2xl font-bold flex items-center gap-3 text-on-surface">
            <div class="w-1.5 h-6 bg-primary rounded-full"></div>
            Validasi Absensi Imam
        </h2>
        <div class="text-sm text-on-surface-variant font-medium mt-1 flex items-center gap-2 tracking-wide">
            <span class="material-symbols-outlined text-[16px]">admin_panel_settings</span>
            Admin — Manajemen Absen Kehadiran
        </div>
    </div>
</div>

@if(!$season)
    <div class="card p-12 text-center border-dashed border-2 border-outline-variant/30 bg-surface-container/50">
        <span class="material-symbols-outlined text-6xl text-on-surface-variant/30 mb-4 block">event_busy</span>
        <h3 class="text-xl font-bold text-on-surface mb-2">Belum ada Season Aktif</h3>
        <p class="text-on-surface-variant max-w-md mx-auto mb-6">Silakan buat dan aktifkan Season Ramadan terlebih dahulu untuk mulai mengelola kehadiran absensi imam.</p>
        <a href="{{ route('admin.seasons.index') }}" class="btn btn-primary inline-flex items-center gap-2">
            <span class="material-symbols-outlined text-sm">settings</span> Atur Season
        </a>
    </div>
@else
    <!-- Skema Panel -->
    <div class="card mb-8">
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between mb-6 pb-6 border-b border-outline-variant/20 gap-4">
            <div>
                <h3 class="text-lg font-bold text-on-surface flex items-center gap-2">
                    <span class="material-symbols-outlined text-primary">rule_settings</span>
                    Skema Persetujuan Absensi & Pencairan Fee
                </h3>
                <p class="text-sm text-on-surface-variant mt-1.5">Tentukan bagaimana absensi kehadiran (beserta laporan fee-nya) akan divalidasi oleh sistem.</p>
            </div>
        </div>
        
        @if($feeConfig && !$feeConfig->is_enabled)
            <div class="bg-warning/10 border border-warning/30 rounded-2xl p-4 mb-6 relative overflow-hidden flex gap-4">
                <div class="absolute top-0 left-0 w-1 h-full bg-warning"></div>
                <span class="material-symbols-outlined text-warning text-3xl">money_off</span>
                <div>
                    <h4 class="font-bold text-warning text-sm mb-1 uppercase tracking-wider">Warning: Sistem Keuangan Nonaktif</h4>
                    <p class="text-sm text-on-surface-variant leading-relaxed">
                        Sistem Manajemen Keuangan dan pencairan fee saat ini <strong class="text-error">NONAKTIF</strong>. 
                        Absensi yang disetujui disini sifatnya hanya merekam kehadiran operasional dan <strong class="text-warning">tidak akan menambah saldo Fee apapun</strong> kepada Imam.
                    </p>
                </div>
            </div>
        @endif

        <form action="{{ route('admin.fee-configs.toggle-auto-approve') }}" method="POST">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                <!-- Manual Validation -->
                <label class="relative flex items-start gap-4 p-5 rounded-2xl cursor-pointer border-2 transition-all duration-200 {{ !$feeConfig->is_auto_approve_attendance ? 'border-primary bg-primary/5 shadow-[0_0_15px_rgba(16,185,129,0.1)]' : 'border-outline-variant/20 bg-surface-container-low hover:border-primary/50' }}">
                    <input type="radio" name="is_auto_approve_attendance" value="0" {{ !$feeConfig->is_auto_approve_attendance ? 'checked' : '' }} class="mt-1 w-5 h-5 accent-primary bg-surface-container-highest border-outline-variant/50 focus:ring-primary focus:ring-offset-surface shrink-0 cursor-pointer">
                    <div class="flex-1">
                        <strong class="block text-base font-bold {{ !$feeConfig->is_auto_approve_attendance ? 'text-primary' : 'text-on-surface' }}">Validasi Manual (Rekomendasi)</strong>
                        <p class="text-sm text-on-surface-variant mt-2 leading-relaxed">Admin harus mengecek ulang dan menyetujui foto bukti absensi secara manual barulah fee dicairkan ke dompet Imam.</p>
                    </div>
                    @if(!$feeConfig->is_auto_approve_attendance)
                        <span class="material-symbols-outlined absolute top-4 right-4 text-primary opacity-20 text-4xl pointer-events-none">verified_user</span>
                    @endif
                </label>

                <!-- Auto Validation -->
                <label class="relative flex items-start gap-4 p-5 rounded-2xl cursor-pointer border-2 transition-all duration-200 {{ $feeConfig->is_auto_approve_attendance ? 'border-primary bg-primary/5 shadow-[0_0_15px_rgba(16,185,129,0.1)]' : 'border-outline-variant/20 bg-surface-container-low hover:border-primary/50' }}">
                    <input type="radio" name="is_auto_approve_attendance" value="1" {{ $feeConfig->is_auto_approve_attendance ? 'checked' : '' }} class="mt-1 w-5 h-5 accent-primary bg-surface-container-highest border-outline-variant/50 focus:ring-primary focus:ring-offset-surface shrink-0 cursor-pointer">
                    <div class="flex-1">
                        <strong class="block text-base font-bold {{ $feeConfig->is_auto_approve_attendance ? 'text-primary' : 'text-on-surface' }}">Setujui Otomatis (Robot Scheduler)</strong>
                        <p class="text-sm text-on-surface-variant mt-2 leading-relaxed">Sistem akan menyetujui absensi otomatis dan memproses pencairan Fee <strong class="text-primary font-semibold">30 menit setelah waktu sholat</strong>. Bukti foto tetap terarsip.</p>
                    </div>
                    @if($feeConfig->is_auto_approve_attendance)
                        <span class="material-symbols-outlined absolute top-4 right-4 text-primary opacity-20 text-4xl pointer-events-none">smart_toy</span>
                    @endif
                </label>
            </div>
            
            <button type="submit" class="btn btn-primary group flex items-center justify-center gap-2 w-full sm:w-auto px-6 py-3 font-semibold tracking-wide">
                <span class="material-symbols-outlined text-[18px] group-hover:scale-110 transition-transform">save</span> Simpan Skema Validasi
            </button>
        </form>
    </div>

    <!-- Antrean Absensi -->
    <div class="card p-0 overflow-hidden">
        <div class="p-6 border-b border-outline-variant/20 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-surface-container/50">
            <div>
                <h3 class="text-lg font-bold text-on-surface flex items-center gap-2">
                    <span class="material-symbols-outlined text-accent">pending_actions</span>
                    Antrean Validasi Absensi
                </h3>
                <p class="text-sm text-on-surface-variant">Menampilkan status kehadiran aktual Imam yang butuh persetujuan.</p>
            </div>
        </div>

        @if(session('success'))
            <div class="p-4 mx-6 mt-6 rounded-xl bg-primary/10 border border-primary/20 text-primary text-sm font-medium flex items-center gap-2"><span class="material-symbols-outlined text-lg">check_circle</span>{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="p-4 mx-6 mt-6 rounded-xl bg-error/10 border border-error/20 text-error text-sm font-medium flex items-center gap-2"><span class="material-symbols-outlined text-lg">error</span>{{ session('error') }}</div>
        @endif

        <div class="table-wrapper border-0 rounded-none bg-surface-container-low mt-4">
            <table>
                <thead>
                    <tr>
                        <th class="bg-surface-container-low !px-6">Waktu Absen</th>
                        <th class="bg-surface-container-low">Imam</th>
                        <th class="bg-surface-container-low">Jadwal Sholat</th>
                        <th class="bg-surface-container-low">Bukti Hadir</th>
                        <th class="bg-surface-container-low">Status</th>
                        <th class="bg-surface-container-low text-right !px-6">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-outline-variant/10">
                    @forelse($attendances as $row)
                        <tr class="hover:bg-surface-container/50 transition-colors">
                            <td class="!px-6">
                                <div class="font-bold text-on-surface">{{ $row->created_at->format('d/m/Y') }}</div>
                                <div class="text-xs text-on-surface-variant flex items-center gap-1 mt-1">
                                    <span class="material-symbols-outlined text-[12px]">schedule</span> 
                                    {{ $row->created_at->format('H:i') }} WIB
                                </div>
                            </td>
                            <td>
                                <div class="font-bold text-primary flex items-center gap-2">
                                    <div class="w-7 h-7 rounded-full bg-primary/20 flex items-center justify-center text-[10px] font-bold text-primary">{{ substr($row->schedule->user->name ?? 'A', 0, 1) }}</div>
                                    {{ $row->schedule->user->name ?? '-' }}
                                </div>
                            </td>
                            <td>
                                <x-badge type="tertiary" class="!text-[10px] !py-0.5 !px-2 mb-1">{{ $row->schedule->prayerType->name ?? '-' }}</x-badge>
                                <div class="text-xs text-on-surface-variant">{{ \Carbon\Carbon::parse($row->schedule->date)->format('d/m/Y') }}</div>
                            </td>
                            <td>
                                @if($row->proof_path)
                                    <a href="{{ Storage::url($row->proof_path) }}" target="_blank" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-info/10 text-info border border-info/20 hover:bg-info/20 rounded-lg text-xs font-bold transition-colors">
                                        <span class="material-symbols-outlined text-[14px]">image</span> Lihat Foto
                                    </a>
                                @else
                                    <span class="text-xs text-on-surface-variant/60 italic inline-flex items-center gap-1">
                                        <span class="material-symbols-outlined text-[12px]">no_photography</span> Tanpa Foto
                                    </span>
                                @endif
                            </td>
                            <td>
                                @if($row->status === 'pending')
                                    <x-badge type="warning" class="shadow-sm">
                                        <span class="material-symbols-outlined text-[12px] animate-pulse">hourglass_top</span> Menunggu
                                    </x-badge>
                                @elseif($row->status === 'approved')
                                    <x-badge type="success" class="shadow-sm">
                                        <span class="material-symbols-outlined text-[12px]">check_circle</span> Disetujui
                                    </x-badge>
                                @elseif($row->status === 'expired')
                                    <x-badge type="error" class="shadow-sm">
                                        <span class="material-symbols-outlined text-[12px]">timer_off</span> Kedaluwarsa
                                    </x-badge>
                                @else
                                    <x-badge type="error" class="shadow-sm">
                                        <span class="material-symbols-outlined text-[12px]">cancel</span> Ditolak
                                    </x-badge>
                                @endif
                            </td>
                            <td class="!px-6">
                                @if($row->status === 'pending')
                                    <div class="flex items-center justify-end gap-2">
                                        <form action="{{ route('admin.attendances.approve', $row->id) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="px-3 py-1.5 bg-primary/10 text-primary border border-primary/20 hover:bg-primary/20 rounded-lg text-xs font-bold flex items-center gap-1.5 transition-colors" title="Setujui & Cairkan Fee">
                                                <span class="material-symbols-outlined text-[14px]">check_circle</span> Setujui
                                            </button>
                                        </form>
                                        <form action="{{ route('admin.attendances.reject', $row->id) }}" method="POST" onsubmit="return confirm('Tolak absensi ini?');">
                                            @csrf
                                            <button type="submit" class="px-3 py-1.5 bg-error/10 text-error border border-error/20 hover:bg-error/20 rounded-lg text-xs font-bold flex items-center gap-1.5 transition-colors" title="Tolak">
                                                <span class="material-symbols-outlined text-[14px]">cancel</span> Tolak
                                            </button>
                                        </form>
                                    </div>
                                @elseif($row->status === 'expired')
                                    <div class="flex flex-col items-end gap-2">
                                        <form action="{{ route('admin.attendances.approve', $row->id) }}" method="POST" class="w-full">
                                            @csrf
                                            <button type="submit" class="w-full px-3 py-1.5 bg-primary/10 text-primary border border-primary/20 hover:bg-primary/20 rounded-lg text-[10px] font-bold flex items-center justify-center gap-1 transition-colors whitespace-nowrap">
                                                <span class="material-symbols-outlined text-[12px]">verified</span> Anggap Hadir
                                            </button>
                                        </form>
                                        <form action="{{ route('admin.attendances.reject', $row->id) }}" method="POST" class="w-full" onsubmit="return confirm('Konfirmasi imam ini tidak hadir?');">
                                            @csrf
                                            <input type="hidden" name="notes" value="Imam dikonfirmasi TIDAK hadir karena lewat batas waktu.">
                                            <button type="submit" class="w-full px-3 py-1.5 bg-error/10 text-error border border-error/20 hover:bg-error/20 rounded-lg text-[10px] font-bold flex items-center justify-center gap-1 transition-colors whitespace-nowrap">
                                                <span class="material-symbols-outlined text-[12px]">person_off</span> Konfirmasi Tidak Hadir
                                            </button>
                                        </form>
                                    </div>
                                @else
                                    <div class="text-right">
                                        <span class="text-[10px] uppercase tracking-widest text-on-surface-variant font-bold flex items-center justify-end gap-1 px-3 py-1.5 border border-outline-variant/10 rounded-lg bg-surface-container w-fit ml-auto">
                                            <span class="material-symbols-outlined text-[12px]">lock</span> Telah Diproses
                                        </span>
                                    </div>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-12 text-center">
                                <div class="flex flex-col items-center justify-center text-on-surface-variant/50">
                                    <span class="material-symbols-outlined text-5xl mb-3">inbox</span>
                                    <p class="text-sm font-medium">Kosong. Belum ada antrean absensi masuk yang butuh disetujui.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($attendances->hasPages())
        <div class="pagination-wrapper p-4 border-t border-outline-variant/10 bg-surface-container">
            {{ $attendances->links('pagination::bootstrap-5') }}
        </div>
        @endif
    </div>
@endif
@endsection
