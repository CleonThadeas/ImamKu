@extends('layouts.app')
@section('title', 'Waktu Sholat')

@section('content')
<div class="main-header">
    <div>
        <h2>Waktu Sholat</h2>
        <div class="breadcrumb">Sinkronisasi API, override manual & CRUD waktu sholat</div>
    </div>
    <div style="display:flex;gap:8px;align-items:center">
        @if($season)
            <button class="btn btn-primary btn-sm" onclick="openManualModal()">+ Tambah Manual</button>
            <form method="POST" action="{{ route('admin.prayer-times.sync') }}" id="syncForm" style="margin:0">
                @csrf
                <input type="hidden" name="season_id" value="{{ $season->id }}">
                <button type="submit" class="btn btn-success btn-sm" id="syncBtn">Sync dari API</button>
            </form>
        @endif
    </div>
</div>

@if($season)
<div class="card" style="margin-bottom:20px;padding:16px">
    <form method="GET" style="display:flex;align-items:center;gap:12px;flex-wrap:wrap">
        <label class="form-label" style="margin:0;white-space:nowrap">Tanggal:</label>
        <input type="date" name="date" class="form-input" style="max-width:200px; background-color: #111827 !important; color: #E5E7EB !important; color-scheme: dark !important;"
               value="{{ $selectedDate }}"
               min="{{ $season->start_date->format('Y-m-d') }}"
               max="{{ $season->end_date->format('Y-m-d') }}"
               onchange="this.form.submit()">
        <span class="badge badge-gold">{{ $season->name }}</span>
        <div style="margin-left:auto;display:flex;gap:8px;align-items:center">
            <span style="font-size:0.7rem;color:var(--clr-text-muted)">LEGENDA:</span>
            <span class="badge badge-info" style="font-size:0.6rem">API</span>
            <span class="badge badge-warning" style="font-size:0.6rem">Override</span>
            <span class="badge badge-success" style="font-size:0.6rem">Manual</span>
        </div>
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
                        <th>Sumber</th>
                        <th>Waktu API</th>
                        <th>Override</th>
                        <th>Waktu Efektif</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($prayerTimes as $pt)
                        <tr>
                            <td>
                                <strong>{{ $pt->prayerType->name }}</strong>
                                @if($pt->prayerType->is_default)
                                    <span class="badge badge-gold" style="font-size:0.5rem;margin-left:4px">DEFAULT</span>
                                @endif
                            </td>
                            <td><span class="badge badge-info">{{ $pt->prayerType->group_code }}</span></td>
                            <td>
                                @if($pt->is_manual)
                                    <span class="badge badge-success">Manual</span>
                                @elseif($pt->override_time)
                                    <span class="badge badge-warning">Override</span>
                                @else
                                    <span class="badge badge-info">API</span>
                                @endif
                            </td>
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
                                <div style="display:flex;gap:4px;flex-wrap:wrap">
                                    <button class="btn btn-secondary btn-xs" onclick="openOverrideModal({{ $pt->id }}, '{{ $pt->prayerType->name }}', '{{ $pt->override_time }}')">Edit</button>
                                    @if($pt->override_time && $pt->api_time && !$pt->is_manual)
                                        <form method="POST" action="{{ route('admin.prayer-times.reset', $pt) }}" style="margin:0" onsubmit="return confirm('Reset ke waktu API?')">
                                            @csrf
                                            <button type="submit" class="btn btn-warning btn-xs">Reset API</button>
                                        </form>
                                    @endif
                                    @if($pt->is_manual)
                                        <form method="POST" action="{{ route('admin.prayer-times.destroy', $pt) }}" style="margin:0" onsubmit="return confirm('Hapus waktu manual ini?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-xs">Hapus</button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <div class="empty-state">
            <div class="empty-icon" style="font-size:2rem;opacity:0.3">—</div>
            <p>Belum ada data waktu sholat untuk tanggal ini.</p>
            <p style="font-size:0.8rem;color:var(--clr-text-muted);margin-top:8px">Gunakan "Sync dari API" atau "Tambah Manual" untuk menambahkan data.</p>
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
        <h3 class="modal-title">Edit Waktu Sholat</h3>
        <p id="overrideInfo" style="font-size:0.85rem;color:var(--clr-text-muted);margin-bottom:16px"></p>
        <form method="POST" id="overrideForm">
            @csrf @method('PATCH')
            <div class="form-group">
                <label class="form-label">Waktu Baru (HH:MM)</label>
                <input type="time" name="override_time" id="overrideTime" class="form-input" style="background-color: #111827 !important; color: #E5E7EB !important; color-scheme: dark !important;" required>
            </div>
            <div style="display:flex;gap:8px">
                <button type="submit" class="btn btn-primary">Simpan</button>
                <button type="button" class="btn btn-secondary" onclick="closeOverrideModal()">Batal</button>
            </div>
        </form>
    </div>
</div>

<!-- Manual Add Modal -->
<div class="modal-overlay" id="manualModal">
    <div class="modal-content" style="max-width:540px">
        <h3 class="modal-title" style="display:flex;align-items:center;gap:8px">
            <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg>
            Tambah Waktu Sholat Manual
        </h3>
        <p style="font-size:0.8rem;color:var(--clr-text-muted);margin-bottom:16px">
            Waktu manual <strong>tidak akan tertimpa</strong> oleh sinkronisasi API. Jenis sholat yang sudah terdaftar untuk tanggal ini akan ditandai.
        </p>
        @if($season)
        <form method="POST" action="{{ route('admin.prayer-times.store') }}">
            @csrf
            <input type="hidden" name="season_id" value="{{ $season->id }}">
            <div class="form-group">
                <label class="form-label">Tanggal</label>
                <input type="date" name="date" class="form-input" style="background-color: #111827 !important; color: #E5E7EB !important; color-scheme: dark !important;" value="{{ $selectedDate }}"
                       min="{{ $season->start_date->format('Y-m-d') }}"
                       max="{{ $season->end_date->format('Y-m-d') }}" required>
            </div>
            <div class="form-group">
                <label class="form-label">Jenis Sholat</label>
                <select name="prayer_type_id" id="manualPrayerType" class="form-select" required>
                    <option value="">— Pilih Sholat —</option>

                    <optgroup label="🕌 Sholat Wajib + Tarawih (Default)">
                        @php /** @var \App\Models\PrayerType[] $defaultTypes */ @endphp
                        @foreach($defaultTypes as $type)
                            <option value="{{ $type->id }}"
                                {{ in_array($type->id, $existingTypeIds) ? 'disabled' : '' }}
                                data-has-api="{{ $type->api_key ? '1' : '0' }}">
                                {{ $type->name }} ({{ $type->group_code }})
                                {{ in_array($type->id, $existingTypeIds) ? ' ✓ Sudah terdaftar' : '' }}
                            </option>
                        @endforeach
                    </optgroup>

                    <optgroup label="⭐ Sholat Sunnah & Khusus">
                        @php /** @var \App\Models\PrayerType[] $specialTypes */ @endphp
                        @foreach($specialTypes as $type)
                            <option value="{{ $type->id }}"
                                {{ in_array($type->id, $existingTypeIds) ? 'disabled' : '' }}
                                data-has-api="0">
                                {{ $type->name }}
                                {{ in_array($type->id, $existingTypeIds) ? ' ✓ Sudah terdaftar' : '' }}
                            </option>
                        @endforeach
                    </optgroup>
                </select>
                <small style="color:var(--clr-text-muted);font-size:0.65rem">Total {{ $prayerTypes->count() }} jenis sholat tersedia — yang sudah terdaftar ditandai & di-disable</small>
            </div>

            <!-- Time Source Toggle -->
            <div class="form-group">
                <label class="form-label">Sumber Waktu</label>
                <div style="display:flex;gap:8px;margin-bottom:4px">
                    <label style="flex:1;display:flex;align-items:center;gap:8px;padding:10px 14px;background:rgba(212,168,67,0.08);border:1.5px solid var(--clr-accent);border-radius:8px;cursor:pointer;transition:all 0.2s" id="labelApi">
                        <input type="radio" name="time_source" value="api" checked onchange="toggleTimeSource()" style="accent-color:var(--clr-accent)">
                        <div>
                            <div style="font-size:0.8rem;font-weight:600;color:var(--clr-text)">Dari API</div>
                            <div style="font-size:0.65rem;color:var(--clr-text-muted)">Gunakan waktu Aladhan</div>
                        </div>
                    </label>
                    <label style="flex:1;display:flex;align-items:center;gap:8px;padding:10px 14px;background:var(--clr-surface-light);border:1.5px solid var(--clr-border);border-radius:8px;cursor:pointer;transition:all 0.2s" id="labelCustom">
                        <input type="radio" name="time_source" value="custom" onchange="toggleTimeSource()" style="accent-color:var(--clr-accent)">
                        <div>
                            <div style="font-size:0.8rem;font-weight:600;color:var(--clr-text)">Kustom</div>
                            <div style="font-size:0.65rem;color:var(--clr-text-muted)">Atur waktu sendiri</div>
                        </div>
                    </label>
                </div>
            </div>

            <!-- API Time Preview -->
            <div id="apiTimePreview" style="padding:12px;background:var(--clr-surface-light);border:1px solid var(--clr-border);border-radius:8px;margin-bottom:16px">
                <div style="display:flex;align-items:center;justify-content:space-between">
                    <span style="font-size:0.75rem;color:var(--clr-text-muted)">Waktu dari API:</span>
                    <span id="apiTimeValue" style="font-size:1rem;font-weight:700;color:var(--clr-accent)">— Pilih sholat —</span>
                </div>
                <div id="apiTimeNote" style="font-size:0.65rem;color:var(--clr-text-muted);margin-top:4px;display:none"></div>
            </div>

            <!-- Custom Time Input (hidden by default) -->
            <div class="form-group" id="customTimeGroup" style="display:none">
                <label class="form-label">Waktu Kustom (HH:MM)</label>
                <input type="time" name="time" id="manualTimeInput" class="form-input" style="background-color: #111827 !important; color: #E5E7EB !important; color-scheme: dark !important;">
            </div>

            <div style="display:flex;gap:8px">
                <button type="submit" class="btn btn-primary" id="manualSubmitBtn">Simpan</button>
                <button type="button" class="btn btn-secondary" onclick="closeManualModal()">Batal</button>
            </div>
        </form>
        @endif
    </div>
</div>

@push('scripts')
<script>
// API times map for client-side preview
const apiTimesMap = @json($apiTimesForDate ?? []);
const existingTypeIds = @json($existingTypeIds ?? []);

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
document.getElementById('overrideModal')?.addEventListener('click', function(e) {
    if (e.target === this) closeOverrideModal();
});

function openManualModal() {
    document.getElementById('manualModal').classList.add('active');
}
function closeManualModal() {
    document.getElementById('manualModal').classList.remove('active');
}
document.getElementById('manualModal')?.addEventListener('click', function(e) {
    if (e.target === this) closeManualModal();
});

// Toggle time source between API and Custom
function toggleTimeSource() {
    const isApi = document.querySelector('input[name="time_source"]:checked').value === 'api';
    const apiPreview = document.getElementById('apiTimePreview');
    const customGroup = document.getElementById('customTimeGroup');
    const timeInput = document.getElementById('manualTimeInput');
    const labelApi = document.getElementById('labelApi');
    const labelCustom = document.getElementById('labelCustom');

    if (isApi) {
        apiPreview.style.display = 'block';
        customGroup.style.display = 'none';
        timeInput.removeAttribute('required');
        labelApi.style.borderColor = 'var(--clr-accent)';
        labelApi.style.background = 'rgba(212,168,67,0.08)';
        labelCustom.style.borderColor = 'var(--clr-border)';
        labelCustom.style.background = 'var(--clr-surface-light)';
    } else {
        apiPreview.style.display = 'none';
        customGroup.style.display = 'block';
        timeInput.setAttribute('required', 'required');
        labelCustom.style.borderColor = 'var(--clr-accent)';
        labelCustom.style.background = 'rgba(212,168,67,0.08)';
        labelApi.style.borderColor = 'var(--clr-border)';
        labelApi.style.background = 'var(--clr-surface-light)';
    }
}

// Update API time preview when prayer type changes
document.getElementById('manualPrayerType')?.addEventListener('change', function() {
    const typeId = this.value;
    const apiTimeEl = document.getElementById('apiTimeValue');
    const apiNoteEl = document.getElementById('apiTimeNote');
    const selectedOption = this.options[this.selectedIndex];
    const hasApi = selectedOption?.dataset?.hasApi === '1';

    if (typeId && apiTimesMap[typeId]) {
        apiTimeEl.textContent = apiTimesMap[typeId];
        apiTimeEl.style.color = 'var(--clr-accent)';
        apiNoteEl.textContent = 'Tersedia dari data API yang sudah tersinkronisasi.';
        apiNoteEl.style.display = 'block';
    } else if (typeId && hasApi) {
        apiTimeEl.textContent = 'Belum tersinkronisasi';
        apiTimeEl.style.color = 'var(--clr-warning)';
        apiNoteEl.textContent = 'Data API dapat diambil otomatis saat disimpan, atau gunakan Sync dari API terlebih dahulu.';
        apiNoteEl.style.display = 'block';
    } else if (typeId) {
        apiTimeEl.textContent = 'Tidak tersedia';
        apiTimeEl.style.color = 'var(--clr-text-muted)';
        apiNoteEl.textContent = 'Jenis sholat ini tidak memiliki data dari API. Gunakan mode Kustom.';
        apiNoteEl.style.display = 'block';

        // Auto-switch to custom mode for types without API
        document.querySelector('input[name="time_source"][value="custom"]').checked = true;
        toggleTimeSource();
    } else {
        apiTimeEl.textContent = '— Pilih sholat —';
        apiTimeEl.style.color = 'var(--clr-accent)';
        apiNoteEl.style.display = 'none';
    }
});
</script>
@endpush
@endsection
