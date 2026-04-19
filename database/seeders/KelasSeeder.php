<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class KelasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $guru = \App\Models\Guru::first();

        $kelas = [
            ['nama_kelas' => 'Kelas 1A', 'tingkat' => 1],
            ['nama_kelas' => 'Kelas 2A', 'tingkat' => 2],
            ['nama_kelas' => 'Kelas 3A', 'tingkat' => 3],
            ['nama_kelas' => 'Kelas 4A', 'tingkat' => 4],
            ['nama_kelas' => 'Kelas 5A', 'tingkat' => 5],
            ['nama_kelas' => 'Kelas 6A', 'tingkat' => 6],
        ];

        foreach ($kelas as $k) {
            \App\Models\Kelas::create([
                ...$k,
                'wali_kelas_id' => $guru?->id,
                'tahun_ajaran'  => '2024/2025',
                'kapasitas'     => 28,
            ]);
        }
    }
}
