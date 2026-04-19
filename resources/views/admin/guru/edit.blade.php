@extends('layouts.app')
@section('title', 'Edit Guru')

@section('content')

<div style="display:flex;align-items:center;gap:10px;margin-bottom:1rem">
    <a href="{{ route('admin.guru.index') }}" class="btn btn-secondary btn-sm">← Kembali</a>
    <h2 style="font-size:1.1rem;font-weight:700;">
        Edit: {{ $guru->nama_lengkap }}
    </h2>
</div>

<div class="card">
    {{-- Gunakan method spoofing karena HTML form tidak support PUT --}}
    <form method="POST" action="{{ route('admin.guru.update', $guru) }}">
        @csrf
        @method('PUT')

        <div style="display:grid;grid-template-columns:1fr 1fr;gap:0 1rem">

            <div class="form-group" style="grid-column:1/-1">
                <label>Nama Lengkap <span style="color:red">*</span></label>
                <input type="text" name="nama_lengkap"
                       value="{{ old('nama_lengkap', $guru->nama_lengkap) }}"
                       class="{{ $errors->has('nama_lengkap') ? 'is-invalid' : '' }}">
                @error('nama_lengkap')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label>NISN <span style="color:red">*</span></label>
                <input type="text" name="nisn"
                       value="{{ old('nisn', $guru->nisn) }}"
                       class="{{ $errors->has('nisn') ? 'is-invalid' : '' }}"
                       maxlength="10">
                @error('nisn')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label>NIS</label>
                <input type="text" name="nis"
                       value="{{ old('nis', $guru->nis) }}">
            </div>

            <div class="form-group">
                <label>Jenis Kelamin <span style="color:red">*</span></label>
                <select name="jenis_kelamin">
                    <option value="L" {{ old('jenis_kelamin', $guru->jenis_kelamin) == 'L' ? 'selected' : '' }}>Laki-laki</option>
                    <option value="P" {{ old('jenis_kelamin', $guru->jenis_kelamin) == 'P' ? 'selected' : '' }}>Perempuan</option>
                </select>
            </div>

            <div class="form-group">
                <label>Kelas <span style="color:red">*</span></label>
                <select name="kelas_id">
                    @foreach ($kelas as $k)
                        <option value="{{ $k->id }}"
                            {{ old('kelas_id', $guru->kelas_id) == $k->id ? 'selected' : '' }}>
                            {{ $k->nama_kelas }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label>Tanggal Lahir</label>
                <input type="date" name="tanggal_lahir"
                       value="{{ old('tanggal_lahir', $guru->tanggal_lahir?->format('Y-m-d')) }}">
            </div>

            <div class="form-group">
                <label>Tempat Lahir</label>
                <input type="text" name="tempat_lahir"
                       value="{{ old('tempat_lahir', $guru->tempat_lahir) }}">
            </div>

            <div class="form-group">
                <label>Agama</label>
                <select name="agama">
                    @foreach (['Islam','Kristen','Katolik','Hindu','Buddha','Konghucu'] as $agama)
                        <option value="{{ $agama }}"
                            {{ old('agama', $guru->agama) == $agama ? 'selected' : '' }}>
                            {{ $agama }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group" style="grid-column:1/-1">
                <label>Alamat</label>
                <textarea name="alamat" rows="2">{{ old('alamat', $guru->alamat) }}</textarea>
            </div>

            <div class="form-group">
                <label>Nama Wali</label>
                <input type="text" name="nama_wali"
                       value="{{ old('nama_wali', $guru->nama_wali) }}">
            </div>

            <div class="form-group">
                <label>No. Telepon Wali</label>
                <input type="tel" name="no_telepon_wali"
                       value="{{ old('no_telepon_wali', $guru->no_telepon_wali) }}">
            </div>

            <div class="form-group">
                <label>Status</label>
                <select name="status">
                    @foreach (['aktif','lulus','pindah','keluar'] as $status)
                        <option value="{{ $status }}"
                            {{ old('status', $guru->status) == $status ? 'selected' : '' }}>
                            {{ ucfirst($status) }}
                        </option>
                    @endforeach
                </select>
            </div>

        </div>

        <div style="display:flex;gap:8px;margin-top:.5rem">
            <button type="submit" class="btn btn-primary">💾 Simpan Perubahan</button>
            <a href="{{ route('admin.guru.index') }}" class="btn btn-secondary">Batal</a>
        </div>
    </form>
</div>

@endsection