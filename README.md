# PPID Pengadilan Negeri Sukadana

Portal resmi PPID yang dibangun sebagai **plain-local Next.js + TypeScript** starter untuk dikembangkan ke lingkungan produksi pengadilan.

> **Status:** UI dan alur interaksi utama sudah berfungsi sebagai demo. Data saat ini berupa data demo statis dan belum terhubung ke database atau layanan storage/email produksi.

## Analisis referensi & sitemap

Referensi yang diberikan adalah `https://ppid.pn-prabumulih.go.id/permohonan-informasi`. Saat dianalisis pada 30 September 2026, halaman berada di balik Cloudflare sehingga struktur internalnya tidak dapat diverifikasi dari lingkungan ini. Karena itu implementasi tidak mengklaim menyalin fitur spesifik referensi; IA dan istilah mengikuti praktik umum portal PPID pengadilan serta brief yang diberikan.

Sitemap tahap awal:

- `/` — Beranda, CTA permohonan, cek status, informasi terkini, kontak.
- `/informasi-publik` — pencarian, filter kategori, daftar dan tombol unduh.
- `/berita` — kartu berita dan pengumuman.
- `/permohonan-informasi` — formulir tiga langkah, validasi browser, upload file, persetujuan, nomor tiket, bukti cetak.
- `/cek-status` — pencarian nomor permohonan dan timeline status.
- `/keberatan` — pengajuan keberatan terhadap permohonan.
- `/admin/login` — layar login petugas.
- `/admin` — dashboard statistik, chart, dan permohonan terbaru.
- `/admin/permohonan` — tabel, filter status dan export placeholder.
- `/admin/settings` — identitas, kode, kontak, format nomor, batas upload.
- `/admin/laporan` — ringkasan dan rekap status.

Alur pengguna: **Pemohon → isi data → unggah identitas → kirim → nomor PPID-SKD-2026-000124 → cek timeline → (opsional) ajukan keberatan.** Alur petugas: **login → dashboard → verifikasi → ubah status → siapkan jawaban → audit/notifikasi** (bagian server-side masih roadmap).

## Menjalankan lokal

```bash
npm install
npm run dev
# buka http://localhost:3000
```

## Build produksi

```bash
npm run lint
npm run build
npm start
```

## Teknologi

- Next.js 16 App Router, TypeScript, CSS responsif.
- Route static/SSR-ready untuk halaman publik dan dashboard.
- Bahasa antarmuka: Indonesia.
- Identitas awal: PPID Pengadilan Negeri Sukadana.

## Pemetaan ke backend produksi

Untuk memenuhi spesifikasi produksi, tambahkan API/database dengan model berikut: `users`, `roles`, `permissions`, `information_requests`, `request_documents`, `request_status_histories`, `request_responses`, `objections`, `public_information`, `public_information_categories`, `documents`, `news`, `site_settings`, `ppid_officers`, `audit_logs`, `notifications`, dan `password_resets`.

Rekomendasi: PostgreSQL + Prisma, session cookie httpOnly, Argon2/bcrypt, Zod validation, object storage private/public terpisah, signed download URL, rate limiting, Cloudflare Turnstile, SMTP provider, dan audit log append-only. NIK, dokumen identitas, alamat dan kontak wajib dienkripsi/ditutup pada tampilan publik. Jangan menyimpan file privat di `public/`.

### Environment produksi (contoh)

Salin `.env.example` menjadi `.env.local` dan isi hanya pada server/secret manager:

```text
DATABASE_URL=
SESSION_SECRET=
SMTP_HOST=
SMTP_PORT=587
SMTP_USER=
SMTP_PASSWORD=
TURNSTILE_SECRET_KEY=
STORAGE_ENDPOINT=
STORAGE_ACCESS_KEY=
STORAGE_SECRET_KEY=
STORAGE_BUCKET_PUBLIC=
STORAGE_BUCKET_PRIVATE=
PUBLIC_SITE_URL=https://ppid.pn-sukadana.go.id
```

Mekanisme **Create Initial Admin** yang direkomendasikan: CLI satu kali yang membaca `INITIAL_ADMIN_EMAIL` dan password dari secret manager, memaksa pergantian password pertama kali, lalu menghapus/menonaktifkan secret bootstrap.

## Deployment Linux/VPS

1. Siapkan Node.js LTS, PostgreSQL, reverse proxy Nginx/Caddy, TLS dan object storage.
2. Set seluruh environment variable di secret manager/server, bukan di Git.
3. `npm ci && npm run build`.
4. Jalankan `npm start` di systemd/PM2, proxy HTTPS ke port aplikasi.
5. Jalankan migration Prisma dan seed demo hanya pada environment demo.
6. Backup database terjadwal terenkripsi di luar folder publik; backup object storage privat terpisah.

## Seed dan demo

Data yang terlihat di UI ditandai `DATA DEMO` pada admin. Sebelum produksi, ganti data contoh, identitas, alamat, dasar hukum, persyaratan, biaya dan SLA setelah diverifikasi pihak Pengadilan. Jangan mengarang atau mempublikasikan dasar hukum/prosedur yang belum disahkan.

## Penggantian identitas pengadilan

Ubah nilai default pada `components/portal.tsx`, `components/admin.tsx`, metadata di `app/layout.tsx`, dan konfigurasi backend/CMS saat sudah tersedia. Versi produksi sebaiknya memindahkan seluruh identitas ke tabel `site_settings` dan mengeditnya melalui `/admin/settings`, termasuk logo, kontak, Google Maps, format nomor, batas upload, footer, pejabat PPID dan template notifikasi.

## Fitur yang sudah berfungsi di starter ini

- Homepage resmi responsif dengan navigasi desktop/mobile.
- Informasi publik: search, filter kategori, tabel, unduh demo.
- Berita, kontak, profile/CTA.
- Form permohonan 3 langkah, validasi required/type/email, file picker, persetujuan, nomor tiket demo, salin, cetak.
- Cek status dengan timeline demo.
- Form keberatan dengan nomor tiket demo.
- Admin login demo, dashboard, statistik, chart, daftar permohonan, filter, settings form, laporan.
- Metadata dasar SEO, `sitemap.xml`, `robots.txt`, semantic labels, focus states, responsive mobile table.

## Belum selesai / harus dikerjakan sebelum produksi

- Persistensi PostgreSQL/Prisma dan API server.
- Session auth, RBAC granular, password reset, rate limit, CSRF, Turnstile.
- Validasi MIME server-side, virus scan, private object storage dan signed downloads.
- Email/SMS notifications, response documents, audit log append-only, export XLSX/PDF/CSV nyata.
- CMS CRUD penuh untuk berita, informasi, dokumen, pengguna dan settings.
- Penetapan konten hukum, SLA, biaya, persyaratan dan branding final oleh Pengadilan.
