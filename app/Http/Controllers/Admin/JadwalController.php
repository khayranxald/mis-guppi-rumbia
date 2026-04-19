<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\JadwalPelajaran;
use App\Models\Kelas;
use App\Models\Guru;
use App\Services\JadwalService;
use App\Enums\JamPelajaran;
use Illuminate\Http\Request;

class JadwalController extends Controller
{
    // Inject JadwalService lewat constructor
    public function __construct(private JadwalService $jadwalService) {}

    /**
     * Tampilkan grid jadwal per kelas
     * URL: GET /admin/jadwal
     */
    public function index(Request $request)
    {
        $tahunAjaran = $request->get('tahun_ajaran', '2024/2025');
        $kelas       = Kelas::where('tahun_ajaran', $tahunAjaran)
                            ->orderBy('tingkat')->get();

        // Default: tampilkan kelas pertama
        $kelasAktif  = $kelas->find($request->get('kelas_id'))
                      ?? $kelas->first();

        $grid        = $kelasAktif
            ? $this->jadwalService->getJadwalGrid($kelasAktif->id, $tahunAjaran)
            : [];

        return view('admin.jadwal.index', compact(
            'kelas', 'kelasAktif', 'grid', 'tahunAjaran'
        ));
    }

    /**
     * Form tambah jadwal
     * URL: GET /admin/jadwal/create
     */
    public function create(Request $request)
    {
        $kelas    = Kelas::orderBy('tingkat')->get();
        $guru     = Guru::orderBy('nama_lengkap')->get();
        $slots    = JamPelajaran::SLOTS;
        $hari     = JamPelajaran::HARI;
        $mapel    = array_keys(JamPelajaran::WARNA_MAPEL);

        // Boleh pre-fill dari query string (klik sel kosong di grid)
        $defaultKelas = $request->query('kelas_id');
        $defaultHari  = $request->query('hari');
        $defaultJam   = $request->query('jam_ke');

        return view('admin.jadwal.create', compact(
            'kelas', 'guru', 'slots', 'hari', 'mapel',
            'defaultKelas', 'defaultHari', 'defaultJam'
        ));
    }

    /**
     * Simpan jadwal baru
     * URL: POST /admin/jadwal
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'kelas_id'      => 'required|exists:kelas,id',
            'guru_id'       => 'required|exists:guru,id',
            'mata_pelajaran'=> 'required|string|max:50',
            'hari'          => 'required|in:' . implode(',', JamPelajaran::HARI),
            'jam_ke'        => 'required|integer|min:1|max:8',
            'warna'         => 'nullable|string|max:7',
            'catatan'       => 'nullable|string|max:100',
        ], [
            'kelas_id.required'       => 'Kelas wajib dipilih.',
            'guru_id.required'        => 'Guru wajib dipilih.',
            'mata_pelajaran.required' => 'Mata pelajaran wajib diisi.',
            'hari.required'           => 'Hari wajib dipilih.',
            'jam_ke.required'         => 'Jam pelajaran wajib dipilih.',
        ]);

        // ── Cek bentrok sebelum simpan ─────────────────────────
        $cek = $this->jadwalService->cekBentrok(
            kelasId: $validated['kelas_id'],
            guruId:  $validated['guru_id'],
            hari:    $validated['hari'],
            jamKe:   $validated['jam_ke'],
        );

        if ($cek['bentrok']) {
            // Kembalikan ke form dengan pesan error bentrok
            return back()
                ->withInput()
                ->withErrors(['bentrok' => $cek['pesan']]);
        }

        // Auto-set warna dari nama mapel jika tidak dipilih manual
        if (empty($validated['warna'])) {
            $validated['warna'] = JamPelajaran::WARNA_MAPEL[$validated['mata_pelajaran']]
                                  ?? '#6B7280';
        }

        JadwalPelajaran::create($validated);

        return redirect()
            ->route('admin.jadwal.index', [
                'kelas_id'     => $validated['kelas_id'],
                'tahun_ajaran' => '2024/2025',
            ])
            ->with('success', 'Jadwal berhasil ditambahkan.');
    }

    /**
     * Form edit jadwal
     * URL: GET /admin/jadwal/{jadwal}/edit
     */
    public function edit(JadwalPelajaran $jadwal)
    {
        $kelas = Kelas::orderBy('tingkat')->get();
        $guru  = Guru::orderBy('nama_lengkap')->get();
        $slots = JamPelajaran::SLOTS;
        $hari  = JamPelajaran::HARI;
        $mapel = array_keys(JamPelajaran::WARNA_MAPEL);

        return view('admin.jadwal.edit', compact(
            'jadwal', 'kelas', 'guru', 'slots', 'hari', 'mapel'
        ));
    }

    /**
     * Update jadwal
     * URL: PUT /admin/jadwal/{jadwal}
     */
    public function update(Request $request, JadwalPelajaran $jadwal)
    {
        $validated = $request->validate([
            'kelas_id'      => 'required|exists:kelas,id',
            'guru_id'       => 'required|exists:guru,id',
            'mata_pelajaran'=> 'required|string|max:50',
            'hari'          => 'required|in:' . implode(',', JamPelajaran::HARI),
            'jam_ke'        => 'required|integer|min:1|max:8',
            'warna'         => 'nullable|string|max:7',
            'catatan'       => 'nullable|string|max:100',
        ]);

        // Cek bentrok — kecualikan jadwal yang sedang diedit ($jadwal->id)
        $cek = $this->jadwalService->cekBentrok(
            kelasId:  $validated['kelas_id'],
            guruId:   $validated['guru_id'],
            hari:     $validated['hari'],
            jamKe:    $validated['jam_ke'],
            exceptId: $jadwal->id,         // ← penting saat edit!
        );

        if ($cek['bentrok']) {
            return back()->withInput()->withErrors(['bentrok' => $cek['pesan']]);
        }

        $jadwal->update($validated);

        return redirect()
            ->route('admin.jadwal.index', ['kelas_id' => $jadwal->kelas_id])
            ->with('success', 'Jadwal berhasil diperbarui.');
    }

    /**
     * Hapus satu slot jadwal
     * URL: DELETE /admin/jadwal/{jadwal}
     */
    public function destroy(JadwalPelajaran $jadwal)
    {
        $kelasId = $jadwal->kelas_id;
        $jadwal->delete();

        return redirect()
            ->route('admin.jadwal.index', ['kelas_id' => $kelasId])
            ->with('success', 'Jadwal berhasil dihapus.');
    }

    /**
     * API endpoint: cek bentrok via AJAX (dipanggil dari form)
     * URL: POST /admin/jadwal/cek-bentrok
     */
    public function cekBentrokAjax(Request $request)
    {
        $request->validate([
            'kelas_id' => 'required|integer',
            'guru_id'  => 'required|integer',
            'hari'     => 'required|string',
            'jam_ke'   => 'required|integer',
        ]);

        $cek = $this->jadwalService->cekBentrok(
            kelasId:  $request->kelas_id,
            guruId:   $request->guru_id,
            hari:     $request->hari,
            jamKe:    $request->jam_ke,
            exceptId: $request->except_id,
        );

        return response()->json($cek);
    }
}