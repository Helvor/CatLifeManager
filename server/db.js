const Database = require('better-sqlite3');
const path = require('path');
const fs = require('fs');

// Créer le dossier database s'il n'existe pas
const dbDir = path.join(__dirname, '../database');
if (!fs.existsSync(dbDir)) {
  fs.mkdirSync(dbDir, { recursive: true });
}

const dbPath = path.join(dbDir, 'catlife.db');
const db = new Database(dbPath);

// Activer les clés étrangères
db.pragma('foreign_keys = ON');

// Initialiser la base de données
function initDatabase() {
  // Table des chats
  db.exec(`
    CREATE TABLE IF NOT EXISTS cats (
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
      created_at TEXT DEFAULT CURRENT_TIMESTAMP,
      updated_at TEXT DEFAULT CURRENT_TIMESTAMP
    )
  `);

  // Table des vaccinations
  db.exec(`
    CREATE TABLE IF NOT EXISTS vaccinations (
      id INTEGER PRIMARY KEY AUTOINCREMENT,
      cat_id INTEGER NOT NULL,
      vaccine_type TEXT NOT NULL,
      date TEXT NOT NULL,
      next_date TEXT,
      vet_name TEXT,
      notes TEXT,
      document_url TEXT,
      created_at TEXT DEFAULT CURRENT_TIMESTAMP,
      FOREIGN KEY (cat_id) REFERENCES cats(id) ON DELETE CASCADE
    )
  `);

  // Table des traitements (vermifuge, antipuce, médicaments)
  db.exec(`
    CREATE TABLE IF NOT EXISTS treatments (
      id INTEGER PRIMARY KEY AUTOINCREMENT,
      cat_id INTEGER NOT NULL,
      treatment_type TEXT NOT NULL,
      product_name TEXT,
      date TEXT NOT NULL,
      next_date TEXT,
      dosage TEXT,
      notes TEXT,
      created_at TEXT DEFAULT CURRENT_TIMESTAMP,
      FOREIGN KEY (cat_id) REFERENCES cats(id) ON DELETE CASCADE
    )
  `);

  // Table du poids
  db.exec(`
    CREATE TABLE IF NOT EXISTS weight_records (
      id INTEGER PRIMARY KEY AUTOINCREMENT,
      cat_id INTEGER NOT NULL,
      weight REAL NOT NULL,
      date TEXT NOT NULL,
      notes TEXT,
      created_at TEXT DEFAULT CURRENT_TIMESTAMP,
      FOREIGN KEY (cat_id) REFERENCES cats(id) ON DELETE CASCADE
    )
  `);

  // Table des photos
  db.exec(`
    CREATE TABLE IF NOT EXISTS photos (
      id INTEGER PRIMARY KEY AUTOINCREMENT,
      cat_id INTEGER NOT NULL,
      url TEXT NOT NULL,
      title TEXT,
      tags TEXT,
      date TEXT NOT NULL,
      location TEXT,
      created_at TEXT DEFAULT CURRENT_TIMESTAMP,
      FOREIGN KEY (cat_id) REFERENCES cats(id) ON DELETE CASCADE
    )
  `);

  // Table des rappels
  db.exec(`
    CREATE TABLE IF NOT EXISTS reminders (
      id INTEGER PRIMARY KEY AUTOINCREMENT,
      cat_id INTEGER NOT NULL,
      title TEXT NOT NULL,
      description TEXT,
      reminder_date TEXT NOT NULL,
      reminder_type TEXT NOT NULL,
      is_completed INTEGER DEFAULT 0,
      created_at TEXT DEFAULT CURRENT_TIMESTAMP,
      FOREIGN KEY (cat_id) REFERENCES cats(id) ON DELETE CASCADE
    )
  `);

  // Table des visites vétérinaires
  db.exec(`
    CREATE TABLE IF NOT EXISTS vet_visits (
      id INTEGER PRIMARY KEY AUTOINCREMENT,
      cat_id INTEGER NOT NULL,
      date TEXT NOT NULL,
      reason TEXT NOT NULL,
      diagnosis TEXT,
      treatment TEXT,
      cost REAL,
      notes TEXT,
      document_url TEXT,
      created_at TEXT DEFAULT CURRENT_TIMESTAMP,
      FOREIGN KEY (cat_id) REFERENCES cats(id) ON DELETE CASCADE
    )
  `);

  console.log('✅ Base de données initialisée avec succès');
}

// Fonction de backup
function createBackup() {
  const backupDir = path.join(dbDir, 'backups');
  if (!fs.existsSync(backupDir)) {
    fs.mkdirSync(backupDir, { recursive: true });
  }

  const timestamp = new Date().toISOString().replace(/[:.]/g, '-');
  const backupPath = path.join(backupDir, `catlife_backup_${timestamp}.db`);
  
  db.backup(backupPath)
    .then(() => {
      console.log(`✅ Backup créé: ${backupPath}`);
      // Garder seulement les 30 derniers backups
      cleanOldBackups(backupDir);
    })
    .catch(err => {
      console.error('❌ Erreur lors du backup:', err);
    });
}

function cleanOldBackups(backupDir) {
  const files = fs.readdirSync(backupDir)
    .filter(f => f.startsWith('catlife_backup_'))
    .map(f => ({
      name: f,
      path: path.join(backupDir, f),
      time: fs.statSync(path.join(backupDir, f)).mtime.getTime()
    }))
    .sort((a, b) => b.time - a.time);

  // Supprimer les backups au-delà de 30
  if (files.length > 30) {
    files.slice(30).forEach(f => {
      fs.unlinkSync(f.path);
      console.log(`🗑️  Ancien backup supprimé: ${f.name}`);
    });
  }
}

// Export de la connexion et des fonctions
module.exports = {
  db,
  initDatabase,
  createBackup
};

// Initialiser la base de données au démarrage
initDatabase();
