@extends('layouts.app')
@section('title', 'Konfigurasi Fee')

@section('content')
<div class="flex flex-col md:flex-row md:justify-between md:items-end gap-4 mb-8">
    <div>
        <h2 class="text-3xl font-extrabold tracking-tight text-on-surface mb-1">Fee & Laporan</h2>
        <p class="text-on-surface-variant text-sm font-medium">Manajemen Keuangan dan Konfigurasi Tarif Imam</p>
    </div>
</div>

<!-- TABS -->
<div class="flex items-center gap-2 border-b border-outline-variant/20 mb-8 pb-4">
    <a href="{{ route('admin.fees.index') }}" class="px-6 py-2.5 rounded-xl font-bold text-sm transition-all flex items-center gap-2 bg-primary/20 text-primary border border-primary/30">
        <span class="material-symbols-outlined text-[18px]">settings</span> Konfigurasi Fee
    </a>
    <a href="{{ route('admin.fees.report') }}" class="px-6 py-2.5 rounded-xl font-bold text-sm transition-all flex items-center gap-2 text-on-surface-variant hover:bg-surface-container hover:text-on-surface">
        <span class="material-symbols-outlined text-[18px]">assessment</span> Laporan Keuangan
    </a>
</div>

@if($season)
<div class="card" style="max-width:700px">
    <form method="POST" action="{{ route('admin.fees.update') }}">
        @csrf
        <input type="hidden" name="season_id" value="{{ $season->id }}">

        <div class="form-group">
            <div class="form-checkbox-wrapper">
                <input type="hidden" name="is_enabled" value="0">
                <input type="checkbox" name="is_enabled" value="1" class="form-checkbox"
                    {{ $feeConfig && $feeConfig->is_enabled ? 'checked' : '' }} id="feeEnabled">
                <label class="form-label" style="margin:0">Aktifkan Fitur Fee</label>
            </div>
        </div>

        <div class="form-group">
            <label class="form-label">Mode Perhitungan</label>
            <select name="mode" class="form-select" id="feeMode" onchange="toggleFeeMode()">
                <option value="per_schedule" {{ $feeConfig && $feeConfig->mode === 'per_schedule' ? 'selected' : '' }}>Per Jadwal (per sholat)</option>
                <option value="per_day" {{ $feeConfig && $feeConfig->mode === 'per_day' ? 'selected' : '' }}>Per Hari (flat rate)</option>
            </select>
        </div>

        <!-- Per Schedule -->
        <div id="perScheduleSection">
            <div class="form-label" style="margin-bottom:12px">Fee per Jenis Sholat (Rp)</div>
            @foreach($prayerTypes as $pt)
                <div class="form-group" style="display:flex;align-items:center;gap:12px">
                    <label style="width:100px;font-size:0.85rem;font-weight:500">{{ $pt->name }}</label>
                    <input type="number" name="fee_{{ $pt->id }}" class="form-input" style="max-width:200px"
                        value="{{ $feeDetails->has($pt->id) ? $feeDetails[$pt->id]->amount : 0 }}" min="0" step="1000">
                </div>
            @endforeach
        </div>

        <!-- Per Day -->
        <div id="perDaySection" style="display:none">
            <div class="form-group">
                <label class="form-label">Tarif Harian (Rp)</label>
                <input type="number" name="daily_rate" class="form-input" style="max-width:200px"
                    value="{{ $feeDetails->has(null) ? $feeDetails[null]->amount : 0 }}" min="0" step="1000">
            </div>
        </div>

        <button type="submit" class="btn btn-primary" style="margin-top:12px;display:flex;align-items:center;gap:8px"><svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M19 21H5a2 2 0 01-2-2V5a2 2 0 012-2h11l5 5v11a2 2 0 01-2 2z"/><path d="M17 21v-8H7v8M7 3v5h8"/></svg> Simpan Konfigurasi</button>
    </form>
</div>
@else
    <div class="card">
        <div class="empty-state">
            <div class="empty-icon"><svg width="48" height="48" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" style="opacity:0.5"><path d="M21 12.79A9 9 0 1111.21 3 7 7 0 0021 12.79z"/></svg></div>
            <p>Buat Season Ramadan terlebih dahulu.</p>
        </div>
    </div>
@endif

@push('scripts')
<script>
function toggleFeeMode() {
    const mode = document.getElementById('feeMode').value;
    document.getElementById('perScheduleSection').style.display = mode === 'per_schedule' ? 'block' : 'none';
    document.getElementById('perDaySection').style.display = mode === 'per_day' ? 'block' : 'none';
}
toggleFeeMode();
</script>
@endpush
@endsection
