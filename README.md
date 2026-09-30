# PPID Pengadilan Negeri Sukadana — PHP + MySQL

Versi proyek ini menggunakan **PHP native, HTML semantik, CSS responsif, PDO, dan MySQL**. Tidak lagi membutuhkan Node.js atau Next.js.

## Struktur

- `index.php` — front controller dan route publik/admin.
- `config/config.php` — konfigurasi MySQL dan aplikasi.
- `includes/functions.php` — PDO, escaping, CSRF, helper, fallback demo.
- `includes/layout.php` — header/footer publik dan layout admin.
- `assets/css/style.css` — desain responsif navy/gold.
- `database/schema.sql` — tabel MySQL dengan foreign key dan index.
- `database/seed.sql` — data contoh informasi publik dan berita.
- `scripts/create_admin.php` — membuat admin dengan password hashing.
- `storage/private` — dokumen privat, tidak boleh diakses langsung.
- `storage/public` — dokumen publik setelah pemeriksaan akses.

## Menjalankan lokal

```bash
cp .env.example .env
# export variable dari .env atau set di Apache/PHP-FPM
mysql -u root -p < database/schema.sql
mysql -u root -p ppid_sukadana < database/seed.sql
php scripts/create_admin.php admin@pn-sukadana.go.id 'GantiPasswordMinimal12Karakter' 'Super Admin'
php -S 0.0.0.0:8080
```

Buka `http://localhost:8080`.

Login admin berada di `index.php?page=admin-login`. Jangan memakai password demo pada produksi.

## Database

Tabel utama: `users`, `public_information`, `information_requests`, `request_status_histories`, `request_documents`, `objections`, `news`, dan `audit_logs`.

Semua query aplikasi menggunakan PDO prepared statements. Sebelum produksi, aktifkan MySQL user khusus aplikasi dengan privilege minimum dan backup terenkripsi di luar web root.

## URL

Pretty URL dapat dipakai setelah Apache/Nginx mengarahkan seluruh request ke `index.php`. Tanpa rewrite, gunakan query route:

- `index.php?page=info`
- `index.php?page=request`
- `index.php?page=status&number=PPID-SKD-2026-000124`
- `index.php?page=objection`
- `index.php?page=admin-login`
- `index.php?page=admin`
- `index.php?page=admin-requests`
- `index.php?page=admin-settings`
- `index.php?page=admin-reports`

## Keamanan

- Password admin menggunakan `password_hash()` dan `password_verify()`.
- Form POST memakai token CSRF.
- Output di-escape dengan `htmlspecialchars`.
- Query memakai prepared statements.
- Dokumen privat ditempatkan di `storage/private` dan tidak boleh dilayani sebagai file publik.
- Upload produksi wajib menambah pemeriksaan MIME server-side, sanitasi nama, batas ukuran, antivirus, dan download melalui endpoint terautentikasi.
- Tambahkan HTTPS, secure/httpOnly/SameSite session cookie, rate limit login, timeout session, Turnstile, audit log lengkap, dan backup database.

## Status implementasi

Sudah tersedia: portal publik, informasi publik, berita, form permohonan MySQL, nomor permohonan, cek status, keberatan, login admin, dashboard, daftar permohonan, settings demo, laporan, schema, seed, dan create-admin CLI.

Sebelum produksi: email notification, upload persistence ke `request_documents`, CRUD CMS lengkap, perubahan status dari admin, response documents, export CSV/PDF/Excel, RBAC granular, reset password, storage object privat, dan konten resmi yang diverifikasi Pengadilan.
