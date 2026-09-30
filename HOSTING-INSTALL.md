# Panduan Instalasi Manual di Hosting

## 1. Upload file

Upload seluruh isi arsip project ke document root hosting, biasanya `public_html/`. Struktur `index.php`, `.htaccess`, `assets/`, `config/`, `includes/`, `database/`, `scripts/`, dan `storage/` harus tetap dipertahankan.

Jika hosting mengizinkan document root di luar `public_html`, lebih aman letakkan `storage/private/` di luar document root. Jika tidak memungkinkan, pastikan aturan server memblokir akses langsung ke folder tersebut.

## 2. Buat database MySQL

Melalui cPanel atau panel hosting, buat:

- database MySQL;
- user MySQL;
- password user;
- privilege penuh user tersebut ke database.

Buka phpMyAdmin, pilih database, lalu import `database/schema.sql` dan setelah itu `database/seed.sql`.

## 3. Atur konfigurasi

Buat environment variable melalui panel hosting jika tersedia. Jika hosting hanya menyediakan file konfigurasi, salin `.env.example` menjadi `.env` dan isi:

```text
APP_URL=https://domain-anda.go.id
DB_HOST=localhost
DB_PORT=3306
DB_NAME=nama_database
DB_USER=nama_user
DB_PASS=password_database
SESSION_SECRET=string-acak-panjang
```

Pada sebagian shared hosting, environment variable PHP tidak otomatis membaca file `.env`. Untuk kondisi tersebut, isi nilai database pada `config/config.php` melalui mekanisme konfigurasi hosting yang aman, dan jangan mengunggah file berisi password ke repository publik.

## 4. Atur PHP

Gunakan PHP 8.1 atau lebih baru dan aktifkan ekstensi:

- `pdo_mysql`;
- `mbstring`;
- `fileinfo`.

Pastikan folder `storage/private/` dan `storage/public/` dapat ditulis oleh PHP, tetapi tidak dapat mengeksekusi script.

## 5. Buat admin awal

Jika hosting menyediakan SSH:

```bash
php scripts/create_admin.php admin@domain-anda.go.id 'Password-kuat-minimal-12-karakter' 'Super Admin'
```

Jika tidak ada SSH, buat admin melalui script lokal/CLI yang aman atau minta administrator hosting menjalankan command tersebut. Jangan menaruh password admin di source code.

Login tersedia melalui:

```text
https://domain-anda.go.id/index.php?page=admin-login
```

## 6. Pengujian setelah upload

Periksa halaman berikut:

- `/index.php`
- `/index.php?page=info`
- `/index.php?page=request`
- `/index.php?page=status`
- `/index.php?page=objection`
- `/index.php?page=admin-login`

Lakukan satu pengajuan demo dan pastikan record tersimpan di tabel `information_requests`.

## 7. Keamanan sebelum produksi

Aktifkan HTTPS, ubah `SESSION_SECRET`, ganti password admin, hapus data demo yang tidak diperlukan, dan verifikasi seluruh dasar hukum, persyaratan, biaya, SLA, identitas, alamat, email, serta kontak resmi Pengadilan. Jangan membuka akses publik ke backup database, `.env`, file SQL, dokumen KTP, atau folder privat.

## 8. CAPTCHA / anti-spam

Form Permohonan Informasi dan Pengajuan Keberatan menggunakan **Cloudflare Turnstile** bila key sudah dikonfigurasi. Buat widget pada Cloudflare Turnstile, lalu set environment variable berikut pada hosting:

```text
TURNSTILE_SITE_KEY=site-key-dari-cloudflare
TURNSTILE_SECRET_KEY=secret-key-dari-cloudflare
```

`TURNSTILE_SECRET_KEY` hanya boleh berada di server dan tidak boleh masuk ke HTML, Git, atau ZIP publik. Server melakukan verifikasi ke endpoint resmi Cloudflare sebelum menyimpan data. Saat key belum diisi pada development, form memakai honeypot anti-spam agar preview tetap bisa digunakan; **sebelum produksi wajib mengisi kedua key Turnstile**.

## 9. Jika admin tidak bisa login

Jalankan dari SSH pada folder project:

```bash
php scripts/check_hosting.php
```

Semua baris idealnya berstatus `[OK]`. Jika `MySQL connection` gagal, koreksi nilai `DB_HOST`, `DB_NAME`, `DB_USER`, dan `DB_PASS`. Pada cPanel, nama database dan user sering otomatis diberi prefix akun, misalnya `akun_ppid_sukadana`, bukan hanya `ppid_sukadana`.

Jika koneksi berhasil tetapi `Admin users` bernilai `0`, buat admin:

```bash
php scripts/create_admin.php admin@domain-anda.go.id 'Password-kuat-minimal-12-karakter' 'Super Admin'
```

Jika tidak ada SSH, minta administrator hosting menjalankan command tersebut. Jangan membuat hash password secara manual dan jangan memasukkan password ke file PHP.

Versi ini membaca file `.env` secara langsung, sehingga file `.env` harus berada satu folder dengan `index.php`, bukan di dalam `config/`. Pastikan nama file benar-benar `.env`, bukan `.env.txt`. Setelah perubahan konfigurasi, hapus cookie/session browser lalu buka halaman login kembali.
