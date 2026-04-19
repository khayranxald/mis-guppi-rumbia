<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Guru\AbsensiController as GuruAbsensiController;

// Gunakan middleware auth:sanctum atau session
// Untuk project ini pakai session (sama dengan web)
Route::middleware(['web', 'auth'])->group(function () {

    // Sync absensi offline → server
    Route::post('/absensi/sync', [GuruAbsensiController::class, 'sync'])
         ->name('api.absensi.sync');
});