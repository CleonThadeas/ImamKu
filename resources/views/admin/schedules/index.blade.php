@extends('layouts.app')
@section('title', 'Jadwal Imam')

@section('content')
<div class="flex flex-col md:flex-row md:justify-between md:items-end gap-4 mb-8">
    <div>
        <h2 class="text-3xl font-extrabold tracking-tight text-on-surface mb-1">Jadwal Imam</h2>
        <p class="text-on-surface-variant text-sm font-medium">Kelola penugasan imam per slot sholat</p>
    </div>
    @if($selectedSeason)
        <form method="POST" action="{{ route('admin.schedules.generate') }}" id="generateForm" class="m-0">
            @csrf
            <input type="hidden" name="season_id" value="{{ $selectedSeason->id }}">
            <button type="submit" class="px-6 py-2.5 bg-primary/10 text-primary font-bold text-sm rounded-xl hover:bg-primary/20 transition-colors flex items-center gap-2 border-none cursor-pointer" id="generateBtn">
                <span class="material-symbols-outlined text-sm">magic_button</span> Generate Slots
            </button>
        </form>
    @endif
</div>

<!-- Season Selector -->
@if($seasons->count() > 0)
<div class="bg-surface-container rounded-3xl p-6 mb-8 border border-outline-variant/10 shadow-sm flex items-center gap-4">
    <span class="material-symbols-outlined text-primary">filter_alt</span>
    <form method="GET" class="flex items-center gap-3 w-full max-w-sm m-0">
        <label class="text-xs font-bold uppercase tracking-widest text-on-surface-variant whitespace-nowrap mb-0">Filter Season</label>
        <select name="season_id" class="w-full bg-surface-container-highest border border-outline-variant/20 rounded-xl px-4 py-2 text-sm focus:ring-1 focus:ring-primary outline-none text-on-surface" onchange="this.form.submit()">
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
    <div class="bg-surface-container-low p-4 rounded-2xl mb-8 flex flex-wrap gap-3 items-center border border-outline-variant/10">
        <span class="text-[10px] font-bold uppercase tracking-widest text-on-surface-variant bg-surface-container px-3 py-1.5 rounded-lg mr-2">Legenda Imam</span>
        @foreach($imams as $i => $imam)
            @php
                // Distinct Emerald Slate friendly colors based on index
                $colors = ['text-primary bg-primary/10 border border-primary/20', 'text-tertiary bg-tertiary/10 border border-tertiary/20', 'text-secondary bg-secondary/10 border border-secondary/20', 'text-[var(--clr-accent)] bg-[var(--clr-accent)]/10 border border-[var(--clr-accent)]/20', 'text-on-surface bg-surface-container-highest border border-outline-variant/30'];
                $c = $colors[$i % count($colors)];
            @endphp
            <span class="px-3 py-1 rounded-lg text-xs font-bold {{ $c }}">
                {{ $imam->name }}
            </span>
        @endforeach
        <span class="text-[10px] text-on-surface-variant/60 italic ml-auto flex items-center gap-1"><span class="material-symbols-outlined text-[14px]">touch_app</span> Klik slot untuk mengubah imam</span>
    </div>

    <!-- System Rule Warning -->
    <div class="bg-primary/5 border border-primary/20 p-5 rounded-2xl mb-8 flex items-start gap-4">
        <span class="material-symbols-outlined text-primary mt-1 text-2xl">rule</span>
        <div>
            <h4 class="text-xs font-bold text-primary uppercase tracking-widest mb-1">Peraturan Sistem Penjadwalan</h4>
            <p class="text-sm text-on-surface-variant leading-relaxed">
                <strong>Sistem melarang Imam bertugas secara berturut-turut.</strong> Imam harus dijadwalkan secara selang-seling (contoh: Imam 1 Subuh, maka Imam 1 <b>TIDAK BOLEH</b> lanjut di Dzuhur). Jadwal wajib diberikan jeda pergantian antar Imam untuk memastikan rotasi yang adil, kecuali sholat dalam rentang waktu yang sama (contoh: Isya & Tarawih).
            </p>
        </div>
    </div>

    <!-- STRICT SCHEDULE UI GRID: Preserving existing generic classes and HTML structure -->
    <div class="bg-surface-container rounded-3xl p-6 overflow-x-auto shadow-sm border border-outline-variant/10 mb-12 custom-scrollbar">
        <!-- We keep schedule-grid class to inherit old table grid css, but override colors -->
        <!-- Injecting small raw block scope to override legacy borders with new Emerald slate token mapping -->
        <style>
            .schedule-grid { border-color: rgba(60, 74, 66, 0.2); border-radius: 16px; background: #0b1326; }
            .schedule-cell { border-color: rgba(60, 74, 66, 0.15); padding: 16px; }
            .schedule-cell.header { background: #131b2e; color: #afb9cb; font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.1em; }
            .schedule-cell.date-cell span:last-child { color: #86948a; }
            .schedule-cell:hover:not(.header):not(.date-cell) { background: rgba(78, 222, 163, 0.05); }
        </style>

        <div class="schedule-grid" style="grid-template-columns: 120px repeat({{ $prayerTypes->count() }}, 1fr)">
            <!-- Header Row -->
            <div class="schedule-cell header flex items-center gap-2" style="display:flex;"><span class="material-symbols-outlined text-[16px]">calendar_month</span> Tanggal</div>
            @foreach($prayerTypes as $pt)
                <div class="schedule-cell header text-center">{{ $pt->name }}</div>
            @endforeach

            <!-- Data Rows -->
            @foreach($schedules as $date => $dateSchedules)
                <div class="schedule-cell date-cell flex flex-col justify-center items-center h-full" style="display:flex; flex-direction:column; justify-content:center; align-items:center;">
                    <span class="text-on-surface font-bold text-sm">{{ \Carbon\Carbon::parse($date)->format('d/m') }}</span>
                    <span class="text-[10px] font-medium tracking-wide uppercase mt-1">{{ \Carbon\Carbon::parse($date)->translatedFormat('l') }}</span>
                </div>
                @foreach($prayerTypes as $pt)
                    @php
                        $schedule = $dateSchedules->firstWhere('prayer_type_id', $pt->id);
                        $imamIndex = $schedule && $schedule->user ? $imams->search(fn($im) => $im->id === $schedule->user_id) : false;
                        
                        $statusClass = '';
                        $bgColor = 'transparent';
                        $textColor = 'rgba(218, 226, 253, 0.4)'; // text-on-surface-variant fading
                        
                        if ($imamIndex !== false) {
                            $colors = ['rgba(78,222,163,0.1)', 'rgba(255,185,95,0.1)', 'rgba(217,227,246,0.1)', 'rgba(16,185,129,0.1)', 'rgba(64,74,89,0.5)'];
                            $textColors = ['#4edea3', '#ffb95f', '#dae2fd', '#10b981', '#dae2fd'];
                            $bgColor = $colors[$imamIndex % count($colors)];
                            $textColor = $textColors[$imamIndex % count($textColors)];
                            $statusClass = 'has-imam';
                        }

                        $statusBadge = '';
                        $isError = false;
                        if ($schedule && $schedule->user) {
                            if ($schedule->attendance) {
                                if (in_array($schedule->attendance->status, ['approved', 'pending'])) {
                                    $statusBadge = '<span class="text-[9px] uppercase tracking-widest font-bold px-1.5 py-0.5 rounded bg-primary/20 text-primary mt-1 w-full text-center block">Selesai</span>';
                                } else {
                                    $statusBadge = '<span class="text-[9px] uppercase tracking-widest font-bold px-1.5 py-0.5 rounded bg-error/20 text-error mt-1 w-full text-center block">Expired</span>';
                                    $isError = true;
                                }
                            } else {
                                $schDate = \Carbon\Carbon::parse($schedule->date)->toDateString();
                                if ($schDate < now()->toDateString() || ($schDate == now()->toDateString() && $schedule->prayerTime && now()->format('H:i:s') > $schedule->prayerTime->effective_time)) {
                                    $statusBadge = '<span class="text-[9px] uppercase tracking-widest font-bold px-1.5 py-0.5 rounded bg-error/20 text-error mt-1 w-full text-center block opacity-60">Terlewat</span>';
                                    $isError = true;
                                }
                            }
                        }
                    @endphp
                    <div class="schedule-cell flex flex-col justify-center items-center text-center {{ $statusClass }}" onclick="openAssignModal({{ $schedule?->id ?? 'null' }}, '{{ $date }}', '{{ $pt->name }}', {{ $pt->id }}, {{ $schedule?->user_id ?? 'null' }})" style="display:flex; justify-content:center; align-items:center; cursor:pointer; {{ $isError ? 'opacity: 0.7;' : '' }}">
                        @if($schedule && $schedule->user)
                            <div class="flex flex-col items-center justify-center w-full p-2.5 rounded-xl transition-all" style="background: {{ $bgColor }}; color: {{ $textColor }}; border: 1px solid {{ str_replace('0.1)', '0.3)', $bgColor) }}; width:100%;">
                                <span class="text-xs font-bold leading-tight">{{ $schedule->user->name }}</span>
                                {!! $statusBadge !!}
                            </div>
                        @else
                            <span class="text-on-surface-variant/30 font-bold opacity-30">—</span>
                        @endif
                    </div>
                @endforeach
            @endforeach
        </div>
    </div>
@elseif($selectedSeason)
    <div class="py-16 bg-surface-container rounded-3xl flex flex-col items-center justify-center text-center border border-outline-variant/10">
        <div class="w-20 h-20 rounded-2xl bg-surface-container-low flex items-center justify-center mb-6">
            <span class="material-symbols-outlined text-5xl text-on-surface-variant/30">event_busy</span>
        </div>
        <h3 class="text-xl font-bold text-on-surface mb-2">Slot Jadwal Kosong</h3>
        <p class="text-on-surface-variant text-sm mb-8">Belum ada slot jadwal. Klik "Generate Slots" untuk membuat slot berdasarkan season.</p>
    </div>
@else
    <div class="py-16 bg-surface-container rounded-3xl flex flex-col items-center justify-center text-center border border-outline-variant/10">
        <div class="w-20 h-20 rounded-2xl bg-error/10 flex items-center justify-center mb-6">
            <span class="material-symbols-outlined text-5xl text-error/50">warning</span>
        </div>
        <h3 class="text-xl font-bold text-on-surface mb-2">Pilih atau Buat Season</h3>
        <p class="text-on-surface-variant text-sm mb-6 max-w-md">Silakan buat Season Ramadan terlebih dahulu sebelum melihat jadwal.</p>
        <a href="{{ route('admin.seasons.create') }}" class="px-6 py-3 bg-gradient-to-br from-primary-container to-primary text-on-primary-container text-xs font-bold rounded-xl shadow-lg shadow-primary/20 hover:scale-105 transition-transform flex inline-flex items-center">
            Buat Season
        </a>
    </div>
@endif

<!-- Assign Modal -->
<!-- We preserve id="assignModal", "assignForm", "assignScheduleId", "assignUserId" to ensure JS functionality -->
<div id="assignModal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.6); backdrop-filter:blur(8px); z-index:9999; justify-content:center; align-items:center;">
    <div class="bg-surface-container p-8 rounded-3xl w-full max-w-[450px] relative m-4 border border-outline-variant/20 shadow-2xl">
        <button onclick="closeAssignModal()" class="absolute top-6 right-6 text-on-surface-variant hover:text-error transition-colors" style="border:none;background:none;cursor:pointer;">
            <span class="material-symbols-outlined text-2xl">close</span>
        </button>
        
        <h3 class="text-xl font-bold text-on-surface mb-1 flex items-center gap-3">
            <div class="p-2 bg-primary/10 rounded-lg text-primary"><span class="material-symbols-outlined">person_add</span></div> 
            Assign Imam
        </h3>
        <p id="assignInfo" class="text-sm font-mono text-primary mb-2 pb-2 tracking-tight"></p>
        <p class="text-[10px] text-on-surface-variant/80 italic mb-6 pb-6 border-b border-outline-variant/10 leading-snug">
            * Hindari penugasan berurutan tanpa jeda, sistem akan otomatis menolak jika Imam melanggar aturan selang-seling.
        </p>
        
        <form method="POST" action="{{ route('admin.schedules.assign') }}" id="assignForm">
            @csrf
            <input type="hidden" name="schedule_id" id="assignScheduleId">
            
            <div class="mb-6">
                <label class="block text-xs font-bold uppercase tracking-widest text-on-surface-variant mb-2">Pilih Imam</label>
                <select name="user_id" id="assignUserId" class="w-full bg-surface-container-highest border border-outline-variant/20 rounded-xl px-4 py-3 text-sm focus:ring-1 focus:ring-primary outline-none text-on-surface" required>
                    <option value="">— Pilih —</option>
                    @foreach($imams as $imam)
                        <option value="{{ $imam->id }}">{{ $imam->name }}</option>
                    @endforeach
                </select>
            </div>
            
            <div class="flex gap-3 mt-8">
                <button type="submit" class="flex-1 py-3 bg-gradient-to-br from-primary-container to-primary text-on-primary-container text-xs font-bold rounded-xl hover:scale-[1.02] transition-transform border-none cursor-pointer">
                    Simpan Assign
                </button>
            </div>
        </form>
        
        <div id="removeSection" style="display:none;" class="mt-4 pt-4 border-t border-outline-variant/10">
            <form method="POST" id="removeForm">
                @csrf @method('DELETE')
                <button type="submit" class="w-full py-3 bg-error/10 text-error text-xs font-bold rounded-xl hover:bg-error/20 transition-colors flex items-center justify-center gap-2 border border-error/20 cursor-pointer">
                    <span class="material-symbols-outlined text-[16px]">person_remove</span> Hapus Penugasan
                </button>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.getElementById('generateForm')?.addEventListener('submit', function(e) {
    this.querySelector('#generateBtn').innerHTML = '<span class="material-symbols-outlined text-sm animate-spin">refresh</span> Memproses...';
    this.querySelector('#generateBtn').disabled = true;
});

function openAssignModal(scheduleId, date, prayerName, prayerTypeId, currentUserId) {
    if (!scheduleId) return;
    document.getElementById('assignModal').style.display = 'flex';
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

    // Connect to Backend to check consecutive rule / availability
    const select = document.getElementById('assignUserId');
    Array.from(select.options).forEach(opt => {
        if (opt.value) {
            opt.disabled = true; // Temporary disable while loading
            opt.textContent = opt.textContent.replace(' (Tidak bisa berurutan)', '');
        }
    });

    fetch(`/admin/schedules/available-imams?date=${date}&prayer_type_id=${prayerTypeId}`)
        .then(res => res.json())
        .then(availableImams => {
            const availableIds = availableImams.map(i => i.id.toString());
            Array.from(select.options).forEach(opt => {
                if (opt.value) {
                    if (availableIds.includes(opt.value) || opt.value == currentUserId) {
                        opt.disabled = false;
                    } else {
                        opt.disabled = true;
                        opt.textContent += ' (Tidak bisa berurutan)';
                    }
                }
            });
        })
        .catch(err => console.error(err));
}

function closeAssignModal() {
    document.getElementById('assignModal').style.display = 'none';
}

document.getElementById('assignModal').addEventListener('click', function(e) {
    if (e.target === this) closeAssignModal();
});
</script>
@endpush
@endsection
