# 📡 Documentation API - CatLife Tracker

Base URL: `http://localhost:6000/api`

## 🐱 Cats (Profils des chats)

### GET /cats
Récupère la liste de tous les chats.

**Réponse :**
```json
[
  {
    "id": 1,
    "name": "Minou",
    "breed": "Européen",
    "birth_date": "2020-05-15",
    "gender": "Mâle",
    "color": "Tigré",
    "is_neutered": 1,
    "microchip_number": "123456789",
    "vet_clinic": "Clinique Vétérinaire",
    "vet_phone": "+32 2 123 45 67",
    "vet_email": "contact@vet.be",
    "photo_url": "/uploads/cat_photo.jpg",
    "created_at": "2024-01-15 10:30:00",
    "updated_at": "2024-12-14 15:45:00"
  }
]
```

### GET /cats/:id
Récupère les détails d'un chat spécifique.

### POST /cats
Crée un nouveau profil de chat.

**Body :**
```json
{
  "name": "Minou",
  "breed": "Européen",
  "birth_date": "2020-05-15",
  "gender": "Mâle",
  "color": "Tigré",
  "is_neutered": 1,
  "microchip_number": "123456789",
  "vet_clinic": "Clinique Vétérinaire",
  "vet_phone": "+32 2 123 45 67",
  "vet_email": "contact@vet.be"
}
```

### PUT /cats/:id
Met à jour un profil de chat.

---

## ⚖️ Weight (Suivi du poids)

### GET /cats/:catId/weight
Récupère l'historique des pesées d'un chat.

**Réponse :**
```json
[
  {
    "id": 1,
    "cat_id": 1,
    "weight": 4.5,
    "date": "2024-12-14",
    "notes": "Poids normal",
    "created_at": "2024-12-14 10:00:00"
  }
]
```

### POST /cats/:catId/weight
Ajoute une nouvelle pesée.

**Body :**
```json
{
  "weight": 4.5,
  "date": "2024-12-14",
  "notes": "Poids stable"
}
```

---

## 💉 Vaccinations

### GET /cats/:catId/vaccinations
Récupère l'historique des vaccinations.

**Réponse :**
```json
[
  {
    "id": 1,
    "cat_id": 1,
    "vaccine_type": "Rage",
    "date": "2024-03-15",
    "next_date": "2025-03-15",
    "vet_name": "Dr. Dupont",
    "notes": "Rappel dans 1 an",
    "document_url": "/uploads/vacc_cert.pdf",
    "created_at": "2024-03-15 14:30:00"
  }
]
```

### POST /cats/:catId/vaccinations
Enregistre une nouvelle vaccination.

**Body :**
```json
{
  "vaccine_type": "Rage",
  "date": "2024-03-15",
  "next_date": "2025-03-15",
  "vet_name": "Dr. Dupont",
  "notes": "Rappel dans 1 an"
}
```

---

## 💊 Treatments (Traitements)

### GET /cats/:catId/treatments
Récupère l'historique des traitements.

**Réponse :**
```json
[
  {
    "id": 1,
    "cat_id": 1,
    "treatment_type": "Vermifuge",
    "product_name": "Milbemax",
    "date": "2024-12-01",
    "next_date": "2025-03-01",
    "dosage": "1 comprimé",
    "notes": "À jeun",
    "created_at": "2024-12-01 09:00:00"
  }
]
```

**Types de traitement :**
- `Vermifuge`
- `Antipuce`
- `Antibiotique`
- `Anti-inflammatoire`
- `Autre`

### POST /cats/:catId/treatments
Enregistre un nouveau traitement.

**Body :**
```json
{
  "treatment_type": "Vermifuge",
  "product_name": "Milbemax",
  "date": "2024-12-01",
  "next_date": "2025-03-01",
  "dosage": "1 comprimé",
  "notes": "À jeun"
}
```

---

## 📸 Photos

### GET /cats/:catId/photos
Récupère la galerie photos d'un chat.

**Réponse :**
```json
[
  {
    "id": 1,
    "cat_id": 1,
    "url": "/uploads/photo_123.jpg",
    "title": "Sieste au soleil",
    "tags": "Sommeil,Mignon",
    "date": "2024-12-14",
    "location": "Salon",
    "created_at": "2024-12-14 15:20:00"
  }
]
```

### POST /cats/:catId/photos
Upload une nouvelle photo.

**Content-Type :** `multipart/form-data`

**Form Data :**
```
photo: [file]
title: "Sieste au soleil"
tags: "Sommeil,Mignon"
date: "2024-12-14"
location: "Salon"
```

**Tags suggérés :**
- Joyeux, Triste, Curieux
- Sommeil, Jeu, Repas
- Mignon, Drôle, Majestueux

---

## 🔔 Reminders (Rappels)

### GET /cats/:catId/reminders
Récupère les rappels actifs d'un chat.

**Réponse :**
```json
[
  {
    "id": 1,
    "cat_id": 1,
    "title": "Rappel Antipuce",
    "description": "Appliquer Frontline",
    "reminder_date": "2025-01-01",
    "reminder_type": "treatment",
    "is_completed": 0,
    "created_at": "2024-12-01 10:00:00"
  }
]
```

**Types de rappel :**
- `vaccination`
- `treatment`
- `vet`
- `grooming`
- `other`

### POST /cats/:catId/reminders
Crée un nouveau rappel.

**Body :**
```json
{
  "title": "Rappel Antipuce",
  "description": "Appliquer Frontline",
  "reminder_date": "2025-01-01",
  "reminder_type": "treatment"
}
```

### PATCH /reminders/:id/complete
Marque un rappel comme complété.

---

## 🏥 Vet Visits (Visites vétérinaires)

### GET /cats/:catId/vet-visits
Récupère l'historique des visites vétérinaires.

**Réponse :**
```json
[
  {
    "id": 1,
    "cat_id": 1,
    "date": "2024-12-01",
    "reason": "Visite annuelle",
    "diagnosis": "Bonne santé générale",
    "treatment": "Vaccins à jour",
    "cost": 65.50,
    "notes": "Poids idéal, comportement normal",
    "document_url": "/uploads/visit_report.pdf",
    "created_at": "2024-12-01 16:00:00"
  }
]
```

### POST /cats/:catId/vet-visits
Enregistre une nouvelle visite vétérinaire.

**Body :**
```json
{
  "date": "2024-12-01",
  "reason": "Visite annuelle",
  "diagnosis": "Bonne santé générale",
  "treatment": "Vaccins à jour",
  "cost": 65.50,
  "notes": "Poids idéal"
}
```

---

## 💾 Backup

### POST /backup
Crée un backup manuel de la base de données.

**Réponse :**
```json
{
  "message": "Backup créé avec succès"
}
```

---

## 🔍 Health Check

### GET /health
Vérifie l'état de l'API.

**Réponse :**
```json
{
  "status": "ok",
  "timestamp": "2024-12-14T10:30:00.000Z"
}
```

---

## 📊 Codes de statut HTTP

- `200` - Succès
- `201` - Créé
- `400` - Mauvaise requête
- `404` - Non trouvé
- `500` - Erreur serveur

## 🔒 Sécurité

- Toutes les requêtes SQL utilisent des requêtes préparées
- Upload limité à 50MB
- CORS activé
- Validation des entrées côté serveur

## 💡 Exemples d'utilisation

### JavaScript / Fetch

```javascript
// Récupérer tous les chats
const cats = await fetch('http://localhost:6000/api/cats')
  .then(res => res.json());

// Créer un nouveau chat
const newCat = await fetch('http://localhost:6000/api/cats', {
  method: 'POST',
  headers: { 'Content-Type': 'application/json' },
  body: JSON.stringify({
    name: 'Minou',
    breed: 'Européen',
    birth_date: '2020-05-15'
  })
}).then(res => res.json());

// Ajouter une pesée
const weight = await fetch('http://localhost:6000/api/cats/1/weight', {
  method: 'POST',
  headers: { 'Content-Type': 'application/json' },
  body: JSON.stringify({
    weight: 4.5,
    date: '2024-12-14',
    notes: 'Poids stable'
  })
}).then(res => res.json());
```

### cURL

```bash
# Récupérer tous les chats
curl http://localhost:6000/api/cats

# Créer un nouveau chat
curl -X POST http://localhost:6000/api/cats \
  -H "Content-Type: application/json" \
  -d '{"name":"Minou","breed":"Européen","birth_date":"2020-05-15"}'

# Upload une photo
curl -X POST http://localhost:6000/api/cats/1/photos \
  -F "photo=@chat.jpg" \
  -F "title=Belle photo" \
  -F "tags=Mignon,Joyeux" \
  -F "date=2024-12-14"
```

---

**Pour toute question sur l'API, consultez le code source dans `server/index.js`**
