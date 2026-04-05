@extends('layouts.app')
@section('title', 'Season Ramadan')

@section('content')
<div class="main-header">
    <div>
        <h2 style="display:flex;align-items:center;gap:10px"><svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M21 12.79A9 9 0 1111.21 3 7 7 0 0021 12.79z"/></svg> Season Ramadan</h2>
        <div class="breadcrumb">Kelola periode Ramadan</div>
    </div>
    <a href="{{ route('admin.seasons.create') }}" class="btn btn-primary">+ Tambah Season</a>
</div>

<div class="card">
    @if($seasons->count() > 0)
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>Nama</th>
                        <th>Tahun Hijriah</th>
                        <th>Mulai</th>
                        <th>Selesai</th>
                        <th>Jumlah Hari</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($seasons as $season)
                        <tr>
                            <td><strong>{{ $season->name }}</strong></td>
                            <td>{{ $season->hijri_year }}H</td>
                            <td>{{ $season->start_date->format('d/m/Y') }}</td>
                            <td>{{ $season->end_date->format('d/m/Y') }}</td>
                            <td>{{ $season->days_count }} hari</td>
                            <td>
                                @if($season->is_active)
                                    <span class="badge badge-success">Aktif</span>
                                @else
                                    <span class="badge badge-neutral">Nonaktif</span>
                                @endif
                            </td>
                            <td>
                                <div style="display:flex;gap:6px">
                                    <a href="{{ route('admin.seasons.edit', $season) }}" class="btn btn-secondary btn-xs">Edit</a>
                                    <form method="POST" action="{{ route('admin.seasons.destroy', $season) }}" onsubmit="return confirm('Hapus season ini dan semua datanya?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-xs">Hapus</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <div class="empty-state">
            <div class="empty-icon"><svg width="48" height="48" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" style="opacity:0.5"><path d="M21 12.79A9 9 0 1111.21 3 7 7 0 0021 12.79z"/></svg></div>
            <p>Belum ada season Ramadan.</p>
        </div>
    @endif
</div>
@endsection
