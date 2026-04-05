@extends('layouts.app')
@section('title', 'Laporan Fee')

@section('content')
<div class="main-header">
    <div>
        <h2 style="display:flex;align-items:center;gap:10px"><svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><path d="M14 2v6h6M16 13H8M16 17H8M10 9H8"/></svg> Laporan Fee Imam</h2>
        <div class="breadcrumb">{{ $season ? $season->name : 'Tidak ada season aktif' }}</div>
    </div>
</div>

@if($season && $summary->count() > 0)
    <div class="stats-grid" style="margin-bottom:24px">
        @foreach($summary as $item)
            <div class="stat-card">
                <div class="stat-icon gold"><svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 1v22M17 5H9.5a3.5 3.5 0 000 7h5a3.5 3.5 0 010 7H6"/></svg></div>
                <div>
                    <div class="stat-value">Rp {{ number_format($item['report']['total'], 0, ',', '.') }}</div>
                    <div class="stat-label">{{ $item['user']->name }}</div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="card">
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>Imam</th>
                        <th>Mode</th>
                        <th>Detail</th>
                        <th>Total Fee</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($summary as $item)
                        <tr>
                            <td><strong>{{ $item['user']->name }}</strong></td>
                            <td><span class="badge badge-info">{{ $item['report']['mode'] }}</span></td>
                            <td>
                                @if($item['report']['mode'] === 'per_schedule')
                                    @foreach($item['report']['details'] as $prayer => $detail)
                                        <div style="font-size:0.8rem">{{ $prayer }}: {{ $detail['count'] }}x × Rp {{ number_format($detail['fee_per'], 0, ',', '.') }} = Rp {{ number_format($detail['subtotal'], 0, ',', '.') }}</div>
                                    @endforeach
                                @elseif($item['report']['mode'] === 'per_day')
                                    <div style="font-size:0.8rem">{{ $item['report']['details']['days'] }} hari × Rp {{ number_format($item['report']['details']['daily_rate'], 0, ',', '.') }}</div>
                                @endif
                            </td>
                            <td><strong style="color:var(--clr-accent)">Rp {{ number_format($item['report']['total'], 0, ',', '.') }}</strong></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@else
    <div class="card">
        <div class="empty-state">
            <div class="empty-icon"><svg width="48" height="48" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" style="opacity:0.5"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><path d="M14 2v6h6M16 13H8M16 17H8M10 9H8"/></svg></div>
            <p>Belum ada data fee untuk ditampilkan.</p>
        </div>
    </div>
@endif
@endsection
