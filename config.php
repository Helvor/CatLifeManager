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
$isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
        || (($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https');

session_set_cookie_params([
    'lifetime' => 0,
    'path'     => '/',
    'secure'   => $isHttps,
    'httponly' => true,
    'samesite' => 'Lax',
]);
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

// ─── Security headers (fallback si Caddy n'est pas devant l'app) ───────────
// Caddy les pose en production via Caddyfile. On les ajoute ici pour le dev HTTP.
if (!headers_sent()) {
    header('X-Content-Type-Options: nosniff');
    header('X-Frame-Options: DENY');
    header('Referrer-Policy: strict-origin-when-cross-origin');
    // CSP avec unsafe-inline obligatoire pour les onclick/style inline existants.
    // À durcir (nonce ou externalisation JS) dans une prochaine itération.
    header("Content-Security-Policy: default-src 'self'; "
        . "script-src 'self' 'unsafe-inline'; "
        . "style-src 'self' 'unsafe-inline' https://fonts.googleapis.com; "
        . "font-src 'self' https://fonts.gstatic.com; "
        . "img-src 'self' data: blob:; "
        . "connect-src 'self'");
}
