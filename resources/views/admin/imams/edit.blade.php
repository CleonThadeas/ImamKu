@extends('layouts.app')
@section('title', 'Edit Imam')

@section('content')
<div class="main-header">
    <div>
        <h2 style="display:flex;align-items:center;gap:10px"><svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/></svg> Edit Imam</h2>
        <div class="breadcrumb"><a href="{{ route('admin.imams.index') }}" style="color:var(--clr-accent);text-decoration:none">Data Imam</a> / Edit</div>
    </div>
</div>

<div class="card" style="max-width:600px">
    <form method="POST" action="{{ route('admin.imams.update', $imam) }}">
        @csrf @method('PUT')
        <div class="form-group">
            <label class="form-label">Nama Lengkap</label>
            <input type="text" name="name" class="form-input" style="background-color: #111827 !important; color: #E5E7EB !important; color-scheme: dark !important;" value="{{ old('name', $imam->name) }}" required>
        </div>
        <div class="form-group">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-input" style="background-color: #111827 !important; color: #E5E7EB !important; color-scheme: dark !important;" value="{{ old('email', $imam->email) }}" placeholder="contoh@gmail.com" required>
            <small style="color:var(--clr-text-muted);font-size:0.7rem">Hanya @gmail.com atau @yahoo.com</small>
        </div>
        <div class="form-group">
            <label class="form-label">No. HP (WhatsApp)</label>
            <input type="tel" name="phone" class="form-input" style="background-color: #111827 !important; color: #E5E7EB !important; color-scheme: dark !important;" value="{{ old('phone', $imam->phone) }}" placeholder="628xxxxxxxxxx" pattern="[0-9]*" inputmode="numeric" minlength="10" maxlength="15">
            <small style="color:var(--clr-text-muted);font-size:0.7rem">Hanya angka, 10-15 digit</small>
        </div>
        <div class="form-group">
            <label class="form-label">Password Baru (kosongkan jika tidak berubah)</label>
            <input type="password" name="password" class="form-input" style="background-color: #111827 !important; color: #E5E7EB !important; color-scheme: dark !important;" minlength="8">
        </div>
        <div class="form-group">
            <label class="form-label">Konfirmasi Password Baru</label>
            <input type="password" name="password_confirmation" class="form-input" style="background-color: #111827 !important; color: #E5E7EB !important; color-scheme: dark !important;">
        </div>
        <div class="form-group">
            <div class="form-checkbox-wrapper">
                <input type="hidden" name="is_active" value="0">
                <input type="checkbox" name="is_active" value="1" class="form-checkbox" {{ $imam->is_active ? 'checked' : '' }}>
                <label class="form-label" style="margin:0">Aktif</label>
            </div>
        </div>
        <div style="display:flex;gap:12px">
            <button type="submit" class="btn btn-primary" style="display:flex;align-items:center;gap:8px"><svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M19 21H5a2 2 0 01-2-2V5a2 2 0 012-2h11l5 5v11a2 2 0 01-2 2z"/><path d="M17 21v-8H7v8M7 3v5h8"/></svg> Update</button>
            <a href="{{ route('admin.imams.index') }}" class="btn btn-secondary">Batal</a>
        </div>
    </form>
</div>
@endsection
