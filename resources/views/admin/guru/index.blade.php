@extends('layouts.app')
@section('title', 'Data Guru')

@section('content')

<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1rem">
    <h2 style="font-size:1.1rem;font-weight:700;">Data Guru</h2>
    <a href="{{ route('admin.guru.create') }}" class="btn btn-primary btn-sm">+ Tambah</a>
</div>

<form method="GET" action="{{ route('admin.guru.index') }}" class="search-bar">
    <input type="text" name="search" placeholder="Cari nama / NIP..."
           value="{{ request('search') }}">
    <button type="submit" class="btn btn-secondary btn-sm">Cari</button>
</form>

<div class="card" style="padding:0;overflow:hidden">
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Nama Lengkap</th>
                    <th>NIP</th>
                    <th>Status</th>
                    <th>Telepon</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($guru as $g)
                <tr>
                    <td>
                        <strong>{{ $g->nama_lengkap }}</strong><br>
                        <small style="color:#9ca3af">{{ $g->user->email }}</small>
                    </td>
                    <td style="font-family:monospace">{{ $g->nip ?? '-' }}</td>
                    <td>
                        @php
                            $badge = match($g->status_kepegawaian) {
                                'PNS'     => 'badge-green',
                                'GTT'     => 'badge-yellow',
                                'Honorer' => 'badge-gray',
                                default   => 'badge-gray',
                            };
                        @endphp
                        <span class="badge {{ $badge }}">{{ $g->status_kepegawaian }}</span>
                    </td>
                    <td>{{ $g->no_telepon ?? '-' }}</td>
                    <td>
                        <a href="{{ route('admin.guru.edit', $g) }}"
                           class="btn btn-warning btn-sm">Edit</a>

                        <form method="POST"
                              action="{{ route('admin.guru.destroy', $g) }}"
                              style="display:inline"
                              onsubmit="return confirm('Yakin hapus guru ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" style="text-align:center;color:#9ca3af;padding:2rem">
                        Belum ada data guru.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{ $guru->links() }}

@endsection