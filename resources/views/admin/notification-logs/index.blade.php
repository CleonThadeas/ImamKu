@extends('layouts.app')
@section('title', 'Log Notifikasi')

@section('content')
<div class="main-header">
    <div>
        <h2 style="display:flex;align-items:center;gap:10px"><svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M18 8A6 6 0 006 8c0 7-3 9-3 9h18s-3-2-3-9M13.73 21a2 2 0 01-3.46 0"/></svg> Log Notifikasi</h2>
        <div class="breadcrumb">Riwayat pengiriman notifikasi</div>
    </div>
</div>

<div class="card" style="margin-bottom:20px;padding:16px">
    <form method="GET" style="display:flex;gap:12px;align-items:center;flex-wrap:wrap">
        <select name="channel" class="form-select" style="max-width:150px" onchange="this.form.submit()">
            <option value="">Semua Channel</option>
            <option value="mail" {{ request('channel') === 'mail' ? 'selected' : '' }}>Email</option>
            <option value="whatsapp" {{ request('channel') === 'whatsapp' ? 'selected' : '' }}>WhatsApp</option>
            <option value="database" {{ request('channel') === 'database' ? 'selected' : '' }}>Aplikasi (Web)</option>
        </select>
        <select name="status" class="form-select" style="max-width:150px" onchange="this.form.submit()">
            <option value="">Semua Status</option>
            <option value="sent" {{ request('status') === 'sent' ? 'selected' : '' }}>Sent</option>
            <option value="failed" {{ request('status') === 'failed' ? 'selected' : '' }}>Failed</option>
            <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
        </select>
    </form>
</div>

<div class="card">
    @if($logs->count() > 0)
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>Waktu</th>
                        <th>Imam</th>
                        <th>Channel</th>
                        <th>Tipe</th>
                        <th>Jadwal</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($logs as $log)
                        <tr>
                            <td style="font-size:0.8rem;color:var(--clr-text-muted)">{{ $log->created_at->format('d/m/Y H:i') }}</td>
                            <td>{{ $log->user->name ?? '-' }}</td>
                            <td>
                                @if(str_contains($log->channel, 'mail') || str_contains($log->channel, 'email'))
                                    <span class="badge badge-info" style="display:flex;align-items:center;gap:4px"><svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><path d="M22 6l-10 7L2 6"/></svg> Email</span>
                                @elseif(str_contains($log->channel, 'whatsapp'))
                                    <span class="badge" style="background-color:#25D366;color:white;display:flex;align-items:center;gap:4px"><svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M21 11.5a8.38 8.38 0 01-.9 3.8 8.5 8.5 0 01-7.6 4.7 8.38 8.38 0 01-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 01-.9-3.8 8.5 8.5 0 014.7-7.6 8.38 8.38 0 013.8-.9h.5a8.48 8.48 0 018 8v.5z"/></svg> WhatsApp</span>
                                @else
                                    <span class="badge badge-neutral" style="display:flex;align-items:center;gap:4px"><svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="2" y="3" width="20" height="14" rx="2" ry="2"/><path d="M8 21h8M12 17v4"/></svg> Aplikasi</span>
                                @endif
                            </td>
                            <td><span class="badge badge-neutral">{{ $log->type }}</span></td>
                            <td style="font-size:0.8rem">
                                @if($log->schedule)
                                    {{ $log->schedule->date?->format('d/m') }} — {{ $log->schedule->prayerType?->name }}
                                @else
                                    -
                                @endif
                            </td>
                            <td>
                                @if($log->status === 'sent')
                                    <span class="badge badge-success">Sent</span>
                                @elseif($log->status === 'failed')
                                    <span class="badge badge-danger" title="{{ $log->error_message }}">Failed</span>
                                @else
                                    <span class="badge badge-warning">Pending</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="pagination-wrapper">
            {{ $logs->withQueryString()->links() }}
        </div>
    @else
        <div class="empty-state">
            <div class="empty-icon"><svg width="48" height="48" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" style="opacity:0.5"><path d="M18 8A6 6 0 006 8c0 7-3 9-3 9h18s-3-2-3-9M13.73 21a2 2 0 01-3.46 0"/></svg></div>
            <p>Belum ada log notifikasi.</p>
        </div>
    @endif
</div>
@endsection
