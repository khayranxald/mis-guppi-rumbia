<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kelas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('wali_kelas_id')
                  ->nullable()
                  ->constrained('guru')
                  ->onDelete('set null');

            $table->string('nama_kelas', 20);    // contoh: "Kelas 4A"
            $table->unsignedTinyInteger('tingkat'); // 1-6
            $table->string('tahun_ajaran', 9);   // contoh: "2024/2025"
            $table->unsignedSmallInteger('kapasitas')->default(30);
            $table->timestamps();

            // Satu kelas unik per tingkat & tahun ajaran
            $table->unique(['nama_kelas', 'tahun_ajaran']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kelas');
    }
};