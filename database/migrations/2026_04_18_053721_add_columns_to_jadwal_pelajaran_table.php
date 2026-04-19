<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('jadwal_pelajaran', function (Blueprint $table) {
            // Jam ke berapa (1-8) — untuk sorting yang mudah
            $table->unsignedTinyInteger('jam_ke')->after('guru_id');

            // Warna untuk tampilan grid (opsional, beda warna per mapel)
            $table->string('warna', 7)->default('#3B82F6')->after('jam_selesai');

            // Catatan tambahan
            $table->string('catatan')->nullable()->after('warna');
        });
    }

    public function down(): void
    {
        Schema::table('jadwal_pelajaran', function (Blueprint $table) {
            $table->dropColumn(['jam_ke', 'warna', 'catatan']);
        });
    }
};