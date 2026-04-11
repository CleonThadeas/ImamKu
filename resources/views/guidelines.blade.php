@extends('layouts.app')

@section('title', 'Peraturan & Ketentuan')

@section('content')
<div class="main-header">
    <div>
        <h2>Peraturan & Ketentuan Sistem</h2>
        <div class="breadcrumb">Panduan operasional harian mengenai absensi, denda (penalty), dan pembayaran (fee) di ImamKu.</div>
    </div>
</div>

@php
    $season = \App\Models\RamadanSeason::where('is_active', true)->first();
    $config = $season ? \App\Models\MosqueConfig::where('season_id', $season->id)->first() : null;
    $minutesBefore = $config ? $config->attendance_window_minutes : 30;
    $minutesAfter = $config ? $config->attendance_window_after_minutes : 30;
@endphp
<div class="card" style="margin-bottom: 24px;">
    <div class="card-header">
        <h3 class="card-title">1. Ketentuan Waktu Absensi (Kehadiran)</h3>
    </div>
    <div style="font-size: 0.9rem; line-height: 1.6; color: var(--clr-text);">
        <ul>
            <li class="mb-2"><strong>Waktu Terbuka:</strong> Absensi baru dapat dilakukan mulai dari <strong>{{ $minutesBefore }} menit sebelum</strong> waktu sholat berjalan. Selang waktu ini diatur secara dinamis oleh sistem.</li>
            <li class="mb-2"><strong>Waktu Ditutup / Kedaluwarsa:</strong> Waktu absen akan otomatis ditutup <strong>{{ $minutesAfter }} menit setelah</strong> jadwal sholat terlewati. Apabila slot waktu toleransi ini terlewati, absen akan gagal dan ditandai <em>Expired</em> (Tidak Hadir).</li>
            <li class="mb-2"><strong>Syarat Utama:</strong> Imam wajib berada di dalam radius koordinat Masjid (zona toleransi GPS yang diatur admin). Jika di luar batas, sistem akan menolak kehadiran dan Anda tidak bisa absen. Pastikan Izin Lokasi di browser Anda dalam keadaan aktif.</li>
        </ul>
    </div>
</div>

<div class="card" style="margin-bottom: 24px;">
    <div class="card-header">
        <h3 class="card-title">2. Ketentuan Swap (Tukar Jadwal)</h3>
    </div>
    <div style="font-size: 0.9rem; line-height: 1.6; color: var(--clr-text);">
        <ul>
            <li class="mb-2">Bila Imam berhalangan, Imam diwajibkan melakukan <strong>Pengajuan Swap</strong> selambat-lambatnya sebelum waktu jadwal tersebut mendekati masa aktif.</li>
            <li class="mb-2">Pilih nama Imam pengganti yang tersedia di kolom form pengajuan. Sistem akan memberi notifikasi konfirmasi kepada Imam bersangkutan.</li>
            <li class="mb-2"><strong>Catatan Mutlak:</strong> Selama Imam pengganti <u>BELUM</u> menyetujui penugasan tukar tersebut, maka jadwal tersebut <strong>masih menjadi tanggung jawab penuh Imam asli.</strong></li>
            <li class="mb-2">Jika Imam pengganti mengabaikan persetujuan hingga batas waktu absensi kedaluwarsa, dan Imam asli tidak hadir, maka denda penalti akan tetap dikenakan kepada <strong>Imam Asli</strong>.</li>
        </ul>
    </div>
</div>

<div class="card" style="margin-bottom: 24px;">
    <div class="card-header">
        <h3 class="card-title">3. Ketentuan Poin Penalti & Suspend Akun</h3>
    </div>
    <div style="font-size: 0.9rem; line-height: 1.6; color: var(--clr-text);">
        <ul>
            <li class="mb-2"><strong>Pengurangan Poin (Denda No-show):</strong> Imam yang tidak melakukan absen tanpa mendelegasikan jadwalnya hingga batas waktu (Expired) akan terekam oleh <em>Cron Job</em> Otomatis dan dipotong sebanyak <strong>-20 Poin.</strong></li>
            <li class="mb-2"><strong>Denda Keterlambatan:</strong> Jika Imam absen (Hadir) lebih dari waktu sholat yang ditentukan, maka Imam terkena penalti keterlambatan sebesar <strong>-5 Poin.</strong></li>
            <li class="mb-2"><strong>Denda Swap Kedaluwarsa:</strong> Pengajuan tukar jadwal yang tidak disetujui siapapun dan dibiarkan kedaluwarsa mendapat denda <strong>-10 Poin.</strong></li>
            <li class="mb-2"><strong>Syarat Hadir Tepat Waktu:</strong> Apabila Imam hadir pada rentang waktu yang sesuai, Imam memenangkan Ganjaran <strong>+10 Poin.</strong></li>
            <li class="mb-2"><strong>Batas Aman Penalti (Sistem Kunci):</strong> Apabila akumulasi poin menyentuh rentang <strong><= -30 Poin</strong>, sistem akan membekukan akun Imam (Suspend). Seluruh jadwal Anda yang akan datang otomatis <strong>dicabut</strong> oleh sistem.</li>
            <li class="mb-2"><strong>Pengaktifan Kembali:</strong> Akun hanya bisa diaktifkan kembali jika Administrator secara manual mengangkat Suspensi tersebut melalui halaman Penalti. Poin kemudian akan direset kembali menjadi 0.</li>
        </ul>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">4. Ketentuan Honor (Fee) Imam</h3>
    </div>
    <div style="font-size: 0.9rem; line-height: 1.6; color: var(--clr-text);">
        <ul>
            <li class="mb-2">Sistem penggajian dihitung dengan metode <em>Pay per Sholat</em>. Nominal sudah diatur otomatis oleh sistem berdasarkan jenis hari (Workday/Weekend) dan Waktu Salat secara spesifik.</li>
            <li class="mb-2"><u><strong>Status Honor (Penting):</strong></u> Nominal dan validitas Honor hanya akan dibayarkan <u>apabila Administrator menyalakan Status Honor (Mode Fee)</u>. Jika dinonaktifkan dari pusat, maka semua perhitungan otomatis dianggap nol.</li>
            <li class="mb-2"><strong>Syarat Pembayaran:</strong> Honor hanya akan tercatat untuk setiap presensi dengan status <strong>Hadir (Present)</strong>.</li>
            <li class="mb-2"><strong>Approval Admin:</strong> Meskipun telah Hadir di lokasi, honor hanya akan masuk ke <em>Laporan Pendapatan (Fee Berhasil)</em> apabila Administrator sudah menyetujui log kehadiran bersangkutan (Kecuali Mode <em>Auto-Approve</em> dinyalakan oleh Pusat).</li>
            <li class="mb-2">Sistem tidak akan mencatat Fee sepersenpun apabila status Absensi Anda adalah <em>Expired</em>, <em>Absent</em>, atau Ditolak (*Rejected*) oleh Admin.</li>
        </ul>
    </div>
</div>
@endsection
