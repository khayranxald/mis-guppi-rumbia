@extends('layouts.app')
@section('title', 'Dashboard')

@section('content')

<div class="hero-banner">
    <div class="hero-badge">{{ strtoupper(auth()->user()->role) }}</div>
    <h2>Halo, {{ auth()->user()->name }}! 👋</h2>
    <p>Sistem Informasi MIS Guppi Rumbia</p>
    <div class="hero-date">📅 {{ now()->isoFormat('dddd, D MMMM Y') }}</div>
</div>

<div class="section-label">Ringkasan Hari Ini</div>
<div class="stats-grid">
    <div class="stat-card">
        <span class="stat-icon">👦</span>
        <div class="stat-num">{{ $stats['total_siswa'] }}</div>
        <div class="stat-label">Siswa Aktif</div>
    </div>
    <div class="stat-card">
        <span class="stat-icon">👩‍🏫</span>
        <div class="stat-num">{{ $stats['total_guru'] }}</div>
        <div class="stat-label">Guru</div>
    </div>
    <div class="stat-card">
        <span class="stat-icon">🏫</span>
        <div class="stat-num">{{ $stats['total_kelas'] }}</div>
        <div class="stat-label">Kelas</div>
    </div>
</div>

<div class="section-label">Menu Utama</div>
<div class="menu-grid">
    <a href="{{ route('admin.siswa.index') }}" class="menu-card"
       style="--accent:#2563eb">
        <div class="menu-icon" style="background:#eff6ff">👦</div>
        <div class="menu-title">Data Siswa</div>
        <div class="menu-sub">Kelola data siswa</div>
        <span class="menu-arrow">›</span>
    </a>
    <a href="{{ route('admin.guru.index') }}" class="menu-card">
        <div class="menu-icon" style="background:#f0fdf4">👩‍🏫</div>
        <div class="menu-title">Data Guru</div>
        <div class="menu-sub">Kelola data guru</div>
        <span class="menu-arrow">›</span>
    </a>
    <a href="{{ route('admin.jadwal.index') }}" class="menu-card">
        <div class="menu-icon" style="background:#fff7ed">📅</div>
        <div class="menu-title">Jadwal</div>
        <div class="menu-sub">Atur jadwal pelajaran</div>
        <span class="menu-arrow">›</span>
    </a>
    <a href="{{ route('admin.absensi.rekap') }}" class="menu-card">
        <div class="menu-icon" style="background:#fdf4ff">📊</div>
        <div class="menu-title">Rekap Absensi</div>
        <div class="menu-sub">Laporan kehadiran</div>
        <span class="menu-arrow">›</span>
    </a>
</div>

{{-- Warna per menu --}}
<style>
.menu-grid .menu-card:nth-child(1)::before{background:#2563eb}
.menu-grid .menu-card:nth-child(2)::before{background:#10b981}
.menu-grid .menu-card:nth-child(3)::before{background:#f59e0b}
.menu-grid .menu-card:nth-child(4)::before{background:#8b5cf6}
</style>

<div class="section-label">Info Sekolah</div>
<div class="card" style="padding:.5rem">
    <div class="list-item">
        <div class="list-icon" style="background:#eff6ff">📍</div>
        <div style="flex:1">
            <div style="font-size:.88rem;font-weight:600">Lokasi</div>
            <div style="font-size:.75rem;color:var(--muted)">Rumbia, Enrekang, Sulawesi Selatan</div>
        </div>
    </div>
    <div class="list-item">
        <div class="list-icon" style="background:#f0fdf4">📆</div>
        <div style="flex:1">
            <div style="font-size:.88rem;font-weight:600">Tahun Ajaran</div>
            <div style="font-size:.75rem;color:var(--muted)">2024 / 2025</div>
        </div>
    </div>
    <div class="list-item">
        <div class="list-icon" style="background:#fff7ed">🕐</div>
        <div style="flex:1">
            <div style="font-size:.88rem;font-weight:600">Jam Masuk</div>
            <div style="font-size:.75rem;color:var(--muted)">07:00 WIB</div>
        </div>
    </div>
</div>

@endsection