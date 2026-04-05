@extends('layouts.app')
@section('title', 'Swap Jadwal')

@section('content')
<div class="main-header">
    <div>
        <h2 style="display:flex;align-items:center;gap:10px"><svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M16 3h5v5M4 20L21 3M21 16v5h-5M15 21l6-6M9 8l-6 6M4 14v5h5M4 19l6-6M3 3l6 6"/></svg> Swap Jadwal</h2>
        <div class="breadcrumb">Riwayat dan siaran pertukaran jadwal</div>
    </div>
    <a href="{{ route('imam.swaps.create') }}" class="btn btn-primary">+ Siarkan Swap Baru</a>
</div>

<!-- Available Broadcast Swaps -->
@if($availableSwaps->count() > 0)
<div class="card" style="margin-bottom:24px">
    <div class="card-header">
        <h3 class="card-title" style="display:flex;align-items:center;gap:8px"><svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 2a3 3 0 00-3 3v7a3 3 0 006 0V5a3 3 0 00-3-3z"/><path d="M19 10v2a7 7 0 01-14 0v-2M12 19v4M8 23h8"/></svg> Broadcast Swap Tersedia</h3>
    </div>
    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>Peminta</th>
                    <th>Jadwal yang Ditawarkan</th>
                    <th>Aksi (Tukar Dengan)</th>
                </tr>
            </thead>
            <tbody>
                @foreach($availableSwaps as $swap)
                    <tr>
                        <td><strong>{{ $swap->requester->name }}</strong></td>
                        <td>
                            <span class="badge badge-info">{{ $swap->schedule->date?->format('d/m') }} — {{ $swap->schedule->prayerType?->name }}</span>
                        </td>
                        <td>
                            <form method="POST" action="{{ route('imam.swaps.respond', $swap) }}" style="display:flex;gap:6px;align-items:center">
                                @csrf
                                <input type="hidden" name="action" value="accept">
                                <select name="target_schedule_id" class="form-select form-select-sm" style="font-size:0.75rem;padding:4px 8px;width:180px" required>
                                    <option value="">-- Pilih Jadwal Anda --</option>
                                    @foreach($mySchedules as $s)
                                        <option value="{{ $s->id }}">{{ $s->date->format('d/m') }} - {{ $s->prayerType->name }}</option>
                                    @endforeach
                                </select>
                                <button type="submit" class="btn btn-success btn-xs" onclick="return confirm('Anda yakin ingin mengambil jadwal ini dan memberikan jadwal pilihan Anda?')" style="display:flex;align-items:center;gap:4px"><svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M20 6L9 17l-5-5"/></svg> Tukar</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif

<!-- My Swap Requests -->
<div class="card">
    <div class="card-header">
        <h3 class="card-title" style="display:flex;align-items:center;gap:8px"><svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M7 17l9.2-9.2M17 17V7H7"/></svg> Riwayat Siaran Saya</h3>
    </div>
    @if($mySwapRequests->count() > 0)
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>Jadwal Saya</th>
                        <th>Status</th>
                        <th>Penukar & Jadwal</th>
                        <th>Aksi / Tanggal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($mySwapRequests as $swap)
                        <tr>
                            <td>
                                <span class="badge badge-gold">{{ $swap->schedule->date?->format('d/m') }} — {{ $swap->schedule->prayerType?->name }}</span>
                            </td>
                            <td>
                                @if($swap->status === 'accepted')
                                    <span class="badge badge-success">Selesai Berhasil</span>
                                @elseif($swap->status === 'rejected')
                                    <span class="badge badge-danger">Dibatalkan</span>
                                @elseif($swap->status === 'pending')
                                    <span class="badge badge-warning">Menunggu (Pending)</span>
                                @else
                                    <span class="badge badge-neutral">{{ ucfirst($swap->status) }}</span>
                                @endif
                            </td>
                            <td>
                                @if($swap->targetSchedule)
                                    <strong>{{ $swap->targetSchedule->user?->name ?? '-' }}</strong> memberikan:<br>
                                    <span class="badge badge-info">{{ $swap->targetSchedule->date?->format('d/m') }} — {{ $swap->targetSchedule->prayerType?->name }}</span>
                                @else
                                    <span class="empty-slot" style="font-size:0.7rem;color:#888">— Belum Diambil —</span>
                                @endif
                            </td>
                            <td>
                                @if($swap->status === 'pending')
                                    <form method="POST" action="{{ route('imam.swaps.respond', $swap) }}">
                                        @csrf
                                        <input type="hidden" name="action" value="cancel">
                                        <button class="btn btn-danger btn-xs" onclick="return confirm('Batal menyiarkan swap ini?')">Batalkan</button>
                                    </form>
                                @else
                                    <span style="color:var(--clr-text-muted);font-size:0.8rem">{{ $swap->updated_at->format('d/m/Y H:i') }}</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="pagination-wrapper">{{ $mySwapRequests->links() }}</div>
    @else
        <div class="empty-state">
            <div class="empty-icon"><svg width="48" height="48" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" style="opacity:0.5"><path d="M16 3h5v5M4 20L21 3M21 16v5h-5M15 21l6-6M9 8l-6 6M4 14v5h5M4 19l6-6M3 3l6 6"/></svg></div>
            <p>Belum ada riwayat swap.</p>
        </div>
    @endif
</div>
@endsection
