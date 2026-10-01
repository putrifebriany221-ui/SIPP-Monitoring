

## 10. Mode khusus cPanel tanpa SSH

Paket ini dapat dipasang tanpa Node.js dan tanpa Composer. Gunakan **PHP 8.1 atau lebih baru** pada menu **MultiPHP Manager** atau **Select PHP Version**.

Upload ZIP ke `public_html` melalui File Manager, lalu pilih **Extract**. Pastikan `index.php` berada langsung di `public_html`, bukan di dalam folder ZIP tambahan.

Pada menu **MySQL Databases**, buat database dan user. Catat nama lengkap dengan prefix cPanel. Contoh:

```text
Database: akun123_ppid
User:     akun123_ppiduser
```

Import `database/schema.sql` melalui phpMyAdmin. Setelah itu pilih database yang sama dan import `database/seed.sql`.

Di File Manager, salin `config/config.local.php.example` menjadi `config/config.local.php`, lalu edit isinya:

```php
'app_url' => 'https://domain-anda.go.id',
'db' => [
    'host' => 'localhost',
    'port' => '3306',
    'name' => 'akun123_ppid',
    'user' => 'akun123_ppiduser',
    'pass' => 'password-database',
],
```

Isi `session_secret` dengan string acak panjang dan isi key Cloudflare Turnstile. File `config/config.local.php` sudah diblokir oleh `.htaccess` dan tidak boleh dibagikan.

Jika tidak tersedia SSH, buat akun admin dengan cara berikut: buat hash password menggunakan PHP CLI di komputer lokal atau minta administrator hosting menjalankan `php scripts/create_admin.php`. Jangan membuat atau mengunggah file PHP sementara yang mencetak password/hash ke browser.

Setelah upload, buka `https://domain-anda.go.id/index.php?page=admin-login`. Jika gagal, periksa `error_log` cPanel dan jalankan `php scripts/check_hosting.php` bila SSH tersedia. Hapus cache browser dan cookie lama setelah mengganti domain atau HTTPS.
