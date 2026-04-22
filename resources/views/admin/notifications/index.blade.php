@extends('layouts.app')
@section('title', 'Notifikasi Sistem (Admin)')

@section('content')
<div class="flex flex-col md:flex-row md:justify-between md:items-end gap-4 mb-8">
    <div>
        <h2 class="text-3xl font-extrabold tracking-tight text-on-surface mb-1">Komunikasi</h2>
        <p class="text-on-surface-variant text-sm font-medium">Kirim Broadcast dan Lihat Riwayat Notifikasi</p>
    </div>
</div>

<!-- TABS -->
<div class="flex items-baseline gap-2 border-b border-outline-variant/20 mb-8 pb-4" style="overflow-x:auto;">
    <a href="{{ route('admin.broadcast.index') }}" class="px-6 py-2.5 rounded-xl font-bold text-sm transition-all flex items-center gap-2 {{ request()->routeIs('admin.broadcast.*') ? 'bg-primary/20 text-primary border border-primary/30' : 'text-on-surface-variant hover:bg-surface-container hover:text-on-surface min-w-max' }}">
        <span class="material-symbols-outlined text-[18px]">campaign</span> Broadcast Pesan
    </a>
    <a href="{{ route('admin.notification-logs.index') }}" class="px-6 py-2.5 rounded-xl font-bold text-sm transition-all flex items-center gap-2 {{ request()->routeIs('admin.notification-logs.*') ? 'bg-primary/20 text-primary border border-primary/30' : 'text-on-surface-variant hover:bg-surface-container hover:text-on-surface min-w-max' }}">
        <span class="material-symbols-outlined text-[18px]">notifications_active</span> Log Notifikasi
    </a>
    <a href="{{ route('admin.notifications.index') }}" class="px-6 py-2.5 rounded-xl font-bold text-sm transition-all flex items-center gap-2 relative {{ request()->routeIs('admin.notifications.*') ? 'bg-primary/20 text-primary border border-primary/30' : 'text-on-surface-variant hover:bg-surface-container hover:text-on-surface min-w-max' }}">
        <span class="material-symbols-outlined text-[18px]">warning</span> Notifikasi Sistem
        @if(auth()->user()->unreadNotifications->count() > 0)
            <span class="absolute top-2 left-4 w-2 h-2 bg-error rounded-full ring-2 ring-surface-container-low"></span>
        @endif
    </a>
</div>

<div class="card">
    @if($notifications->count() > 0)
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>Tipe / Pesan</th>
                        <th width="150px">Waktu</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($notifications as $notif)
                        <tr style="{{ is_null($notif->read_at) ? 'background-color: var(--clr-surface-hover); border-left: 3px solid var(--clr-accent);' : '' }}">
                            <td>
                                @if(isset($notif->data['type']) && $notif->data['type'] === 'system_alert')
                                    <strong style="color:var(--clr-danger)">[CRITICAL SYSTEM ALERT]</strong><br>
                                @endif
                                {{ $notif->data['message'] ?? 'Tidak ada pesan' }}
                            </td>
                            <td style="color:var(--clr-text-muted);font-size:0.8rem">
                                {{ $notif->created_at->diffForHumans() }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="pagination-wrapper">{{ $notifications->links() }}</div>
    @else
        <div class="empty-state">
            <div class="empty-icon"><span class="material-symbols-outlined text-5xl">notifications_off</span></div>
            <p>Tidak ada notifikasi sistem saat ini.</p>
        </div>
    @endif
</div>
@endsection
