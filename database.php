<?php
require_once 'config.php';

// Connexion à la base de données SQLite
function getDB() {
    try {
        $db = new PDO('sqlite:' . DB_PATH);
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $db;
    } catch (PDOException $e) {
        die("Erreur de connexion : " . $e->getMessage());
    }
}

// Initialiser la base de données
function initDB() {
    $db = getDB();
    
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
}

// Initialiser la DB au chargement
initDB();

// Fonctions pour les chats
function getAllCats() {
    $db = getDB();
    $stmt = $db->query("SELECT * FROM cats ORDER BY created_at DESC");
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
    $stmt = $db->prepare("INSERT INTO cats (name, breed, birth_date, gender, color, is_neutered, microchip_number, vet_clinic, vet_phone, vet_email) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
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
        $data['vet_email']
    ]);
    return $db->lastInsertId();
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
