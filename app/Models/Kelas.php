<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Kelas extends Model
{
    protected $fillable = [
        'wali_kelas_id', 'nama_kelas',
        'tingkat', 'tahun_ajaran', 'kapasitas',
    ];

    // Wali kelas
    public function waliKelas(): BelongsTo
    {
        return $this->belongsTo(Guru::class, 'wali_kelas_id');
    }

    // Siswa dalam kelas ini
    public function siswa(): HasMany
    {
        return $this->hasMany(Siswa::class);
    }

    // Jadwal kelas ini
    public function jadwalPelajaran(): HasMany
    {
        return $this->hasMany(JadwalPelajaran::class);
    }

    // Hitung jumlah siswa aktif
    public function getJumlahSiswaAttribute(): int
    {
        return $this->siswa()->where('status', 'aktif')->count();
    }
}