<?php

namespace App\Services;

use App\Models\JadwalPelajaran;
use App\Enums\JamPelajaran;

class JadwalService
{
    /**
     * Cek apakah jadwal bentrok.
     * Ada 2 jenis bentrok:
     *   (A) Kelas sudah ada pelajaran di jam & hari yang sama
     *   (B) Guru sudah mengajar di jam & hari yang sama (di kelas lain)
     *
     * @param  int       $kelasId
     * @param  int       $guruId
     * @param  string    $hari
     * @param  int       $jamKe
     * @param  int|null  $exceptId  — isi saat edit agar tidak cek diri sendiri
     * @return array  ['bentrok' => bool, 'pesan' => string]
     */
    public function cekBentrok(
        int $kelasId,
        int $guruId,
        string $hari,
        int $jamKe,
        ?int $exceptId = null
    ): array {
        $query = JadwalPelajaran::where('hari', $hari)
                                ->where('jam_ke', $jamKe);

        // Saat edit, exclude jadwal yang sedang diedit
        if ($exceptId) {
            $query->where('id', '!=', $exceptId);
        }

        // ── Bentrok tipe A: kelas ──────────────────────────────
        $bentrokKelas = (clone $query)
            ->where('kelas_id', $kelasId)
            ->first();

        if ($bentrokKelas) {
            return [
                'bentrok' => true,
                'pesan'   => "Kelas sudah ada pelajaran \"{$bentrokKelas->mata_pelajaran}\" "
                           . "pada {$hari} jam ke-{$jamKe}.",
            ];
        }

        // ── Bentrok tipe B: guru ───────────────────────────────
        $bentrokGuru = (clone $query)
            ->where('guru_id', $guruId)
            ->with('kelas')
            ->first();

        if ($bentrokGuru) {
            return [
                'bentrok' => true,
                'pesan'   => "Guru sudah mengajar \"{$bentrokGuru->mata_pelajaran}\" "
                           . "di {$bentrokGuru->kelas?->nama_kelas} "
                           . "pada {$hari} jam ke-{$jamKe}.",
            ];
        }

        return ['bentrok' => false, 'pesan' => ''];
    }

    /**
     * Ambil seluruh jadwal satu kelas, dikelompokkan per hari.
     * Hasil: ['Senin' => [jamKe => jadwal], 'Selasa' => [...], ...]
     */
    public function getJadwalGrid(int $kelasId, string $tahunAjaran): array
    {
        $jadwal = JadwalPelajaran::with('guru')
            ->where('kelas_id', $kelasId)
            ->whereHas('kelas', fn($q) => $q->where('tahun_ajaran', $tahunAjaran))
            ->orderBy('jam_ke')
            ->get();

        // Buat grid kosong dulu
        $grid = [];
        foreach (JamPelajaran::HARI as $hari) {
            foreach (array_keys(JamPelajaran::SLOTS) as $jamKe) {
                $grid[$hari][$jamKe] = null;
            }
        }

        // Isi grid dengan data dari database
        foreach ($jadwal as $j) {
            $grid[$j->hari][$j->jam_ke] = $j;
        }

        return $grid;
    }

    /**
     * Ambil jadwal satu guru untuk semua kelas (tampilan di profil guru)
     */
    public function getJadwalGuru(int $guruId): array
    {
        $jadwal = JadwalPelajaran::with('kelas')
            ->where('guru_id', $guruId)
            ->orderByRaw("FIELD(hari, 'Senin','Selasa','Rabu','Kamis','Jumat')")
            ->orderBy('jam_ke')
            ->get();

        $grid = [];
        foreach (JamPelajaran::HARI as $hari) {
            foreach (array_keys(JamPelajaran::SLOTS) as $jamKe) {
                $grid[$hari][$jamKe] = null;
            }
        }

        foreach ($jadwal as $j) {
            $grid[$j->hari][$j->jam_ke] = $j;
        }

        return $grid;
    }

    /**
     * Hitung beban mengajar guru (total jam per minggu)
     */
    public function getBebanMengajar(int $guruId): int
    {
        return JadwalPelajaran::where('guru_id', $guruId)->count();
    }
}