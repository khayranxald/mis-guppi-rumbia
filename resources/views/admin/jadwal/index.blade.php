@extends('layouts.app')
@section('title', 'Jadwal Pelajaran')

@section('content')

<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1rem">
    <h2 style="font-size:1.1rem;font-weight:700;">Jadwal Pelajaran</h2>
    <a href="{{ route('admin.jadwal.create', ['kelas_id' => $kelasAktif?->id]) }}"
       class="btn btn-primary btn-sm">+ Tambah</a>
</div>

{{-- Pilih Kelas --}}
<div class="card" style="padding:.75rem 1rem;margin-bottom:1rem">
    <form method="GET" style="display:flex;gap:8px;flex-wrap:wrap;align-items:center">
        <select name="kelas_id" onchange="this.form.submit()"
                style="flex:1;min-width:140px">
            @foreach ($kelas as $k)
                <option value="{{ $k->id }}"
                    {{ $kelasAktif?->id == $k->id ? 'selected' : '' }}>
                    {{ $k->nama_kelas }} ({{ $k->tahun_ajaran }})
                </option>
            @endforeach
        </select>
        <input type="hidden" name="tahun_ajaran" value="{{ $tahunAjaran }}">
    </form>
</div>

@if ($kelasAktif)

{{-- Info kelas --}}
<div style="font-size:.8rem;color:#6b7280;margin-bottom:.75rem">
    Wali Kelas:
    <strong>{{ $kelasAktif->waliKelas?->nama_lengkap ?? 'Belum ditentukan' }}</strong>
    &nbsp;|&nbsp; Kapasitas: {{ $kelasAktif->kapasitas }} siswa
</div>

{{-- Grid Jadwal --}}
@php
    $slots = \App\Enums\JamPelajaran::SLOTS;
    $hari  = \App\Enums\JamPelajaran::HARI;
@endphp

<div class="card" style="padding:0;overflow:hidden">
<div style="overflow-x:auto">
<table style="width:100%;border-collapse:collapse;min-width:480px;font-size:.8rem">
    <thead>
        <tr style="background:#f9fafb">
            <th style="padding:8px 10px;text-align:left;border-bottom:2px solid #e5e7eb;
                       min-width:80px;color:#374151">Jam</th>
            @foreach ($hari as $h)
            <th style="padding:8px 6px;text-align:center;border-bottom:2px solid #e5e7eb;
                       color:#374151">{{ $h }}</th>
            @endforeach
        </tr>
    </thead>
    <tbody>
        @foreach ($slots as $jamKe => $slot)
        <tr style="border-bottom:1px solid #f3f4f6">
            {{-- Kolom jam --}}
            <td style="padding:6px 10px;color:#6b7280;white-space:nowrap;
                       background:#fafafa;border-right:1px solid #f3f4f6">
                <div style="font-weight:600;color:#374151">Jam {{ $jamKe }}</div>
                <div style="font-size:.72rem">{{ $slot['mulai'] }}–{{ $slot['selesai'] }}</div>
            </td>

            {{-- Sel per hari --}}
            @foreach ($hari as $h)
            @php $jadwal = $grid[$h][$jamKe] ?? null; @endphp
            <td style="padding:4px;text-align:center;vertical-align:top">

                @if ($jadwal)
                {{-- Sel terisi --}}
                <div style="background:{{ $jadwal->warna }}18;
                            border-left:3px solid {{ $jadwal->warna }};
                            border-radius:6px;padding:5px 6px;text-align:left;
                            position:relative">
                    <div style="font-weight:600;font-size:.78rem;
                                color:{{ $jadwal->warna }}">
                        {{ $jadwal->mata_pelajaran }}
                    </div>
                    <div style="font-size:.7rem;color:#6b7280;margin-top:1px">
                        {{ $jadwal->guru?->nama_lengkap }}
                    </div>
                    {{-- Tombol aksi kecil --}}
                    <div style="display:flex;gap:3px;margin-top:4px">
                        <a href="{{ route('admin.jadwal.edit', $jadwal) }}"
                           style="font-size:.65rem;padding:2px 6px;background:#f3f4f6;
                                  border-radius:4px;text-decoration:none;color:#374151">
                            Edit
                        </a>
                        <form method="POST"
                              action="{{ route('admin.jadwal.destroy', $jadwal) }}"
                              onsubmit="return confirm('Hapus jadwal ini?')">
                            @csrf @method('DELETE')
                            <button type="submit"
                                    style="font-size:.65rem;padding:2px 6px;background:#fee2e2;
                                           border:none;border-radius:4px;color:#991b1b;cursor:pointer">
                                Hapus
                            </button>
                        </form>
                    </div>
                </div>

                @else
                {{-- Sel kosong — klik untuk tambah --}}
                <a href="{{ route('admin.jadwal.create', [
                       'kelas_id' => $kelasAktif->id,
                       'hari'     => $h,
                       'jam_ke'   => $jamKe,
                   ]) }}"
                   style="display:block;min-height:52px;border:1.5px dashed #e5e7eb;
                          border-radius:6px;color:#d1d5db;font-size:1.2rem;
                          display:flex;align-items:center;justify-content:center;
                          text-decoration:none;transition:all .15s"
                   onmouseover="this.style.borderColor='#93c5fd';this.style.color='#3b82f6'"
                   onmouseout="this.style.borderColor='#e5e7eb';this.style.color='#d1d5db'">
                    +
                </a>
                @endif

            </td>
            @endforeach
        </tr>
        @endforeach
    </tbody>
</table>
</div>
</div>

{{-- Legenda --}}
<div style="margin-top:.75rem;font-size:.75rem;color:#6b7280">
    💡 Klik sel <strong>+</strong> untuk menambah jadwal di slot tersebut.
</div>

@else
<div class="card" style="text-align:center;color:#9ca3af;padding:2rem">
    Belum ada kelas terdaftar. Silakan tambah kelas terlebih dahulu.
</div>
@endif

@endsection