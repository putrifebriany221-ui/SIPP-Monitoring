<?php
declare(strict_types=1);
$config = require __DIR__ . '/../config/config.php';
function db(): ?PDO { static $pdo = null; static $error = null; global $config; if ($pdo instanceof PDO) return $pdo; if ($error instanceof Throwable) return null; try { $dsn = "mysql:host={$config['db']['host']};port={$config['db']['port']};dbname={$config['db']['name']};charset=utf8mb4"; $pdo = new PDO($dsn, $config['db']['user'], $config['db']['pass'], [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, PDO::ATTR_EMULATE_PREPARES => false]); return $pdo; } catch (Throwable $e) { $error = $e; error_log('[PPID DB] ' . $e->getMessage()); return null; } }
function e(mixed $value): string { return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8'); }
function url(string $page = 'home', array $params = []): string { return 'index.php?' . http_build_query(array_merge(['page' => $page], $params)); }
function redirect(string $page, array $params = []): never { header('Location: ' . url($page, $params)); exit; }
function csrf(): string { if (empty($_SESSION['csrf'])) $_SESSION['csrf'] = bin2hex(random_bytes(32)); return $_SESSION['csrf']; }
function valid_csrf(): bool { return hash_equals($_SESSION['csrf'] ?? '', $_POST['csrf'] ?? ''); }
function flash(string $type, string $message): void { $_SESSION['flash'] = ['type' => $type, 'message' => $message]; }
function take_flash(): ?array { $f = $_SESSION['flash'] ?? null; unset($_SESSION['flash']); return $f; }
function demo_info(): array { return [['title'=>'Laporan Layanan Informasi Publik Tahun 2025','category'=>'Informasi Berkala','year'=>2025,'type'=>'PDF'],['title'=>'Daftar Informasi Publik Pengadilan Negeri Sukadana','category'=>'Informasi Setiap Saat','year'=>2026,'type'=>'PDF'],['title'=>'Standar Pelayanan Informasi Publik','category'=>'Informasi Setiap Saat','year'=>2026,'type'=>'PDF'],['title'=>'Pengumuman Layanan Terpadu Satu Pintu','category'=>'Informasi Serta Merta','year'=>2026,'type'=>'PDF']]; }
function get_info(): array { $pdo = db(); if (!$pdo) return demo_info(); try { $rows = $pdo->query("SELECT * FROM public_information WHERE is_published=1 ORDER BY published_at DESC")->fetchAll(); return $rows ?: demo_info(); } catch (Throwable $e) { return demo_info(); } }
function get_status(string $number): ?array { $pdo = db(); if (!$pdo) return $number ? ['request_number'=>$number,'applicant_name'=>'Siti Rahmawati','status'=>'Diajukan','created_at'=>'2026-09-30 10:24:00'] : null; $stmt=$pdo->prepare('SELECT * FROM information_requests WHERE request_number=?'); $stmt->execute([$number]); return $stmt->fetch() ?: null; }
function verify_captcha(): bool {
  global $config;
  if (!empty($_POST['website'])) return false;
  $secret = $config['captcha']['secret_key'] ?? '';
  if ($secret === '') return true; // local development fallback; honeypot remains active
  $token = trim((string)($_POST['cf-turnstile-response'] ?? ''));
  if ($token === '') return false;
  $payload = http_build_query(['secret'=>$secret,'response'=>$token,'remoteip'=>$_SERVER['REMOTE_ADDR'] ?? '']);
  if (function_exists('curl_init')) {
    $ch=curl_init('https://challenges.cloudflare.com/turnstile/v0/siteverify');
    curl_setopt_array($ch,[CURLOPT_POST=>true,CURLOPT_POSTFIELDS=>$payload,CURLOPT_RETURNTRANSFER=>true,CURLOPT_TIMEOUT=>8]);
    $response=curl_exec($ch); curl_close($ch);
  } else { $response=@file_get_contents('https://challenges.cloudflare.com/turnstile/v0/siteverify', false, stream_context_create(['http'=>['method'=>'POST','header'=>'Content-Type: application/x-www-form-urlencoded','content'=>$payload,'timeout'=>8]])); }
  $result=json_decode((string)$response,true); return !empty($result['success']);
}
function captcha_widget(): string {
  global $config; $site=e($config['captcha']['site_key'] ?? '');
  if ($site === '') return '<div class="captcha-note">Perlindungan anti-spam aktif melalui honeypot. Aktifkan Cloudflare Turnstile sebelum produksi.</div><input type="text" name="website" class="hp" tabindex="-1" autocomplete="off">';
  return '<script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script><div class="cf-turnstile" data-sitekey="'.$site.'"></div><input type="text" name="website" class="hp" tabindex="-1" autocomplete="off">';
}
function admin_required(): void { if (empty($_SESSION['admin_id'])) redirect('admin-login'); }

function db_error_hint(): string { return 'Koneksi database belum tersedia. Periksa DB_HOST, DB_NAME, DB_USER, DB_PASS, privilege user, dan import database/schema.sql.'; }
