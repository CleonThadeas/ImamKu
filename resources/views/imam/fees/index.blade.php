@extends('layouts.app')
@section('title', 'Laporan Pendapatan (Fee)')

@inject('feeService', 'App\Services\FeeService')

@section('content')
<div class="main-header">
    <div>
        <h2 style="display:flex;align-items:center;gap:10px"><svg width="28" height="28" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="color:var(--clr-accent)"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 000 7h5a3.5 3.5 0 010 7H6"/></svg> Laporan Pendapatan (Fee)</h2>
        <div class="breadcrumb">Rincian transparan pendapatan insentif Anda sepanjang bulan Ramadan</div>
    </div>
</div>

@if(!$season)
<div class="empty-state card">
    <div class="empty-icon"><svg width="48" height="48" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" style="color:var(--clr-gold); opacity:0.8;"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg></div>
    <h3>Season Belum Tersedia</h3>
    <p>Belum ada Season Ramadan yang diaktifkan oleh admin. Laporan insentif akan muncul di sini setelah season dimulai.</p>
</div>
@else
<div class="stats-grid" style="grid-template-columns: 2fr 1fr 1fr;">
    <div class="stat-card" style="border: 1px solid var(--clr-accent);">
        <div class="stat-icon gold"><svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 1v22M17 5H9.5a3.5 3.5 0 000 7h5a3.5 3.5 0 010 7H6"/></svg></div>
        <div>
            <div class="stat-label" style="font-size:0.8rem; font-weight:600">Total Akumulasi Fee Terkumpul</div>
            <div class="stat-value" style="font-size:2rem; color: var(--clr-accent)">Rp {{ number_format($report['total'] ?? 0, 0, ',', '.') }}</div>
            <div class="stat-label mt-1">Estimasi pendapatan ditarik dari absensi "Selesai" (Approved).</div>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon green"><svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg></div>
        <div>
            <div class="stat-value">{{ $schedules->total() }}</div>
            <div class="stat-label">Jadwal Tuntas</div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon blue"><svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg></div>
        <div>
            <div class="stat-value" style="font-size:1.1rem; padding-top:4px">{{ $report['mode'] === 'per_schedule' ? 'Per Waktu Sholat' : ($report['mode'] === 'per_day' ? 'Per Hari/Pukul Rata' : 'Tidak Ditentukan') }}</div>
            <div class="stat-label">Metode Perhitungan Aktif</div>
        </div>
    </div>
</div>

<div class="card mb-4" style="background: rgba(16, 185, 129, 0.05); border-color: rgba(16, 185, 129, 0.2);">
    <div style="display:flex; gap:15px; align-items:center;">
        <span style="font-size:1.5rem; color:var(--clr-accent)"><svg width="32" height="32" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 1v22M17 5H9.5a3.5 3.5 0 000 7h5a3.5 3.5 0 010 7H6"/></svg></span>
        <div>
            <strong>Transparansi Pembayaran</strong><br>
            <span class="text-muted" style="font-size:0.85rem">Nilai yang tercantum di sini otomatis dikalkulasi berdasarkan harga insentif terbaru yang diresmikan oleh pihak Masjid untuk Season <strong>{{ $season->name }}</strong>. Angka ini secara sah menjadi hak Anda.</span>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <div class="card-title">Daftar Rincian Riwayat Jadwal & Angka Fee</div>
    </div>
    @if($schedules->isEmpty())
        <div class="empty-state">
            <div class="empty-icon"><svg width="48" height="48" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" style="color:var(--clr-text-muted); opacity:0.6;"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><path d="M14 2v6h6M16 13H8M16 17H8M10 9H8"/></svg></div>
            <h3>Belum Ada Jadwal Yang Selesai</h3>
            <p>Jadwal yang telah Anda isi dan divalidasi oleh admin akan terekapitulasi di sini.</p>
        </div>
    @else
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th style="width:50px">No</th>
                        <th>Tanggal</th>
                        <th>Waktu Sholat</th>
                        <th>Status Validasi</th>
                        <th style="text-align:right">Rincian Nominal (IDR)</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($schedules as $index => $schedule)
                        <tr>
                            <td>{{ $schedules->firstItem() + $index }}</td>
                            <td>
                                <strong>{{ $schedule->date->format('d M Y') }}</strong>
                            </td>
                            <td>
                                <span class="badge badge-info">{{ $schedule->prayerType->name }}</span>
                            </td>
                            <td>
                                <span class="badge badge-success">Approved / Selesai</span>
                                <div style="font-size:0.65rem; color:var(--clr-text-muted); margin-top:4px">Catatan: {{ $schedule->attendance->notes ?? '-' }}</div>
                            </td>
                            <td style="text-align:right; font-weight:700; color:var(--clr-accent);">
                                Rp {{ number_format($feeService->calculateScheduleFee($schedule), 0, ',', '.') }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="pagination-wrapper">
            {{ $schedules->links('pagination::bootstrap-4') }}
        </div>
    @endif
</div>
@endif
@endsection
