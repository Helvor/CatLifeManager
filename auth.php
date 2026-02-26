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
        if (($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '') === 'fetch') {
            http_response_code(401);
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode([
                'success'  => false,
                'error'    => 'Session expirée. Veuillez vous reconnecter.',
                'redirect' => '/login.php',
            ]);
            exit;
        }
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

// ─── Client IP (proxy-aware) ──────────────────────────────────────────────────

/**
 * Retourne l'IP réelle du client.
 * Lorsque l'app est derrière Caddy, l'IP réelle est dans X-Forwarded-For.
 * ⚠️ Ne faire confiance à ce header QUE si l'app est derrière un proxy de confiance.
 */
function getClientIp(): string
{
    $xff = $_SERVER['HTTP_X_FORWARDED_FOR'] ?? '';
    if ($xff !== '') {
        $firstIp = trim(explode(',', $xff)[0]);
        if (filter_var($firstIp, FILTER_VALIDATE_IP)) {
            return $firstIp;
        }
    }
    return $_SERVER['REMOTE_ADDR'];
}

// ─── Timing attack mitigation ─────────────────────────────────────────────────

/**
 * Hash factice pour toujours appeler password_verify() même si l'email n'existe pas.
 * Élimine la différence de timing qui permettrait d'énumérer les comptes existants.
 * Stocké en session afin d'être recalculé une seule fois par session.
 */
function getDummyHash(): string
{
    if (empty($_SESSION['_dummy_hash'])) {
        $_SESSION['_dummy_hash'] = password_hash(bin2hex(random_bytes(16)), PASSWORD_ARGON2ID);
    }
    return $_SESSION['_dummy_hash'];
}
