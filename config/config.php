<?php
declare(strict_types=1);
$root = dirname(__DIR__);
$dotenv = $root . '/.env';
if (is_readable($dotenv)) {
  foreach (file($dotenv, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) ?: [] as $line) {
    $line = trim($line);
    if ($line === '' || str_starts_with($line, '#') || !str_contains($line, '=')) continue;
    [$key, $value] = explode('=', $line, 2);
    $key = trim($key); $value = trim($value);
    if (($value[0] ?? '') === '"' && str_ends_with($value, '"')) $value = substr($value, 1, -1);
    if (($value[0] ?? '') === "'" && str_ends_with($value, "'")) $value = substr($value, 1, -1);
    if (getenv($key) === false) putenv($key . '=' . $value);
  }
}
$env = static function (string $key, string $default = ''): string { $value = getenv($key); return $value === false ? $default : $value; };
$secure = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || (($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https');
if (session_status() !== PHP_SESSION_ACTIVE) {
  session_set_cookie_params(['lifetime'=>0, 'path'=>'/', 'secure'=>$secure, 'httponly'=>true, 'samesite'=>'Lax']);
  session_start();
}
const APP_NAME = 'PPID Pengadilan Negeri Sukadana';
const APP_CODE = 'SKD';
const UPLOAD_MAX_BYTES = 5242880;
return [
  'db' => ['host' => $env('DB_HOST', '127.0.0.1'), 'port' => $env('DB_PORT', '3306'), 'name' => $env('DB_NAME', 'ppid_sukadana'), 'user' => $env('DB_USER', 'root'), 'pass' => $env('DB_PASS', '')],
  'base_url' => rtrim($env('APP_URL', 'http://localhost:8080'), '/'),
  'session_secret' => $env('SESSION_SECRET', 'change-this-before-production'),
  'captcha' => ['site_key' => $env('TURNSTILE_SITE_KEY', ''), 'secret_key' => $env('TURNSTILE_SECRET_KEY', '')],
];
