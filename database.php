<?php
require_once 'config.php';

// Connexion à la base de données SQLite
function getDB() {
    try {
        $db = new PDO('sqlite:' . DB_PATH);
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $db;
    } catch (PDOException $e) {
        // Ne pas exposer les détails PDO en production (chemin DB, type, schéma)
        if (defined('APP_ENV') && APP_ENV === 'development') {
            die("Erreur de connexion : " . $e->getMessage());
        }
        http_response_code(500);
        die("Erreur interne du serveur. Veuillez réessayer.");
    }
}

// Initialiser la base de données
function initDB() {
    $db = getDB();

    // Table des utilisateurs
    $db->exec("CREATE TABLE IF NOT EXISTS users (
        id            INTEGER PRIMARY KEY AUTOINCREMENT,
        email         TEXT UNIQUE NOT NULL,
        name          TEXT,
        avatar_url    TEXT,
        password_hash TEXT,
        provider      TEXT NOT NULL DEFAULT 'email',
        provider_id   TEXT,
        created_at    DATETIME DEFAULT CURRENT_TIMESTAMP
    )");

    // Table de suivi des tentatives de connexion (rate limiting)
    $db->exec("CREATE TABLE IF NOT EXISTS login_attempts (
        id         INTEGER PRIMARY KEY AUTOINCREMENT,
        email      TEXT NOT NULL,
        ip         TEXT NOT NULL,
        success    INTEGER NOT NULL DEFAULT 0,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP
    )");

    // Table des chats
    $db->exec("CREATE TABLE IF NOT EXISTS cats (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        name TEXT NOT NULL,
        breed TEXT,
        birth_date TEXT,
        gender TEXT,
        color TEXT,
        is_neutered INTEGER DEFAULT 0,
        microchip_number TEXT,
        vet_clinic TEXT,
        vet_phone TEXT,
        vet_email TEXT,
        photo_url TEXT,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP
    )");
    
    // Table des vaccinations
    $db->exec("CREATE TABLE IF NOT EXISTS vaccinations (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        cat_id INTEGER NOT NULL,
        vaccine_type TEXT NOT NULL,
        date TEXT NOT NULL,
        next_date TEXT,
        vet_name TEXT,
        notes TEXT,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (cat_id) REFERENCES cats(id) ON DELETE CASCADE
    )");
    
    // Table des traitements
    $db->exec("CREATE TABLE IF NOT EXISTS treatments (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        cat_id INTEGER NOT NULL,
        treatment_type TEXT NOT NULL,
        product_name TEXT,
        date TEXT NOT NULL,
        next_date TEXT,
        dosage TEXT,
        notes TEXT,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (cat_id) REFERENCES cats(id) ON DELETE CASCADE
    )");
    
    // Table du poids
    $db->exec("CREATE TABLE IF NOT EXISTS weight_records (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        cat_id INTEGER NOT NULL,
        weight REAL NOT NULL,
        date TEXT NOT NULL,
        notes TEXT,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (cat_id) REFERENCES cats(id) ON DELETE CASCADE
    )");
    
    // Table des photos
    $db->exec("CREATE TABLE IF NOT EXISTS photos (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        cat_id INTEGER NOT NULL,
        filename TEXT NOT NULL,
        title TEXT,
        tags TEXT,
        date TEXT NOT NULL,
        location TEXT,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (cat_id) REFERENCES cats(id) ON DELETE CASCADE
    )");
    
    // Table des rappels
    $db->exec("CREATE TABLE IF NOT EXISTS reminders (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        cat_id INTEGER NOT NULL,
        title TEXT NOT NULL,
        description TEXT,
        reminder_date TEXT NOT NULL,
        reminder_type TEXT NOT NULL,
        is_completed INTEGER DEFAULT 0,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (cat_id) REFERENCES cats(id) ON DELETE CASCADE
    )");

    // Migration : ajouter user_id sur cats si la colonne n'existe pas encore
    try {
        $db->exec("ALTER TABLE cats ADD COLUMN user_id INTEGER REFERENCES users(id) ON DELETE CASCADE");
    } catch (PDOException $e) {
        // La colonne existe déjà — on ignore
    }
}

// Initialiser la DB au chargement
initDB();

// ─── Fonctions utilisateurs ───────────────────────────────────────────────────

function getUserByEmail(string $email): ?array
{
    $db   = getDB();
    $stmt = $db->prepare("SELECT * FROM users WHERE email = ? LIMIT 1");
    $stmt->execute([strtolower(trim($email))]);
    $row  = $stmt->fetch(PDO::FETCH_ASSOC);
    return $row ?: null;
}

function getUserById(int $id): ?array
{
    $db   = getDB();
    $stmt = $db->prepare("SELECT * FROM users WHERE id = ? LIMIT 1");
    $stmt->execute([$id]);
    $row  = $stmt->fetch(PDO::FETCH_ASSOC);
    return $row ?: null;
}

function createUser(string $email, string $name, string $passwordHash): int
{
    $db   = getDB();
    $stmt = $db->prepare("INSERT INTO users (email, name, password_hash, provider) VALUES (?, ?, ?, 'email')");
    $stmt->execute([strtolower(trim($email)), trim($name), $passwordHash]);
    return (int) $db->lastInsertId();
}

/** Réclame tous les chats orphelins (user_id NULL) pour le premier utilisateur. */
function claimOrphanCats(int $userId): void
{
    $db   = getDB();
    $stmt = $db->prepare("UPDATE cats SET user_id = ? WHERE user_id IS NULL");
    $stmt->execute([$userId]);
}

// ─── Ownership / Access control ──────────────────────────────────────────────

/** Vérifie qu'un chat appartient à l'utilisateur donné. */
function catBelongsToUser(int $catId, int $userId): bool
{
    $db   = getDB();
    $stmt = $db->prepare("SELECT 1 FROM cats WHERE id = ? AND user_id = ?");
    $stmt->execute([$catId, $userId]);
    return $stmt->fetchColumn() !== false;
}

/** Vérifie qu'une photo appartient (via son chat) à l'utilisateur donné. */
function photoBelongsToUser(int $photoId, int $userId): bool
{
    $db   = getDB();
    $stmt = $db->prepare("
        SELECT 1 FROM photos p
        JOIN cats c ON p.cat_id = c.id
        WHERE p.id = ? AND c.user_id = ?
    ");
    $stmt->execute([$photoId, $userId]);
    return $stmt->fetchColumn() !== false;
}

// ─── Rate limiting ────────────────────────────────────────────────────────────

function logLoginAttempt(string $email, string $ip, bool $success): void
{
    $db   = getDB();
    $stmt = $db->prepare("INSERT INTO login_attempts (email, ip, success) VALUES (?, ?, ?)");
    $stmt->execute([strtolower(trim($email)), $ip, $success ? 1 : 0]);
}

/**
 * Nombre de tentatives échouées dans les 15 dernières minutes
 * pour cet email OU cette IP.
 */
function countRecentFailedAttempts(string $email, string $ip): int
{
    $db   = getDB();
    $stmt = $db->prepare("
        SELECT COUNT(*) FROM login_attempts
        WHERE success = 0
          AND created_at >= datetime('now', '-15 minutes')
          AND (email = ? OR ip = ?)
    ");
    $stmt->execute([strtolower(trim($email)), $ip]);
    return (int) $stmt->fetchColumn();
}

/** Enregistre une tentative d'inscription (pour le rate limiting). */
function logRegistrationAttempt(string $ip): void
{
    $db   = getDB();
    $stmt = $db->prepare("INSERT INTO login_attempts (email, ip, success) VALUES (?, ?, 0)");
    $stmt->execute(['_reg:' . $ip, $ip]);
}

/** Nombre d'inscriptions récentes depuis cette IP (1 heure glissante). */
function countRecentRegistrations(string $ip): int
{
    $db   = getDB();
    $stmt = $db->prepare("
        SELECT COUNT(*) FROM login_attempts
        WHERE ip = ?
          AND email LIKE '_reg:%'
          AND created_at >= datetime('now', '-1 hour')
    ");
    $stmt->execute([$ip]);
    return (int) $stmt->fetchColumn();
}

// ─── Fonctions pour les chats ─────────────────────────────────────────────────

function getAllCats(int $userId = 0) {
    $db = getDB();
    if ($userId > 0) {
        $stmt = $db->prepare("SELECT * FROM cats WHERE user_id = ? ORDER BY created_at DESC");
        $stmt->execute([$userId]);
    } else {
        $stmt = $db->query("SELECT * FROM cats ORDER BY created_at DESC");
    }
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getCatById($id) {
    $db = getDB();
    $stmt = $db->prepare("SELECT * FROM cats WHERE id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function addCat($data) {
    $db = getDB();
    $stmt = $db->prepare("INSERT INTO cats (name, breed, birth_date, gender, color, is_neutered, microchip_number, vet_clinic, vet_phone, vet_email, user_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([
        $data['name'],
        $data['breed'],
        $data['birth_date'],
        $data['gender'],
        $data['color'],
        $data['is_neutered'] ?? 0,
        $data['microchip_number'],
        $data['vet_clinic'],
        $data['vet_phone'],
        $data['vet_email'],
        $data['user_id'] ?? null,
    ]);
    return $db->lastInsertId();
}

function updateCat($id, $data) {
    $db = getDB();
    $stmt = $db->prepare("UPDATE cats SET name = ?, breed = ?, birth_date = ?, gender = ?, color = ?, is_neutered = ?, microchip_number = ?, vet_clinic = ?, vet_phone = ?, vet_email = ? WHERE id = ?");
    $stmt->execute([
        $data['name'],
        $data['breed'],
        $data['birth_date'],
        $data['gender'],
        $data['color'],
        $data['is_neutered'] ?? 0,
        $data['microchip_number'],
        $data['vet_clinic'],
        $data['vet_phone'],
        $data['vet_email'],
        $id
    ]);
}

function deleteCat($id) {
    $db = getDB();
    $stmt = $db->prepare("DELETE FROM cats WHERE id = ?");
    $stmt->execute([$id]);
}

// Fonctions pour les vaccinations
function getVaccinations($catId) {
    $db = getDB();
    $stmt = $db->prepare("SELECT * FROM vaccinations WHERE cat_id = ? ORDER BY date DESC");
    $stmt->execute([$catId]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function addVaccination($catId, $data) {
    $db = getDB();
    $stmt = $db->prepare("INSERT INTO vaccinations (cat_id, vaccine_type, date, next_date, vet_name, notes) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([$catId, $data['vaccine_type'], $data['date'], $data['next_date'], $data['vet_name'], $data['notes']]);
    return $db->lastInsertId();
}

// Fonctions pour les traitements
function getTreatments($catId) {
    $db = getDB();
    $stmt = $db->prepare("SELECT * FROM treatments WHERE cat_id = ? ORDER BY date DESC");
    $stmt->execute([$catId]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function addTreatment($catId, $data) {
    $db = getDB();
    $stmt = $db->prepare("INSERT INTO treatments (cat_id, treatment_type, product_name, date, next_date, dosage, notes) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$catId, $data['treatment_type'], $data['product_name'], $data['date'], $data['next_date'], $data['dosage'], $data['notes']]);
    return $db->lastInsertId();
}

// Fonctions pour le poids
function getWeightRecords($catId) {
    $db = getDB();
    $stmt = $db->prepare("SELECT * FROM weight_records WHERE cat_id = ? ORDER BY date ASC");
    $stmt->execute([$catId]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function addWeight($catId, $data) {
    $db = getDB();
    $stmt = $db->prepare("INSERT INTO weight_records (cat_id, weight, date, notes) VALUES (?, ?, ?, ?)");
    $stmt->execute([$catId, $data['weight'], $data['date'], $data['notes']]);
    return $db->lastInsertId();
}

// Fonctions pour les photos
function getPhotos($catId) {
    $db = getDB();
    $stmt = $db->prepare("SELECT * FROM photos WHERE cat_id = ? ORDER BY date DESC");
    $stmt->execute([$catId]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function addPhoto($catId, $data) {
    $db = getDB();
    $stmt = $db->prepare("INSERT INTO photos (cat_id, filename, title, tags, date, location) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([$catId, $data['filename'], $data['title'], $data['tags'], $data['date'], $data['location']]);
    return $db->lastInsertId();
}

function deletePhoto($photoId) {
    $db = getDB();

    // Récupérer le nom du fichier
    $stmt = $db->prepare("SELECT filename FROM photos WHERE id = ?");
    $stmt->execute([$photoId]);
    $photo = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$photo) {
        return;
    }

    // Supprimer le fichier physique
    $filePath = UPLOAD_DIR . $photo['filename'];
    if (file_exists($filePath)) {
        unlink($filePath);
    }

    // Supprimer l'entrée en base
    $stmt = $db->prepare("DELETE FROM photos WHERE id = ?");
    $stmt->execute([$photoId]);
}

// Fonctions pour les rappels
function getReminders($catId) {
    $db = getDB();
    $stmt = $db->prepare("SELECT * FROM reminders WHERE cat_id = ? AND is_completed = 0 ORDER BY reminder_date ASC");
    $stmt->execute([$catId]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function addReminder($catId, $data) {
    $db = getDB();
    $stmt = $db->prepare("INSERT INTO reminders (cat_id, title, description, reminder_date, reminder_type) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$catId, $data['title'], $data['description'], $data['reminder_date'], $data['reminder_type']]);
    return $db->lastInsertId();
}

function completeReminder($id) {
    $db = getDB();
    $stmt = $db->prepare("UPDATE reminders SET is_completed = 1 WHERE id = ?");
    $stmt->execute([$id]);
}

// Fonction pour calculer l'âge
function calculateAge($birthDate) {
    if (!$birthDate) return 0;
    $birth = new DateTime($birthDate);
    $today = new DateTime();
    return $birth->diff($today)->y;
}
?>
