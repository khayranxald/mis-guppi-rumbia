<?php

namespace App\Services;

use App\Models\Absensi;
use App\Models\Siswa;
use App\Models\Kelas;
use Illuminate\Support\Collection;
use Carbon\Carbon;

class AbsensiService
{
    /**
     * Ambil data absensi satu kelas untuk satu tanggal.
     * Jika siswa belum diabsen, tetap muncul dengan status null.
     *
     * Return: Collection of ['siswa' => Siswa, 'absensi' => Absensi|null]
     */
    public function getAbsensiHarian(int $kelasId, string $tanggal): Collection
    {
        // Semua siswa aktif di kelas ini
        $siswa = Siswa::where('kelas_id', $kelasId)
                      ->where('status', 'aktif')
                      ->orderBy('nama_lengkap')
                      ->get();

        // Absensi yang sudah ada untuk tanggal ini
        $absensiAda = Absensi::where('kelas_id', $kelasId)
                             ->where('tanggal', $tanggal)
                             ->get()
                             ->keyBy('siswa_id'); // index by siswa_id

        // Gabungkan: tiap siswa + absensinya (null kalau belum)
        return $siswa->map(fn($s) => [
            'siswa'   => $s,
            'absensi' => $absensiAda->get($s->id),
        ]);
    }

    /**
     * Simpan atau update absensi satu kelas sekaligus (bulk).
     * $data = [ siswa_id => ['status' => 'hadir', 'keterangan' => '...'], ... ]
     */
    public function simpanAbsensiBulk(
        int $kelasId,
        string $tanggal,
        int $userId,
        array $data
    ): void {
        foreach ($data as $siswaId => $nilai) {
            Absensi::updateOrCreate(
                // Kondisi: cari record yang sama
                ['siswa_id' => $siswaId, 'tanggal' => $tanggal],
                // Data yang disimpan / di-update
                [
                    'kelas_id'    => $kelasId,
                    'dicatat_oleh'=> $userId,
                    'status'      => $nilai['status'] ?? 'hadir',
                    'keterangan'  => $nilai['keterangan'] ?? null,
                ]
            );
        }
    }

    /**
     * Rekap absensi per siswa dalam satu bulan.
     * Return: [ siswa_id => ['nama' => ..., 'hadir' => N, 'sakit' => N, ...] ]
     */
    public function rekapBulanan(int $kelasId, int $bulan, int $tahun): Collection
    {
        $siswa = Siswa::where('kelas_id', $kelasId)
                      ->where('status', 'aktif')
                      ->orderBy('nama_lengkap')
                      ->get();

        // Ambil semua absensi bulan ini untuk kelas ini
        $semuaAbsensi = Absensi::where('kelas_id', $kelasId)
                               ->bulan($bulan, $tahun)
                               ->get()
                               ->groupBy('siswa_id');

        // Total hari efektif bulan ini (yang sudah ada absensi)
        $hariEfektif = Absensi::where('kelas_id', $kelasId)
                              ->bulan($bulan, $tahun)
                              ->distinct('tanggal')
                              ->count('tanggal');

        return $siswa->map(function ($s) use ($semuaAbsensi, $hariEfektif) {
            $absensiSiswa = $semuaAbsensi->get($s->id, collect());

            $hadir = $absensiSiswa->where('status', 'hadir')->count();
            $sakit = $absensiSiswa->where('status', 'sakit')->count();
            $izin  = $absensiSiswa->where('status', 'izin')->count();
            $alpha = $absensiSiswa->where('status', 'alpha')->count();

            return [
                'siswa'        => $s,
                'hadir'        => $hadir,
                'sakit'        => $sakit,
                'izin'         => $izin,
                'alpha'        => $alpha,
                'hari_efektif' => $hariEfektif,
                // Persentase kehadiran
                'persen_hadir' => $hariEfektif > 0
                    ? round(($hadir / $hariEfektif) * 100)
                    : 0,
            ];
        });
    }

    /**
     * Rekap detail satu siswa — absensi per hari dalam satu bulan.
     */
    public function rekapSiswa(int $siswaId, int $bulan, int $tahun): Collection
    {
        return Absensi::where('siswa_id', $siswaId)
                      ->bulan($bulan, $tahun)
                      ->orderBy('tanggal')
                      ->get();
    }

    /**
     * Cek apakah absensi kelas di tanggal tertentu sudah diinput.
     */
    public function sudahDiabsen(int $kelasId, string $tanggal): bool
    {
        return Absensi::where('kelas_id', $kelasId)
                      ->where('tanggal', $tanggal)
                      ->exists();
    }

    /**
     * Ringkasan hari ini untuk dashboard guru.
     * Return berapa kelas yang sudah / belum diabsen.
     */
    public function ringkasanHariIni(int $guruId): array
    {
        $hari  = Carbon::today()->locale('id')->dayName;
        $hariEn = Carbon::today()->englishDayOfWeek; // Senin, dll

        // Kelas yang diajar guru ini hari ini
        $kelasHariIni = \App\Models\JadwalPelajaran::where('guru_id', $guruId)
            ->where('hari', ucfirst(strtolower(Carbon::today()->locale('id')->isoFormat('dddd'))))
            ->distinct('kelas_id')
            ->pluck('kelas_id');

        $sudah = 0;
        $belum = 0;

        foreach ($kelasHariIni as $kelasId) {
            if ($this->sudahDiabsen($kelasId, today()->toDateString())) {
                $sudah++;
            } else {
                $belum++;
            }
        }

        return compact('sudah', 'belum');
    }
}