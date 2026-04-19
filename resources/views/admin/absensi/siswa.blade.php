@extends('layouts.app')
@section('title', 'Detail Absensi Siswa')

@section('content')

<div style="display:flex;align-items:center;gap:10px;margin-bottom:1rem">
    <a href="javascript:history.back()" class="btn btn-secondary btn-sm">← Kembali</a>
    <div>
        <h2 style="font-size:1.05rem;font-weight:700;margin:0">
            {{ $siswa->nama_lengkap }}
        </h2>
        <div style="font-size:.8rem;color:#6b7280">
            NISN: {{ $siswa->nisn }} | Kelas: {{ $siswa->kelas?->nama_kelas }}
        </div>
    </div>
</div>

{{-- Filter bulan --}}
<div class="card" style="padding:.75rem 1rem;margin-bottom:1rem">
    <form method="GET" style="display:flex;gap:8px;align-items:flex-end">
        <div style="flex:1">
            <select name="bulan" onchange="this.form.submit()">
                @foreach ($daftarBulan as $num => $nama)
                    <option value="{{ $num }}" {{ $bulan == $num ? 'selected' : '' }}>
                        {{ $nama }}
                    </option>
                @endforeach
            </select>
        </div>
        <div style="flex:0 0 80px">
            <input type="number" name="tahun" value="{{ $tahun }}"
                   min="2020" max="2030" onchange="this.form.submit()"
                   style="text-align:center">
        </div>
    </form>
</div>

{{-- Ringkasan bulan ini --}}
<div class="stats-grid" style="grid-template-columns:repeat(4,1fr);margin-bottom:1rem">
    <div class="stat-card">
        <div class="stat-num" style="color:#16a34a">{{ $ringkasan['hadir'] }}</div>
        <div class="stat-label">Hadir</div>
    </div>
    <div class="stat-card">
        <div class="stat-num" style="color:#d97706">{{ $ringkasan['sakit'] }}</div>
        <div class="stat-label">Sakit</div>
    </div>
    <div class="stat-card">
        <div class="stat-num" style="color:#2563eb">{{ $ringkasan['izin'] }}</div>
        <div class="stat-label">Izin</div>
    </div>
    <div class="stat-card">
        <div class="stat-num" style="color:#dc2626">{{ $ringkasan['alpha'] }}</div>
        <div class="stat-label">Alpha</div>
    </div>
</div>

{{-- Detail harian --}}
@if ($detail->isNotEmpty())
<div class="card" style="padding:0;overflow:hidden">
    <div style="padding:.875rem 1rem;border-bottom:1px solid #f3f4f6;font-weight:600">
        Riwayat Kehadiran — {{ $daftarBulan[$bulan] }} {{ $tahun }}
    </div>
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Tanggal</th>
                    <th>Hari</th>
                    <th style="text-align:center">Status</th>
                    <th>Keterangan</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($detail as $d)
                @php $label = $d->label_status; @endphp
                <tr>
                    <td>{{ $d->tanggal->format('d/m/Y') }}</td>
                    <td style="color:#6b7280">
                        {{ $d->tanggal->isoFormat('dddd') }}
                    </td>
                    <td style="text-align:center">
                        <span class="badge {{ $label['warna'] }}">
                            {{ $label['label'] }}
                        </span>
                    </td>
                    <td style="color:#6b7280;font-size:.85rem">
                        {{ $d->keterangan ?? '-' }}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@else
<div class="card" style="text-align:center;color:#9ca3af;padding:2rem">
    Belum ada data absensi bulan ini.
</div>
@endif

@endsection