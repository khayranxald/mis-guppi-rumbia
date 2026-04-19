@extends('layouts.app')
@section('title', 'Dashboard Kepala Sekolah')

@section('content')

<div class="hero-banner">
    <h2>Selamat datang 👋</h2>
    <p>{{ auth()->user()->name }}</p>
    <div class="hero-date">📅 {{ now()->isoFormat('dddd, D MMMM Y') }}</div>
</div>

<div class="section-label">Ringkasan</div>
<div class="stats-grid">
    <div class="stat-card">
        <span class="stat-icon">👦</span>
        <div class="stat-num">{{ $stats['total_siswa'] }}</div>
        <div class="stat-label">Siswa</div>
    </div>
    <div class="stat-card">
        <span class="stat-icon">👩‍🏫</span>
        <div class="stat-num">{{ $stats['total_guru'] }}</div>
        <div class="stat-label">Guru</div>
    </div>
    <div class="stat-card">
        <span class="stat-icon">📋</span>
        <div class="stat-num">{{ $stats['absensi_hari_ini'] }}</div>
        <div class="stat-label">Absensi Hari Ini</div>
    </div>
</div>

<div class="section-label">Akses Cepat</div>
<div class="menu-grid">
    <a href="{{ route('admin.absensi.rekap') }}" class="menu-item">
        <div class="menu-icon" style="background:#eff6ff">📊</div>
        <span>Rekap Absensi</span>
        <small>Laporan per kelas</small>
    </a>
    <a href="{{ route('admin.siswa.index') }}" class="menu-item">
        <div class="menu-icon" style="background:#f0fdf4">👦</div>
        <span>Data Siswa</span>
        <small>Lihat semua siswa</small>
    </a>
</div>

@endsection