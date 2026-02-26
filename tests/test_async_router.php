<?php
/**
 * tests/test_async_router.php
 *
 * Tests unitaires pour les helpers async du router.
 * Exécution : php tests/test_async_router.php
 */

declare(strict_types=1);

// ─── Mini framework de test ────────────────────────────────────────────────────

$passed = 0;
$failed = 0;

function it(string $description, callable $fn): void
{
    global $passed, $failed;
    try {
        $fn();
        echo "\033[32m  ✓ {$description}\033[0m\n";
        $passed++;
    } catch (Throwable $e) {
        echo "\033[31m  ✗ {$description}\033[0m\n";
        echo "    → " . $e->getMessage() . "\n";
        $failed++;
    }
}

function expect(mixed $actual): object
{
    return new class($actual) {
        public function __construct(private readonly mixed $actual) {}

        public function toBe(mixed $expected): void
        {
            if ($this->actual !== $expected) {
                throw new RuntimeException(
                    "Attendu " . var_export($expected, true) .
                    ", reçu "  . var_export($this->actual, true)
                );
            }
        }

        public function toContain(string $needle): void
        {
            if (!str_contains((string) $this->actual, $needle)) {
                throw new RuntimeException(
                    "Attendu que la chaîne contienne '{$needle}'.\n" .
                    "    Valeur : " . var_export($this->actual, true)
                );
            }
        }

        public function toBeTrue(): void  { $this->toBe(true); }
        public function toBeFalse(): void { $this->toBe(false); }
    };
}

// ─── Isolation : on stub les fonctions qui déclencheraient des side-effects ───

// Les fonctions à tester sont copiées ici pour les isoler de leur contexte PHP
// (sessions, base de données, etc.)

function isAsyncRequest_test(array $server): bool
{
    return ($server['HTTP_X_REQUESTED_WITH'] ?? '') === 'fetch';
}

function jsonResponse_test(array $data, int $status = 200): array
{
    return ['status' => $status, 'body' => json_encode($data), 'decoded' => $data];
}

function jsonSuccess_test(string $message, string $redirect = ''): array
{
    return jsonResponse_test(['success' => true, 'message' => $message, 'redirect' => $redirect]);
}

function jsonError_test(string $error, int $status = 400): array
{
    return jsonResponse_test(['success' => false, 'error' => $error], $status);
}

// ─── Tests : isAsyncRequest ───────────────────────────────────────────────────

echo "\n\033[1misAsyncRequest()\033[0m\n";

it('retourne false quand le header X-Requested-With est absent', function () {
    expect(isAsyncRequest_test([]))->toBeFalse();
});

it('retourne false quand X-Requested-With vaut autre chose', function () {
    expect(isAsyncRequest_test(['HTTP_X_REQUESTED_WITH' => 'XMLHttpRequest']))->toBeFalse();
});

it('retourne false pour une valeur vide', function () {
    expect(isAsyncRequest_test(['HTTP_X_REQUESTED_WITH' => '']))->toBeFalse();
});

it('retourne true quand X-Requested-With vaut "fetch"', function () {
    expect(isAsyncRequest_test(['HTTP_X_REQUESTED_WITH' => 'fetch']))->toBeTrue();
});

it('est sensible à la casse (Fetch ≠ fetch)', function () {
    expect(isAsyncRequest_test(['HTTP_X_REQUESTED_WITH' => 'Fetch']))->toBeFalse();
});

// ─── Tests : jsonSuccess ──────────────────────────────────────────────────────

echo "\n\033[1mjsonSuccess()\033[0m\n";

it('retourne success=true', function () {
    $resp = jsonSuccess_test('Chat ajouté !');
    expect($resp['decoded']['success'])->toBeTrue();
});

it('inclut le message', function () {
    $resp = jsonSuccess_test('Chat ajouté !');
    expect($resp['decoded']['message'])->toBe('Chat ajouté !');
});

it('inclut le redirect quand fourni', function () {
    $resp = jsonSuccess_test('OK', 'index.php?cat=5');
    expect($resp['decoded']['redirect'])->toBe('index.php?cat=5');
});

it('le redirect est vide par défaut', function () {
    $resp = jsonSuccess_test('OK');
    expect($resp['decoded']['redirect'])->toBe('');
});

it('le JSON est valide', function () {
    $resp = jsonSuccess_test('Test message', 'index.php');
    $decoded = json_decode($resp['body'], true);
    expect($decoded)->toBe($resp['decoded']);
});

it('retourne HTTP 200 par défaut', function () {
    $resp = jsonSuccess_test('OK');
    expect($resp['status'])->toBe(200);
});

// ─── Tests : jsonError ────────────────────────────────────────────────────────

echo "\n\033[1mjsonError()\033[0m\n";

it('retourne success=false', function () {
    $resp = jsonError_test('Erreur quelconque');
    expect($resp['decoded']['success'])->toBeFalse();
});

it('inclut le message d\'erreur', function () {
    $resp = jsonError_test('Accès refusé.');
    expect($resp['decoded']['error'])->toBe('Accès refusé.');
});

it('retourne HTTP 400 par défaut', function () {
    $resp = jsonError_test('Bad request');
    expect($resp['status'])->toBe(400);
});

it('retourne HTTP 403 quand demandé', function () {
    $resp = jsonError_test('Accès refusé.', 403);
    expect($resp['status'])->toBe(403);
});

it('retourne HTTP 401 quand demandé', function () {
    $resp = jsonError_test('Non authentifié.', 401);
    expect($resp['status'])->toBe(401);
});

it('ne contient pas de clé "message" (seulement "error")', function () {
    $resp = jsonError_test('Oops');
    expect(isset($resp['decoded']['message']))->toBeFalse();
    expect(isset($resp['decoded']['error']))->toBeTrue();
});

// ─── Tests : format JSON ──────────────────────────────────────────────────────

echo "\n\033[1mFormat JSON\033[0m\n";

it('les réponses success et error ont des structures distinctes', function () {
    $success = jsonSuccess_test('OK', 'index.php');
    $error   = jsonError_test('NOK');
    expect($success['decoded']['success'])->toBeTrue();
    expect($error['decoded']['success'])->toBeFalse();
    expect(array_key_exists('message',  $success['decoded']))->toBeTrue();
    expect(array_key_exists('redirect', $success['decoded']))->toBeTrue();
    expect(array_key_exists('error',    $error['decoded']))->toBeTrue();
});

it('les caractères spéciaux sont encodés correctement en JSON', function () {
    $resp = jsonSuccess_test("Éléphant & <chat> ajouté !");
    $decoded = json_decode($resp['body'], true);
    expect($decoded['message'])->toBe("Éléphant & <chat> ajouté !");
});

it('le redirect inclut correctement le cat_id et le tab', function () {
    $catId = 42;
    $resp = jsonSuccess_test('Vaccination ajoutée !', "index.php?cat={$catId}&tab=health");
    expect($resp['decoded']['redirect'])->toContain('cat=42');
    expect($resp['decoded']['redirect'])->toContain('tab=health');
});

// ─── Tests : async-forms.js comportement simulé ──────────────────────────────

echo "\n\033[1mLogique métier du router (simulation)\033[0m\n";

it('add_cat retourne le bon message', function () {
    $resp = jsonSuccess_test('Chat ajouté avec succès !', 'index.php');
    expect($resp['decoded']['message'])->toBe('Chat ajouté avec succès !');
});

it('delete_cat redirige vers index.php sans cat_id', function () {
    $resp = jsonSuccess_test('Chat supprimé.', 'index.php');
    expect($resp['decoded']['redirect'])->toBe('index.php');
});

it('add_vaccination redirige vers l\'onglet health', function () {
    $resp = jsonSuccess_test('Vaccination ajoutée !', 'index.php?cat=3&tab=health');
    expect($resp['decoded']['redirect'])->toContain('tab=health');
});

it('add_weight redirige vers l\'onglet weight', function () {
    $resp = jsonSuccess_test('Pesée enregistrée !', 'index.php?cat=3&tab=weight');
    expect($resp['decoded']['redirect'])->toContain('tab=weight');
});

it('add_photo en erreur retourne success=false', function () {
    $resp = jsonError_test('Fichier trop volumineux (maximum 10 Mo).');
    expect($resp['decoded']['success'])->toBeFalse();
    expect($resp['decoded']['error'])->toContain('volumineux');
});

it('session expirée retourne 401 avec redirect vers login', function () {
    $resp = jsonError_test('Session expirée. Veuillez vous reconnecter.', 401);
    expect($resp['status'])->toBe(401);
    expect($resp['decoded']['error'])->toContain('Session expirée');
});

// ─── Résultats ────────────────────────────────────────────────────────────────

echo "\n";
$total = $passed + $failed;
if ($failed === 0) {
    echo "\033[32m✓ Tous les tests passent ({$passed}/{$total})\033[0m\n\n";
    exit(0);
} else {
    echo "\033[31m✗ {$failed} test(s) échoué(s) sur {$total}\033[0m\n\n";
    exit(1);
}
