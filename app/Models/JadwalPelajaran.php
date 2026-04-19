<?php

namespace App\Models;

use App\Enums\JamPelajaran;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JadwalPelajaran extends Model
{
    protected $table = 'jadwal_pelajaran';

    protected $fillable = [
        'kelas_id', 'guru_id', 'mata_pelajaran',
        'hari', 'jam_ke', 'jam_mulai', 'jam_selesai',
        'warna', 'catatan',
    ];

    public function kelas(): BelongsTo
    {
        return $this->belongsTo(Kelas::class);
    }

    public function guru(): BelongsTo
    {
        return $this->belongsTo(Guru::class);
    }

    // Accessor: ambil info slot jam dari konstanta
    public function getSlotJamAttribute(): array
    {
        return JamPelajaran::getSlot($this->jam_ke);
    }

    // Accessor: "07:00 - 07:35"
    public function getJamLabelAttribute(): string
    {
        $slot = $this->slot_jam;
        return "{$slot['mulai']} – {$slot['selesai']}";
    }

    // Scope: filter per hari
    public function scopeHari($query, string $hari)
    {
        return $query->where('hari', $hari);
    }

    // Auto-set jam_mulai & jam_selesai dari jam_ke sebelum simpan
    protected static function booted(): void
    {
        static::saving(function (JadwalPelajaran $jadwal) {
            $slot = JamPelajaran::getSlot($jadwal->jam_ke);
            $jadwal->jam_mulai  = $slot['mulai'];
            $jadwal->jam_selesai = $slot['selesai'];
        });
    }
}