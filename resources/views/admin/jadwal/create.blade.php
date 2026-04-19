@extends('layouts.app')
@section('title', 'Tambah Jadwal')

@section('content')

<div style="display:flex;align-items:center;gap:10px;margin-bottom:1rem">
    <a href="{{ route('admin.jadwal.index') }}" class="btn btn-secondary btn-sm">← Kembali</a>
    <h2 style="font-size:1.1rem;font-weight:700;">Tambah Jadwal Pelajaran</h2>
</div>

{{-- Alert bentrok --}}
@error('bentrok')
<div class="alert alert-error">⚠️ {{ $message }}</div>
@enderror

<div class="card">
    <form method="POST" action="{{ route('admin.jadwal.store') }}" id="formJadwal">
        @csrf

        <div style="display:grid;grid-template-columns:1fr 1fr;gap:0 1rem">

            <div class="form-group">
                <label>Kelas <span style="color:red">*</span></label>
                <select name="kelas_id" id="kelas_id" required
                        class="{{ $errors->has('kelas_id') ? 'is-invalid' : '' }}">
                    <option value="">-- Pilih Kelas --</option>
                    @foreach ($kelas as $k)
                        <option value="{{ $k->id }}"
                            {{ old('kelas_id', $defaultKelas) == $k->id ? 'selected' : '' }}>
                            {{ $k->nama_kelas }}
                        </option>
                    @endforeach
                </select>
                @error('kelas_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label>Guru Pengampu <span style="color:red">*</span></label>
                <select name="guru_id" id="guru_id" required
                        class="{{ $errors->has('guru_id') ? 'is-invalid' : '' }}">
                    <option value="">-- Pilih Guru --</option>
                    @foreach ($guru as $g)
                        <option value="{{ $g->id }}"
                            {{ old('guru_id') == $g->id ? 'selected' : '' }}>
                            {{ $g->nama_lengkap }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label>Hari <span style="color:red">*</span></label>
                <select name="hari" id="hari" required>
                    <option value="">-- Pilih Hari --</option>
                    @foreach ($hari as $h)
                        <option value="{{ $h }}"
                            {{ old('hari', $defaultHari) == $h ? 'selected' : '' }}>
                            {{ $h }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label>Jam Ke <span style="color:red">*</span></label>
                <select name="jam_ke" id="jam_ke" required>
                    <option value="">-- Pilih Jam --</option>
                    @foreach ($slots as $no => $slot)
                        <option value="{{ $no }}"
                            {{ old('jam_ke', $defaultJam) == $no ? 'selected' : '' }}>
                            Jam {{ $no }} ({{ $slot['mulai'] }} – {{ $slot['selesai'] }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group" style="grid-column:1/-1">
                <label>Mata Pelajaran <span style="color:red">*</span></label>
                <select name="mata_pelajaran" id="mata_pelajaran" required
                        onchange="setWarna(this.value)">
                    <option value="">-- Pilih Mata Pelajaran --</option>
                    @foreach ($mapel as $m)
                        <option value="{{ $m }}"
                            {{ old('mata_pelajaran') == $m ? 'selected' : '' }}>
                            {{ $m }}
                        </option>
                    @endforeach
                    <option value="__lain__">Lainnya...</option>
                </select>
                {{-- Input manual jika pilih "Lainnya" --}}
                <input type="text" id="mapel_manual" name="mapel_manual"
                       placeholder="Tulis nama mata pelajaran"
                       style="margin-top:6px;display:none">
            </div>

            <div class="form-group">
                <label>Warna Label</label>
                <div style="display:flex;align-items:center;gap:8px">
                    <input type="color" name="warna" id="warna"
                           value="{{ old('warna', '#3B82F6') }}"
                           style="width:48px;height:36px;padding:2px;border-radius:6px">
                    <span style="font-size:.8rem;color:#6b7280">
                        Warna otomatis dari mapel
                    </span>
                </div>
            </div>

            <div class="form-group">
                <label>Catatan</label>
                <input type="text" name="catatan"
                       value="{{ old('catatan') }}"
                       placeholder="Opsional">
            </div>

        </div>

        {{-- Indikator cek bentrok realtime --}}
        <div id="bentrok-info" style="display:none;padding:.6rem .875rem;
             border-radius:8px;margin-bottom:1rem;font-size:.875rem"></div>

        <div style="display:flex;gap:8px;margin-top:.5rem">
            <button type="submit" class="btn btn-primary" id="btnSimpan">
                💾 Simpan Jadwal
            </button>
            <a href="{{ route('admin.jadwal.index') }}" class="btn btn-secondary">Batal</a>
        </div>
    </form>
</div>

<script>
// ── Warna otomatis berdasarkan mata pelajaran ─────────────────
const warnaMapel = @json(\App\Enums\JamPelajaran::WARNA_MAPEL);

function setWarna(mapel) {
    const inputManual = document.getElementById('mapel_manual');
    const inputMapel  = document.querySelector('[name="mata_pelajaran"]');

    if (mapel === '__lain__') {
        inputManual.style.display = 'block';
        inputManual.required = true;
        // Agar value yang terkirim adalah dari input manual
        inputMapel.name = '_mapel_select';
        inputManual.name = 'mata_pelajaran';
    } else {
        inputManual.style.display = 'none';
        inputManual.required = false;
        inputMapel.name = 'mata_pelajaran';
        inputManual.name = 'mapel_manual';
    }

    if (warnaMapel[mapel]) {
        document.getElementById('warna').value = warnaMapel[mapel];
    }
}

// ── Cek bentrok realtime via AJAX ─────────────────────────────
const fields = ['kelas_id', 'guru_id', 'hari', 'jam_ke'];

fields.forEach(id => {
    document.getElementById(id)?.addEventListener('change', cekBentrokRealtime);
});

async function cekBentrokRealtime() {
    const kelasId = document.getElementById('kelas_id').value;
    const guruId  = document.getElementById('guru_id').value;
    const hari    = document.getElementById('hari').value;
    const jamKe   = document.getElementById('jam_ke').value;
    const info    = document.getElementById('bentrok-info');
    const btn     = document.getElementById('btnSimpan');

    // Hanya cek kalau semua field sudah diisi
    if (!kelasId || !guruId || !hari || !jamKe) return;

    try {
        const res = await fetch('{{ route("admin.jadwal.cek-bentrok") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
            },
            body: JSON.stringify({
                kelas_id: kelasId,
                guru_id:  guruId,
                hari:     hari,
                jam_ke:   jamKe,
            }),
        });

        const data = await res.json();
        info.style.display = 'block';

        if (data.bentrok) {
            info.style.background = '#fef2f2';
            info.style.border     = '1px solid #fecaca';
            info.style.color      = '#991b1b';
            info.innerHTML        = '⚠️ ' + data.pesan;
            btn.disabled          = true;
            btn.style.opacity     = '.5';
        } else {
            info.style.background = '#f0fdf4';
            info.style.border     = '1px solid #bbf7d0';
            info.style.color      = '#166534';
            info.innerHTML        = '✅ Jadwal tersedia, tidak ada bentrok.';
            btn.disabled          = false;
            btn.style.opacity     = '1';
        }
    } catch (e) {
        info.style.display = 'none';
    }
}
</script>

@endsection