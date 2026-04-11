@extends('layouts.app')
@section('title', 'Penalty & Ranking Imam')

@section('content')
<div class="main-header">
    <div>
        <h2>Penalty & Ranking Imam</h2>
        <div class="breadcrumb">Monitoring performa dan sistem penalti imam</div>
    </div>
</div>

<!-- Penalty Config Summary -->
<div class="card" style="margin-bottom:20px;padding:14px">
    <div style="display:flex;flex-wrap:wrap;gap:12px;align-items:center">
        <span style="font-size:0.75rem;color:var(--clr-text-muted);font-weight:600">ATURAN POIN:</span>
        <span class="badge badge-success" style="font-size:0.65rem">Hadir: +{{ $penaltyConfig['attendance_ontime'] }}</span>
        <span class="badge badge-warning" style="font-size:0.65rem">Terlambat: {{ $penaltyConfig['attendance_late'] }}</span>
        <span class="badge badge-danger" style="font-size:0.65rem">No-Show: {{ $penaltyConfig['no_show'] }}</span>
        <span class="badge badge-danger" style="font-size:0.65rem">Swap Expired: {{ $penaltyConfig['swap_expired'] }}</span>
        <span class="badge badge-info" style="font-size:0.65rem">Restriction: ≤ {{ $penaltyConfig['restriction_threshold'] }} poin</span>
    </div>
</div>

<!-- Imam Ranking Table -->
<div class="card">
    <div class="card-header">
        <h3 class="card-title" style="display:flex;align-items:center;gap:8px">
            <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87L18.18 22 12 18.27 5.82 22 7 14.14 2 9.27l6.91-1.01z"/></svg>
            Ranking Imam
        </h3>
    </div>

    @if($imams->count() > 0)
    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Imam</th>
                    <th>Total Poin</th>
                    <th>Hadir</th>
                    <th>Terlambat</th>
                    <th>No-Show</th>
                    <th>Swap Exp.</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($imams as $i => $imam)
                    @php
                        $bd = $imam->penalty_breakdown;
                        $ontime = $bd->get('attendance_ontime');
                        $late = $bd->get('attendance_late');
                        $noshow = $bd->get('no_show');
                        $swapExp = $bd->get('swap_expired');
                    @endphp
                    <tr style="{{ $imam->is_restricted ? 'background:rgba(255,0,0,0.05)' : '' }}">
                        <td><strong>{{ $i + 1 }}</strong></td>
                        <td>
                            <strong>{{ $imam->name }}</strong>
                            @if(!$imam->is_active)
                                <span class="badge badge-warning" style="font-size:0.55rem">Nonaktif</span>
                            @endif
                        </td>
                        <td>
                            <strong style="color:{{ $imam->penalty_points >= 0 ? 'var(--clr-success)' : 'var(--clr-danger)' }}">
                                {{ $imam->penalty_points >= 0 ? '+' : '' }}{{ $imam->penalty_points }}
                            </strong>
                        </td>
                        <td>{{ $ontime?->count ?? 0 }}</td>
                        <td>{{ $late?->count ?? 0 }}</td>
                        <td>{{ $noshow?->count ?? 0 }}</td>
                        <td>{{ $swapExp?->count ?? 0 }}</td>
                        <td>
                            @if($imam->is_restricted)
                                <span class="badge badge-danger">Dibatasi</span>
                            @else
                                <span class="badge badge-success">Aktif</span>
                            @endif
                        </td>
                        <td>
                            <div style="display:flex;gap:4px">
                                <a href="{{ route('admin.penalties.history', $imam) }}" class="btn btn-secondary btn-xs">Riwayat</a>
                                @if($imam->is_restricted)
                                    <form method="POST" action="{{ route('admin.penalties.lift', $imam) }}" style="margin:0" onsubmit="return confirm('Angkat pembatasan untuk {{ $imam->name }}?')">
                                        @csrf
                                        <button type="submit" class="btn btn-success btn-xs">Angkat</button>
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
    <div class="empty-state">
        <p>Belum ada data imam.</p>
    </div>
    @endif
</div>
@endsection
