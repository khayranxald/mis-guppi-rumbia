@extends('layouts.app')
@section('title', 'Input Absensi')

@section('content')

{{-- ✅ FIX 2: Tambahkan badge-pending --}}
<div id="badge-pending"
     style="display:none;background:#fef3c7;border:1px solid #fcd34d;
            color:#92400e;padding:.6rem 1rem;border-radius:8px;
            margin-bottom:1rem;font-size:.875rem;font-weight:600">
    ⏳ Ada data absensi offline yang belum tersync ke server
</div>

<div style="display:flex;align-items:center;gap:10px;margin-bottom:1rem">
    <a href="{{ route('guru.absensi.index', ['kelas_id' => $kelas->id, 'tanggal' => $tanggal]) }}"
       class="btn btn-secondary btn-sm">← Kembali</a>
    <div>
        <h2 style="font-size:1.05rem;font-weight:700;margin:0">
            Input Absensi — {{ $kelas->nama_kelas }}
        </h2>
        <div style="font-size:.8rem;color:#6b7280">
            {{ \Carbon\Carbon::parse($tanggal)->isoFormat('dddd, D MMMM Y') }}
        </div>
    </div>
</div>

{{-- Tombol tandai semua cepat --}}
<div class="card" style="padding:.75rem 1rem;margin-bottom:.75rem">
    <div style="font-size:.8rem;color:#6b7280;margin-bottom:.5rem;font-weight:600">
        Tandai semua sebagai:
    </div>
    <div style="display:flex;gap:8px;flex-wrap:wrap">
        <button type="button" class="btn btn-sm"
                style="background:#dcfce7;color:#166534"
                onclick="tandaiSemua('hadir')">✅ Semua Hadir</button>
        <button type="button" class="btn btn-sm"
                style="background:#fef9c3;color:#854d0e"
                onclick="tandaiSemua('sakit')">🤒 Semua Sakit</button>
        <button type="button" class="btn btn-sm"
                style="background:#eff6ff;color:#1e40af"
                onclick="tandaiSemua('izin')">📝 Semua Izin</button>
        <button type="button" class="btn btn-sm"
                style="background:#fee2e2;color:#991b1b"
                onclick="tandaiSemua('alpha')">❌ Semua Alpha</button>
    </div>
</div>

<form method="POST" action="{{ route('guru.absensi.simpan') }}" id="formAbsensi">
    @csrf
    <input type="hidden" name="kelas_id" value="{{ $kelas->id }}">
    <input type="hidden" name="tanggal"  value="{{ $tanggal }}">

    <div class="card" style="padding:0;overflow:hidden">
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th style="width:36px">#</th>
                        <th>Nama Siswa</th>
                        @foreach ($daftarStatus as $val => $label)
                        <th style="text-align:center;min-width:60px">
                            <span style="cursor:pointer;font-size:.75rem"
                                  onclick="tandaiSemua('{{ $val }}')"
                                  title="Tandai semua {{ $label }}">
                                {{ $label }}
                            </span>
                        </th>
                        @endforeach
                        <th style="min-width:120px">Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($dataAbsensi as $i => $d)
                    @php
                        $statusAwal     = $d['absensi']?->status ?? 'hadir';
                        $keteranganAwal = $d['absensi']?->keterangan ?? '';
                        $siswaId        = $d['siswa']->id;
                    @endphp
                    <tr id="row-{{ $siswaId }}" data-status="{{ $statusAwal }}">
                        <td style="color:#9ca3af">{{ $i + 1 }}</td>
                        <td>
                            <strong>{{ $d['siswa']->nama_lengkap }}</strong>
                            <div style="font-size:.72rem;color:#9ca3af">
                                {{ $d['siswa']->nisn }}
                            </div>
                        </td>

                        @foreach ($daftarStatus as $val => $label)
                        <td style="text-align:center">
                            <input type="radio"
                                   name="absensi[{{ $siswaId }}][status]"
                                   value="{{ $val }}"
                                   class="radio-status"
                                   data-row="{{ $siswaId }}"
                                   {{ $statusAwal === $val ? 'checked' : '' }}
                                   onchange="updateRow({{ $siswaId }}, '{{ $val }}')"
                                   style="width:18px;height:18px;accent-color:
                                       {{ match($val) {
                                           'hadir' => '#16a34a',
                                           'sakit' => '#d97706',
                                           'izin'  => '#2563eb',
                                           'alpha' => '#dc2626',
                                           default => '#6b7280'
                                       } }}">
                        </td>
                        @endforeach

                        <td>
                            <input type="text"
                                   name="absensi[{{ $siswaId }}][keterangan]"
                                   value="{{ $keteranganAwal }}"
                                   placeholder="Opsional"
                                   style="padding:4px 8px;font-size:.8rem;
                                          border-radius:6px;border:1.5px solid #e5e7eb;
                                          width:100%">
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- Sticky tombol simpan --}}
    <div style="position:sticky;bottom:70px;padding:.75rem 0;
                background:rgba(243,244,246,.95);backdrop-filter:blur(4px)">
        <div style="display:flex;gap:8px;max-width:768px;margin:0 auto">
            {{-- ✅ FIX 1: Tambahkan id="btnSimpan" --}}
            <button type="submit"
                    id="btnSimpan"
                    class="btn btn-primary"
                    style="flex:1;justify-content:center">
                💾 Simpan Absensi ({{ $dataAbsensi->count() }} Siswa)
            </button>
        </div>
    </div>
</form>

<script>
const warnaBaris = {
    hadir: '',
    sakit: '#fffbeb',
    izin:  '#eff6ff',
    alpha: '#fef2f2',
};

function updateRow(siswaId, status) {
    const row = document.getElementById('row-' + siswaId);
    row.style.background = warnaBaris[status] ?? '';
    row.dataset.status   = status;
}

document.querySelectorAll('tbody tr').forEach(row => {
    const status = row.dataset.status;
    if (status) row.style.background = warnaBaris[status] ?? '';
});

function tandaiSemua(status) {
    document.querySelectorAll(`input[type=radio][value="${status}"]`)
        .forEach(radio => {
            radio.checked = true;
            updateRow(parseInt(radio.dataset.row), status);
        });
}
</script>

{{-- Load offline script SEBELUM script yang memanggilnya --}}
<script src="/js/absensi-offline.js"></script>

<script>
const CSRF_TOKEN = '{{ csrf_token() }}';
const KELAS_ID   = {{ $kelas->id }};
const TANGGAL    = '{{ $tanggal }}';

setupAutoSync(CSRF_TOKEN, () => updateBadgePending());

async function updateBadgePending() {
    const jumlah = await hitungPending();
    const badge  = document.getElementById('badge-pending');
    if (!badge) return;
    badge.style.display = jumlah > 0 ? 'block' : 'none';
    if (jumlah > 0) {
        badge.textContent = `⏳ ${jumlah} data absensi belum tersync ke server`;
    }
}
updateBadgePending();

document.getElementById('formAbsensi').addEventListener('submit', async function(e) {
    e.preventDefault();

    const btnSave = document.getElementById('btnSimpan'); // ✅ sekarang ketemu

    // Kumpulkan data
    const absensiData = {};
    document.querySelectorAll('input[type=radio]:checked').forEach(radio => {
        const siswaId = radio.name.match(/\[(\d+)\]/)?.[1];
        if (!siswaId) return;

        absensiData[siswaId] = {
            status:     radio.value,
            keterangan: document.querySelector(
                `input[name="absensi[${siswaId}][keterangan]"]`
            )?.value ?? '',
        };
    });

    // Validasi
    const totalSiswa   = document.querySelectorAll('tbody tr').length;
    const totalDipilih = Object.keys(absensiData).length;

    if (totalDipilih < totalSiswa) {
        alert(`Masih ada ${totalSiswa - totalDipilih} siswa yang belum diisi.`);
        return;
    }

    if (navigator.onLine) {
        // ── Online: kirim ke server ─────────────────────────
        btnSave.textContent = '⏳ Menyimpan...';
        btnSave.disabled    = true;

        try {
            const response = await fetch('/api/absensi/sync', {
                method:  'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN':  CSRF_TOKEN,
                    'Accept':        'application/json',
                },
                body: JSON.stringify({
                    kelas_id: KELAS_ID,
                    tanggal:  TANGGAL,
                    absensi:  absensiData,
                }),
            });

            if (response.ok) {
                window.location.href =
                    `/guru/absensi?kelas_id=${KELAS_ID}&tanggal=${TANGGAL}&saved=1`;
            } else {
                throw new Error('Server error: ' + response.status);
            }
        } catch (err) {
            console.error('Gagal online, fallback offline:', err);
            btnSave.textContent = '💾 Simpan Absensi';
            btnSave.disabled    = false;
            await simpanOfflineAndRedirect(absensiData);
        }

    } else {
        // ── Offline: simpan ke IndexedDB ────────────────────
        await simpanOfflineAndRedirect(absensiData);
    }
});

async function simpanOfflineAndRedirect(absensiData) {
    try {
        await simpanAbsensiOffline({
            kelas_id: KELAS_ID,
            tanggal:  TANGGAL,
            absensi:  absensiData,
        });
        window.location.href =
            `/guru/absensi?kelas_id=${KELAS_ID}&tanggal=${TANGGAL}&offline=1`;
    } catch (err) {
        alert('Gagal menyimpan data. Silakan coba lagi.');
        console.error(err);
    }
}
</script>

@endsection