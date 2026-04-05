@extends('layouts.app')
@section('title', 'Waktu Sholat')

@section('content')
<div class="main-header">
    <div>
        <h2>Waktu Sholat</h2>
        <div class="breadcrumb">Sinkronisasi API & override manual</div>
    </div>
    @if($season)
        <form method="POST" action="{{ route('admin.prayer-times.sync') }}" id="syncForm" style="margin:0">
            @csrf
            <input type="hidden" name="season_id" value="{{ $season->id }}">
            <button type="submit" class="btn btn-success btn-sm" id="syncBtn">Sync dari API</button>
        </form>
    @endif
</div>

@if($season)
<div class="card" style="margin-bottom:20px;padding:16px">
    <form method="GET" style="display:flex;align-items:center;gap:12px">
        <label class="form-label" style="margin:0;white-space:nowrap">Tanggal:</label>
        <input type="date" name="date" class="form-input" style="max-width:200px"
               value="{{ $selectedDate }}"
               min="{{ $season->start_date->format('Y-m-d') }}"
               max="{{ $season->end_date->format('Y-m-d') }}"
               onchange="this.form.submit()">
        <span class="badge badge-gold">{{ $season->name }}</span>
    </form>
</div>

<div class="card">
    @if($prayerTimes->count() > 0)
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>Sholat</th>
                        <th>Group</th>
                        <th>Waktu API</th>
                        <th>Override</th>
                        <th>Waktu Efektif</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($prayerTimes as $pt)
                        <tr>
                            <td><strong>{{ $pt->prayerType->name }}</strong></td>
                            <td><span class="badge badge-info">{{ $pt->prayerType->group_code }}</span></td>
                            <td style="color:var(--clr-text-muted)">{{ $pt->api_time ?? '—' }}</td>
                            <td>
                                @if($pt->override_time)
                                    <span class="badge badge-warning">{{ $pt->override_time }}</span>
                                @else
                                    <span style="color:var(--clr-text-muted)">—</span>
                                @endif
                            </td>
                            <td>
                                <strong style="color:var(--clr-accent)">{{ $pt->effective_time ?? '—' }}</strong>
                            </td>
                            <td>
                                <button class="btn btn-secondary btn-xs" onclick="openOverrideModal({{ $pt->id }}, '{{ $pt->prayerType->name }}', '{{ $pt->override_time }}')">Override</button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <div class="empty-state">
            <div class="empty-icon" style="font-size:2rem;opacity:0.3">—</div>
            <p>Belum ada data waktu sholat untuk tanggal ini. Silakan sync dari API.</p>
        </div>
    @endif
</div>
@else
    <div class="card">
        <div class="empty-state">
            <div class="empty-icon" style="font-size:2rem;opacity:0.3">—</div>
            <p>Buat Season Ramadan terlebih dahulu.</p>
        </div>
    </div>
@endif

<!-- Override Modal -->
<div class="modal-overlay" id="overrideModal">
    <div class="modal-content">
        <h3 class="modal-title">Override Waktu Sholat</h3>
        <p id="overrideInfo" style="font-size:0.85rem;color:var(--clr-text-muted);margin-bottom:16px"></p>
        <form method="POST" id="overrideForm">
            @csrf @method('PATCH')
            <div class="form-group">
                <label class="form-label">Waktu Baru (HH:MM)</label>
                <input type="time" name="override_time" id="overrideTime" class="form-input">
            </div>
            <div style="display:flex;gap:8px">
                <button type="submit" class="btn btn-primary">Simpan</button>
                <button type="button" class="btn btn-secondary" onclick="closeOverrideModal()">Batal</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
document.getElementById('syncForm')?.addEventListener('submit', function(e) {
    this.querySelector('#syncBtn').textContent = 'Memproses sinkronisasi...';
    this.querySelector('#syncBtn').disabled = true;
});

function openOverrideModal(id, name, currentTime) {
    document.getElementById('overrideModal').classList.add('active');
    document.getElementById('overrideInfo').textContent = `Sholat: ${name}`;
    document.getElementById('overrideForm').action = `/admin/prayer-times/${id}/override`;
    document.getElementById('overrideTime').value = currentTime || '';
}
function closeOverrideModal() {
    document.getElementById('overrideModal').classList.remove('active');
}
document.getElementById('overrideModal').addEventListener('click', function(e) {
    if (e.target === this) closeOverrideModal();
});
</script>
@endpush
@endsection
