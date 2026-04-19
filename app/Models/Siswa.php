<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Siswa extends Model
{
    use SoftDeletes;

    protected $table = 'siswa';

    protected $fillable = [
        'kelas_id', 'nisn', 'nis', 'nama_lengkap',
        'jenis_kelamin', 'tempat_lahir', 'tanggal_lahir',
        'agama', 'alamat', 'nama_wali', 'pekerjaan_wali',
        'no_telepon_wali', 'status', 'foto',
    ];

    protected $casts = [
        'tanggal_lahir' => 'date',
    ];

    // Siswa ada di satu kelas
    public function kelas(): BelongsTo
    {
        return $this->belongsTo(Kelas::class);
    }

    // Accessor: umur otomatis dari tanggal lahir
    public function getUmurAttribute(): ?int
    {
        return $this->tanggal_lahir?->age;
    }

    // Scope: hanya siswa aktif
    public function scopeAktif($query)
    {
        return $query->where('status', 'aktif');
    }

    public function absensi(): HasMany
    {
        return $this->hasMany(Absensi::class);
    }

    // Hitung kehadiran bulan tertentu
    public function hitungKehadiran(int $bulan, int $tahun): array
    {
        $data = $this->absensi()
            ->bulan($bulan, $tahun)
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();

        return [
            'hadir' => $data['hadir'] ?? 0,
            'sakit' => $data['sakit'] ?? 0,
            'izin'  => $data['izin']  ?? 0,
            'alpha' => $data['alpha'] ?? 0,
        ];
    }
}