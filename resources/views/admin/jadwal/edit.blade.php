@extends('layouts.app')
@section('title', 'Edit Jadwal')

@section('content')

<div style="display:flex;align-items:center;gap:10px;margin-bottom:1rem">
    <a href="{{ route('admin.jadwal.index', ['kelas_id' => $jadwal->kelas_id]) }}"
       class="btn btn-secondary btn-sm">← Kembali</a>
    <h2 style="font-size:1.1rem;font-weight:700;">Edit Jadwal</h2>
</div>

@error('bentrok')
<div class="alert alert-error">⚠️ {{ $message }}</div>
@enderror

<div class="card">
    <form method="POST" action="{{ route('admin.jadwal.update', $jadwal) }}" id="formJadwal">
        @csrf
        @method('PUT')

        {{-- Kirim except_id untuk AJAX cek bentrok --}}
        <input type="hidden" id="except_id" value="{{ $jadwal->id }}">

        <div style="display:grid;grid-template-columns:1fr 1fr;gap:0 1rem">

            <div class="form-group">
                <label>Kelas <span style="color:red">*</span></label>
                <select name="kelas_id" id="kelas_id" required>
                    @foreach ($kelas as $k)
                        <option value="{{ $k->id }}"
                            {{ old('kelas_id', $jadwal->kelas_id) == $k->id ? 'selected' : '' }}>
                            {{ $k->nama_kelas }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label>Guru Pengampu <span style="color:red">*</span></label>
                <select name="guru_id" id="guru_id" required>
                    @foreach ($guru as $g)
                        <option value="{{ $g->id }}"
                            {{ old('guru_id', $jadwal->guru_id) == $g->id ? 'selected' : '' }}>
                            {{ $g->nama_lengkap }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label>Hari <span style="color:red">*</span></label>
                <select name="hari" id="hari" required>
                    @foreach ($hari as $h)
                        <option value="{{ $h }}"
                            {{ old('hari', $jadwal->hari) == $h ? 'selected' : '' }}>
                            {{ $h }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label>Jam Ke <span style="color:red">*</span></label>
                <select name="jam_ke" id="jam_ke" required>
                    @foreach ($slots as $no => $slot)
                        <option value="{{ $no }}"
                            {{ old('jam_ke', $jadwal->jam_ke) == $no ? 'selected' : '' }}>
                            Jam {{ $no }} ({{ $slot['mulai'] }} – {{ $slot['selesai'] }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group" style="grid-column:1/-1">
                <label>Mata Pelajaran <span style="color:red">*</span></label>
                <select name="mata_pelajaran" id="mata_pelajaran" required
                        onchange="setWarna(this.value)">
                    @foreach ($mapel as $m)
                        <option value="{{ $m }}"
                            {{ old('mata_pelajaran', $jadwal->mata_pelajaran) == $m ? 'selected' : '' }}>
                            {{ $m }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label>Warna Label</label>
                <input type="color" name="warna"
                       value="{{ old('warna', $jadwal->warna) }}"
                       style="width:48px;height:36px;padding:2px;border-radius:6px">
            </div>

            <div class="form-group">
                <label>Catatan</label>
                <input type="text" name="catatan"
                       value="{{ old('catatan', $jadwal->catatan) }}">
            </div>
        </div>

        <div id="bentrok-info" style="display:none;padding:.6rem .875rem;
             border-radius:8px;margin-bottom:1rem;font-size:.875rem"></div>

        <div style="display:flex;gap:8px;margin-top:.5rem">
            <button type="submit" class="btn btn-primary" id="btnSimpan">
                💾 Simpan Perubahan
            </button>
            <a href="{{ route('admin.jadwal.index', ['kelas_id' => $jadwal->kelas_id]) }}"
               class="btn btn-secondary">Batal</a>
        </div>
    </form>
</div>

<script>
const warnaMapel = @json(\App\Enums\JamPelajaran::WARNA_MAPEL);
function setWarna(mapel) {
    if (warnaMapel[mapel]) document.querySelector('[name="warna"]').value = warnaMapel[mapel];
}

const fields = ['kelas_id', 'guru_id', 'hari', 'jam_ke'];
fields.forEach(id => {
    document.getElementById(id)?.addEventListener('change', cekBentrokRealtime);
});

async function cekBentrokRealtime() {
    const kelasId  = document.getElementById('kelas_id').value;
    const guruId   = document.getElementById('guru_id').value;
    const hari     = document.getElementById('hari').value;
    const jamKe    = document.getElementById('jam_ke').value;
    const exceptId = document.getElementById('except_id').value;
    const info     = document.getElementById('bentrok-info');
    const btn      = document.getElementById('btnSimpan');

    if (!kelasId || !guruId || !hari || !jamKe) return;

    const res  = await fetch('{{ route("admin.jadwal.cek-bentrok") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
        },
        body: JSON.stringify({
            kelas_id: kelasId, guru_id: guruId,
            hari: hari, jam_ke: jamKe,
            except_id: exceptId,
        }),
    });
    const data = await res.json();
    info.style.display = 'block';

    if (data.bentrok) {
        info.style.cssText = 'display:block;padding:.6rem .875rem;border-radius:8px;' +
            'margin-bottom:1rem;font-size:.875rem;background:#fef2f2;' +
            'border:1px solid #fecaca;color:#991b1b';
        info.innerHTML = '⚠️ ' + data.pesan;
        btn.disabled = true; btn.style.opacity = '.5';
    } else {
        info.style.cssText = 'display:block;padding:.6rem .875rem;border-radius:8px;' +
            'margin-bottom:1rem;font-size:.875rem;background:#f0fdf4;' +
            'border:1px solid #bbf7d0;color:#166534';
        info.innerHTML = '✅ Tidak ada bentrok.';
        btn.disabled = false; btn.style.opacity = '1';
    }
}
</script>

@endsection