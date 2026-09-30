<?php
declare(strict_types=1);
if (PHP_SAPI !== 'cli') { http_response_code(404); exit('Not found'); }
require __DIR__ . '/../includes/functions.php';
global $config;
function result(string $name, bool $ok, string $detail): void { echo ($ok ? '[OK]  ' : '[FAIL]') . " {$name}: {$detail}\n"; }
echo "PPID hosting diagnostic\n========================\n";
result('PHP version', PHP_VERSION_ID >= 80100, PHP_VERSION);
result('PDO MySQL', extension_loaded('pdo_mysql'), extension_loaded('pdo_mysql') ? 'loaded' : 'missing');
result('mbstring', extension_loaded('mbstring'), extension_loaded('mbstring') ? 'loaded' : 'missing');
result('fileinfo', extension_loaded('fileinfo'), extension_loaded('fileinfo') ? 'loaded' : 'missing');
result('.env', is_readable(dirname(__DIR__) . '/.env'), is_readable(dirname(__DIR__) . '/.env') ? 'found' : 'not found; hosting environment variables may still be used');
$result = db();
result('MySQL connection', $result instanceof PDO, $result instanceof PDO ? 'connected to configured database' : 'failed; check DB_HOST/DB_NAME/DB_USER/DB_PASS');
if ($result instanceof PDO) {
  try { $tables = $result->query("SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = DATABASE() AND table_name IN ('users','information_requests','public_information')")->fetchColumn(); result('Schema', (int)$tables >= 3, "{$tables}/3 core tables found"); $users = $result->query('SELECT COUNT(*) FROM users')->fetchColumn(); result('Admin users', (int)$users > 0, "{$users} user(s) found"); } catch (Throwable $e) { result('Schema query', false, 'database connected but schema query failed'); }
}
$resultPath = session_save_path() ?: sys_get_temp_dir(); result('Session directory', is_dir($resultPath) && is_writable($resultPath), $resultPath);
echo "\nJika MySQL gagal, periksa nama database/user di cPanel dan import database/schema.sql.\n";
