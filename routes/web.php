<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\SiswaController;
use App\Http\Controllers\Admin\GuruController;
use App\Http\Controllers\Admin\JadwalController;
use App\Http\Controllers\Guru\DashboardController as GuruDashboardController;
use App\Http\Controllers\KepalaSekolah\DashboardController as KepalaDashboardController;
use App\Http\Controllers\Guru\AbsensiController as GuruAbsensiController;
use App\Http\Controllers\Admin\AbsensiController as AdminAbsensiController;

Route::get('/offline', fn() => view('offline'))->name('offline');

// ─── Auth ─────────────────────────────────────────────────────
Route::get('/', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.post');
Route::post('/logout', [LoginController::class, 'logout'])
     ->middleware('auth')->name('logout');

// ─── Admin ────────────────────────────────────────────────────
Route::prefix('admin')
     ->middleware(['auth', 'role:admin'])
     ->name('admin.')
     ->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])
         ->name('dashboard');

    Route::resource('siswa', SiswaController::class);
    Route::resource('guru', GuruController::class);

    // ✅ cek-bentrok HARUS di atas resource jadwal
    Route::post('jadwal/cek-bentrok', [JadwalController::class, 'cekBentrokAjax'])
         ->name('jadwal.cek-bentrok');                    // ← hapus 'admin.' di sini

    Route::resource('jadwal', JadwalController::class);

    Route::get('/absensi/rekap', [AdminAbsensiController::class, 'rekap'])
         ->name('absensi.rekap');
    Route::get('/absensi/siswa/{siswa}', [AdminAbsensiController::class, 'detailSiswa'])
         ->name('absensi.siswa');
});

// ─── Guru ─────────────────────────────────────────────────────
Route::prefix('guru')
     ->middleware(['auth', 'role:guru,admin'])
     ->name('guru.')
     ->group(function () {

    // ✅ pakai controller, bukan closure
    Route::get('/dashboard', [GuruDashboardController::class, 'index'])
         ->name('dashboard');

    Route::get('/absensi',         [GuruAbsensiController::class, 'index'])
         ->name('absensi.index');
    Route::get('/absensi/input',   [GuruAbsensiController::class, 'input'])
         ->name('absensi.input');
    Route::post('/absensi/simpan', [GuruAbsensiController::class, 'simpan'])
         ->name('absensi.simpan');
});

// ─── Kepala Sekolah ───────────────────────────────────────────
Route::prefix('kepala')
     ->middleware(['auth', 'role:kepala_sekolah,admin'])
     ->name('kepala.')
     ->group(function () {

    Route::get('/dashboard', [KepalaDashboardController::class, 'index'])
         ->name('dashboard');
});