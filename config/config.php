<?php
declare(strict_types=1);
session_start();
const APP_NAME = 'PPID Pengadilan Negeri Sukadana';
const APP_CODE = 'SKD';
const UPLOAD_MAX_BYTES = 5242880;
$env = static function (string $key, string $default = ''): string { $value = getenv($key); return $value === false ? $default : $value; };
return [
  'db' => ['host' => $env('DB_HOST', '127.0.0.1'), 'port' => $env('DB_PORT', '3306'), 'name' => $env('DB_NAME', 'ppid_sukadana'), 'user' => $env('DB_USER', 'root'), 'pass' => $env('DB_PASS', '')],
  'base_url' => rtrim($env('APP_URL', 'http://localhost:8080'), '/'),
  'session_secret' => $env('SESSION_SECRET', 'change-this-before-production'),
];
