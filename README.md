<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

# 🎓 MIS Guppi Rumbia

Sistem Informasi Sekolah berbasis web yang dibangun menggunakan Laravel untuk mengelola data siswa, guru, jadwal, dan absensi secara digital.

---

## 🚀 Fitur Utama

### 🔐 Autentikasi Multi Role

* Admin
* Guru
* Kepala Sekolah

---

### 👨‍🎓 Manajemen Siswa

* Tambah, edit, hapus data siswa
* NIS & NISN
* Relasi dengan kelas

---

### 👨‍🏫 Manajemen Guru

* CRUD data guru
* Terintegrasi dengan jadwal

---

### 📅 Jadwal Pelajaran

* Input jadwal
* Validasi bentrok otomatis
* Relasi guru & kelas

---

### 📊 Absensi Siswa

* Input absensi oleh guru
* Status:

  * Hadir
  * Sakit
  * Izin
  * Alpha

---

### 📈 Rekap Absensi

* Rekap per kelas
* Rekap per siswa
* Filter bulanan

---

### 📱 Progressive Web App (PWA)

* Bisa di-install di HP
* Tampilan seperti aplikasi
* Support offline terbatas

---

## 🛠️ Teknologi yang Digunakan

* Laravel
* PHP 8+
* MySQL
* Blade Template
* JavaScript
* Service Worker (PWA)

---

## ⚙️ Instalasi

### 1. Clone Repository

```bash
git clone https://github.com/khayranxald/mis-guppi-rumbia.git
cd mis-guppi-rumbia
```

---

### 2. Install Dependency

```bash
composer install
```

---

### 3. Setup Environment

```bash
cp .env.example .env
php artisan key:generate
```

---

### 4. Setup Database

* Buat database di MySQL
* Edit file `.env`

```env
DB_DATABASE=nama_database
DB_USERNAME=root
DB_PASSWORD=
```

---

### 5. Migrasi Database

```bash
php artisan migrate
```

---

### 6. Jalankan Server

```bash
php artisan serve
```

---

## 📱 Akses dari HP (PWA)

1. Jalankan:

```bash
php artisan serve --host=0.0.0.0
```

2. Buka di HP:

```
http://IP-KOMPUTER:8000
```

3. Install:

* Safari (iPhone): Share → Add to Home Screen
* Android: Install App

---

## 🧪 Testing

* Login sesuai role
* Input absensi
* Cek rekap
* Test offline mode (PWA)

---

## 📂 Struktur Folder Penting

```
app/
resources/views/
routes/web.php
public/
```

---

## ⚠️ Catatan

* File `.env` tidak disertakan
* Jalankan `composer install` sebelum menjalankan project
* Pastikan database sudah dibuat

---

## 👨‍💻 Developer

Dikembangkan oleh:
**Khayran**

---

## ⭐ Kontribusi

Silakan fork dan pull request jika ingin berkontribusi.

---

## 📄 Lisensi

Project ini bersifat open-source untuk pembelajaran.
