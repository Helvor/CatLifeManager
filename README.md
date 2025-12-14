# 🐱 CatLife Tracker

Application web containerisée pour suivre la santé et le bien-être de vos chats.

![CatLife Tracker](https://img.shields.io/badge/version-1.0.0-purple) ![Docker](https://img.shields.io/badge/docker-ready-blue) ![License](https://img.shields.io/badge/license-MIT-green)

## ✨ Fonctionnalités

### 🏥 Carnet de Santé
- 💉 Suivi des vaccinations avec rappels automatiques
- 💊 Gestion des traitements (vermifuge, antipuce, médicaments)
- 📄 Upload de documents PDF/images (certificats vétérinaires)
- 🏥 Historique des visites vétérinaires

### ⚖️ Suivi du Poids
- 📊 Graphiques d'évolution du poids
- 📈 Calcul d'indicateurs de santé
- 📝 Notes et observations
- 🔔 Alertes sur variations importantes

### 📸 Galerie Photos
- 🖼️ Organisation par tags (Joyeux, Sommeil, Jeu...)
- 📅 Association date et localisation
- 🔍 Recherche et filtres
- 💾 Stockage illimité

### 📊 Statistiques & Graphiques
- 📈 Évolution du poids dans le temps
- 📊 Fréquence des visites vétérinaires
- 📉 Analyses de tendances
- 📋 Rapports de santé

### 🔔 Rappels & Notifications
- ⏰ Rappels de vaccination
- 💊 Rappels de traitements
- 🏥 Rendez-vous vétérinaire
- ✅ Marquage des tâches complétées

### 👤 Profil Complet
- 📝 Informations détaillées (nom, race, âge, stérilisation)
- 🏥 Coordonnées du vétérinaire
- 🆔 Numéro de puce électronique
- 📸 Photo de profil

## 🚀 Installation

### Prérequis
- Docker et Docker Compose installés
- Port 3000 et 5000 disponibles

### Installation rapide

1. **Cloner ou créer le dossier du projet**
```bash
mkdir catlife-tracker
cd catlife-tracker
```

2. **Créer la structure des fichiers**

Créez les fichiers suivants avec le contenu fourni :

```
catlife-tracker/
├── docker-compose.yml
├── Dockerfile
├── package.json
├── vite.config.js
├── index.html
├── server/
│   ├── index.js
│   ├── db.js
│   └── backup.js
└── src/
    ├── main.jsx
    ├── index.css
    ├── App.jsx
    └── components/
        └── CatLifeTracker.jsx
```

3. **Lancer l'application**
```bash
docker-compose up --build
```

4. **Accéder à l'application**
- Frontend : http://localhost:3000
- API Backend : http://localhost:6000

## 📖 Utilisation

### Premier lancement
1. Ouvrez http://localhost:3000
2. Cliquez sur "+ Nouveau chat"
3. Remplissez les informations de votre chat
4. Commencez à ajouter des données !

### Commandes Docker utiles

```bash
# Démarrer en arrière-plan
docker-compose up -d

# Arrêter l'application
docker-compose down

# Voir les logs
docker-compose logs -f

# Redémarrer après modifications
docker-compose restart

# Rebuild complet
docker-compose up --build
```

### Backup de la base de données

#### Backup automatique
- Backup quotidien automatique à 3h du matin
- Conservation des 30 derniers backups
- Stockage dans `database/backups/`

#### Backup manuel
```bash
# Via Docker
docker-compose exec app npm run backup

# Via l'API
curl -X POST http://localhost:6000/api/backup
```

### Restauration d'un backup

```bash
# Arrêter l'application
docker-compose down

# Copier le backup souhaité
cp database/backups/catlife_backup_2024-12-14.db database/catlife.db

# Redémarrer
docker-compose up -d
```

## 🛠️ Architecture Technique

### Stack Technologique
- **Frontend** : React 18 + Vite
- **Backend** : Node.js + Express
- **Base de données** : SQLite3
- **UI** : Tailwind CSS
- **Graphiques** : Recharts
- **Container** : Docker + Docker Compose

### Structure de la base de données

```sql
-- Tables principales
cats                 -- Profils des chats
vaccinations         -- Historique vaccinal
treatments           -- Traitements (vermifuge, antipuce, médicaments)
weight_records       -- Suivi du poids
photos               -- Galerie photos
reminders            -- Rappels et notifications
vet_visits           -- Visites vétérinaires
```

### API Endpoints

```
GET    /api/cats                      -- Liste des chats
POST   /api/cats                      -- Créer un chat
GET    /api/cats/:id                  -- Détails d'un chat
PUT    /api/cats/:id                  -- Modifier un chat

GET    /api/cats/:catId/weight        -- Historique poids
POST   /api/cats/:catId/weight        -- Ajouter pesée

GET    /api/cats/:catId/vaccinations  -- Liste vaccinations
POST   /api/cats/:catId/vaccinations  -- Ajouter vaccination

GET    /api/cats/:catId/treatments    -- Liste traitements
POST   /api/cats/:catId/treatments    -- Ajouter traitement

GET    /api/cats/:catId/photos        -- Galerie photos
POST   /api/cats/:catId/photos        -- Upload photo

GET    /api/cats/:catId/reminders     -- Liste rappels
POST   /api/cats/:catId/reminders     -- Créer rappel
PATCH  /api/reminders/:id/complete    -- Marquer complété

POST   /api/backup                    -- Créer backup manuel
```

## 🔧 Développement

### Mode développement local

```bash
# Installer les dépendances
npm install

# Démarrer le backend
npm run server

# Dans un autre terminal, démarrer le frontend
npm run client
```

### Ajouter de nouvelles fonctionnalités

1. **Ajouter une nouvelle table**
```javascript
// Dans server/db.js
db.exec(`
  CREATE TABLE IF NOT EXISTS nouvelle_table (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    cat_id INTEGER NOT NULL,
    // vos champs...
    FOREIGN KEY (cat_id) REFERENCES cats(id) ON DELETE CASCADE
  )
`);
```

2. **Ajouter une nouvelle route API**
```javascript
// Dans server/index.js
app.get('/api/cats/:catId/nouvelle-route', (req, res) => {
  // votre logique
});
```

3. **Ajouter un nouveau composant React**
```javascript
// Dans src/components/
const NouveauComposant = () => {
  // votre composant
};
```

## 📦 Structure des fichiers

```
catlife-tracker/
├── docker-compose.yml          # Configuration Docker Compose
├── Dockerfile                  # Image Docker
├── package.json                # Dépendances Node.js
├── vite.config.js             # Configuration Vite
├── index.html                  # Point d'entrée HTML
│
├── server/                     # Backend
│   ├── index.js               # Serveur Express
│   ├── db.js                  # Configuration SQLite
│   ├── backup.js              # Script de backup
│   └── uploads/               # Fichiers uploadés
│
├── database/                   # Base de données
│   ├── catlife.db            # BDD principale
│   └── backups/              # Backups automatiques
│
└── src/                       # Frontend React
    ├── main.jsx              # Point d'entrée React
    ├── App.jsx               # Composant racine
    ├── index.css             # Styles globaux
    └── components/
        └── CatLifeTracker.jsx # Composant principal
```

## 🔐 Sécurité

- ✅ Validation des entrées côté serveur
- ✅ Protection contre les injections SQL (requêtes préparées)
- ✅ Limitation de taille des uploads (50MB)
- ✅ CORS configuré
- ✅ Clés étrangères activées

## 🌟 Roadmap

### Version 1.1
- [ ] Multi-utilisateurs avec authentification
- [ ] Export PDF des rapports
- [ ] Notifications par email
- [ ] Mode sombre

### Version 1.2
- [ ] Application mobile (React Native)
- [ ] Intégration calendrier
- [ ] Partage avec le vétérinaire
- [ ] Import/Export de données

### Version 2.0
- [ ] IA pour analyse de photos
- [ ] Recommandations personnalisées
- [ ] Communauté d'utilisateurs
- [ ] Marketplace produits

## 🐛 Résolution de problèmes

### Problème de permissions
```bash
# Vérifier les ports
netstat -an | grep "3000\|6000"

# Vérifier les logs
docker-compose logs
```

### Erreur de base de données
```bash
# Recréer la base
rm database/catlife.db
docker-compose restart
```

### Problème de permissions
```bash
# Donner les permissions
chmod -R 755 database/
chmod -R 755 server/uploads/
```
