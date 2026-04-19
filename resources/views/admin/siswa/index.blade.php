@extends('layouts.app')
@section('title', 'Data Siswa')

@section('content')

<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1rem">
    <h2 style="font-size:1.1rem;font-weight:700;">Data Siswa</h2>
    <a href="{{ route('admin.siswa.create') }}" class="btn btn-primary btn-sm">
        + Tambah
    </a>
</div>

{{-- Form pencarian --}}
<form method="GET" action="{{ route('admin.siswa.index') }}" class="search-bar">
    <input type="text" name="search" placeholder="Cari nama / NISN..."
           value="{{ request('search') }}">
    <select name="kelas_id" style="width:auto;min-width:110px">
        <option value="">Semua Kelas</option>
        @foreach ($kelas as $k)
            <option value="{{ $k->id }}"
                {{ request('kelas_id') == $k->id ? 'selected' : '' }}>
                {{ $k->nama_kelas }}
            </option>
        @endforeach
    </select>
    <button type="submit" class="btn btn-secondary btn-sm">Cari</button>
</form>

<div class="card" style="padding:0;overflow:hidden">
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Nama Lengkap</th>
                    <th>NISN</th>
                    <th>Kelas</th>
                    <th>L/P</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($siswa as $s)
                <tr>
                    <td><strong>{{ $s->nama_lengkap }}</strong></td>
                    <td style="font-family:monospace">{{ $s->nisn }}</td>
                    <td>{{ $s->kelas?->nama_kelas ?? '-' }}</td>
                    <td>{{ $s->jenis_kelamin }}</td>
                    <td>
                        @php
                            $badge = match($s->status) {
                                'aktif'  => 'badge-green',
                                'lulus'  => 'badge-gray',
                                'pindah' => 'badge-yellow',
                                'keluar' => 'badge-red',
                                default  => 'badge-gray',
                            };
                        @endphp
                        <span class="badge {{ $badge }}">{{ ucfirst($s->status) }}</span>
                    </td>
                    <td>
                        <a href="{{ route('admin.siswa.edit', $s) }}"
                           class="btn btn-warning btn-sm">Edit</a>

                        <form method="POST"
                              action="{{ route('admin.siswa.destroy', $s) }}"
                              style="display:inline"
                              onsubmit="return confirm('Yakin hapus siswa ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                    class="btn btn-danger btn-sm">Hapus</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" style="text-align:center;color:#9ca3af;padding:2rem">
                        Belum ada data siswa.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Pagination --}}
<div style="margin-top:1rem">
    {{ $siswa->links() }}
</div>

@endsection