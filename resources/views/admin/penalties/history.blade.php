@extends('layouts.app')
@section('title', 'Riwayat Penalty — ' . $user->name)

@section('content')
<div class="main-header">
    <div>
        <h2>Riwayat Penalty</h2>
        <div class="breadcrumb">{{ $user->name }} — Total: {{ $user->penalty_points >= 0 ? '+' : '' }}{{ $user->penalty_points }} poin</div>
    </div>
    <a href="{{ route('admin.penalties.index') }}" class="btn btn-secondary btn-sm">← Kembali</a>
</div>

<div class="card">
    @if($logs->count() > 0)
    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>Tanggal</th>
                    <th>Event</th>
                    <th>Poin</th>
                    <th>Jadwal</th>
                    <th>Keterangan</th>
                </tr>
            </thead>
            <tbody>
                @foreach($logs as $log)
                    <tr>
                        <td style="font-size:0.8rem">{{ $log->created_at->format('d/m/Y H:i') }}</td>
                        <td>
                            @switch($log->event_type)
                                @case('attendance_ontime')
                                    <span class="badge badge-success">Hadir</span>
                                    @break
                                @case('attendance_late')
                                    <span class="badge badge-warning">Terlambat</span>
                                    @break
                                @case('no_show')
                                    <span class="badge badge-danger">No-Show</span>
                                    @break
                                @case('swap_expired')
                                    <span class="badge badge-danger">Swap Expired</span>
                                    @break
                            @endswitch
                        </td>
                        <td>
                            <strong style="color:{{ $log->points >= 0 ? 'var(--clr-success)' : 'var(--clr-danger)' }}">
                                {{ $log->points >= 0 ? '+' : '' }}{{ $log->points }}
                            </strong>
                        </td>
                        <td style="font-size:0.8rem">
                            @if($log->schedule)
                                {{ $log->schedule->prayerType->name ?? '-' }} — {{ $log->schedule->date?->format('d/m') }}
                            @else
                                —
                            @endif
                        </td>
                        <td style="font-size:0.8rem;color:var(--clr-text-muted)">{{ $log->description }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div style="padding:16px">
        {{ $logs->links() }}
    </div>
    @else
    <div class="empty-state">
        <p>Belum ada riwayat penalty untuk imam ini.</p>
    </div>
    @endif
</div>
@endsection
