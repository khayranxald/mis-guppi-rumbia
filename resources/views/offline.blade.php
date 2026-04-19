<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#1a56db">
    <title>Offline — MIS Guppi Rumbia</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Segoe UI', sans-serif;
            background: #f3f4f6;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1rem;
        }
        .card {
            background: white;
            border-radius: 16px;
            padding: 2rem 1.5rem;
            text-align: center;
            max-width: 360px;
            width: 100%;
            box-shadow: 0 4px 20px rgba(0,0,0,.08);
        }
        .icon     { font-size: 4rem; margin-bottom: 1rem; }
        h1        { font-size: 1.2rem; color: #111827; margin-bottom: .5rem; }
        p         { color: #6b7280; font-size: .875rem; line-height: 1.6;
                    margin-bottom: 1.5rem; }
        .btn      {
            display: block; width: 100%;
            padding: .875rem;
            background: #1a56db; color: white;
            border: none; border-radius: 10px;
            font-size: 1rem; font-weight: 600;
            cursor: pointer; margin-bottom: .75rem;
        }
        .btn-sec  { background: #f3f4f6; color: #374151; }
        .tip      {
            margin-top: 1.25rem;
            padding: .75rem;
            background: #f0fdf4;
            border-radius: 8px;
            font-size: .78rem;
            color: #166534;
            text-align: left;
        }
    </style>
</head>
<body>
<div class="card">
    <div class="icon">📡</div>
    <h1>Tidak Ada Koneksi</h1>
    <p>
        Anda sedang offline. Periksa koneksi WiFi
        atau data seluler, lalu coba lagi.
    </p>

    <button class="btn" onclick="window.location.reload()">
        🔄 Coba Lagi
    </button>
    <button class="btn btn-sec" onclick="window.history.back()">
        ← Kembali
    </button>

    <div class="tip">
        💡 <strong>Tips:</strong> Halaman yang pernah Anda buka
        sebelumnya masih bisa diakses meski offline.
    </div>
</div>
</body>
</html>