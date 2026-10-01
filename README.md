

## Dukungan cPanel

Untuk cPanel, salin `config/config.local.php.example` menjadi `config/config.local.php` dan isi prefix nama database/user dari menu MySQL Databases. Aplikasi membaca konfigurasi lokal tersebut sebelum `.env`, sehingga instalasi dapat dilakukan melalui File Manager tanpa Node.js, Composer, atau SSH. File konfigurasi lokal diblokir oleh `.htaccess` dan tidak boleh dimasukkan ke repository.
