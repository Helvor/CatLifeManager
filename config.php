<?php
// Configuration de l'application
define('DB_PATH', __DIR__ . '/database/catlife.db');
define('UPLOAD_DIR', __DIR__ . '/uploads/');
define('MAX_UPLOAD_SIZE', 10 * 1024 * 1024); // 10 MB

// Démarrer la session
session_start();

// Timezone
date_default_timezone_set('Europe/Brussels');

// Afficher les erreurs en développement
ini_set('display_errors', 1);
error_reporting(E_ALL);
?>
