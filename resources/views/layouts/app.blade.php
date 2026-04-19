<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#2563eb">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="apple-mobile-web-app-title" content="MIS Guppi">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="manifest" href="/manifest.json">
    <link rel="apple-touch-icon" href="/icons/icon-192.png">
    <link rel="stylesheet" href="/css/app.css">
    <title>@yield('title', 'MIS Guppi Rumbia')</title>
    @stack('styles')
</head>
<body>

{{-- Offline bar --}}
<div id="offline-bar">📡 Offline — data akan sync otomatis saat ada koneksi</div>

{{-- Topbar --}}
<div class="topbar">
    <div class="topbar-brand">
        <div class="brand-icon">🏫</div>
        <div>
            <h1>MIS Guppi Rumbia</h1>
            <span>Rumbia, Enrekang</span>
        </div>
    </div>
    <div class="topbar-right">
        <div class="user-avatar" title="{{ auth()->user()->name }}">
            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
        </div>
        <form method="POST" action="{{ route('logout') }}" style="margin:0">
            @csrf
            <button type="submit" class="btn-logout">Keluar</button>
        </form>
    </div>
</div>

{{-- Konten --}}
<div class="main">

    @if (session('success'))
        <div class="alert alert-success">✅ {{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="alert alert-error">❌ {{ session('error') }}</div>
    @endif

    @yield('content')
</div>

{{-- Bottom Nav --}}
<nav class="bottom-nav">
    @if(auth()->user()->isAdmin())
        <a href="{{ route('admin.dashboard') }}"
           class="nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
            <span class="nav-icon">🏠</span>Beranda
        </a>
        <a href="{{ route('admin.siswa.index') }}"
           class="nav-item {{ request()->routeIs('admin.siswa.*') ? 'active' : '' }}">
            <span class="nav-icon">👦</span>Siswa
        </a>
        <a href="{{ route('admin.guru.index') }}"
           class="nav-item {{ request()->routeIs('admin.guru.*') ? 'active' : '' }}">
            <span class="nav-icon">👩‍🏫</span>Guru
        </a>
        <a href="{{ route('admin.jadwal.index') }}"
           class="nav-item {{ request()->routeIs('admin.jadwal.*') ? 'active' : '' }}">
            <span class="nav-icon">📅</span>Jadwal
        </a>
        <a href="{{ route('admin.absensi.rekap') }}"
           class="nav-item {{ request()->routeIs('admin.absensi.*') ? 'active' : '' }}">
            <span class="nav-icon">📋</span>Absensi
        </a>
    @elseif(auth()->user()->isGuru())
        <a href="{{ route('guru.dashboard') }}"
           class="nav-item {{ request()->routeIs('guru.dashboard') ? 'active' : '' }}">
            <span class="nav-icon">🏠</span>Beranda
        </a>
        <a href="{{ route('guru.absensi.index') }}"
           class="nav-item {{ request()->routeIs('guru.absensi.*') ? 'active' : '' }}">
            <span class="nav-icon">📋</span>Absensi
        </a>
    @elseif(auth()->user()->isKepalaSekolah())
        <a href="{{ route('kepala.dashboard') }}"
           class="nav-item {{ request()->routeIs('kepala.dashboard') ? 'active' : '' }}">
            <span class="nav-icon">🏠</span>Beranda
        </a>
        <a href="{{ route('admin.absensi.rekap') }}"
           class="nav-item {{ request()->routeIs('admin.absensi.*') ? 'active' : '' }}">
            <span class="nav-icon">📋</span>Absensi
        </a>
        <a href="{{ route('admin.siswa.index') }}"
           class="nav-item {{ request()->routeIs('admin.siswa.*') ? 'active' : '' }}">
            <span class="nav-icon">👦</span>Siswa
        </a>
    @endif
</nav>

{{-- PWA Banner --}}
<div id="pwa-banner">
    <div style="font-size:2rem">🏫</div>
    <div style="flex:1">
        <div style="font-weight:700;font-size:.88rem;color:#1e293b">
            Install MIS Guppi Rumbia
        </div>
        <div style="font-size:.75rem;color:#94a3b8">
            Tambahkan ke layar utama HP Anda
        </div>
    </div>
    <div style="display:flex;gap:6px">
        <button onclick="installPWA()" class="btn btn-primary btn-sm">Install</button>
        <button onclick="tutupBanner()" class="btn btn-secondary btn-sm">✕</button>
    </div>
</div>

<script>
    if ('serviceWorker' in navigator) {
        window.addEventListener('load', async () => {
            try {
                const reg = await navigator.serviceWorker.register('/sw.js', { scope: '/' });
                reg.addEventListener('updatefound', () => {
                    const w = reg.installing;
                    w.addEventListener('statechange', () => {
                        if (w.state === 'installed' && navigator.serviceWorker.controller) {
                            if (confirm('Versi baru tersedia. Muat ulang?')) {
                                w.postMessage({ type: 'SKIP_WAITING' });
                                location.reload();
                            }
                        }
                    });
                });
            } catch(e) { console.error('[SW]', e); }
        });
    }

    const offlineBar = document.getElementById('offline-bar');
    function updateOnline() { offlineBar.classList.toggle('show', !navigator.onLine); }
    window.addEventListener('online',  updateOnline);
    window.addEventListener('offline', updateOnline);
    updateOnline();

    let deferredPrompt = null;
    window.addEventListener('beforeinstallprompt', e => {
        e.preventDefault();
        deferredPrompt = e;
        if (!localStorage.getItem('pwa-dismissed')) {
            setTimeout(() => document.getElementById('pwa-banner').classList.add('show'), 4000);
        }
    });
    async function installPWA() {
        if (!deferredPrompt) return;
        await deferredPrompt.prompt();
        deferredPrompt = null;
        tutupBanner();
    }
    function tutupBanner() {
        document.getElementById('pwa-banner').classList.remove('show');
        localStorage.setItem('pwa-dismissed', '1');
    }
    window.addEventListener('appinstalled', tutupBanner);
</script>

@stack('scripts')
</body>
</html>