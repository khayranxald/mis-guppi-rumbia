<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Guru;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class GuruController extends Controller
{
    /**
     * Daftar semua guru
     * URL: GET /admin/guru
     */
    public function index(Request $request)
    {
        $query = Guru::with('user')->latest();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama_lengkap', 'like', "%{$search}%")
                  ->orWhere('nip', 'like', "%{$search}%");
            });
        }

        $guru = $query->paginate(15)->withQueryString();

        return view('admin.guru.index', compact('guru'));
    }

    /**
     * Form tambah guru
     * URL: GET /admin/guru/create
     */
    public function create()
    {
        return view('admin.guru.create');
    }

    /**
     * Simpan guru baru + buat akun login sekaligus
     * URL: POST /admin/guru
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_lengkap'       => 'required|string|max:100',
            'nip'                => 'nullable|digits_between:15,18|unique:guru,nip',
            'jenis_kelamin'      => 'required|in:L,P',
            'tanggal_lahir'      => 'nullable|date|before:today',
            'no_telepon'         => 'nullable|string|max:15',
            'alamat'             => 'nullable|string',
            'status_kepegawaian' => 'required|in:PNS,GTT,Honorer',
            // Akun login
            'email'              => 'required|email|unique:users,email',
            'password'           => 'required|min:8|confirmed',
        ], [
            'nama_lengkap.required'       => 'Nama lengkap wajib diisi.',
            'nip.digits_between'          => 'NIP harus 15-18 digit.',
            'nip.unique'                  => 'NIP sudah terdaftar.',
            'jenis_kelamin.required'      => 'Jenis kelamin wajib dipilih.',
            'status_kepegawaian.required' => 'Status kepegawaian wajib dipilih.',
            'email.required'              => 'Email wajib diisi.',
            'email.unique'                => 'Email sudah digunakan.',
            'password.min'                => 'Password minimal 8 karakter.',
            'password.confirmed'          => 'Konfirmasi password tidak cocok.',
        ]);

        // Gunakan DB transaction agar user & guru dibuat bersamaan
        // Jika salah satu gagal, keduanya dibatalkan
        DB::transaction(function () use ($validated) {
            $user = User::create([
                'name'     => $validated['nama_lengkap'],
                'email'    => $validated['email'],
                'password' => Hash::make($validated['password']),
                'role'     => 'guru',
            ]);

            Guru::create([
                'user_id'            => $user->id,
                'nama_lengkap'       => $validated['nama_lengkap'],
                'nip'                => $validated['nip'] ?? null,
                'jenis_kelamin'      => $validated['jenis_kelamin'],
                'tanggal_lahir'      => $validated['tanggal_lahir'] ?? null,
                'no_telepon'         => $validated['no_telepon'] ?? null,
                'alamat'             => $validated['alamat'] ?? null,
                'status_kepegawaian' => $validated['status_kepegawaian'],
            ]);
        });

        return redirect()
            ->route('admin.guru.index')
            ->with('success', 'Data guru dan akun login berhasil dibuat.');
    }

    /**
     * Form edit guru
     * URL: GET /admin/guru/{guru}/edit
     */
    public function edit(Guru $guru)
    {
        return view('admin.guru.edit', compact('guru'));
    }

    /**
     * Update data guru
     * URL: PUT /admin/guru/{guru}
     */
    public function update(Request $request, Guru $guru)
    {
        $validated = $request->validate([
            'nama_lengkap'       => 'required|string|max:100',
            'nip'                => 'nullable|digits_between:15,18|unique:guru,nip,' . $guru->id,
            'jenis_kelamin'      => 'required|in:L,P',
            'tanggal_lahir'      => 'nullable|date|before:today',
            'no_telepon'         => 'nullable|string|max:15',
            'alamat'             => 'nullable|string',
            'status_kepegawaian' => 'required|in:PNS,GTT,Honorer',
            // Password opsional saat edit
            'password'           => 'nullable|min:8|confirmed',
        ]);

        DB::transaction(function () use ($validated, $guru) {
            $guru->update([
                'nama_lengkap'       => $validated['nama_lengkap'],
                'nip'                => $validated['nip'] ?? null,
                'jenis_kelamin'      => $validated['jenis_kelamin'],
                'tanggal_lahir'      => $validated['tanggal_lahir'] ?? null,
                'no_telepon'         => $validated['no_telepon'] ?? null,
                'alamat'             => $validated['alamat'] ?? null,
                'status_kepegawaian' => $validated['status_kepegawaian'],
            ]);

            // Update nama di tabel users juga
            $guru->user->update(['name' => $validated['nama_lengkap']]);

            // Ganti password hanya jika diisi
            if (!empty($validated['password'])) {
                $guru->user->update([
                    'password' => Hash::make($validated['password']),
                ]);
            }
        });

        return redirect()
            ->route('admin.guru.index')
            ->with('success', 'Data guru berhasil diperbarui.');
    }

    /**
     * Hapus guru (soft delete)
     * URL: DELETE /admin/guru/{guru}
     */
    public function destroy(Guru $guru)
    {
        $guru->delete();

        return redirect()
            ->route('admin.guru.index')
            ->with('success', 'Data guru berhasil dihapus.');
    }
}