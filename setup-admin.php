<?php
declare(strict_types=1);
require __DIR__ . '/includes/functions.php';
global $config;
$setupKey = (string)($config['setup_key'] ?? '');
if ($setupKey === '' || $setupKey === 'GANTI_SETUP_KEY_SEKALI_PAKAI') { http_response_code(404); exit('Not found'); }
$message = ''; $error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!hash_equals($setupKey, (string)($_POST['setup_key'] ?? '')) || !valid_csrf()) { $error = 'Setup key atau sesi tidak valid.'; }
    elseif (!db()) { $error = 'Database tidak tersambung. Periksa config/config.local.php dan privilege MySQL.'; }
    elseif (!filter_var($_POST['email'] ?? '', FILTER_VALIDATE_EMAIL)) { $error = 'Email tidak valid.'; }
    elseif (strlen((string)($_POST['password'] ?? '')) < 12) { $error = 'Password minimal 12 karakter.'; }
    else { try { $pdo=db(); $stmt=$pdo->prepare('INSERT INTO users(name,email,password_hash,role,is_active) VALUES(?,?,?,?,1)'); $stmt->execute([trim((string)$_POST['name']),trim((string)$_POST['email']),password_hash((string)$_POST['password'],PASSWORD_DEFAULT),'SUPER_ADMIN']); $message='Admin berhasil dibuat. Hapus file setup-admin.php dan hapus setup_key dari config/config.local.php sekarang juga.'; } catch (Throwable $e) { $error = str_contains($e->getMessage(),'Duplicate') ? 'Email admin tersebut sudah terdaftar.' : 'Admin gagal dibuat. Pastikan tabel users sudah di-import.'; } }
}
$csrf = csrf();
?><!doctype html><html lang="id"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Setup Admin PPID</title><style>body{font:15px Arial;background:#09263d;margin:0;padding:40px;color:#14212b}.box{max-width:520px;margin:auto;background:#fff;padding:32px}h1{color:#09263d}label{display:block;font-weight:bold;font-size:13px;margin:16px 0}input{display:block;width:100%;box-sizing:border-box;padding:12px;margin-top:7px;border:1px solid #dbe2e5}button{background:#d6a94b;border:0;padding:13px 18px;font-weight:bold;margin-top:12px}.note{background:#fff7dc;padding:13px;font-size:12px;line-height:1.5}.error{background:#fff0ee;color:#9c372f;padding:12px}.success{background:#eaf4ee;color:#2d7655;padding:12px}</style></head><body><div class="box"><h1>Setup Admin PPID</h1><p>Wizard ini khusus dipakai satu kali pada cPanel tanpa SSH.</p><?php if($error): ?><div class="error"><?= e($error) ?></div><?php endif; ?><?php if($message): ?><div class="success"><?= e($message) ?></div><?php else: ?><div class="note"><b>Penting:</b> Setelah admin berhasil dibuat, hapus file <code>setup-admin.php</code> dan hapus <code>setup_key</code> dari <code>config/config.local.php</code>.</div><form method="post"><input type="hidden" name="csrf" value="<?= e($csrf) ?>"><label>Setup key<input type="password" name="setup_key" required></label><label>Nama admin<input name="name" value="Super Admin" required></label><label>Email admin<input type="email" name="email" required></label><label>Password admin<input type="password" name="password" minlength="12" required></label><button type="submit">Buat admin</button></form><?php endif; ?></div></body></html>
