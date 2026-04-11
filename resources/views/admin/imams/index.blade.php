@extends('layouts.app')
@section('title', 'Data Imam')

@section('content')
<div class="main-header">
    <div>
        <h2 style="display:flex;align-items:center;gap:10px"><svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/></svg> Data Imam</h2>
        <div class="breadcrumb">Kelola data imam masjid</div>
    </div>
    <a href="{{ route('admin.imams.create') }}" class="btn btn-primary">+ Tambah Imam</a>
</div>

<div class="card">
    @if($imams->count() > 0)
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Nama</th>
                        <th>Email</th>
                        <th>No. HP</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($imams as $i => $imam)
                        <tr>
                            <td>{{ $i + 1 }}</td>
                            <td>
                                <div style="display:flex;align-items:center;gap:10px">
                                    <div style="width:32px;height:32px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:0.75rem" class="imam-color-{{ ($i % 5) + 1 }}">
                                        {{ strtoupper(substr($imam->name, 0, 2)) }}
                                    </div>
                                    <strong>{{ $imam->name }}</strong>
                                </div>
                            </td>
                            <td style="color:var(--clr-text-muted)">{{ $imam->email }}</td>
                            <td>{{ $imam->phone ?? '-' }}</td>
                            <td>
                                @if($imam->is_active)
                                    <span class="badge badge-success">Aktif</span>
                                @else
                                    <span class="badge badge-danger">Nonaktif</span>
                                @endif
                            </td>
                            <td>
                                <div style="display:flex;gap:6px">
                                    <a href="{{ route('admin.imams.edit', $imam) }}" class="btn btn-secondary btn-xs">Edit</a>
                                    <form method="POST" action="{{ route('admin.imams.destroy', $imam) }}" onsubmit="return confirm('Hapus imam ini?')">
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
            <div class="empty-icon"><svg width="48" height="48" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" style="opacity:0.5"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/></svg></div>
            <p>Belum ada data imam.</p>
            <a href="{{ route('admin.imams.create') }}" class="btn btn-primary btn-sm" style="margin-top:12px">Tambah Imam Pertama</a>
        </div>
    @endif
</div>
@endsection
