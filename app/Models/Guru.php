<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Guru extends Model
{
    use SoftDeletes;

    protected $table = 'guru';

    protected $fillable = [
        'user_id', 'nip', 'nama_lengkap', 'jenis_kelamin',
        'tanggal_lahir', 'alamat', 'no_telepon',
        'status_kepegawaian', 'foto',
    ];

    protected $casts = [
        'tanggal_lahir' => 'date',
    ];

    // Relasi ke User (login)
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Guru bisa menjadi wali kelas
    public function kelasWali(): HasMany
    {
        return $this->hasMany(Kelas::class, 'wali_kelas_id');
    }

    // Guru mengajar banyak jadwal
    public function jadwalPelajaran(): HasMany
    {
        return $this->hasMany(JadwalPelajaran::class);
    }

    // Accessor: nama lengkap + gelar
    public function getNamaGelarAttribute(): string
    {
        return $this->nama_lengkap;
    }
}