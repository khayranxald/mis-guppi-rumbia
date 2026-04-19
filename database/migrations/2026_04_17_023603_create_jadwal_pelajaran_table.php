<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jadwal_pelajaran', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kelas_id')
                  ->constrained('kelas')
                  ->onDelete('cascade');
            $table->foreignId('guru_id')
                  ->constrained('guru')
                  ->onDelete('cascade');

            $table->string('mata_pelajaran', 50);
            $table->enum('hari', [
                'Senin', 'Selasa', 'Rabu',
                'Kamis', 'Jumat', 'Sabtu'
            ]);
            $table->time('jam_mulai');
            $table->time('jam_selesai');
            $table->timestamps();

            // Cegah bentrok jadwal kelas di hari & jam yang sama
            $table->unique(['kelas_id', 'hari', 'jam_mulai']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jadwal_pelajaran');
    }
};