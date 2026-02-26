<?php
require_once 'config.php';
require_once 'auth.php';
require_once 'database.php';

// Déjà connecté → app
if (currentUser()) {
    header('Location: /index.php');
    exit;
}

$error   = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCsrfToken($_POST['_token'] ?? '')) {
        $error = 'Requête invalide. Veuillez réessayer.';
    } else {
        $name     = trim($_POST['name'] ?? '');
        $email    = trim(strtolower($_POST['email'] ?? ''));
        $password = $_POST['password'] ?? '';
        $confirm  = $_POST['password_confirm'] ?? '';

        if (empty($name) || strlen($name) < 2) {
            $error = 'Le prénom doit contenir au moins 2 caractères.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = 'Adresse email invalide.';
        } elseif (strlen($password) < 8) {
            $error = 'Le mot de passe doit contenir au moins 8 caractères.';
        } elseif ($password !== $confirm) {
            $error = 'Les mots de passe ne correspondent pas.';
        } elseif (getUserByEmail($email)) {
            $error = 'Cette adresse email est déjà utilisée.';
        } else {
            $hash   = password_hash($password, PASSWORD_ARGON2ID);
            $userId = createUser($email, $name, $hash);

            // Réclame les chats orphelins pour le premier utilisateur
            claimOrphanCats($userId);

            $user = getUserById($userId);
            loginUser($user);
            header('Location: /index.php');
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr" data-theme="system">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>Créer un compte — CatLife Manager</title>
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#6c5ce7">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <script>(function(){const s=localStorage.getItem('theme');if(s)document.documentElement.dataset.theme=s;})()</script>
</head>
<body class="auth-body">
    <div class="auth-container">
        <div class="auth-card">
            <div class="auth-logo">
                <div class="logo-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M20.42 4.58a5.4 5.4 0 0 0-7.65 0l-.77.78-.77-.78a5.4 5.4 0 0 0-7.65 7.65l8.42 8.42 8.42-8.42a5.4 5.4 0 0 0 0-7.65z"/>
                    </svg>
                </div>
                <span class="logo-text">CatLife</span>
            </div>

            <h1 class="auth-title">Créer un compte</h1>
            <p class="auth-subtitle">Rejoignez CatLife pour gérer la vie de vos chats.</p>

            <?php if ($error): ?>
                <div class="auth-error">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                    <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
                </div>
            <?php endif; ?>

            <form method="POST" class="auth-form">
                <?= csrfInput() ?>

                <div class="form-group">
                    <label class="form-label" for="name">Prénom</label>
                    <input
                        type="text"
                        id="name"
                        name="name"
                        class="form-input"
                        value="<?= htmlspecialchars($_POST['name'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                        autocomplete="given-name"
                        required
                        autofocus
                    >
                </div>

                <div class="form-group">
                    <label class="form-label" for="email">Adresse email</label>
                    <input
                        type="email"
                        id="email"
                        name="email"
                        class="form-input"
                        value="<?= htmlspecialchars($_POST['email'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                        autocomplete="email"
                        required
                    >
                </div>

                <div class="form-group">
                    <label class="form-label" for="password">Mot de passe <span class="form-hint">(8 caractères minimum)</span></label>
                    <input
                        type="password"
                        id="password"
                        name="password"
                        class="form-input"
                        autocomplete="new-password"
                        minlength="8"
                        required
                    >
                </div>

                <div class="form-group">
                    <label class="form-label" for="password_confirm">Confirmer le mot de passe</label>
                    <input
                        type="password"
                        id="password_confirm"
                        name="password_confirm"
                        class="form-input"
                        autocomplete="new-password"
                        required
                    >
                </div>

                <button type="submit" class="btn btn-primary btn-full">
                    Créer mon compte
                </button>
            </form>

            <p class="auth-switch">
                Déjà un compte ?
                <a href="/login.php">Se connecter</a>
            </p>
        </div>
    </div>
</body>
</html>
