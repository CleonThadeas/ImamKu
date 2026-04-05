@extends('layouts.app')
@section('title', 'Validasi Absensi Imam')

@section('content')
<div class="main-header">
    <div>
        <h2 style="display:flex;align-items:center;gap:10px"><svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M23 19a2 2 0 01-2 2H3a2 2 0 01-2-2V8a2 2 0 012-2h4l2-3h6l2 3h4a2 2 0 012 2v11z"/><circle cx="12" cy="13" r="4"/></svg> Validasi Absensi Imam</h2>
        <div class="breadcrumb">Admin — Manajemen Absen Kehadiran</div>
    </div>
</div>

@if(!$season)
    <div class="card">
        <div class="empty-state">
            <div class="empty-icon"><svg width="48" height="48" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" style="opacity:0.5"><path d="M21 12.79A9 9 0 1111.21 3 7 7 0 0021 12.79z"/></svg></div>
            <p>Silakan buat dan aktifkan Season Ramadan terlebih dahulu.</p>
            <a href="{{ route('admin.seasons.index') }}" class="btn btn-primary" style="margin-top: 10px;">Atur Season</a>
        </div>
    </div>
@else
    <div class="card mb-4" style="padding: 20px;">
        <h3 style="display:flex;align-items:center;gap:8px"><svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 00.33 1.82l.06.06a2 2 0 010 2.83 2 2 0 01-2.83 0l-.06-.06a1.65 1.65 0 00-1.82-.33 1.65 1.65 0 00-1 1.51V21a2 2 0 01-2 2 2 2 0 01-2-2v-.09A1.65 1.65 0 009 19.4a1.65 1.65 0 00-1.82.33l-.06.06a2 2 0 01-2.83 0 2 2 0 010-2.83l.06-.06A1.65 1.65 0 004.68 15a1.65 1.65 0 00-1.51-1H3a2 2 0 01-2-2 2 2 0 012-2h.09A1.65 1.65 0 004.6 9a1.65 1.65 0 00-.33-1.82l-.06-.06a2 2 0 010-2.83 2 2 0 012.83 0l.06.06A1.65 1.65 0 009 4.68a1.65 1.65 0 001-1.51V3a2 2 0 012-2 2 2 0 012 2v.09a1.65 1.65 0 001 1.51 1.65 1.65 0 001.82-.33l.06-.06a2 2 0 012.83 0 2 2 0 010 2.83l-.06.06a1.65 1.65 0 00-.33 1.82V9a1.65 1.65 0 001.51 1H21a2 2 0 012 2 2 2 0 01-2 2h-.09a1.65 1.65 0 00-1.51 1z"/></svg> Skema Persetujuan Absensi & Pencairan Fee</h3>
        
        @if($feeConfig && !$feeConfig->is_enabled)
            <div class="alert alert-warning" style="margin-bottom: 15px; display:flex; align-items:flex-start; gap:10px;">
                <span style="font-size:1.5em; line-height:1;"><svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="color:var(--clr-warning)"><path d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0zM12 9v4M12 17h.01"/></svg></span>
                <div>
                    <strong>Perhatian:</strong> Sistem Fee Manajemen Keuangan saat ini <strong style="color:var(--clr-danger)">NONAKTIF</strong>.<br>
                    Absensi yang disetujui (baik manual/otomatis) disini sifatnya hanya merekam kehadiran operasional dan <strong>tidak akan menambah saldo Fee apapun</strong> kepada Imam.
                </div>
            </div>
        @endif

        <p class="text-muted mb-3" style="font-size:0.9rem;">Tentukan bagaimana absensi kehadiran (beserta laporan fee-nya) akan divalidasi oleh sistem.</p>
    
    <form action="{{ route('admin.fee-configs.toggle-auto-approve') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label style="display:flex; align-items:baseline; gap:10px; cursor:pointer; margin-bottom:10px; padding:10px; border:1px solid var(--clr-border); border-radius:6px; background:var(--clr-surface-light);">
                <input type="radio" name="is_auto_approve_attendance" value="0" {{ !$feeConfig->is_auto_approve_attendance ? 'checked' : '' }} style="margin-top:3px;">
                <div>
                    <strong style="color:var(--clr-accent)">Validasi Manual (Rekomendasi)</strong><br>
                    <span class="text-muted" style="font-size:0.85rem;">Admin harus mengecek dan menyetujui foto bukti absensi secara manual di halaman ini barulah fee dicairkan ke dompet Imam.</span>
                </div>
            </label>
            <label style="display:flex; align-items:baseline; gap:10px; cursor:pointer; padding:10px; border:1px solid var(--clr-border); border-radius:6px; background:var(--clr-surface-light);">
                <input type="radio" name="is_auto_approve_attendance" value="1" {{ $feeConfig->is_auto_approve_attendance ? 'checked' : '' }} style="margin-top:3px;">
                <div>
                    <strong style="color:var(--clr-accent)">Setujui Otomatis (Robot Scheduler)</strong><br>
                    <span class="text-muted" style="font-size:0.85rem;">Sistem akan menyetujui absensi yang dikirim Imam secara otomatis dan mencairkan Fee-nya tepat <strong style="color:var(--clr-success)">30 menit setelah waktu sholat</strong>. Bukti foto tetap terarsip di sini.</span>
                </div>
            </label>
        </div>
        <button type="submit" class="btn btn-primary btn-sm">💾 Simpan Skema</button>
    </form>
</div>

<div class="card">
    @if(session('success'))
        <div class="alert alert-success" style="margin:20px 20px 0 20px;">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger" style="margin:20px 20px 0 20px;">{{ session('error') }}</div>
    @endif

    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>Waktu Absen</th>
                    <th>Imam</th>
                    <th>Jadwal Sholat</th>
                    <th>Bukti Hadir</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($attendances as $row)
                    <tr>
                        <td><strong>{{ $row->created_at->format('d/m/Y') }}</strong><br><small class="text-muted">{{ $row->created_at->format('H:i') }}</small></td>
                        <td>{{ $row->schedule->user->name ?? '-' }}</td>
                        <td>
                            <span class="badge badge-gold">{{ $row->schedule->prayerType->name ?? '-' }}</span><br>
                            <small class="text-muted">{{ \Carbon\Carbon::parse($row->schedule->date)->format('d/m/Y') }}</small>
                        </td>
                        <td>
                            @if($row->proof_path)
                                <a href="{{ Storage::url($row->proof_path) }}" target="_blank" class="btn btn-secondary btn-xs">Lihat Foto</a>
                            @else
                                <span class="text-muted" style="font-size:0.75rem; font-style:italic">Tidak ada foto</span>
                            @endif
                        </td>
                        <td>
                            @if($row->status === 'pending')
                                <span class="badge badge-warning">Menunggu</span>
                            @elseif($row->status === 'approved')
                                <span class="badge badge-success" style="background-color: var(--clr-success);">Disetujui</span>
                            @elseif($row->status === 'expired')
                                <span class="badge badge-danger">Kedaluwarsa (Terlewat 30m)</span>
                            @else
                                <span class="badge badge-danger" style="background-color: var(--clr-danger);">Ditolak</span>
                            @endif
                        </td>
                        <td>
                            @if($row->status === 'pending')
                                <div style="display:flex; gap:5px;">
                                    <form action="{{ route('admin.attendances.approve', $row->id) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="btn btn-primary btn-xs">✅ Setujui & Cairkan Fee</button>
                                    </form>
                                    <form action="{{ route('admin.attendances.reject', $row->id) }}" method="POST" onsubmit="return confirm('Tolak absensi ini?');">
                                        @csrf
                                        <button type="submit" class="btn btn-secondary btn-xs" style="background:var(--clr-danger);color:white;border-color:var(--clr-danger);">❌ Tolak</button>
                                    </form>
                                </div>
                            @elseif($row->status === 'expired')
                                <div style="display:flex; gap:5px; flex-direction:column;">
                                    <form action="{{ route('admin.attendances.approve', $row->id) }}" method="POST" style="width:100%">
                                        @csrf
                                        <button type="submit" class="btn btn-primary btn-xs" style="width:100%">✅ Tetap Anggap Hadir</button>
                                    </form>
                                    <form action="{{ route('admin.attendances.reject', $row->id) }}" method="POST" style="width:100%" onsubmit="return confirm('Konfirmasi imam ini tidak hadir?');">
                                        @csrf
                                        <input type="hidden" name="notes" value="Imam dikonfirmasi TIDAK hadir karena lewat batas waktu.">
                                        <button type="submit" class="btn btn-secondary btn-xs" style="background:var(--clr-danger);color:white;width:100%">❌ Konfirmasi Tdk Hadir</button>
                                    </form>
                                </div>
                            @else
                                <span class="text-muted" style="font-size:0.8rem; font-style:italic;">Sudah diproses</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center" style="padding: 20px;">Belum ada antrean absensi masuk.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div style="padding: 15px;">
        {{ $attendances->links('pagination::bootstrap-5') }}
    </div>
</div>
@endif
@endsection
