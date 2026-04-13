@extends('layouts.app')
@section('title', 'Season Ramadan')

@section('content')
<div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-8">
    <div>
        <h2 class="text-2xl font-bold flex items-center gap-3 text-on-surface">
            <div class="w-1.5 h-6 bg-primary rounded-full"></div>
            Season Ramadan
        </h2>
        <div class="text-sm text-on-surface-variant font-medium mt-1 flex items-center gap-2 tracking-wide">
            <span class="material-symbols-outlined text-[16px]">event</span>
            Kelola periode Ramadan
        </div>
    </div>
    <a href="{{ route('admin.seasons.create') }}" class="btn btn-primary shadow-lg shadow-primary/20 flex items-center gap-2">
        <span class="material-symbols-outlined text-[18px]">add</span> Tambah Season
    </a>
</div>

<div class="card p-0 overflow-hidden">
    @if($seasons->count() > 0)
        <div class="table-wrapper border-0 rounded-none bg-surface-container-low mb-0">
            <table>
                <thead>
                    <tr>
                        <th class="bg-surface-container-low !px-6">Nama</th>
                        <th class="bg-surface-container-low">Tahun Hijriah</th>
                        <th class="bg-surface-container-low">Mulai</th>
                        <th class="bg-surface-container-low">Selesai</th>
                        <th class="bg-surface-container-low">Jumlah Hari</th>
                        <th class="bg-surface-container-low">Status</th>
                        <th class="bg-surface-container-low text-right !px-6">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-outline-variant/10">
                    @foreach($seasons as $season)
                        <tr class="hover:bg-surface-container/50 transition-colors">
                            <td class="!px-6">
                                <strong class="text-primary font-bold">{{ $season->name }}</strong>
                            </td>
                            <td><div class="text-on-surface font-medium">{{ $season->hijri_year }}H</div></td>
                            <td><div class="text-on-surface-variant text-sm">{{ $season->start_date->format('d/m/Y') }}</div></td>
                            <td><div class="text-on-surface-variant text-sm">{{ $season->end_date->format('d/m/Y') }}</div></td>
                            <td><div class="text-on-surface-variant text-sm">{{ $season->days_count }} hari</div></td>
                            <td>
                                @if($season->is_active)
                                    <x-badge type="success"><span class="material-symbols-outlined text-[12px]">check_circle</span> Aktif</x-badge>
                                @else
                                    <x-badge type="tertiary">Nonaktif</x-badge>
                                @endif
                            </td>
                            <td class="!px-6 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('admin.seasons.edit', $season) }}" class="px-3 py-1.5 bg-secondary/10 text-secondary border border-secondary/20 hover:bg-secondary/20 rounded-lg text-xs font-bold transition-colors">Edit</a>
                                    <form method="POST" action="{{ route('admin.seasons.destroy', $season) }}" onsubmit="return confirm('Hapus season ini dan semua datanya?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="px-3 py-1.5 bg-error/10 text-error border border-error/20 hover:bg-error/20 rounded-lg text-xs font-bold transition-colors">Hapus</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <div class="p-16 text-center">
            <span class="material-symbols-outlined text-6xl text-on-surface-variant/30 mb-4 block">event_busy</span>
            <h3 class="text-xl font-bold text-on-surface mb-2">Belum ada season Ramadan.</h3>
            <p class="text-on-surface-variant text-sm max-w-sm mx-auto">Silakan buat season Ramadan pertama Anda untuk memulai manajemen jadwal dan attendance.</p>
        </div>
    @endif
</div>
@endsection
