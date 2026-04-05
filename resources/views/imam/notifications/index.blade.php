@extends('layouts.app')
@section('title', 'Inbox Notifikasi')

@section('content')
<div class="main-header">
    <div>
        <h2 style="display:flex;align-items:center;gap:10px"><svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M18 8A6 6 0 006 8c0 7-3 9-3 9h18s-3-2-3-9M13.73 21a2 2 0 01-3.46 0"/></svg> Notifikasi</h2>
        <div class="breadcrumb">Pesan dan informasi terbaru</div>
    </div>
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
                                @if(isset($notif->data['type']) && $notif->data['type'] === 'broadcast')
                                    <strong style="color:var(--clr-accent)">[BROADCAST ADMIN]</strong><br>
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
            <div class="empty-icon"><svg width="48" height="48" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" style="opacity:0.5"><path d="M18 8A6 6 0 006 8c0 7-3 9-3 9h18s-3-2-3-9M13.73 21a2 2 0 01-3.46 0"/></svg></div>
            <p>Tidak ada notifikasi saat ini.</p>
        </div>
    @endif
</div>
@endsection
