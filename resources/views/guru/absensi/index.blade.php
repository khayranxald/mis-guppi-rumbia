@extends('layouts.app')
@section('title', 'Absensi Siswa')

@section('content')

{{-- Badge data pending --}}
<div id="badge-pending"
     style="display:none;background:#fef3c7;border:1px solid #fcd34d;
            color:#92400e;padding:.6rem 1rem;border-radius:8px;
            margin-bottom:1rem;font-size:.875rem;font-weight:600">
    ⏳ Ada data absensi yang belum tersimpan ke server
</div>

{{-- Notif kalau baru simpan offline --}}
@if(request('offline') == '1')
<div class="alert" style="background:#fef3c7;border:1px solid #fcd34d;color:#92400e">
    📱 Absensi disimpan di perangkat. Akan otomatis dikirim saat ada internet.
</div>
@endif

{{-- Notif kalau baru simpan online --}}
@if(request('saved') == '1')
<div class="alert alert-success">
    ✅ Absensi berhasil disimpan ke server.
</div>
@endif

<h2 style="font-size:1.1rem;font-weight:700;margin-bottom:1rem">📋 Absensi Siswa</h2>

{{-- Form Pilih Kelas & Tanggal --}}
<div class="card">
    <form method="GET" action="{{ route('guru.absensi.index') }}">
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:0 1rem">
            <div class="form-group">
                <label>Pilih Kelas</label>
                <select name="kelas_id" onchange="this.form.submit()">
                    @foreach ($kelas as $k)
                        <option value="{{ $k->id }}"
                            {{ $kelasAktif?->id == $k->id ? 'selected' : '' }}>
                            {{ $k->nama_kelas }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label>Tanggal</label>
                <input type="date" name="tanggal"
                       value="{{ $tanggal }}"
                       max="{{ today()->toDateString() }}"
                       onchange="this.form.submit()">
            </div>
        </div>
    </form>
</div>

@if ($kelasAktif)

{{-- Status absensi hari ini --}}
<div style="margin-bottom:1rem">
    @if ($sudahDiabsen)
        <div class="alert alert-success" style="display:flex;justify-content:space-between;align-items:center">
            <span>✅ Absensi tanggal ini sudah diinput</span>
            <a href="{{ route('guru.absensi.input', ['kelas_id' => $kelasAktif->id, 'tanggal' => $tanggal]) }}"
               class="btn btn-warning btn-sm">✏️ Edit</a>
        </div>
    @else
        <div class="alert" style="background:#eff6ff;border:1px solid #bfdbfe;
             color:#1e40af;display:flex;justify-content:space-between;align-items:center">
            <span>⏳ Absensi belum diinput untuk tanggal ini</span>
            <a href="{{ route('guru.absensi.input', ['kelas_id' => $kelasAktif->id, 'tanggal' => $tanggal]) }}"
               class="btn btn-primary btn-sm">+ Input Sekarang</a>
        </div>
    @endif
</div>

{{-- Daftar absensi (view only) --}}
@if ($dataAbsensi->isNotEmpty())
<div class="card" style="padding:0;overflow:hidden">
    <div style="padding:.875rem 1rem;border-bottom:1px solid #f3f4f6;
                font-size:.875rem;font-weight:600;color:#374151">
        {{ $kelasAktif->nama_kelas }} —
        {{ \Carbon\Carbon::parse($tanggal)->isoFormat('dddd, D MMMM Y') }}
        <span style="float:right;font-weight:400;color:#6b7280">
            {{ $dataAbsensi->count() }} siswa
        </span>
    </div>

    {{-- Ringkasan kecil --}}
    @php
        $hadir = $dataAbsensi->filter(fn($d) => $d['absensi']?->status === 'hadir')->count();
        $sakit = $dataAbsensi->filter(fn($d) => $d['absensi']?->status === 'sakit')->count();
        $izin  = $dataAbsensi->filter(fn($d) => $d['absensi']?->status === 'izin')->count();
        $alpha = $dataAbsensi->filter(fn($d) => $d['absensi']?->status === 'alpha')->count();
        $belum = $dataAbsensi->filter(fn($d) => $d['absensi'] === null)->count();
    @endphp
    <div style="display:flex;gap:8px;padding:.6rem 1rem;
                background:#f9fafb;flex-wrap:wrap;font-size:.78rem">
        <span class="badge badge-green">Hadir: {{ $hadir }}</span>
        <span class="badge badge-yellow">Sakit: {{ $sakit }}</span>
        <span class="badge" style="background:#eff6ff;color:#1d4ed8">Izin: {{ $izin }}</span>
        <span class="badge badge-red">Alpha: {{ $alpha }}</span>
        @if($belum > 0)
        <span class="badge badge-gray">Belum: {{ $belum }}</span>
        @endif
    </div>

    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th style="width:36px">#</th>
                    <th>Nama Siswa</th>
                    <th style="text-align:center">Status</th>
                    <th>Keterangan</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($dataAbsensi as $i => $d)
                <tr>
                    <td style="color:#9ca3af">{{ $i + 1 }}</td>
                    <td><strong>{{ $d['siswa']->nama_lengkap }}</strong></td>
                    <td style="text-align:center">
                        @if ($d['absensi'])
                            @php $label = $d['absensi']->label_status; @endphp
                            <span class="badge {{ $label['warna'] }}">
                                {{ $label['label'] }}
                            </span>
                        @else
                            <span class="badge badge-gray">—</span>
                        @endif
                    </td>
                    <td style="color:#6b7280;font-size:.8rem">
                        {{ $d['absensi']?->keterangan ?? '-' }}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif

@endif

<script src="/js/absensi-offline.js"></script>
<script>
    const CSRF_TOKEN = '{{ csrf_token() }}';

    // Tampilkan badge kalau ada pending
    async function updateBadgePending() {
        const jumlah = await hitungPending();
        const badge  = document.getElementById('badge-pending');
        if (jumlah > 0) {
            badge.textContent = `⏳ ${jumlah} absensi belum tersync ke server`;
            badge.style.display = 'block';
        } else {
            badge.style.display = 'none';
        }
    }

    // Setup auto sync
    setupAutoSync(CSRF_TOKEN, () => updateBadgePending());
    updateBadgePending();

    // Tombol sync manual (opsional)
    async function syncManual() {
        if (!navigator.onLine) {
            alert('Tidak ada koneksi internet.');
            return;
        }
        tampilkanNotifSync('syncing', await hitungPending());
        const hasil = await syncKeServer(CSRF_TOKEN);
        tampilkanNotifSync('success', hasil.berhasil);
        updateBadgePending();
    }
</script>

@endsection