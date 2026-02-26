<?php
require_once 'config.php';
require_once 'auth.php';

// Le logout doit être un POST avec token CSRF pour éviter le CSRF logout
// (une simple <img src="/logout.php"> dans une page externe pouvait déconnecter l'utilisateur)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && verifyCsrfToken($_POST['_token'] ?? '')) {
    logoutUser();
}

header('Location: /login.php');
exit;
