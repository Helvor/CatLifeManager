<?php
/**
 * auth.php — Session management, CSRF protection, rate limiting helpers.
 * Included after config.php (which calls session_start()).
 */

// ─── Current User ────────────────────────────────────────────────────────────

function currentUser(): ?array
{
    return $_SESSION['user'] ?? null;
}

function requireAuth(): void
{
    if (!currentUser()) {
        header('Location: /login.php');
        exit;
    }
}

function loginUser(array $user): void
{
    session_regenerate_id(true);
    $_SESSION['user'] = [
        'id'         => $user['id'],
        'email'      => $user['email'],
        'name'       => $user['name'],
        'avatar_url' => $user['avatar_url'],
    ];
}

function logoutUser(): void
{
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $p = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $p['path'], $p['domain'], $p['secure'], $p['httponly']
        );
    }
    session_destroy();
}

// ─── CSRF ─────────────────────────────────────────────────────────────────────

function generateCsrfToken(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verifyCsrfToken(string $token): bool
{
    return isset($_SESSION['csrf_token'])
        && hash_equals($_SESSION['csrf_token'], $token);
}

/** Returns a ready-to-embed hidden <input> with the CSRF token. */
function csrfInput(): string
{
    return '<input type="hidden" name="_token" value="'
        . htmlspecialchars(generateCsrfToken(), ENT_QUOTES, 'UTF-8')
        . '">';
}
