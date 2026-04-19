// ─── Versi cache — ganti angka ini setiap ada update besar ───
const CACHE_VERSION = "mis-guppi-v1";
const CACHE_STATIC = `${CACHE_VERSION}-static`; // file CSS, JS, icon
const CACHE_PAGES = `${CACHE_VERSION}-pages`; // halaman HTML
const CACHE_API = `${CACHE_VERSION}-api`; // response data

// ─── File yang di-cache saat install (app shell) ─────────────
const STATIC_ASSETS = [
    "/",
    "/manifest.json",
    "/icons/icon-192.png",
    "/icons/icon-512.png",
];

// ─── Halaman yang di-cache untuk akses offline ────────────────
const CACHE_ROUTES = [
    "/guru/absensi",
    "/guru/dashboard",
    "/admin/dashboard",
    "/admin/siswa",
    "/admin/guru",
];

// ─── Halaman fallback saat offline ───────────────────────────
const OFFLINE_PAGE = "/offline";

// ═══════════════════════════════════════════════════════════════
// EVENT: INSTALL — cache semua static asset
// ═══════════════════════════════════════════════════════════════
self.addEventListener("install", (event) => {
    console.log("[SW] Installing...");

    event.waitUntil(
        caches.open(CACHE_STATIC).then((cache) => {
            console.log("[SW] Caching static assets");
            return cache.addAll(STATIC_ASSETS);
        }),
    );

    // Langsung aktif tanpa menunggu tab lama ditutup
    self.skipWaiting();
});

// ═══════════════════════════════════════════════════════════════
// EVENT: ACTIVATE — hapus cache versi lama
// ═══════════════════════════════════════════════════════════════
self.addEventListener("activate", (event) => {
    console.log("[SW] Activating...");

    event.waitUntil(
        caches.keys().then((keys) => {
            return Promise.all(
                keys
                    // Hapus cache yang bukan versi sekarang
                    .filter((key) => !key.startsWith(CACHE_VERSION))
                    .map((key) => {
                        console.log("[SW] Deleting old cache:", key);
                        return caches.delete(key);
                    }),
            );
        }),
    );

    // Ambil kontrol semua tab yang sudah terbuka
    self.clients.claim();
});

// ═══════════════════════════════════════════════════════════════
// EVENT: FETCH — strategi cache per jenis request
// ═══════════════════════════════════════════════════════════════
self.addEventListener("fetch", (event) => {
    const { request } = event;
    const url = new URL(request.url);

    // Abaikan request non-GET dan request ke domain lain
    if (request.method !== "GET") return;
    if (url.origin !== location.origin) return;

    // ── 1. File statis (CSS, JS, gambar, font) ─────────────────
    //    Strategi: Cache First → ambil dari cache dulu, kalau tidak ada baru ke network
    if (isStaticAsset(url.pathname)) {
        event.respondWith(cacheFirst(request, CACHE_STATIC));
        return;
    }

    // ── 2. Halaman HTML ────────────────────────────────────────
    //    Strategi: Network First → coba ke server, fallback ke cache
    if (request.headers.get("accept")?.includes("text/html")) {
        event.respondWith(networkFirst(request, CACHE_PAGES));
        return;
    }

    // ── 3. Request lain ────────────────────────────────────────
    event.respondWith(networkFirst(request, CACHE_API));
});

// ═══════════════════════════════════════════════════════════════
// STRATEGI CACHE
// ═══════════════════════════════════════════════════════════════

/**
 * Cache First — cocok untuk aset statis yang jarang berubah
 * Urutan: Cache → Network → Simpan ke cache
 */
async function cacheFirst(request, cacheName) {
    const cached = await caches.match(request);
    if (cached) return cached;

    try {
        const response = await fetch(request);
        if (response.ok) {
            const cache = await caches.open(cacheName);
            cache.put(request, response.clone());
        }
        return response;
    } catch {
        return new Response("Asset tidak tersedia offline", { status: 503 });
    }
}

/**
 * Network First — cocok untuk halaman yang sering berubah
 * Urutan: Network → Simpan ke cache → Jika gagal, ambil dari cache
 */
async function networkFirst(request, cacheName) {
    try {
        const response = await fetch(request);

        if (response.ok) {
            const cache = await caches.open(cacheName);
            cache.put(request, response.clone());
        }

        return response;
    } catch {
        // Network gagal (offline) — coba dari cache
        const cached = await caches.match(request);
        if (cached) return cached;

        // Kalau halaman HTML dan tidak ada cache → halaman offline
        if (request.headers.get("accept")?.includes("text/html")) {
            return (
                caches.match(OFFLINE_PAGE) ||
                new Response(offlineFallbackHTML(), {
                    headers: { "Content-Type": "text/html" },
                })
            );
        }

        return new Response("Tidak tersedia offline", { status: 503 });
    }
}

// ─── Helper: cek apakah URL adalah file statis ────────────────
function isStaticAsset(pathname) {
    return /\.(css|js|png|jpg|jpeg|gif|svg|ico|woff|woff2|ttf)$/.test(pathname);
}

// ─── HTML fallback saat benar-benar offline & tidak ada cache ─
function offlineFallbackHTML() {
    return `<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tidak Ada Koneksi — MIS Guppi Rumbia</title>
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
            padding: 2rem;
            text-align: center;
            max-width: 360px;
            width: 100%;
            box-shadow: 0 4px 20px rgba(0,0,0,.08);
        }
        .icon   { font-size: 4rem; margin-bottom: 1rem; }
        h1      { font-size: 1.25rem; color: #111827; margin-bottom: .5rem; }
        p       { color: #6b7280; font-size: .9rem; margin-bottom: 1.25rem; }
        .btn    {
            display: inline-block;
            padding: .75rem 1.5rem;
            background: #1a56db;
            color: white;
            border-radius: 10px;
            text-decoration: none;
            font-weight: 600;
            cursor: pointer;
            border: none;
            font-size: 1rem;
        }
    </style>
</head>
<body>
<div class="card">
    <div class="icon">📡</div>
    <h1>Tidak Ada Koneksi Internet</h1>
    <p>
        Periksa koneksi WiFi atau data seluler Anda,
        lalu coba lagi.
    </p>
    <button class="btn" onclick="window.location.reload()">
        🔄 Coba Lagi
    </button>
</div>
</body>
</html>`;
}

// ═══════════════════════════════════════════════════════════════
// EVENT: MESSAGE — terima perintah dari halaman
// ═══════════════════════════════════════════════════════════════
self.addEventListener("message", (event) => {
    // Paksa update service worker
    if (event.data?.type === "SKIP_WAITING") {
        self.skipWaiting();
    }

    // Hapus semua cache (untuk logout / reset)
    if (event.data?.type === "CLEAR_CACHE") {
        caches
            .keys()
            .then((keys) => Promise.all(keys.map((k) => caches.delete(k))));
    }
});

// ═══════════════════════════════════════════════════════════════
// BACKGROUND SYNC — sync otomatis bahkan saat tab tertutup
// (Browser akan retry saat koneksi kembali)
// ═══════════════════════════════════════════════════════════════
self.addEventListener('sync', async (event) => {
    if (event.tag === 'sync-absensi') {
        console.log('[SW] Background sync absensi dimulai...');
        event.waitUntil(doBackgroundSync());
    }
});

async function doBackgroundSync() {
    // Kirim pesan ke semua tab yang aktif
    // agar mereka yang handle sync (karena IndexedDB ada di tab)
    const clients = await self.clients.matchAll({ type: 'window' });

    if (clients.length > 0) {
        clients.forEach(client => {
            client.postMessage({ type: 'DO_SYNC' });
        });
    }
}