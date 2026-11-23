# Sistem Inventaris Barang Perpustakaan

Aplikasi web untuk mengelola inventaris barang perpustakaan menggunakan Laravel 12. Sistem ini mencakup pengelolaan barang, kategori, lokasi, pegawai, pengguna, peminjaman, pengembalian, pemindahan, kerusakan, barang hilang, serta laporan analitik.

## Fitur

- Autentikasi pengguna dengan peran **Admin** dan **Pegawai**
- Dashboard ringkasan inventaris
- Manajemen data barang beserta foto dan satuan individual
- Manajemen kategori dan lokasi penyimpanan
- Manajemen data pegawai dan pengguna
- Peminjaman dan pengembalian barang
- Pemindahan barang antar lokasi atau pegawai
- Pelaporan dan persetujuan barang hilang
- Peringatan barang terlambat dikembalikan
- Riwayat transaksi setiap barang
- Analitik dan ekspor data ke CSV
- Panduan penggunaan aplikasi

## Teknologi

- PHP 8.2 atau lebih baru
- Laravel 12
- MySQL atau SQLite
- Vite
- Tailwind CSS 4
- Bootstrap

Versi PHP yang digunakan pada lingkungan pengembangan saat ini: **PHP 8.5.12**.

## Persyaratan

- XAMPP atau lingkungan PHP/Apache yang setara
- PHP 8.2 atau lebih baru
- Composer
- Node.js dan npm
- MySQL/MariaDB atau SQLite

## Instalasi dengan XAMPP

1. Pindahkan atau clone proyek ke folder `C:\xampp\htdocs\Inventaris_barang_laravel22`.

2. Masuk ke folder proyek:

   ```powershell
   cd C:\xampp\htdocs\Inventaris_barang_laravel22
   ```

3. Instal dependensi PHP dan JavaScript:

   ```powershell
   composer install
   npm install
   ```

4. Buat file konfigurasi dan application key:

   ```powershell
   copy .env.example .env
   php artisan key:generate
   ```

5. Konfigurasi database pada file `.env`.

   Contoh untuk MySQL/XAMPP:

   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=inventaris_barang
   DB_USERNAME=root
   DB_PASSWORD=
   ```

   Buat database `inventaris_barang` melalui phpMyAdmin, lalu jalankan migrasi dan seeder:

   ```powershell
   php artisan migrate --seed
   ```

   Untuk SQLite, pastikan file `database/database.sqlite` sudah ada, kemudian atur `.env`:

   ```env
   DB_CONNECTION=sqlite
   ```

   ```powershell
   New-Item -ItemType File database/database.sqlite -Force
   php artisan migrate --seed
   ```

6. Jalankan aplikasi:

   ```powershell
   php artisan serve
   npm run dev
   ```

7. Buka `http://127.0.0.1:8000` di browser.

## Akun Demo

Seeder menyediakan akun berikut:

| Peran | Username | Password |
| --- | --- | --- |
| Admin | `admin` | `password` |
| Pegawai | `pegawai` | `password` |

Ganti password dan data demo sebelum menggunakan aplikasi di lingkungan produksi.

## Perintah Penting

Menjalankan migrasi:

```powershell
php artisan migrate
```

Mengisi ulang data awal dan data demo:

```powershell
php artisan db:seed
```

Menghapus seluruh tabel, migrasi ulang, lalu menjalankan seeder:

```powershell
php artisan migrate:fresh --seed
```

Perintah ini akan menghapus seluruh data database.

Menjalankan pengujian:

```powershell
php artisan test
```

Menjalankan mode pengembangan Laravel, queue, log, dan Vite sekaligus:

```powershell
composer run dev
```

## Struktur Utama

```text
app/
  Http/Controllers/    Kontroler aplikasi
  Http/Middleware/     Middleware autentikasi dan role
  Models/              Model kategori, barang, lokasi, pegawai, transaksi, dan pengguna
database/
  migrations/          Struktur tabel database
  seeders/             Data awal dan data demo
resources/views/       Tampilan Blade
routes/web.php         Definisi route aplikasi
public/                Asset publik dan logo
tests/                 Pengujian aplikasi
```