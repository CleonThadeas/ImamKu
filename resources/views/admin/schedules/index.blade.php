@extends('layouts.app')
@section('title', 'Jadwal Imam')

@section('content')
<div class="main-header">
    <div>
        <h2>Jadwal Imam</h2>
        <div class="breadcrumb">Kelola penugasan imam per slot sholat</div>
    </div>
    <div style="display:flex;gap:8px;align-items:center">
        @if($selectedSeason)
            <form method="POST" action="{{ route('admin.schedules.generate') }}" id="generateForm" style="margin:0">
                @csrf
                <input type="hidden" name="season_id" value="{{ $selectedSeason->id }}">
                <button type="submit" class="btn btn-success btn-sm" id="generateBtn">Generate Slots</button>
            </form>
        @endif
    </div>
</div>

<!-- Season Selector -->
@if($seasons->count() > 0)
<div class="card" style="margin-bottom:20px;padding:16px">
    <form method="GET" style="display:flex;align-items:center;gap:12px">
        <label class="form-label" style="margin:0;white-space:nowrap">Season:</label>
        <select name="season_id" class="form-select" style="max-width:300px" onchange="this.form.submit()">
            @foreach($seasons as $s)
                <option value="{{ $s->id }}" {{ $selectedSeason && $selectedSeason->id === $s->id ? 'selected' : '' }}>
                    {{ $s->name }} {{ $s->is_active ? '(Aktif)' : '' }}
                </option>
            @endforeach
        </select>
    </form>
</div>
@endif

@if($selectedSeason && $schedules->count() > 0)
    <!-- Legend -->
    <div class="card" style="margin-bottom:20px;padding:14px">
        <div style="display:flex;flex-wrap:wrap;gap:12px;align-items:center">
            <span style="font-size:0.75rem;color:var(--clr-text-muted);font-weight:600">LEGENDA:</span>
            @foreach($imams as $i => $imam)
                <span class="imam-tag imam-color-{{ ($i % 5) + 1 }}" style="padding:4px 10px;border-radius:6px;font-size:0.7rem;font-weight:600;border:1px solid">
                    {{ $imam->name }}
                </span>
            @endforeach
            <span style="font-size:0.65rem;color:var(--clr-text-muted);font-style:italic">— Klik slot untuk assign/ubah imam</span>
        </div>
    </div>

    <!-- Schedule Grid -->
    <div class="card" style="padding:12px;overflow-x:auto">
        <div class="schedule-grid" style="grid-template-columns: 100px repeat({{ $prayerTypes->count() }}, 1fr)">
            <!-- Header Row -->
            <div class="schedule-cell header">Tanggal</div>
            @foreach($prayerTypes as $pt)
                <div class="schedule-cell header">{{ $pt->name }}</div>
            @endforeach

            <!-- Data Rows -->
            @foreach($schedules as $date => $dateSchedules)
                <div class="schedule-cell date-cell">
                    <span>{{ \Carbon\Carbon::parse($date)->format('d/m') }}</span>
                    <span style="font-size:0.55rem;color:var(--clr-text-muted)">{{ \Carbon\Carbon::parse($date)->translatedFormat('D') }}</span>
                </div>
                @foreach($prayerTypes as $pt)
                    @php
                        $schedule = $dateSchedules->firstWhere('prayer_type_id', $pt->id);
                        $imamIndex = $schedule && $schedule->user ? $imams->search(fn($im) => $im->id === $schedule->user_id) : false;
                        
                        $statusClass = $imamIndex !== false ? 'imam-color-' . (($imamIndex % 5) + 1) : '';
                        $statusBadge = '';
                        if ($schedule && $schedule->user) {
                            if ($schedule->attendance) {
                                if (in_array($schedule->attendance->status, ['approved', 'pending'])) {
                                    $statusClass = 'bg-success text-white border-0 opacity-75';
                                    $statusBadge = '<span style="font-size:0.55rem;display:block;margin-top:2px;">Selesai</span>';
                                } else {
                                    $statusClass = 'bg-danger text-white border-0';
                                    $statusBadge = '<span style="font-size:0.55rem;display:block;margin-top:2px;">Expired</span>';
                                }
                            } else {
                                $schDate = \Carbon\Carbon::parse($schedule->date)->toDateString();
                                if ($schDate < now()->toDateString() || ($schDate == now()->toDateString() && $schedule->prayerTime && now()->format('H:i:s') > $schedule->prayerTime->effective_time)) {
                                    $statusClass = 'bg-danger text-white border-0 opacity-50';
                                    $statusBadge = '<span style="font-size:0.55rem;display:block;margin-top:2px;">Terlewat</span>';
                                }
                            }
                        }
                    @endphp
                    <div class="schedule-cell" onclick="openAssignModal({{ $schedule?->id ?? 'null' }}, '{{ $date }}', '{{ $pt->name }}', {{ $pt->id }}, {{ $schedule?->user_id ?? 'null' }})" style="cursor:pointer">
                        @if($schedule && $schedule->user)
                            <span class="imam-tag {{ $statusClass }}" style="display:inline-block; line-height:1.2;">
                                {{ $schedule->user->name }}
                                {!! $statusBadge !!}
                            </span>
                        @else
                            <span class="empty-slot">—</span>
                        @endif
                    </div>
                @endforeach
            @endforeach
        </div>
    </div>
@elseif($selectedSeason)
    <div class="card">
        <div class="empty-state">
            <div class="empty-icon" style="font-size:2rem;opacity:0.3">—</div>
            <p>Belum ada slot jadwal. Klik "Generate Slots" untuk membuat slot berdasarkan season.</p>
        </div>
    </div>
@else
    <div class="card">
        <div class="empty-state">
            <div class="empty-icon" style="font-size:2rem;opacity:0.3">—</div>
            <p>Silakan buat Season Ramadan terlebih dahulu.</p>
            <a href="{{ route('admin.seasons.create') }}" class="btn btn-primary btn-sm" style="margin-top:12px">Buat Season</a>
        </div>
    </div>
@endif

<!-- Assign Modal -->
<div class="modal-overlay" id="assignModal">
    <div class="modal-content">
        <h3 class="modal-title">Assign Imam</h3>
        <p id="assignInfo" style="font-size:0.85rem;color:var(--clr-text-muted);margin-bottom:16px"></p>
        <form method="POST" action="{{ route('admin.schedules.assign') }}" id="assignForm">
            @csrf
            <input type="hidden" name="schedule_id" id="assignScheduleId">
            <div class="form-group">
                <label class="form-label">Pilih Imam</label>
                <select name="user_id" id="assignUserId" class="form-select" required>
                    <option value="">— Pilih —</option>
                    @foreach($imams as $imam)
                        <option value="{{ $imam->id }}">{{ $imam->name }}</option>
                    @endforeach
                </select>
            </div>
            <div style="display:flex;gap:8px">
                <button type="submit" class="btn btn-primary">Assign</button>
                <button type="button" class="btn btn-secondary" onclick="closeAssignModal()">Batal</button>
            </div>
        </form>
        <div id="removeSection" style="display:none;margin-top:16px;padding-top:16px;border-top:1px solid var(--clr-border)">
            <form method="POST" id="removeForm">
                @csrf @method('DELETE')
                <button type="submit" class="btn btn-danger btn-sm">Hapus Penugasan</button>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.getElementById('generateForm')?.addEventListener('submit', function(e) {
    this.querySelector('#generateBtn').textContent = 'Memproses...';
    this.querySelector('#generateBtn').disabled = true;
});

function openAssignModal(scheduleId, date, prayerName, prayerTypeId, currentUserId) {
    if (!scheduleId) return;
    document.getElementById('assignModal').classList.add('active');
    document.getElementById('assignScheduleId').value = scheduleId;
    document.getElementById('assignInfo').textContent = `${date} — ${prayerName}`;
    document.getElementById('assignUserId').value = currentUserId || '';

    const removeSection = document.getElementById('removeSection');
    if (currentUserId) {
        removeSection.style.display = 'block';
        document.getElementById('removeForm').action = `/admin/schedules/${scheduleId}/remove`;
    } else {
        removeSection.style.display = 'none';
    }
}

function closeAssignModal() {
    document.getElementById('assignModal').classList.remove('active');
}

document.getElementById('assignModal').addEventListener('click', function(e) {
    if (e.target === this) closeAssignModal();
});
</script>
@endpush
@endsection
