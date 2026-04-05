@extends('layouts.app')
@section('title', 'Konfigurasi Fee')

@section('content')
<div class="main-header">
    <div>
        <h2 style="display:flex;align-items:center;gap:10px"><svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 1v22M17 5H9.5a3.5 3.5 0 000 7h5a3.5 3.5 0 010 7H6"/></svg> Konfigurasi Fee</h2>
        <div class="breadcrumb">Atur fee imam per jadwal atau per hari</div>
    </div>
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
