USE ppid_sukadana;
INSERT INTO public_information(title,category,description,year,is_published,published_at) VALUES
('Laporan Layanan Informasi Publik Tahun 2025','Informasi Berkala','Laporan layanan informasi publik.','2025',1,NOW()),
('Daftar Informasi Publik Pengadilan Negeri Sukadana','Informasi Setiap Saat','Daftar informasi publik yang tersedia.','2026',1,NOW()),
('Standar Pelayanan Informasi Publik','Informasi Setiap Saat','Standar layanan yang perlu diverifikasi sebelum produksi.','2026',1,NOW()),
('Pengumuman Layanan Terpadu Satu Pintu','Informasi Serta Merta','Pengumuman layanan untuk masyarakat.','2026',1,NOW());
INSERT INTO news(title,slug,excerpt,category,is_published,published_at) VALUES ('Pembaruan standar layanan informasi publik','pembaruan-standar-layanan','PPID memperbarui kanal layanan informasi publik.','Pengumuman',1,NOW()),('Jam pelayanan PPID Pengadilan Negeri Sukadana','jam-pelayanan-ppid','Layanan informasi tersedia pada hari kerja.','Layanan',1,NOW());
-- Buat admin melalui CLI: php scripts/create_admin.php admin@example.com 'Password-kuat'
