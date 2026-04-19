<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('siswa', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kelas_id')
                  ->nullable()
                  ->constrained('kelas')
                  ->onDelete('set null');

            $table->string('nisn', 10)->unique();
            $table->string('nis', 8)->nullable();
            $table->string('nama_lengkap', 100);
            $table->enum('jenis_kelamin', ['L', 'P']);
            $table->string('tempat_lahir', 50)->nullable();
            $table->date('tanggal_lahir')->nullable();
            $table->string('agama', 20)->default('Islam');
            $table->text('alamat')->nullable();

            // Data wali
            $table->string('nama_wali', 100)->nullable();
            $table->string('pekerjaan_wali', 50)->nullable();
            $table->string('no_telepon_wali', 15)->nullable();

            $table->enum('status', [
                'aktif', 'lulus', 'pindah', 'keluar'
            ])->default('aktif');

            $table->string('foto')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('siswa');
    }
};