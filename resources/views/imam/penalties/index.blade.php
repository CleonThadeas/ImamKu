@extends('layouts.app')
@section('title', 'Poin Performa & Riwayat Penalti')

@section('content')
<div class="main-header">
    <h2>Poin & Performa Anda</h2>
    <div class="breadcrumb">Pusat Informasi Poin Imam</div>
</div>

<div class="stats-grid" style="margin-bottom:20px;">
    <div class="stat-card" style="border: {{ $user->is_restricted ? '2px solid var(--clr-danger)' : '1px solid var(--clr-border)' }}">
        <div class="stat-icon" style="background: {{ $user->is_restricted ? 'var(--clr-danger)' : 'var(--clr-accent)' }}"><svg width="24" height="24" fill="none" stroke="white" stroke-width="2" viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg></div>
        <div class="stat-info">
            <h3>Total Poin</h3>
            <div class="stat-value" style="color: {{ $user->penalty_points < 0 ? 'var(--clr-danger)' : 'var(--clr-success)' }}">{{ $user->penalty_points > 0 ? '+' : '' }}{{ $user->penalty_points }}</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon"><svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M12 8v4l3 3"/></svg></div>
        <div class="stat-info">
            <h3>Status Akun</h3>
            <div class="stat-value">
                @if($user->is_restricted)
                    <span style="color:var(--clr-danger); font-size:1.2rem;">Dibatasi (Restricted)</span>
                @else
                    <span style="color:var(--clr-success); font-size:1.2rem;">Aktif (Aman)</span>
                @endif
            </div>
        </div>
    </div>
</div>

@if($user->is_restricted)
    <div class="alert alert-danger" style="margin-bottom:20px;">
        <strong>Perhatian!</strong> Akun Anda saat ini sedang dibatasi karena skor poin Anda telah mencapai batas bawah peringatan (≤ -30 poin). Anda tidak diizinkan untuk mengajukan/menerima swap, atau diarahkan ke jadwal baru hingga admin melakukan pemutihan.
    </div>
@endif

<div class="card" style="margin-bottom:20px;">
    <div class="card-header">
        <h3 class="card-title">Ketentuan Sistem Poin</h3>
    </div>
    <div style="padding:15px; background:var(--clr-surface-light);">
        Sistem Poin dirancang untuk memastikan komitmen dan operasional masjid yang lancar. Berikut rinciannya:
        <ul style="margin-top:10px; margin-left:20px; line-height:1.6; color:var(--clr-text-muted);">
            <li><strong>+{{ $penaltyConfig['attendance_ontime'] }} poin</strong> bagi Imam yang hadir dan melakukan absensi tepat waktu sesuai jadwal (di dalam jendela absensi masjid).</li>
            <li><strong>{{ $penaltyConfig['attendance_late'] }} poin</strong> bagi Imam yang melakukan absensi terlambat (setelah waktu sholat masuk).</li>
            <li><strong>{{ $penaltyConfig['swap_expired'] }} poin</strong> bagi Imam yang melakukan Swap Request namun tidak ada imam lain yang mengambil slot tersebut hingga batas waktu kadaluarsa swap habis.</li>
            <li><strong>{{ $penaltyConfig['no_show'] }} poin</strong> bagi Imam yang alfa / tidak hadir tanpa ada pemberitahuan swap atau persetujuan sebelumnya (terdeteksi lewat dari 30 menit).</li>
        </ul>
        <br>
        <p style="font-size:0.85rem; color:var(--clr-danger);"><em>Batas bawah teguran (Restriction) adalah ketika total poin mencapai {{ $penaltyConfig['restriction_threshold'] }} poin.</em></p>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">Riwayat Poin & Penalti</h3>
    </div>
    @if($logs->count() > 0)
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>Tanggal Event</th>
                        <th>Riwayat</th>
                        <th>Perubahan Poin</th>
                        <th>Jadwal / Swap Ref</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($logs as $log)
                        <tr>
                            <td class="text-muted">{{ $log->created_at->translatedFormat('d M Y H:i') }}</td>
                            <td>
                                <strong>
                                    @if($log->event_type === 'attendance_ontime') Absen Tepat Waktu
                                    @elseif($log->event_type === 'attendance_late') Absen Terlambat
                                    @elseif($log->event_type === 'no_show') Tidak Hadir (Alpha)
                                    @elseif($log->event_type === 'swap_expired') Swap Expired
                                    @endif
                                </strong><br>
                                <span style="font-size:0.85rem; color:var(--clr-text-muted);">{{ $log->description }}</span>
                            </td>
                            <td>
                                <strong style="color: {{ $log->points < 0 ? 'var(--clr-danger)' : 'var(--clr-success)' }}">
                                    {{ $log->points > 0 ? '+' : '' }}{{ $log->points }}
                                </strong>
                            </td>
                            <td class="text-muted" style="font-size:0.85rem;">
                                @if($log->schedule)
                                    {{ $log->schedule->prayerType->name ?? 'Sholat' }} - {{ $log->schedule->date->format('d/m/Y') }}
                                @elseif($log->swapRequest)
                                    Swap #{{ $log->swapRequest->id }}
                                @else
                                    -
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div style="padding:15px; border-top:1px solid var(--clr-border);">
            {{ $logs->links() }}
        </div>
    @else
        <div class="empty-state">
            <p>Belum ada riwayat pergerakan poin.</p>
        </div>
    @endif
</div>
@endsection
