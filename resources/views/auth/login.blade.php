<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#1a56db">

    {{-- ✅ TAMBAHAN 1: PWA manifest & icons --}}
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-title" content="MIS Guppi">
    <link rel="manifest" href="/manifest.json">
    <link rel="apple-touch-icon" href="/icons/icon-192.png">

    <title>Login — MIS Guppi Rumbia</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Segoe UI', sans-serif;
            background: linear-gradient(135deg, #1a56db 0%, #0e9f6e 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1rem;
        }
        .card {
            background: white;
            border-radius: 16px;
            padding: 2rem;
            width: 100%;
            max-width: 400px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.15);
        }
        .logo { text-align: center; margin-bottom: 1.5rem; }
        .logo h1 { font-size: 1.25rem; color: #1a56db; font-weight: 700; }
        .logo p  { font-size: 0.85rem; color: #6b7280; margin-top: 4px; }
        .form-group { margin-bottom: 1rem; }
        label {
            display: block; font-size: 0.875rem;
            font-weight: 600; color: #374151; margin-bottom: 6px;
        }
        input {
            width: 100%; padding: 0.75rem 1rem;
            border: 2px solid #e5e7eb; border-radius: 10px;
            font-size: 1rem; transition: border-color 0.2s; outline: none;
        }
        input:focus { border-color: #1a56db; }
        input.error { border-color: #ef4444; }
        .btn {
            width: 100%; padding: 0.875rem;
            background: #1a56db; color: white;
            border: none; border-radius: 10px;
            font-size: 1rem; font-weight: 600;
            cursor: pointer; margin-top: 0.5rem;
            transition: background 0.2s;
        }
        .btn:hover { background: #1648c0; }
        .remember {
            display: flex; align-items: center;
            gap: 8px; margin-bottom: 1rem;
            font-size: 0.875rem; color: #6b7280;
        }
    </style>
</head>
<body>
<div class="card">
    <div class="logo">
        <h1>🏫 MIS Guppi Rumbia</h1>
        <p>Sistem Informasi Sekolah</p>
        <p style="font-size:0.75rem;color:#9ca3af;">
            Rumbia, Enrekang, Sulawesi Selatan
        </p>
    </div>

    @if ($errors->any())
        <div style="background:#fef2f2;border:1px solid #fca5a5;border-radius:8px;
                    padding:0.75rem;margin-bottom:1rem;color:#b91c1c;font-size:0.875rem;">
            {{ $errors->first() }}
        </div>
    @endif

    <form method="POST" action="{{ route('login.post') }}">
        @csrf
        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" id="email" name="email"
                   value="{{ old('email') }}"
                   placeholder="contoh@sekolah.com"
                   class="{{ $errors->has('email') ? 'error' : '' }}"
                   autocomplete="email" required>
        </div>

        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" id="password" name="password"
                   placeholder="••••••••"
                   autocomplete="current-password" required>
        </div>

        <div class="remember">
            <input type="checkbox" id="remember" name="remember"
                   style="width:auto;accent-color:#1a56db;">
            <label for="remember" style="margin:0;font-weight:400;">Ingat saya</label>
        </div>

        <button type="submit" class="btn">Masuk</button>
    </form>
</div>

{{-- ✅ TAMBAHAN 2: Registrasi Service Worker --}}
<script>
    if ('serviceWorker' in navigator) {
        navigator.serviceWorker.register('/sw.js', { scope: '/' })
            .then(reg => console.log('[PWA] SW terdaftar:', reg.scope))
            .catch(err => console.error('[PWA] SW gagal:', err));
    }
</script>

</body>
</html>