<?php
declare(strict_types=1);
require __DIR__ . '/../includes/functions.php';
if ($argc < 3) { fwrite(STDERR, "Usage: php scripts/create_admin.php email@example.com 'Password-kuat' [Nama]\n"); exit(1); }
$email = filter_var($argv[1], FILTER_VALIDATE_EMAIL); $password = $argv[2]; $name = $argv[3] ?? 'Super Admin';
if (!$email || strlen($password) < 12) { fwrite(STDERR, "Email valid dan password minimal 12 karakter wajib.\n"); exit(1); }
$pdo = db(); if (!$pdo) { fwrite(STDERR, "Koneksi MySQL gagal. Periksa environment variables.\n"); exit(1); }
$stmt=$pdo->prepare('INSERT INTO users(name,email,password_hash,role) VALUES(?,?,?,?)'); $stmt->execute([$name,$email,password_hash($password,PASSWORD_DEFAULT),'SUPER_ADMIN']); echo "Admin berhasil dibuat: {$email}\n";
