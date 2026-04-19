<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kelas;
use App\Models\Siswa;
use App\Services\AbsensiService;
use Illuminate\Http\Request;

class AbsensiController extends Controller
{
    public function __construct(private AbsensiService $absensiService) {}

    /**
     * Rekap absensi per kelas per bulan
     * URL: GET /admin/absensi/rekap
     */
    public function rekap(Request $request)
    {
        $bulan       = (int) $request->get('bulan', now()->month);
        $tahun       = (int) $request->get('tahun', now()->year);
        $kelas       = Kelas::orderBy('tingkat')->get();
        $kelasAktif  = $kelas->find($request->get('kelas_id')) ?? $kelas->first();

        $rekapData   = $kelasAktif
            ? $this->absensiService->rekapBulanan($kelasAktif->id, $bulan, $tahun)
            : collect();

        // Daftar bulan untuk dropdown
        $daftarBulan = [
            1=>'Januari', 2=>'Februari', 3=>'Maret',    4=>'April',
            5=>'Mei',     6=>'Juni',     7=>'Juli',      8=>'Agustus',
            9=>'September',10=>'Oktober',11=>'November', 12=>'Desember',
        ];

        return view('admin.absensi.rekap', compact(
            'kelas', 'kelasAktif', 'rekapData',
            'bulan', 'tahun', 'daftarBulan'
        ));
    }

    /**
     * Rekap detail absensi satu siswa
     * URL: GET /admin/absensi/siswa/{siswa}
     */
    public function detailSiswa(Request $request, Siswa $siswa)
    {
        $bulan  = (int) $request->get('bulan', now()->month);
        $tahun  = (int) $request->get('tahun', now()->year);
        $detail = $this->absensiService->rekapSiswa($siswa->id, $bulan, $tahun);

        $daftarBulan = [
            1=>'Januari', 2=>'Februari', 3=>'Maret',    4=>'April',
            5=>'Mei',     6=>'Juni',     7=>'Juli',      8=>'Agustus',
            9=>'September',10=>'Oktober',11=>'November', 12=>'Desember',
        ];

        // Hitung ringkasan
        $ringkasan = [
            'hadir' => $detail->where('status', 'hadir')->count(),
            'sakit' => $detail->where('status', 'sakit')->count(),
            'izin'  => $detail->where('status', 'izin')->count(),
            'alpha' => $detail->where('status', 'alpha')->count(),
        ];

        return view('admin.absensi.siswa', compact(
            'siswa', 'detail', 'ringkasan', 'bulan', 'tahun', 'daftarBulan'
        ));
    }
}