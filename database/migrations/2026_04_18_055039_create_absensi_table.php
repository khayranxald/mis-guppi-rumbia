<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('absensi', function (Blueprint $table) {
            $table->id();

            $table->foreignId('siswa_id')
                  ->constrained('siswa')
                  ->onDelete('cascade');

            $table->foreignId('kelas_id')
                  ->constrained('kelas')
                  ->onDelete('cascade');

            // Guru yang mencatat absensi
            $table->foreignId('dicatat_oleh')
                  ->constrained('users')
                  ->onDelete('cascade');

            $table->date('tanggal');

            // Status kehadiran
            $table->enum('status', ['hadir', 'sakit', 'izin', 'alpha'])
                  ->default('hadir');

            // Keterangan tambahan (misal: surat dokter, dst)
            $table->string('keterangan')->nullable();

            $table->timestamps();

            // Satu siswa hanya boleh 1 record per tanggal
            $table->unique(['siswa_id', 'tanggal']);

            // Index untuk query rekap yang sering dipakai
            $table->index(['kelas_id', 'tanggal']);
            $table->index(['siswa_id', 'tanggal']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('absensi');
    }
};