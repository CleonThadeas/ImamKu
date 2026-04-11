@extends('layouts.app')
@section('title', 'Pusat Export Data')

@section('content')
<div class="main-header">
    <div>
        <h2 style="display:flex;align-items:center;gap:10px"><svg width="28" height="28" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="color:var(--clr-accent)"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4M7 10l5 5 5-5M12 15V3"/></svg> Pusat Export Data</h2>
        <div class="breadcrumb">Pilih bentuk dan rentang data untuk diunduh (CSV)</div>
    </div>
</div>

<div class="card mb-4" style="background-color: rgba(3, 169, 244, 0.05); border-color: rgba(3, 169, 244, 0.2);">
    <div style="display:flex; gap:15px; align-items:center;">
        <span style="font-size:1.5rem; color:var(--clr-info)"><svg width="32" height="32" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 11l3 3L22 4"/><path d="M21 12v7a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h11"/></svg></span>
        <div>
            <strong>Panduan Format</strong><br>
            <span class="text-muted" style="font-size:0.85rem">Jika saat file dibuka di Excel tampilannya berantakan, gunakan fitur <strong>"Data -> Text to Columns"</strong> pada Microsoft Excel, lalu pisahkan dengan pembatas (Delimiter) berupa "Koma". Seluruh karakter telah mendukung standar UTF-8.</span>
        </div>
    </div>
</div>

<div class="grid-3">
    <!-- Export Imam -->
    <div class="card" style="padding:20px">
        <h3 style="margin-bottom:15px; border-bottom:1px solid var(--clr-border); padding-bottom:10px; display:flex; align-items:center; gap:8px"><svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="color:var(--clr-primary)"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2M9 11a4 4 0 100-8 4 4 0 000 8zM23 21v-2a4 4 0 00-3-3.87M16 3.13a4 4 0 010 7.75"/></svg> Daftar Imam Masjid</h3>
        <p class="text-muted mb-4" style="font-size:0.85rem; line-height:1.6">Unduh data profil lengkap seluruh imam yang terdaftar beserta status akun mereka (seperti Nama, Email, Nomor Telepon, dan Rekening).</p>
        <form action="{{ route('admin.exports.download') }}" method="POST" id="formExportImams">
            @csrf
            <input type="hidden" name="type" value="imams">
            <div class="form-group mb-3" style="display:flex; gap:10px;">
                <div style="flex:1">
                    <label class="form-label" style="font-size:0.7rem">Dari Tanggal (Daftar)</label>
                    <input type="date" name="start_date" class="form-control">
                </div>
                <div style="flex:1">
                    <label class="form-label" style="font-size:0.7rem">Sampai Tanggal</label>
                    <input type="date" name="end_date" class="form-control">
                </div>
            </div>
            <div class="form-group mb-4" style="margin-bottom:1.5rem">
                <label class="form-label" style="font-size:0.7rem">Status Aktif</label>
                <select name="is_active" class="form-select">
                    <option value="all">Semua Status</option>
                    <option value="1">Aktif</option>
                    <option value="0">Nonaktif</option>
                </select>
            </div>
        </form>
        <button type="submit" form="formExportImams" class="btn btn-primary" style="width:100%; justify-content:center; box-shadow: 0 4px 12px rgba(0,0,0,0.1)">
            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4M7 10l5 5 5-5M12 15V3"/></svg>
            Unduh Data Imam
        </button>
    </div>

    <!-- Export Jadwal -->
    <div class="card" style="padding:20px">
        <h3 style="margin-bottom:15px; border-bottom:1px solid var(--clr-border); padding-bottom:10px; display:flex; align-items:center; gap:8px"><svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="color:var(--clr-gold)"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><path d="M16 2v4M8 2v4M3 10h18"/></svg> Distribusi Jadwal</h3>
        <p class="text-muted mb-3" style="font-size:0.85rem; line-height:1.6">Unduh rekaman penugasan rotasi shift jadwal harian setiap Imam per Season.</p>
        <form action="{{ route('admin.exports.download') }}" method="POST" id="formExportSchedules">
            @csrf
            <input type="hidden" name="type" value="schedules">
            <div class="form-group mb-3">
                <label class="form-label" style="font-size:0.7rem">Filter Season Jadwal</label>
                <select name="season_id" class="form-select" required>
                    <option value="all">Semua Terekam (All Time)</option>
                    @foreach($seasons as $season)
                        <option value="{{ $season->id }}">{{ $season->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group mb-3" style="display:flex; gap:10px;">
                <div style="flex:1">
                    <label class="form-label" style="font-size:0.7rem">Dari Tanggal Sholat</label>
                    <input type="date" name="start_date" class="form-control">
                </div>
                <div style="flex:1">
                    <label class="form-label" style="font-size:0.7rem">Sampai Tanggal</label>
                    <input type="date" name="end_date" class="form-control">
                </div>
            </div>
            <div class="form-group mb-3">
                <label class="form-label" style="font-size:0.7rem">Filter Imam</label>
                <select name="imam_id" class="form-select">
                    <option value="all">Semua Imam</option>
                    @foreach($imams as $imam)
                        <option value="{{ $imam->id }}">{{ $imam->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group mb-4" style="margin-bottom:1.5rem">
                <label class="form-label" style="font-size:0.7rem">Status Penugasan</label>
                <select name="status_assign" class="form-select">
                    <option value="all">Semua Status</option>
                    <option value="terisi">Sudah Ada Imam (Terisi)</option>
                    <option value="kosong">Belum Ditugaskan (Kosong)</option>
                </select>
            </div>
        </form>
        <button type="submit" form="formExportSchedules" class="btn btn-primary" style="width:100%; justify-content:center; box-shadow: 0 4px 12px rgba(0,0,0,0.1)">
            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4M7 10l5 5 5-5M12 15V3"/></svg>
            Unduh Jadwal Terjadwal
        </button>
    </div>

    <!-- Export Absensi & Fee -->
    <div class="card" style="padding:20px">
        <h3 style="margin-bottom:15px; border-bottom:1px solid var(--clr-border); padding-bottom:10px; display:flex; align-items:center; gap:8px"><svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="color:var(--clr-success)"><path d="M12 1v22M17 5H9.5a3.5 3.5 0 000 7h5a3.5 3.5 0 010 7H6"/></svg> Rekap Absensi & Fee</h3>
        <p class="text-muted mb-3" style="font-size:0.85rem; line-height:1.6">Laporan gabungan presensi kehadiran lengkap dengan jejak status pencairan keuangannya (Fee Transaksi).</p>
        <form action="{{ route('admin.exports.download') }}" method="POST" id="formExportAttendances">
            @csrf
            <input type="hidden" name="type" value="attendances">
            <div class="form-group mb-3">
                <label class="form-label" style="font-size:0.7rem">Filter Transaksi Season</label>
                <select name="season_id" class="form-select" required>
                    <option value="all">Keseluruhan Season</option>
                    @foreach($seasons as $season)
                        <option value="{{ $season->id }}">{{ $season->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group mb-3" style="display:flex; gap:10px;">
                <div style="flex:1">
                    <label class="form-label" style="font-size:0.7rem">Dari Tanggal Sholat</label>
                    <input type="date" name="start_date" class="form-control">
                </div>
                <div style="flex:1">
                    <label class="form-label" style="font-size:0.7rem">Sampai Tanggal</label>
                    <input type="date" name="end_date" class="form-control">
                </div>
            </div>
            <div class="form-group mb-3">
                <label class="form-label" style="font-size:0.7rem">Filter Imam</label>
                <select name="imam_id" class="form-select">
                    <option value="all">Semua Imam</option>
                    @foreach($imams as $imam)
                        <option value="{{ $imam->id }}">{{ $imam->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group mb-4" style="margin-bottom:1.5rem; display:flex; gap:10px;">
                <div style="flex:1">
                    <label class="form-label" style="font-size:0.7rem">Status Kehadiran</label>
                    <select name="attendance_status" class="form-select">
                        <option value="all">Semua</option>
                        <option value="pending">Pending</option>
                        <option value="approved">Approved</option>
                        <option value="rejected">Rejected</option>
                        <option value="expired">Expired</option>
                    </select>
                </div>
                <div style="flex:1">
                    <label class="form-label" style="font-size:0.7rem">Status Pencairan Fee</label>
                    <select name="fee_status" class="form-select">
                        <option value="all">Semua</option>
                        <option value="cair">Cair (Selesai)</option>
                        <option value="pending">Belum Cair (Pending)</option>
                        <option value="batal">Batal (Rejected/Expired)</option>
                    </select>
                </div>
            </div>
        </form>
        <button type="submit" form="formExportAttendances" class="btn btn-primary" style="width:100%; justify-content:center; box-shadow: 0 4px 12px rgba(0,0,0,0.1)">
            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4M7 10l5 5 5-5M12 15V3"/></svg>
            Unduh Absen & Invoice Fee
        </button>
    </div>
</div>
@endsection
