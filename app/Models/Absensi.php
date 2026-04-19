<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


class Absensi extends Model
{
    protected $table = 'absensi';

    protected $fillable = [
        'siswa_id',
        'kelas_id',
        'dicatat_oleh',
        'tanggal',
        'status',
        'keterangan',
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];

    // ── Relasi ───────────────────────────────────────────────
    public function siswa(): BelongsTo
    {
        return $this->belongsTo(Siswa::class);
    }

    public function kelas(): BelongsTo
    {
        return $this->belongsTo(Kelas::class);
    }

    public function pencatat(): BelongsTo
    {
        return $this->belongsTo(User::class, 'dicatat_oleh');
    }

    // ── Scopes ───────────────────────────────────────────────

    // Filter by tanggal
    public function scopeTanggal($query, string $tanggal)
    {
        return $query->where('tanggal', $tanggal);
    }

    // Filter by bulan & tahun
    public function scopeBulan($query, int $bulan, int $tahun)
    {
        return $query->whereMonth('tanggal', $bulan)
                     ->whereYear('tanggal', $tahun);
    }

    // Filter by status
    public function scopeStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    // ── Accessor ─────────────────────────────────────────────

    // Label status dalam Bahasa Indonesia dengan warna
    public function getLabelStatusAttribute(): array
    {
        return match($this->status) {
            'hadir' => ['label' => 'Hadir',  'warna' => 'badge-green'],
            'sakit' => ['label' => 'Sakit',  'warna' => 'badge-yellow'],
            'izin'  => ['label' => 'Izin',   'warna' => 'badge-blue'],
            'alpha' => ['label' => 'Alpha',  'warna' => 'badge-red'],
            default => ['label' => '-',       'warna' => 'badge-gray'],
        };
    }

    // ── Static Helper ─────────────────────────────────────────

    // Daftar status untuk dropdown
    public static function daftarStatus(): array
    {
        return [
            'hadir' => 'Hadir',
            'sakit' => 'Sakit',
            'izin'  => 'Izin',
            'alpha' => 'Alpha (Tanpa Keterangan)',
        ];
    }
}