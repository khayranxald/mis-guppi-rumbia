<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Siswa;
use App\Models\Kelas;
use Illuminate\Http\Request;

class SiswaController extends Controller
{
    /**
     * Halaman daftar siswa
     * URL: GET /admin/siswa
     */
    public function index(Request $request)
    {
        $query = Siswa::with('kelas')->latest();

        // Filter pencarian by nama atau NISN
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama_lengkap', 'like', "%{$search}%")
                  ->orWhere('nisn', 'like', "%{$search}%");
            });
        }

        // Filter by kelas
        if ($request->filled('kelas_id')) {
            $query->where('kelas_id', $request->kelas_id);
        }

        $siswa  = $query->paginate(15)->withQueryString();
        $kelas  = Kelas::orderBy('tingkat')->get();

        return view('admin.siswa.index', compact('siswa', 'kelas'));
    }

    /**
     * Halaman form tambah siswa
     * URL: GET /admin/siswa/create
     */
    public function create()
    {
        $kelas = Kelas::orderBy('tingkat')->get();
        return view('admin.siswa.create', compact('kelas'));
    }

    /**
     * Simpan siswa baru ke database
     * URL: POST /admin/siswa
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nisn'            => 'required|digits:10|unique:siswa,nisn',
            'nis'             => 'nullable|string|max:8',
            'nama_lengkap'    => 'required|string|max:100',
            'jenis_kelamin'   => 'required|in:L,P',
            'kelas_id'        => 'required|exists:kelas,id',
            'tanggal_lahir'   => 'nullable|date|before:today',
            'tempat_lahir'    => 'nullable|string|max:50',
            'agama'           => 'nullable|string|max:20',
            'alamat'          => 'nullable|string',
            'nama_wali'       => 'nullable|string|max:100',
            'pekerjaan_wali'  => 'nullable|string|max:50',
            'no_telepon_wali' => 'nullable|string|max:15',
            'status'          => 'required|in:aktif,lulus,pindah,keluar',
        ], [
            // Pesan error dalam Bahasa Indonesia
            'nisn.required'          => 'NISN wajib diisi.',
            'nisn.digits'            => 'NISN harus 10 digit angka.',
            'nisn.unique'            => 'NISN sudah terdaftar.',
            'nama_lengkap.required'  => 'Nama lengkap wajib diisi.',
            'jenis_kelamin.required' => 'Jenis kelamin wajib dipilih.',
            'kelas_id.required'      => 'Kelas wajib dipilih.',
            'kelas_id.exists'        => 'Kelas tidak ditemukan.',
            'tanggal_lahir.before'   => 'Tanggal lahir tidak valid.',
        ]);

        Siswa::create($validated);

        return redirect()
            ->route('admin.siswa.index')
            ->with('success', 'Data siswa berhasil ditambahkan.');
    }

    /**
     * Halaman form edit siswa
     * URL: GET /admin/siswa/{siswa}/edit
     */
    public function edit(Siswa $siswa)
    {
        $kelas = Kelas::orderBy('tingkat')->get();
        return view('admin.siswa.edit', compact('siswa', 'kelas'));
    }

    /**
     * Update data siswa
     * URL: PUT /admin/siswa/{siswa}
     */
    public function update(Request $request, Siswa $siswa)
    {
        $validated = $request->validate([
            'nisn'            => 'required|digits:10|unique:siswa,nisn,' . $siswa->id,
            'nis'             => 'nullable|string|max:8',
            'nama_lengkap'    => 'required|string|max:100',
            'jenis_kelamin'   => 'required|in:L,P',
            'kelas_id'        => 'required|exists:kelas,id',
            'tanggal_lahir'   => 'nullable|date|before:today',
            'tempat_lahir'    => 'nullable|string|max:50',
            'agama'           => 'nullable|string|max:20',
            'alamat'          => 'nullable|string',
            'nama_wali'       => 'nullable|string|max:100',
            'pekerjaan_wali'  => 'nullable|string|max:50',
            'no_telepon_wali' => 'nullable|string|max:15',
            'status'          => 'required|in:aktif,lulus,pindah,keluar',
        ]);

        $siswa->update($validated);

        return redirect()
            ->route('admin.siswa.index')
            ->with('success', 'Data siswa berhasil diperbarui.');
    }

    /**
     * Hapus siswa (soft delete)
     * URL: DELETE /admin/siswa/{siswa}
     */
    public function destroy(Siswa $siswa)
    {
        $siswa->delete(); // Soft delete — data tidak hilang permanen

        return redirect()
            ->route('admin.siswa.index')
            ->with('success', 'Data siswa berhasil dihapus.');
    }
}