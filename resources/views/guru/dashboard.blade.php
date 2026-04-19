@extends('layouts.app')
@section('title', 'Dashboard Guru')

@section('content')

<div class="hero-banner" style="background:linear-gradient(135deg,#10b981,#059669)">
    <div class="hero-badge">GURU</div>
    <h2>Halo, {{ auth()->user()->name }}! 👋</h2>
    <p>Siap mengajar hari ini?</p>
    <div class="hero-date">📅 {{ now()->isoFormat('dddd, D MMMM Y') }}</div>
</div>

<div class="section-label">Aksi Cepat</div>
<div class="menu-grid">
    <a href="{{ route('guru.absensi.index') }}" class="menu-card">
        <div class="menu-icon" style="background:#eff6ff">📋</div>
        <div class="menu-title">Input Absensi</div>
        <div class="menu-sub">Catat kehadiran siswa</div>
        <span class="menu-arrow">›</span>
    </a>
    <a href="{{ route('guru.absensi.index') }}" class="menu-card">
        <div class="menu-icon" style="background:#f0fdf4">📊</div>
        <div class="menu-title">Riwayat</div>
        <div class="menu-sub">Lihat absensi lalu</div>
        <span class="menu-arrow">›</span>
    </a>
</div>
<style>
.menu-grid .menu-card:nth-child(1)::before{background:#2563eb}
.menu-grid .menu-card:nth-child(2)::before{background:#10b981}
</style>

@if($kelasDiajar->isNotEmpty())
<div class="section-label">Kelas yang Anda Ajar</div>
<div class="card" style="padding:.5rem">
    @foreach($kelasDiajar as $k)
    <a href="{{ route('guru.absensi.index', ['kelas_id' => $k->id]) }}"
       class="list-item">
        <div class="list-icon" style="background:#eff6ff">🏫</div>
        <div style="flex:1">
            <div style="font-size:.88rem;font-weight:600">{{ $k->nama_kelas }}</div>
            <div style="font-size:.75rem;color:var(--muted)">
                Tap untuk input absensi hari ini
            </div>
        </div>
        <span style="color:var(--muted);font-size:.9rem">›</span>
    </a>
    @endforeach
</div>
@else
<div class="card" style="text-align:center;padding:2rem;color:var(--muted)">
    <div style="font-size:2.5rem;margin-bottom:.5rem">📅</div>
    <div style="font-weight:600">Belum ada jadwal mengajar</div>
    <div style="font-size:.8rem;margin-top:.25rem">Hubungi admin untuk pengaturan jadwal</div>
</div>
@endif

@endsection