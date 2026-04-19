<?php

namespace App\Enums;

class JamPelajaran
{
    // Daftar jam pelajaran SD (bisa disesuaikan)
    const SLOTS = [
        1 => ['label' => 'Jam 1', 'mulai' => '07:00', 'selesai' => '07:35'],
        2 => ['label' => 'Jam 2', 'mulai' => '07:35', 'selesai' => '08:10'],
        3 => ['label' => 'Jam 3', 'mulai' => '08:10', 'selesai' => '08:45'],
        4 => ['label' => 'Jam 4', 'mulai' => '09:00', 'selesai' => '09:35'], // istirahat 08:45-09:00
        5 => ['label' => 'Jam 5', 'mulai' => '09:35', 'selesai' => '10:10'],
        6 => ['label' => 'Jam 6', 'mulai' => '10:10', 'selesai' => '10:45'],
        7 => ['label' => 'Jam 7', 'mulai' => '11:00', 'selesai' => '11:35'], // istirahat 10:45-11:00
        8 => ['label' => 'Jam 8', 'mulai' => '11:35', 'selesai' => '12:10'],
    ];

    const HARI = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'];

    // Warna default per mata pelajaran
    const WARNA_MAPEL = [
        'Matematika'         => '#3B82F6', // biru
        'Bahasa Indonesia'   => '#10B981', // hijau
        'IPA'                => '#8B5CF6', // ungu
        'IPS'                => '#F59E0B', // kuning
        'PKn'                => '#EF4444', // merah
        'Agama Islam'        => '#06B6D4', // cyan
        'PJOK'               => '#F97316', // oranye
        'SBdP'               => '#EC4899', // pink
        'Bahasa Inggris'     => '#6366F1', // indigo
        'Muatan Lokal'       => '#84CC16', // lime
    ];

    // Ambil jam mulai & selesai dari nomor jam
    public static function getSlot(int $jamKe): array
    {
        return self::SLOTS[$jamKe] ?? ['mulai' => '07:00', 'selesai' => '07:35'];
    }
}