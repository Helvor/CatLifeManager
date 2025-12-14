const express = require('express');
const cors = require('cors');
const path = require('path');
const fs = require('fs');
const fileUpload = require('express-fileupload');
const cron = require('node-cron');
const { db, initDatabase, createBackup } = require('./db');

const app = express();
const PORT = process.env.PORT || 6000;

// Middleware
app.use(cors());
app.use(express.json());
app.use(express.urlencoded({ extended: true }));
app.use(fileUpload({
  limits: { fileSize: 50 * 1024 * 1024 }, // 50MB max
  useTempFiles: true,
  tempFileDir: '/tmp/'
}));

// Servir les fichiers statiques
app.use('/uploads', express.static(path.join(__dirname, 'uploads')));
app.use(express.static(path.join(__dirname, '../client/dist')));

// Initialiser la base de données
initDatabase();

// Backup automatique tous les jours à 3h du matin
cron.schedule('0 3 * * *', () => {
  console.log('🔄 Lancement du backup automatique...');
  createBackup();
});

// Routes API
// ===========================================

// CATS - Gestion des profils de chats
app.get('/api/cats', (req, res) => {
  try {
    const cats = db.prepare('SELECT * FROM cats ORDER BY created_at DESC').all();
    res.json(cats);
  } catch (error) {
    res.status(500).json({ error: error.message });
  }
});

app.get('/api/cats/:id', (req, res) => {
  try {
    const cat = db.prepare('SELECT * FROM cats WHERE id = ?').get(req.params.id);
    if (!cat) return res.status(404).json({ error: 'Chat non trouvé' });
    res.json(cat);
  } catch (error) {
    res.status(500).json({ error: error.message });
  }
});

app.post('/api/cats', (req, res) => {
  try {
    const { name, breed, birth_date, gender, color, is_neutered, microchip_number, 
            vet_clinic, vet_phone, vet_email } = req.body;
    
    const result = db.prepare(`
      INSERT INTO cats (name, breed, birth_date, gender, color, is_neutered, 
                       microchip_number, vet_clinic, vet_phone, vet_email)
      VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    `).run(name, breed, birth_date, gender, color, is_neutered || 0, 
           microchip_number, vet_clinic, vet_phone, vet_email);
    
    res.json({ id: result.lastInsertRowid, message: 'Chat créé avec succès' });
  } catch (error) {
    res.status(500).json({ error: error.message });
  }
});

app.put('/api/cats/:id', (req, res) => {
  try {
    const { name, breed, birth_date, gender, color, is_neutered, microchip_number,
            vet_clinic, vet_phone, vet_email } = req.body;
    
    db.prepare(`
      UPDATE cats SET name = ?, breed = ?, birth_date = ?, gender = ?, color = ?,
                     is_neutered = ?, microchip_number = ?, vet_clinic = ?,
                     vet_phone = ?, vet_email = ?, updated_at = CURRENT_TIMESTAMP
      WHERE id = ?
    `).run(name, breed, birth_date, gender, color, is_neutered || 0,
           microchip_number, vet_clinic, vet_phone, vet_email, req.params.id);
    
    res.json({ message: 'Chat mis à jour avec succès' });
  } catch (error) {
    res.status(500).json({ error: error.message });
  }
});

app.delete('/api/cats/:id', (req, res) => {
  try {
    db.prepare('DELETE FROM cats WHERE id = ?').run(req.params.id);
    res.json({ message: 'Chat supprimé avec succès' });
  } catch (error) {
    res.status(500).json({ error: error.message });
  }
});

// WEIGHT - Suivi du poids
app.get('/api/cats/:catId/weight', (req, res) => {
  try {
    const records = db.prepare(`
      SELECT * FROM weight_records 
      WHERE cat_id = ? 
      ORDER BY date DESC
    `).all(req.params.catId);
    res.json(records);
  } catch (error) {
    res.status(500).json({ error: error.message });
  }
});

app.post('/api/cats/:catId/weight', (req, res) => {
  try {
    const { weight, date, notes } = req.body;
    const result = db.prepare(`
      INSERT INTO weight_records (cat_id, weight, date, notes)
      VALUES (?, ?, ?, ?)
    `).run(req.params.catId, weight, date, notes);
    
    res.json({ id: result.lastInsertRowid, message: 'Poids enregistré' });
  } catch (error) {
    res.status(500).json({ error: error.message });
  }
});

app.delete('/api/cats/:catId/weight/:weightId', (req, res) => {
  try {
    db.prepare('DELETE FROM weight_records WHERE id = ? AND cat_id = ?')
      .run(req.params.weightId, req.params.catId);
    res.json({ message: 'Pesée supprimée' });
  } catch (error) {
    res.status(500).json({ error: error.message });
  }
});

// VACCINATIONS
app.get('/api/cats/:catId/vaccinations', (req, res) => {
  try {
    const vaccinations = db.prepare(`
      SELECT * FROM vaccinations 
      WHERE cat_id = ? 
      ORDER BY date DESC
    `).all(req.params.catId);
    res.json(vaccinations);
  } catch (error) {
    res.status(500).json({ error: error.message });
  }
});

app.post('/api/cats/:catId/vaccinations', (req, res) => {
  try {
    const { vaccine_type, date, next_date, vet_name, notes } = req.body;
    const result = db.prepare(`
      INSERT INTO vaccinations (cat_id, vaccine_type, date, next_date, vet_name, notes)
      VALUES (?, ?, ?, ?, ?, ?)
    `).run(req.params.catId, vaccine_type, date, next_date, vet_name, notes);
    
    res.json({ id: result.lastInsertRowid, message: 'Vaccination enregistrée' });
  } catch (error) {
    res.status(500).json({ error: error.message });
  }
});

app.delete('/api/cats/:catId/vaccinations/:vaccinationId', (req, res) => {
  try {
    db.prepare('DELETE FROM vaccinations WHERE id = ? AND cat_id = ?')
      .run(req.params.vaccinationId, req.params.catId);
    res.json({ message: 'Vaccination supprimée' });
  } catch (error) {
    res.status(500).json({ error: error.message });
  }
});

// TREATMENTS
app.get('/api/cats/:catId/treatments', (req, res) => {
  try {
    const treatments = db.prepare(`
      SELECT * FROM treatments 
      WHERE cat_id = ? 
      ORDER BY date DESC
    `).all(req.params.catId);
    res.json(treatments);
  } catch (error) {
    res.status(500).json({ error: error.message });
  }
});

app.post('/api/cats/:catId/treatments', (req, res) => {
  try {
    const { treatment_type, product_name, date, next_date, dosage, notes } = req.body;
    const result = db.prepare(`
      INSERT INTO treatments (cat_id, treatment_type, product_name, date, next_date, dosage, notes)
      VALUES (?, ?, ?, ?, ?, ?, ?)
    `).run(req.params.catId, treatment_type, product_name, date, next_date, dosage, notes);
    
    res.json({ id: result.lastInsertRowid, message: 'Traitement enregistré' });
  } catch (error) {
    res.status(500).json({ error: error.message });
  }
});

app.delete('/api/cats/:catId/treatments/:treatmentId', (req, res) => {
  try {
    db.prepare('DELETE FROM treatments WHERE id = ? AND cat_id = ?')
      .run(req.params.treatmentId, req.params.catId);
    res.json({ message: 'Traitement supprimé' });
  } catch (error) {
    res.status(500).json({ error: error.message });
  }
});

// PHOTOS
app.get('/api/cats/:catId/photos', (req, res) => {
  try {
    const photos = db.prepare(`
      SELECT * FROM photos 
      WHERE cat_id = ? 
      ORDER BY date DESC
    `).all(req.params.catId);
    res.json(photos);
  } catch (error) {
    res.status(500).json({ error: error.message });
  }
});

app.post('/api/cats/:catId/photos', (req, res) => {
  try {
    if (!req.files || !req.files.photo) {
      return res.status(400).json({ error: 'Aucune photo fournie' });
    }

    const photo = req.files.photo;
    const fileName = `${Date.now()}_${photo.name}`;
    const uploadPath = path.join(__dirname, 'uploads', fileName);

    photo.mv(uploadPath, (err) => {
      if (err) return res.status(500).json({ error: err.message });

      const { title, tags, date, location } = req.body;
      const result = db.prepare(`
        INSERT INTO photos (cat_id, url, title, tags, date, location)
        VALUES (?, ?, ?, ?, ?, ?)
      `).run(req.params.catId, `/uploads/${fileName}`, title, tags, date, location);

      res.json({ id: result.lastInsertRowid, url: `/uploads/${fileName}`, message: 'Photo ajoutée' });
    });
  } catch (error) {
    res.status(500).json({ error: error.message });
  }
});

app.delete('/api/cats/:catId/photos/:photoId', (req, res) => {
  try {
    const photo = db.prepare('SELECT url FROM photos WHERE id = ? AND cat_id = ?')
      .get(req.params.photoId, req.params.catId);
    
    if (photo) {
      const filePath = path.join(__dirname, photo.url);
      if (fs.existsSync(filePath)) {
        fs.unlinkSync(filePath);
      }
    }
    
    db.prepare('DELETE FROM photos WHERE id = ? AND cat_id = ?')
      .run(req.params.photoId, req.params.catId);
    
    res.json({ message: 'Photo supprimée' });
  } catch (error) {
    res.status(500).json({ error: error.message });
  }
});

// REMINDERS
app.get('/api/cats/:catId/reminders', (req, res) => {
  try {
    const reminders = db.prepare(`
      SELECT * FROM reminders 
      WHERE cat_id = ? AND is_completed = 0
      ORDER BY reminder_date ASC
    `).all(req.params.catId);
    res.json(reminders);
  } catch (error) {
    res.status(500).json({ error: error.message });
  }
});

app.post('/api/cats/:catId/reminders', (req, res) => {
  try {
    const { title, description, reminder_date, reminder_type } = req.body;
    const result = db.prepare(`
      INSERT INTO reminders (cat_id, title, description, reminder_date, reminder_type)
      VALUES (?, ?, ?, ?, ?)
    `).run(req.params.catId, title, description, reminder_date, reminder_type);
    
    res.json({ id: result.lastInsertRowid, message: 'Rappel créé' });
  } catch (error) {
    res.status(500).json({ error: error.message });
  }
});

app.patch('/api/reminders/:id/complete', (req, res) => {
  try {
    db.prepare('UPDATE reminders SET is_completed = 1 WHERE id = ?').run(req.params.id);
    res.json({ message: 'Rappel marqué comme complété' });
  } catch (error) {
    res.status(500).json({ error: error.message });
  }
});

app.delete('/api/reminders/:id', (req, res) => {
  try {
    db.prepare('DELETE FROM reminders WHERE id = ?').run(req.params.id);
    res.json({ message: 'Rappel supprimé' });
  } catch (error) {
    res.status(500).json({ error: error.message });
  }
});

// VET VISITS
app.get('/api/cats/:catId/vet-visits', (req, res) => {
  try {
    const visits = db.prepare(`
      SELECT * FROM vet_visits 
      WHERE cat_id = ? 
      ORDER BY date DESC
    `).all(req.params.catId);
    res.json(visits);
  } catch (error) {
    res.status(500).json({ error: error.message });
  }
});

app.post('/api/cats/:catId/vet-visits', (req, res) => {
  try {
    const { date, reason, diagnosis, treatment, cost, notes } = req.body;
    const result = db.prepare(`
      INSERT INTO vet_visits (cat_id, date, reason, diagnosis, treatment, cost, notes)
      VALUES (?, ?, ?, ?, ?, ?, ?)
    `).run(req.params.catId, date, reason, diagnosis, treatment, cost, notes);
    
    res.json({ id: result.lastInsertRowid, message: 'Visite enregistrée' });
  } catch (error) {
    res.status(500).json({ error: error.message });
  }
});

app.delete('/api/cats/:catId/vet-visits/:visitId', (req, res) => {
  try {
    db.prepare('DELETE FROM vet_visits WHERE id = ? AND cat_id = ?')
      .run(req.params.visitId, req.params.catId);
    res.json({ message: 'Visite supprimée' });
  } catch (error) {
    res.status(500).json({ error: error.message });
  }
});

// BACKUP
app.post('/api/backup', (req, res) => {
  try {
    createBackup();
    res.json({ message: 'Backup créé avec succès' });
  } catch (error) {
    res.status(500).json({ error: error.message });
  }
});

// Health check
app.get('/api/health', (req, res) => {
  res.json({ status: 'ok', timestamp: new Date().toISOString() });
});

app.get('*', (req, res) => {
  res.sendFile(path.join(__dirname, '../client/index.html'));
});

app.listen(3000, '0.0.0.0');

// Démarrer le serveur
app.listen(PORT, () => {
  console.log(`🐱 CatLife Tracker API en cours d'exécution sur le port ${PORT}`);
  console.log(`📊 Base de données: ${path.join(__dirname, '../database/catlife.db')}`);
});
