@extends('layouts.app')
@section('title', 'Buat Swap')

@section('content')
<div class="main-header">
    <div>
        <h2 style="display:flex;align-items:center;gap:10px"><svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M16 3h5v5M4 20L21 3M21 16v5h-5M15 21l6-6M9 8l-6 6M4 14v5h5M4 19l6-6M3 3l6 6"/></svg> Buat Permintaan Swap</h2>
        <div class="breadcrumb"><a href="{{ route('imam.swaps.index') }}" style="color:var(--clr-accent);text-decoration:none">Swap</a> / Buat Baru</div>
    </div>
</div>

<div class="card" style="max-width:700px">
    @if($mySchedules->count() > 0)
        <form method="POST" action="{{ route('imam.swaps.store') }}">
            @csrf
            <div class="form-group">
                <label class="form-label">Jadwal Saya (yang ingin ditukar)</label>
                <select name="schedule_id" class="form-select" required>
                    <option value="">— Pilih Jadwal Saya —</option>
                    @foreach($mySchedules as $s)
                        <option value="{{ $s->id }}">
                            {{ $s->date->format('d/m/Y') }} ({{ $s->date->translatedFormat('l') }}) — {{ $s->prayerType->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="alert alert-info" style="margin:16px 0">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="vertical-align:middle;margin-right:4px"><path d="M12 2a3 3 0 00-3 3v7a3 3 0 006 0V5a3 3 0 00-3-3z"/><path d="M19 10v2a7 7 0 01-14 0v-2M12 19v4M8 23h8"/></svg> <strong>Info:</strong> Permintaan swap ini akan di-broadcast (disiarkan) ke semua imam. Imam lain yang berminat dapat mengambil tawaran ini dengan menukarkan jadwal mereka.
            </div>

            <div class="alert alert-warning" style="margin:16px 0">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="vertical-align:middle;margin-right:4px"><path d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0zM12 9v4M12 17h.01"/></svg> <strong>Perhatian:</strong>
                <ul style="margin:8px 0 0 20px;font-size:0.8rem">
                    <li>Tidak melanggar aturan penjadwalan berurutan apabila ditukar</li>
                    <li>Sistem mengatur bahwa swap maksimal {{ config('imamku.swap_min_hours', 2) }} jam sebelum waktu sholat</li>
                </ul>
            </div>

            <div style="display:flex;gap:12px">
                <button type="submit" class="btn btn-primary" style="display:flex;align-items:center;gap:8px"><svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M22 2L11 13M22 2l-7 20-4-9-9-4 20-7z"/></svg> Siarkan Permintaan Swap</button>
                <a href="{{ route('imam.swaps.index') }}" class="btn btn-secondary">Batal</a>
            </div>
        </form>
    @else
        <div class="empty-state">
            <div class="empty-icon"><svg width="48" height="48" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" style="opacity:0.5"><path d="M16 3h5v5M4 20L21 3M21 16v5h-5"/></svg></div>
            <p>Anda belum memiliki jadwal yang bisa ditanggal untuk saat ini.</p>
        </div>
    @endif
</div>
@endsection
