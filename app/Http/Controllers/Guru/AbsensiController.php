<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\Kelas;
use App\Services\AbsensiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AbsensiController extends Controller
{
    public function __construct(private AbsensiService $absensiService) {}

    /**
     * Halaman pilih kelas & tanggal
     * URL: GET /guru/absensi
     */
    public function index(Request $request)
    {
        // Guru hanya melihat kelas yang ia ajar
        $guruId = Auth::user()->guru->id;

        $kelas = Kelas::whereHas('jadwalPelajaran', fn($q) =>
            $q->where('guru_id', $guruId)
        )->orderBy('tingkat')->get();

        $tanggal    = $request->get('tanggal', today()->toDateString());
        $kelasAktif = $kelas->find($request->get('kelas_id')) ?? $kelas->first();

        $sudahDiabsen = false;
        $dataAbsensi  = collect();

        if ($kelasAktif) {
            $sudahDiabsen = $this->absensiService->sudahDiabsen(
                $kelasAktif->id, $tanggal
            );
            $dataAbsensi  = $this->absensiService->getAbsensiHarian(
                $kelasAktif->id, $tanggal
            );
        }

        return view('guru.absensi.index', compact(
            'kelas', 'kelasAktif', 'tanggal',
            'dataAbsensi', 'sudahDiabsen'
        ));
    }

    /**
     * Halaman form input absensi
     * URL: GET /guru/absensi/input?kelas_id=1&tanggal=2024-01-15
     */
    public function input(Request $request)
    {
        $request->validate([
            'kelas_id' => 'required|exists:kelas,id',
            'tanggal'  => 'required|date|before_or_equal:today',
        ]);

        $kelas       = Kelas::findOrFail($request->kelas_id);
        $tanggal     = $request->tanggal;
        $dataAbsensi = $this->absensiService->getAbsensiHarian($kelas->id, $tanggal);
        $daftarStatus= \App\Models\Absensi::daftarStatus();

        return view('guru.absensi.input', compact(
            'kelas', 'tanggal', 'dataAbsensi', 'daftarStatus'
        ));
    }

    /**
     * Simpan absensi (bulk — semua siswa sekaligus)
     * URL: POST /guru/absensi/simpan
     */
    public function simpan(Request $request)
    {
        $request->validate([
            'kelas_id'   => 'required|exists:kelas,id',
            'tanggal'    => 'required|date|before_or_equal:today',
            'absensi'    => 'required|array',
            'absensi.*.status' => 'required|in:hadir,sakit,izin,alpha',
        ], [
            'tanggal.before_or_equal' => 'Tidak bisa input absensi untuk tanggal mendatang.',
            'absensi.required'        => 'Data absensi tidak boleh kosong.',
        ]);

        $this->absensiService->simpanAbsensiBulk(
            kelasId: $request->kelas_id,
            tanggal: $request->tanggal,
            userId:  Auth::id(),
            data:    $request->absensi,
        );

        return redirect()
            ->route('guru.absensi.index', [
                'kelas_id' => $request->kelas_id,
                'tanggal'  => $request->tanggal,
            ])
            ->with('success', 'Absensi berhasil disimpan.');
    }

    /**
     * Endpoint API untuk sync absensi dari offline (IndexedDB)
     * URL: POST /api/absensi/sync
     */
    public function sync(Request $request)
    {
        // Validasi input JSON
        $validated = $request->validate([
            'kelas_id'         => 'required|exists:kelas,id',
            'tanggal'          => 'required|date|before_or_equal:today',
            'absensi'          => 'required|array',
            'absensi.*.status' => 'required|in:hadir,sakit,izin,alpha',
        ]);

        try {
            $this->absensiService->simpanAbsensiBulk(
                kelasId: $validated['kelas_id'],
                tanggal: $validated['tanggal'],
                userId:  auth()->id(),
                data:    $validated['absensi'],
            );

            return response()->json([
                'success' => true,
                'message' => 'Absensi berhasil disimpan.',
                'tanggal' => $validated['tanggal'],
                'jumlah'  => count($validated['absensi']),
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan: ' . $e->getMessage(),
            ], 500);
        }
    }
}