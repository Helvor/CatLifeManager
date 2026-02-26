<?php
// Charger l'autoloader Composer
require_once __DIR__ . '/vendor/autoload.php';

// Charger le fichier .env s'il existe (silencieux si absent)
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->safeLoad();

// ─── Configuration de l'application ───────────────────────────────────────

define('APP_ENV', $_ENV['APP_ENV'] ?? 'development');
define('APP_URL', $_ENV['APP_URL'] ?? 'http://localhost');

define('DB_PATH',    $_ENV['DB_PATH']    ?: __DIR__ . '/database/catlife.db');
define('UPLOAD_DIR', $_ENV['UPLOAD_DIR'] ?: __DIR__ . '/uploads/');
define('MAX_UPLOAD_SIZE', 10 * 1024 * 1024); // 10 MB

// ─── Session ───────────────────────────────────────────────────────────────
session_start();

// ─── Timezone ──────────────────────────────────────────────────────────────
date_default_timezone_set('Europe/Brussels');

// ─── Erreurs (dev uniquement) ──────────────────────────────────────────────
if (APP_ENV === 'development') {
    ini_set('display_errors', 1);
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', 0);
    error_reporting(0);
}
