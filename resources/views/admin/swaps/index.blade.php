@extends('layouts.app')
@section('title', 'Monitoring Swap Jadwal')

@section('content')
<div class="main-header">
    <div>
        <h2 style="display:flex;align-items:center;gap:10px"><svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M16 3h5v5M4 20L21 3M21 16v5h-5M15 21l6-6M9 8l-6 6M4 14v5h5M4 19l6-6M3 3l6 6"/></svg> Monitoring Swap Jadwal</h2>
        <div class="breadcrumb">Admin — Pantau transaksi pertukaran jadwal antar Imam secara menyeluruh</div>
    </div>
</div>

<div class="card">
    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>Waktu Dibuat</th>
                    <th>Imam Pemohon</th>
                    <th>Jadwal Terdaftar</th>
                    <th>Imam Penerima & Jadwal Pengganti</th>
                    <th>Status Akhir</th>
                </tr>
            </thead>
            <tbody>
                @forelse($swaps as $swap)
                    <tr>
                        <td>
                            <strong>{{ $swap->created_at->format('d/m/Y') }}</strong><br>
                            <small class="text-muted">{{ $swap->created_at->format('H:i') }}</small>
                        </td>
                        <td>{{ $swap->requester->name ?? '-' }}</td>
                        <td>
                            <span class="badge badge-gold">{{ $swap->schedule->prayerType->name ?? '-' }}</span><br>
                            <small class="text-muted">{{ $swap->schedule->date ? \Carbon\Carbon::parse($swap->schedule->date)->format('d/m/Y') : '' }}</small>
                        </td>
                        <td>
                            @if($swap->targetSchedule)
                                <strong>{{ $swap->targetSchedule->user->name ?? '-' }}</strong><br>
                                <span class="badge badge-info">{{ $swap->targetSchedule->prayerType->name ?? '-' }}</span>
                                <small class="text-muted">{{ $swap->targetSchedule->date ? \Carbon\Carbon::parse($swap->targetSchedule->date)->format('d/m/Y') : '' }}</small>
                            @else
                                <span class="text-muted" style="font-size:0.8rem; font-style:italic">Tawaran belum diambil siapapun</span>
                            @endif
                        </td>
                        <td>
                            @if($swap->status === 'pending')
                                <span class="badge badge-warning">Menunggu</span>
                            @elseif($swap->status === 'accepted')
                                <span class="badge badge-success">Berhasil Ditukar</span>
                            @elseif($swap->status === 'rejected')
                                <span class="badge badge-danger">Dibatalkan Pemohon</span>
                            @elseif($swap->status === 'expired')
                                <span class="badge badge-neutral" style="background:#555; color:#fff">Kedaluwarsa (Waktu Terlewat)</span>
                            @else
                                <span class="badge badge-neutral">{{ ucfirst($swap->status) }}</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center" style="padding: 30px;">
                            <svg width="48" height="48" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" style="opacity:0.5"><path d="M16 3h5v5M4 20L21 3M21 16v5h-5"/></svg><br><br>
                            Belum ada riwayat permohonan Swap Jadwal oleh para Imam.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div style="padding: 15px;">
        {{ $swaps->links('pagination::bootstrap-5') }}
    </div>
</div>
@endsection
