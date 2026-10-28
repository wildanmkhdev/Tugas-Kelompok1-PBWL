# Hadir. — Absensi Mahasiswa

Website absensi mahasiswa sederhana dengan desain **glassmorphism biru** yang
didominasi abu-abu dan putih.

## Fitur

- Register akun
- Login dan logout
- Dashboard absensi harian
- Tombol **Hadir** dan **Tidak Hadir** (satu status per hari)
- Rekap: jumlah hadir, tidak hadir, dan persentase kehadiran
- Riwayat 10 absensi terbaru

## Akun Demo

| Username | Password |
|----------|----------|
| admin    | admin123 |

## Menjalankan

```bash
cd laravel-login
php artisan serve
```

Buka `http://127.0.0.1:8000`.

Tidak membutuhkan database. Data user dan absensi disimpan sebagai JSON di
`storage/app/data/`, session memakai driver `file`.

## Struktur

```
app/Http/Controllers/Controller.php            # helper JSON + cek session
app/Http/Controllers/HomeController.php        # redirect "/" ke login/dashboard
app/Http/Controllers/AuthController.php        # login, register, logout
app/Http/Controllers/AttendanceController.php  # hadir, tidak hadir, riwayat
routes/web.php
resources/views/layouts/app.blade.php
resources/views/auth/login.blade.php
resources/views/auth/register.blade.php
resources/views/dashboard.blade.php
public/css/absensi.css
public/js/absensi.js
tests/Feature/AuthTest.php
tests/Feature/AttendanceTest.php
```

Semua logika berada di controller. Setiap file diberi komentar dokumentasi.

## Pengujian

```bash
php artisan test
```

Test memakai folder data terpisah sehingga data asli tidak terganggu.
# Tugas-Kelompok1-PBWL
