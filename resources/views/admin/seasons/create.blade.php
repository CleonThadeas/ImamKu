@extends('layouts.app')
@section('title', 'Tambah Season')

@section('content')
<div class="main-header">
    <div>
        <h2 style="display:flex;align-items:center;gap:10px"><svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M21 12.79A9 9 0 1111.21 3 7 7 0 0021 12.79z"/></svg> Tambah Season Ramadan</h2>
        <div class="breadcrumb"><a href="{{ route('admin.seasons.index') }}" style="color:var(--clr-accent);text-decoration:none">Season</a> / Tambah</div>
    </div>
</div>

<div class="card" style="max-width:600px">
    <form method="POST" action="{{ route('admin.seasons.store') }}">
        @csrf
        <div class="form-group">
            <label class="form-label">Nama Season</label>
            <input type="text" name="name" class="form-input" value="{{ old('name') }}" placeholder="Ramadan 1447H" required>
        </div>
        <div class="form-group">
            <label class="form-label">Tahun Hijriah</label>
            <input type="number" name="hijri_year" class="form-input" value="{{ old('hijri_year', 1447) }}" min="1400" max="1500" required>
        </div>
        <div class="grid-2">
            <div class="form-group">
                <label class="form-label">Tanggal Mulai</label>
                <input type="date" name="start_date" class="form-input" value="{{ old('start_date') }}" required>
            </div>
            <div class="form-group">
                <label class="form-label">Tanggal Selesai</label>
                <input type="date" name="end_date" class="form-input" value="{{ old('end_date') }}" required>
            </div>
        </div>
        <div class="form-group">
            <div class="form-checkbox-wrapper">
                <input type="hidden" name="is_active" value="0">
                <input type="checkbox" name="is_active" value="1" class="form-checkbox" {{ old('is_active') ? 'checked' : 'checked' }}>
                <label class="form-label" style="margin:0">Set sebagai season aktif</label>
            </div>
        </div>
        <div style="display:flex;gap:12px">
            <button type="submit" class="btn btn-primary" style="display:flex;align-items:center;gap:8px"><svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M19 21H5a2 2 0 01-2-2V5a2 2 0 012-2h11l5 5v11a2 2 0 01-2 2z"/><path d="M17 21v-8H7v8M7 3v5h8"/></svg> Simpan</button>
            <a href="{{ route('admin.seasons.index') }}" class="btn btn-secondary">Batal</a>
        </div>
    </form>
</div>
@endsection
