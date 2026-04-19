const DB_NAME = "mis-guppi-db";
const DB_VERSION = 1;
const STORE_ABSENSI = "absensi_pending";

// ─── Buka / buat database ─────────────────────────────────────
function bukaDB() {
    return new Promise((resolve, reject) => {
        const req = indexedDB.open(DB_NAME, DB_VERSION);

        req.onupgradeneeded = (event) => {
            const db = event.target.result;
            if (!db.objectStoreNames.contains(STORE_ABSENSI)) {
                const store = db.createObjectStore(STORE_ABSENSI, {
                    keyPath: "id",
                    autoIncrement: true,
                });
                store.createIndex("kelas_id", "kelas_id", { unique: false });
                store.createIndex("tanggal", "tanggal", { unique: false });
                store.createIndex("status_sync", "status_sync", {
                    unique: false,
                });
            }
        };

        req.onsuccess = () => resolve(req.result);
        req.onerror = () => reject(req.error);
    });
}

// ─── Simpan absensi ke IndexedDB ──────────────────────────────
// ✅ SATU fungsi saja — sudah include background sync
async function simpanAbsensiOffline(payload) {
    const db = await bukaDB();

    return new Promise(async (resolve, reject) => {
        const tx = db.transaction(STORE_ABSENSI, "readwrite");
        const store = tx.objectStore(STORE_ABSENSI);

        const record = {
            ...payload,
            status_sync: "pending",
            waktu_simpan: new Date().toISOString(),
        };

        const req = store.add(record);

        req.onsuccess = async () => {
            console.log("[Offline] Tersimpan lokal, id:", req.result);

            // Daftarkan background sync kalau browser support
            if ("serviceWorker" in navigator && "SyncManager" in window) {
                try {
                    const reg = await navigator.serviceWorker.ready;
                    await reg.sync.register("sync-absensi");
                    console.log("[Offline] Background sync terdaftar");
                } catch (err) {
                    console.log(
                        "[Offline] Background sync tidak didukung:",
                        err,
                    );
                }
            }

            resolve(req.result);
        };

        req.onerror = () => reject(req.error);
    });
}

// ─── Ambil semua absensi pending ──────────────────────────────
async function getAbsensiPending() {
    const db = await bukaDB();

    return new Promise((resolve, reject) => {
        const tx = db.transaction(STORE_ABSENSI, "readonly");
        const store = tx.objectStore(STORE_ABSENSI);
        const index = store.index("status_sync");
        const req = index.getAll("pending");

        req.onsuccess = () => resolve(req.result);
        req.onerror = () => reject(req.error);
    });
}

// ─── Hapus record setelah berhasil sync ───────────────────────
async function tandaiSudahSync(id) {
    const db = await bukaDB();

    return new Promise((resolve, reject) => {
        const tx = db.transaction(STORE_ABSENSI, "readwrite");
        const store = tx.objectStore(STORE_ABSENSI);
        const req = store.delete(id);

        req.onsuccess = () => resolve();
        req.onerror = () => reject(req.error);
    });
}

// ─── Hitung berapa data pending ───────────────────────────────
async function hitungPending() {
    const pending = await getAbsensiPending();
    return pending.length;
}

// ─── Sync ke server ───────────────────────────────────────────
async function syncKeServer(csrfToken) {
    const pending = await getAbsensiPending();

    if (pending.length === 0) {
        console.log("[Sync] Tidak ada data pending.");
        return { berhasil: 0, gagal: 0 };
    }

    console.log(`[Sync] Mengirim ${pending.length} data...`);

    let berhasil = 0;
    let gagal = 0;

    for (const record of pending) {
        try {
            const response = await fetch("/api/absensi/sync", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": csrfToken,
                    Accept: "application/json",
                },
                body: JSON.stringify({
                    kelas_id: record.kelas_id,
                    tanggal: record.tanggal,
                    absensi: record.absensi,
                }),
            });

            if (response.ok) {
                await tandaiSudahSync(record.id);
                berhasil++;
                console.log(`[Sync] ✅ Record ${record.id} berhasil`);
            } else {
                gagal++;
                console.warn(
                    `[Sync] ❌ Record ${record.id} gagal:`,
                    response.status,
                );
            }
        } catch (err) {
            gagal++;
            console.error(`[Sync] ❌ Error record ${record.id}:`, err);
        }
    }

    return { berhasil, gagal };
}

// ─── Auto sync saat koneksi kembali ───────────────────────────
function setupAutoSync(csrfToken, onSyncDone) {
    window.addEventListener("online", async () => {
        console.log("[Sync] Koneksi kembali — mulai sync...");

        const jumlah = await hitungPending();
        if (jumlah === 0) return;

        tampilkanNotifSync("syncing", jumlah);

        const hasil = await syncKeServer(csrfToken);

        if (hasil.berhasil > 0) tampilkanNotifSync("success", hasil.berhasil);
        if (hasil.gagal > 0) tampilkanNotifSync("error", hasil.gagal);

        if (onSyncDone) onSyncDone(hasil);
    });
}

// ─── Toast notifikasi ─────────────────────────────────────────
function tampilkanNotifSync(tipe, jumlah) {
    document.getElementById("notif-sync")?.remove();

    const config = {
        syncing: {
            bg: "#eff6ff",
            border: "#bfdbfe",
            color: "#1e40af",
            icon: "🔄",
            teks: `Menyinkronkan ${jumlah} data absensi...`,
        },
        success: {
            bg: "#f0fdf4",
            border: "#bbf7d0",
            color: "#166534",
            icon: "✅",
            teks: `${jumlah} absensi berhasil disimpan ke server`,
        },
        error: {
            bg: "#fef2f2",
            border: "#fecaca",
            color: "#991b1b",
            icon: "❌",
            teks: `${jumlah} absensi gagal sync — akan dicoba lagi`,
        },
    };

    const c = config[tipe];
    const div = document.createElement("div");
    div.id = "notif-sync";
    div.style.cssText = `
        position:fixed; top:70px; left:1rem; right:1rem;
        background:${c.bg}; border:1px solid ${c.border}; color:${c.color};
        padding:.75rem 1rem; border-radius:10px; font-size:.875rem;
        font-weight:600; z-index:300; display:flex; align-items:center;
        gap:8px; box-shadow:0 4px 12px rgba(0,0,0,.1);
    `;
    div.innerHTML = `<span>${c.icon}</span><span>${c.teks}</span>`;
    document.body.appendChild(div);

    if (tipe !== "syncing") {
        setTimeout(() => div.remove(), 4000);
    }
}
