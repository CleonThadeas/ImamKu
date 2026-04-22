@extends('layouts.app')
@section('title', 'Kirim Broadcast')

@section('content')
<div class="flex flex-col md:flex-row md:justify-between md:items-end gap-4 mb-8">
    <div>
        <h2 class="text-3xl font-extrabold tracking-tight text-on-surface mb-1">Komunikasi</h2>
        <p class="text-on-surface-variant text-sm font-medium">Kirim Broadcast dan Lihat Riwayat Notifikasi</p>
    </div>
</div>

<!-- TABS -->
<div class="flex items-baseline gap-2 border-b border-outline-variant/20 mb-8 pb-4" style="overflow-x:auto;">
    <a href="{{ route('admin.broadcast.index') }}" class="px-6 py-2.5 rounded-xl font-bold text-sm transition-all flex items-center gap-2 {{ request()->routeIs('admin.broadcast.*') ? 'bg-primary/20 text-primary border border-primary/30' : 'text-on-surface-variant hover:bg-surface-container hover:text-on-surface min-w-max' }}">
        <span class="material-symbols-outlined text-[18px]">campaign</span> Broadcast Pesan
    </a>
    <a href="{{ route('admin.notification-logs.index') }}" class="px-6 py-2.5 rounded-xl font-bold text-sm transition-all flex items-center gap-2 {{ request()->routeIs('admin.notification-logs.*') ? 'bg-primary/20 text-primary border border-primary/30' : 'text-on-surface-variant hover:bg-surface-container hover:text-on-surface min-w-max' }}">
        <span class="material-symbols-outlined text-[18px]">notifications_active</span> Log Notifikasi
    </a>
    <a href="{{ route('admin.notifications.index') }}" class="px-6 py-2.5 rounded-xl font-bold text-sm transition-all flex items-center gap-2 relative {{ request()->routeIs('admin.notifications.*') ? 'bg-primary/20 text-primary border border-primary/30' : 'text-on-surface-variant hover:bg-surface-container hover:text-on-surface min-w-max' }}">
        <span class="material-symbols-outlined text-[18px]">warning</span> Notifikasi Sistem
        @if(auth()->user()->unreadNotifications->count() > 0)
            <span class="absolute top-2 left-4 w-2 h-2 bg-error rounded-full ring-2 ring-surface-container-low"></span>
        @endif
    </a>
</div>

<div style="display:grid; grid-template-columns:1fr; gap:24px; max-width:720px;">

    {{-- Card 1: Broadcast Manual --}}
    <div class="card">
        <div class="card-header" style="border-bottom:1px solid var(--clr-border); padding-bottom:16px;">
            <h3 class="card-title" style="display:flex;align-items:center;gap:8px">
                <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M21 11.5a8.38 8.38 0 01-.9 3.8 8.5 8.5 0 01-7.6 4.7 8.38 8.38 0 01-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 01-.9-3.8 8.5 8.5 0 014.7-7.6 8.38 8.38 0 013.8-.9h.5a8.48 8.48 0 018 8v.5z"/></svg>
                Pesan Siaran Manual
            </h3>
        </div>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if(session('warning'))
            <div class="alert alert-success" style="border-color: var(--clr-accent); background: rgba(255,193,7,0.1); color: var(--clr-accent);">{{ session('warning') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-success" style="border-color: var(--clr-danger); background: rgba(244,67,54,0.1); color: var(--clr-danger);">{{ session('error') }}</div>
        @endif

        <form action="{{ route('admin.broadcast.send') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label class="form-label">Kirim Melalui</label>
                <div class="channel-chips">
                    <label class="channel-chip"><input type="checkbox" name="channels[]" value="database" checked> Aplikasi (Web)</label>
                    <label class="channel-chip"><input type="checkbox" name="channels[]" value="whatsapp" checked> WhatsApp</label>
                    <label class="channel-chip"><input type="checkbox" name="channels[]" value="mail"> Email</label>
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label" style="color:var(--clr-accent)">Pesan Broadcast</label>
                <textarea name="message" class="form-control" rows="5" required placeholder="Ketik pesan siaran Anda di sini..." style="resize:vertical; line-height:1.7;"></textarea>
                <small class="text-muted" style="display:block; margin-top:8px; font-size:0.75rem;">Pesan ini akan dikirim melalui jalur (channel) yang dipilih di atas.</small>
            </div>
            <button type="submit" class="btn btn-primary" style="display:flex;align-items:center;gap:8px">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M22 2L11 13M22 2l-7 20-4-9-9-4 20-7z"/></svg>
                Kirim Broadcast
            </button>
        </form>
    </div>

    {{-- Card 2: Pengaturan Otomatis --}}
    <div class="card">
        <div class="card-header" style="border-bottom:1px solid var(--clr-border); padding-bottom:16px;">
            <h3 class="card-title" style="display:flex;align-items:center;gap:8px">
                <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg>
                Pengaturan Pengingat Otomatis
            </h3>
        </div>

        <p class="text-muted mb-3" style="font-size:0.8rem; line-height:1.5;">Atur kapan pesan pengingat jadwal sholat akan dikirim kepada Imam secara otomatis sebelum waktu sholat tiba.</p>

        @if(session('success_config'))
            <div class="alert alert-success">{{ session('success_config') }}</div>
        @endif

        <form action="{{ route('admin.notification-config.update') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label class="form-label">Kirim Melalui</label>
                @php $cfgChannels = explode(',', $config->channels ?? ''); @endphp
                <div class="channel-chips">
                    <label class="channel-chip"><input type="checkbox" name="channels[]" value="database" {{ in_array('database', $cfgChannels) ? 'checked' : '' }}> Aplikasi (Web)</label>
                    <label class="channel-chip"><input type="checkbox" name="channels[]" value="whatsapp" {{ in_array('whatsapp', $cfgChannels) ? 'checked' : '' }}> WhatsApp</label>
                    <label class="channel-chip"><input type="checkbox" name="channels[]" value="mail" {{ in_array('mail', $cfgChannels) ? 'checked' : '' }}> Email</label>
                </div>
                @error('channels')
                    <div style="color:var(--clr-danger); font-size:0.75rem; margin-top:5px">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label class="form-label">Pengingat Pertama (Menit sebelum sholat)</label>
                <input type="number" name="reminder_1_minutes" class="form-control" value="{{ $config->reminder_1_minutes ?? 90 }}" required min="1">
                <small class="text-muted" style="font-size:0.72rem">Contoh: 1 Jam 30 Menit = <strong>90</strong></small>
            </div>

            <div class="mb-3">
                <label class="form-label">Aktifkan Pengingat Kedua?</label>
                <select name="enable_reminder_2" class="form-control" onchange="document.getElementById('reminder_2_group').style.display = this.value === '1' ? 'block' : 'none'">
                    <option value="0" {{ !($config->enable_reminder_2 ?? false) ? 'selected' : '' }}>Hanya 1 Kali</option>
                    <option value="1" {{ ($config->enable_reminder_2 ?? false) ? 'selected' : '' }}>Ya, 2 Kali</option>
                </select>
            </div>

            <div class="mb-3" id="reminder_2_group" style="display: {{ ($config->enable_reminder_2 ?? false) ? 'block' : 'none' }}">
                <label class="form-label">Pengingat Kedua (Menit sebelum sholat)</label>
                <input type="number" name="reminder_2_minutes" class="form-control" value="{{ $config->reminder_2_minutes ?? 30 }}" min="1">
                <small class="text-muted" style="font-size:0.72rem">Contoh: 30 Menit = <strong>30</strong></small>
            </div>

            <button type="submit" class="btn btn-secondary" style="display:flex;align-items:center;gap:8px">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M19 21H5a2 2 0 01-2-2V5a2 2 0 012-2h11l5 5v11a2 2 0 01-2 2z"/><path d="M17 21v-8H7v8M7 3v5h8"/></svg>
                Simpan Pengaturan
            </button>
        </form>
    </div>

</div>
@endsection
