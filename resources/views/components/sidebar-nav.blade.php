@props(['role'])

@if($role === 'admin')
    <!-- ADMIN Group -->
    <div>
        <p class="px-4 mb-2 text-[10px] font-bold uppercase tracking-[0.2em] text-on-surface-variant/40">ADMIN</p>
        <div class="space-y-1">
            <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-lg transition-all duration-200 {{ request()->routeIs('admin.dashboard') ? 'bg-surface-container text-primary font-semibold border-r-4 border-primary' : 'text-on-surface-variant hover:bg-surface-container-high hover:text-white group' }}">
                <span class="material-symbols-outlined text-lg {{ request()->routeIs('admin.dashboard') ? '' : 'opacity-70 group-hover:opacity-100' }}">dashboard</span>
                <span>Dashboard</span>
            </a>
        </div>
    </div>

    <!-- OPERASIONAL Group -->
    <div>
        <p class="px-4 mb-2 mt-4 text-[10px] font-bold uppercase tracking-[0.2em] text-on-surface-variant/40">OPERASIONAL</p>
        <div class="space-y-1">
            <a href="{{ route('admin.schedules.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-lg transition-all duration-200 {{ request()->routeIs('admin.schedules.*') ? 'bg-surface-container text-primary font-semibold border-r-4 border-primary' : 'text-on-surface-variant hover:bg-surface-container-high hover:text-white group' }}">
                <span class="material-symbols-outlined text-lg {{ request()->routeIs('admin.schedules.*') ? '' : 'opacity-70 group-hover:opacity-100' }}">calendar_today</span>
                <span>Jadwal</span>
            </a>
            <a href="{{ route('admin.swaps.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-lg transition-all duration-200 {{ request()->routeIs('admin.swaps.*') ? 'bg-surface-container text-primary font-semibold border-r-4 border-primary' : 'text-on-surface-variant hover:bg-surface-container-high hover:text-white group' }}">
                <span class="material-symbols-outlined text-lg {{ request()->routeIs('admin.swaps.*') ? '' : 'opacity-70 group-hover:opacity-100' }}">swap_horiz</span>
                <span>Swap Monitoring</span>
            </a>
            <a href="{{ route('admin.attendances.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-lg transition-all duration-200 {{ request()->routeIs('admin.attendances.*') ? 'bg-surface-container text-primary font-semibold border-r-4 border-primary' : 'text-on-surface-variant hover:bg-surface-container-high hover:text-white group' }}">
                <span class="material-symbols-outlined text-lg {{ request()->routeIs('admin.attendances.*') ? '' : 'opacity-70 group-hover:opacity-100' }}">fact_check</span>
                <span>Validasi Absensi</span>
            </a>
        </div>
    </div>

    <!-- DATA MASTER Group -->
    <div>
        <p class="px-4 mb-2 mt-4 text-[10px] font-bold uppercase tracking-[0.2em] text-on-surface-variant/40">DATA MASTER</p>
        <div class="space-y-1">
            <a href="{{ route('admin.imams.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-lg transition-all duration-200 {{ request()->routeIs('admin.imams.*') ? 'bg-surface-container text-primary font-semibold border-r-4 border-primary' : 'text-on-surface-variant hover:bg-surface-container-high hover:text-white group' }}">
                <span class="material-symbols-outlined text-lg {{ request()->routeIs('admin.imams.*') ? '' : 'opacity-70 group-hover:opacity-100' }}">person_book</span>
                <span>Data Imam</span>
            </a>
            <a href="{{ route('admin.penalties.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-lg transition-all duration-200 {{ request()->routeIs('admin.penalties.*') ? 'bg-surface-container text-primary font-semibold border-r-4 border-primary' : 'text-on-surface-variant hover:bg-surface-container-high hover:text-white group' }}">
                <span class="material-symbols-outlined text-lg {{ request()->routeIs('admin.penalties.*') ? '' : 'opacity-70 group-hover:opacity-100' }}">military_tech</span>
                <span>Penalty & Ranking</span>
            </a>
            <a href="{{ route('admin.seasons.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-lg transition-all duration-200 {{ request()->routeIs('admin.seasons.*') ? 'bg-surface-container text-primary font-semibold border-r-4 border-primary' : 'text-on-surface-variant hover:bg-surface-container-high hover:text-white group' }}">
                <span class="material-symbols-outlined text-lg {{ request()->routeIs('admin.seasons.*') ? '' : 'opacity-70 group-hover:opacity-100' }}">mosque</span>
                <span>Season Ramadan</span>
            </a>
            <a href="{{ route('admin.prayer-times.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-lg transition-all duration-200 {{ request()->routeIs('admin.prayer-times.*') ? 'bg-surface-container text-primary font-semibold border-r-4 border-primary' : 'text-on-surface-variant hover:bg-surface-container-high hover:text-white group' }}">
                <span class="material-symbols-outlined text-lg {{ request()->routeIs('admin.prayer-times.*') ? '' : 'opacity-70 group-hover:opacity-100' }}">schedule</span>
                <span>Waktu Sholat</span>
            </a>
            <a href="{{ route('admin.mosque-config.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-lg transition-all duration-200 {{ request()->routeIs('admin.mosque-config.*') ? 'bg-surface-container text-primary font-semibold border-r-4 border-primary' : 'text-on-surface-variant hover:bg-surface-container-high hover:text-white group' }}">
                <span class="material-symbols-outlined text-lg {{ request()->routeIs('admin.mosque-config.*') ? '' : 'opacity-70 group-hover:opacity-100' }}">map</span>
                <span>Lokasi Masjid</span>
            </a>
        </div>
    </div>

    <!-- KEUANGAN Group -->
    <div>
        <p class="px-4 mb-2 mt-4 text-[10px] font-bold uppercase tracking-[0.2em] text-on-surface-variant/40">KEUANGAN</p>
        <div class="space-y-1">
            <a href="{{ route('admin.fees.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-lg transition-all duration-200 {{ request()->routeIs('admin.fees.*') ? 'bg-surface-container text-primary font-semibold border-r-4 border-primary' : 'text-on-surface-variant hover:bg-surface-container-high hover:text-white group' }}">
                <span class="material-symbols-outlined text-lg {{ request()->routeIs('admin.fees.*') ? '' : 'opacity-70 group-hover:opacity-100' }}">account_balance_wallet</span>
                <span>Fee & Laporan</span>
            </a>
        </div>
    </div>

    <!-- SISTEM Group -->
    <div>
        <p class="px-4 mb-2 mt-4 text-[10px] font-bold uppercase tracking-[0.2em] text-on-surface-variant/40">SISTEM</p>
        <div class="space-y-1">
            <a href="{{ route('admin.broadcast.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-lg transition-all duration-200 {{ request()->routeIs('admin.broadcast.*') || request()->routeIs('admin.notification-logs.*') ? 'bg-surface-container text-primary font-semibold border-r-4 border-primary' : 'text-on-surface-variant hover:bg-surface-container-high hover:text-white group' }}">
                <span class="material-symbols-outlined text-lg {{ request()->routeIs('admin.broadcast.*') || request()->routeIs('admin.notification-logs.*') ? '' : 'opacity-70 group-hover:opacity-100' }}">forum</span>
                <span>Komunikasi</span>
            </a>
            <a href="{{ route('admin.exports.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-lg transition-all duration-200 {{ request()->routeIs('admin.exports.*') ? 'bg-surface-container text-primary font-semibold border-r-4 border-primary' : 'text-on-surface-variant hover:bg-surface-container-high hover:text-white group' }}">
                <span class="material-symbols-outlined text-lg {{ request()->routeIs('admin.exports.*') ? '' : 'opacity-70 group-hover:opacity-100' }}">download_for_offline</span>
                <span>Pusat Export</span>
            </a>
        </div>
    </div>

    <!-- LAINNYA Group -->
    <div>
        <p class="px-4 mb-2 mt-4 text-[10px] font-bold uppercase tracking-[0.2em] text-on-surface-variant/40">LAINNYA</p>
        <div class="space-y-1">
            <a href="{{ route('guidelines') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-lg transition-all duration-200 {{ request()->routeIs('guidelines') ? 'bg-surface-container text-primary font-semibold border-r-4 border-primary' : 'text-on-surface-variant hover:bg-surface-container-high hover:text-white group' }}">
                <span class="material-symbols-outlined text-lg {{ request()->routeIs('guidelines') ? '' : 'opacity-70 group-hover:opacity-100' }}">help_center</span>
                <span>Panduan</span>
            </a>
            <a href="{{ route('profile.edit') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-lg transition-all duration-200 {{ request()->routeIs('profile.*') ? 'bg-surface-container text-primary font-semibold border-r-4 border-primary' : 'text-on-surface-variant hover:bg-surface-container-high hover:text-white group' }}">
                <span class="material-symbols-outlined text-lg {{ request()->routeIs('profile.*') ? '' : 'opacity-70 group-hover:opacity-100' }}">account_circle</span>
                <span>Profile</span>
            </a>
        </div>
    </div>
@else
    <!-- IMAM MENU -->
    <!-- MENU Group -->
    <div>
        <p class="px-4 mb-2 text-[10px] font-bold uppercase tracking-[0.2em] text-on-surface-variant/40">MENU</p>
        <div class="space-y-1">
            <a href="{{ route('imam.dashboard') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-lg transition-all duration-200 {{ request()->routeIs('imam.dashboard') ? 'bg-surface-container text-primary font-semibold border-r-4 border-primary' : 'text-on-surface-variant hover:bg-surface-container-high hover:text-white group' }}">
                <span class="material-symbols-outlined text-lg {{ request()->routeIs('imam.dashboard') ? '' : 'opacity-70 group-hover:opacity-100' }}">dashboard</span>
                <span>Dashboard</span>
            </a>
            <a href="{{ route('imam.schedules.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-lg transition-all duration-200 {{ request()->routeIs('imam.schedules.*') ? 'bg-surface-container text-primary font-semibold border-r-4 border-primary' : 'text-on-surface-variant hover:bg-surface-container-high hover:text-white group' }}">
                <span class="material-symbols-outlined text-lg {{ request()->routeIs('imam.schedules.*') ? '' : 'opacity-70 group-hover:opacity-100' }}">calendar_today</span>
                <span>Lihat Jadwal</span>
            </a>
            <a href="{{ route('imam.swaps.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-lg transition-all duration-200 {{ request()->routeIs('imam.swaps.*') ? 'bg-surface-container text-primary font-semibold border-r-4 border-primary' : 'text-on-surface-variant hover:bg-surface-container-high hover:text-white group' }}">
                <span class="material-symbols-outlined text-lg {{ request()->routeIs('imam.swaps.*') ? '' : 'opacity-70 group-hover:opacity-100' }}">swap_horiz</span>
                <span>Swap Jadwal</span>
            </a>
        </div>
    </div>

    <!-- PERFORMA Group -->
    <div>
        <p class="px-4 mb-2 mt-4 text-[10px] font-bold uppercase tracking-[0.2em] text-on-surface-variant/40">PERFORMA</p>
        <div class="space-y-1">
            <a href="{{ route('imam.fees.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-lg transition-all duration-200 {{ request()->routeIs('imam.fees.*') ? 'bg-surface-container text-primary font-semibold border-r-4 border-primary' : 'text-on-surface-variant hover:bg-surface-container-high hover:text-white group' }}">
                <span class="material-symbols-outlined text-lg {{ request()->routeIs('imam.fees.*') ? '' : 'opacity-70 group-hover:opacity-100' }}">payments</span>
                <span>Pendapatan</span>
            </a>
            <a href="{{ route('imam.points.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-lg transition-all duration-200 {{ request()->routeIs('imam.points.*') ? 'bg-surface-container text-primary font-semibold border-r-4 border-primary' : 'text-on-surface-variant hover:bg-surface-container-high hover:text-white group' }}">
                <span class="material-symbols-outlined text-lg {{ request()->routeIs('imam.points.*') ? '' : 'opacity-70 group-hover:opacity-100' }}">trending_up</span>
                <span>Poin & Kedisiplinan</span>
            </a>
        </div>
    </div>

    <!-- INFORMASI Group -->
    <div>
        <p class="px-4 mb-2 mt-4 text-[10px] font-bold uppercase tracking-[0.2em] text-on-surface-variant/40">INFORMASI</p>
        <div class="space-y-1">
            <a href="{{ route('imam.notifications.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-lg transition-all duration-200 relative {{ request()->routeIs('imam.notifications.*') ? 'bg-surface-container text-primary font-semibold border-r-4 border-primary' : 'text-on-surface-variant hover:bg-surface-container-high hover:text-white group' }}">
                <span class="material-symbols-outlined text-lg {{ request()->routeIs('imam.notifications.*') ? '' : 'opacity-70 group-hover:opacity-100' }}">notifications</span>
                @if(auth()->user()->unreadNotifications->count() > 0)
                    <span class="absolute top-2 left-6 w-2 h-2 bg-error rounded-full ring-2 ring-surface-container-low"></span>
                @endif
                <span>Notifikasi</span>
            </a>
            <a href="{{ route('guidelines') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-lg transition-all duration-200 {{ request()->routeIs('guidelines') ? 'bg-surface-container text-primary font-semibold border-r-4 border-primary' : 'text-on-surface-variant hover:bg-surface-container-high hover:text-white group' }}">
                <span class="material-symbols-outlined text-lg {{ request()->routeIs('guidelines') ? '' : 'opacity-70 group-hover:opacity-100' }}">help_center</span>
                <span>Panduan</span>
            </a>
        </div>
    </div>

    <!-- AKUN Group -->
    <div>
        <p class="px-4 mb-2 mt-4 text-[10px] font-bold uppercase tracking-[0.2em] text-on-surface-variant/40">AKUN</p>
        <div class="space-y-1">
            <a href="{{ route('profile.edit') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-lg transition-all duration-200 {{ request()->routeIs('profile.*') ? 'bg-surface-container text-primary font-semibold border-r-4 border-primary' : 'text-on-surface-variant hover:bg-surface-container-high hover:text-white group' }}">
                <span class="material-symbols-outlined text-lg {{ request()->routeIs('profile.*') ? '' : 'opacity-70 group-hover:opacity-100' }}">account_circle</span>
                <span>Profile</span>
            </a>
        </div>
    </div>
@endif
