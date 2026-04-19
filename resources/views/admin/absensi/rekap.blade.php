@extends('layouts.app')
@section('title', 'Rekap Absensi')

@section('content')

<h2 style="font-size:1.1rem;font-weight:700;margin-bottom:1rem">📊 Rekap Absensi</h2>

{{-- Filter --}}
<div class="card" style="margin-bottom:1rem">
    <form method="GET" style="display:flex;gap:8px;flex-wrap:wrap;align-items:flex-end">
        <div style="flex:1;min-width:130px">
            <label style="font-size:.8rem;font-weight:600;color:#374151;
                          display:block;margin-bottom:4px">Kelas</label>
            <select name="kelas_id" onchange="this.form.submit()">
                @foreach ($kelas as $k)
                    <option value="{{ $k->id }}"
                        {{ $kelasAktif?->id == $k->id ? 'selected' : '' }}>
                        {{ $k->nama_kelas }}
                    </option>
                @endforeach
            </select>
        </div>
        <div style="flex:1;min-width:120px">
            <label style="font-size:.8rem;font-weight:600;color:#374151;
                          display:block;margin-bottom:4px">Bulan</label>
            <select name="bulan" onchange="this.form.submit()">
                @foreach ($daftarBulan as $num => $nama)
                    <option value="{{ $num }}" {{ $bulan == $num ? 'selected' : '' }}>
                        {{ $nama }}
                    </option>
                @endforeach
            </select>
        </div>
        <div style="flex:0 0 80px">
            <label style="font-size:.8rem;font-weight:600;color:#374151;
                          display:block;margin-bottom:4px">Tahun</label>
            <input type="number" name="tahun" value="{{ $tahun }}"
                   min="2020" max="2030"
                   onchange="this.form.submit()"
                   style="text-align:center">
        </div>
    </form>
</div>

@if ($rekapData->isNotEmpty())

{{-- Ringkasan kelas --}}
@php
    $totalHadir = $rekapData->sum('hadir');
    $totalSakit = $rekapData->sum('sakit');
    $totalIzin  = $rekapData->sum('izin');
    $totalAlpha = $rekapData->sum('alpha');
    $rataHadir  = $rekapData->avg('persen_hadir');
@endphp

<div class="stats-grid" style="grid-template-columns:repeat(4,1fr)">
    <div class="stat-card">
        <div class="stat-num" style="color:#16a34a">{{ $totalHadir }}</div>
        <div class="stat-label">Total Hadir</div>
    </div>
    <div class="stat-card">
        <div class="stat-num" style="color:#d97706">{{ $totalSakit }}</div>
        <div class="stat-label">Total Sakit</div>
    </div>
    <div class="stat-card">
        <div class="stat-num" style="color:#2563eb">{{ $totalIzin }}</div>
        <div class="stat-label">Total Izin</div>
    </div>
    <div class="stat-card">
        <div class="stat-num" style="color:#dc2626">{{ $totalAlpha }}</div>
        <div class="stat-label">Total Alpha</div>
    </div>
</div>

{{-- Tabel rekap per siswa --}}
<div class="card" style="padding:0;overflow:hidden">
    <div style="padding:.875rem 1rem;border-bottom:1px solid #f3f4f6">
        <strong>{{ $kelasAktif->nama_kelas }}</strong>
        — {{ $daftarBulan[$bulan] }} {{ $tahun }}
        <span style="float:right;font-size:.8rem;color:#6b7280">
            Rata-rata kehadiran: <strong>{{ round($rataHadir) }}%</strong>
        </span>
    </div>
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Nama Siswa</th>
                    <th style="text-align:center;color:#16a34a">Hadir</th>
                    <th style="text-align:center;color:#d97706">Sakit</th>
                    <th style="text-align:center;color:#2563eb">Izin</th>
                    <th style="text-align:center;color:#dc2626">Alpha</th>
                    <th style="text-align:center">% Hadir</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach ($rekapData as $i => $row)
                <tr>
                    <td style="color:#9ca3af">{{ $i + 1 }}</td>
                    <td><strong>{{ $row['siswa']->nama_lengkap }}</strong></td>
                    <td style="text-align:center">
                        <span class="badge badge-green">{{ $row['hadir'] }}</span>
                    </td>
                    <td style="text-align:center">
                        <span class="badge badge-yellow">{{ $row['sakit'] }}</span>
                    </td>
                    <td style="text-align:center">
                        <span class="badge" style="background:#eff6ff;color:#1d4ed8">
                            {{ $row['izin'] }}
                        </span>
                    </td>
                    <td style="text-align:center">
                        <span class="badge badge-red">{{ $row['alpha'] }}</span>
                    </td>
                    <td style="text-align:center">
                        @php $persen = $row['persen_hadir']; @endphp
                        {{-- Progress bar kecil --}}
                        <div style="display:flex;align-items:center;gap:6px;
                                    justify-content:center">
                            <div style="width:48px;height:6px;background:#e5e7eb;
                                        border-radius:3px;overflow:hidden">
                                <div style="width:{{ $persen }}%;height:100%;
                                            border-radius:3px;
                                            background:{{ $persen >= 80 ? '#16a34a' : ($persen >= 60 ? '#d97706' : '#dc2626') }}">
                                </div>
                            </div>
                            <span style="font-size:.78rem;font-weight:600;
                                         color:{{ $persen >= 80 ? '#16a34a' : ($persen >= 60 ? '#d97706' : '#dc2626') }}">
                                {{ $persen }}%
                            </span>
                        </div>
                    </td>
                    <td>
                        <a href="{{ route('admin.absensi.siswa', [
                               $row['siswa']->id,
                               'bulan' => $bulan,
                               'tahun' => $tahun,
                           ]) }}"
                           class="btn btn-secondary btn-sm">Detail</a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

@else
<div class="card" style="text-align:center;color:#9ca3af;padding:2rem">
    Belum ada data absensi untuk periode ini.
</div>
@endif

@endsection