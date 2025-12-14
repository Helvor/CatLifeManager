# 🚀 Démarrage Rapide - CatLife Tracker

## Installation en 5 minutes

### Étape 1️⃣ : Prérequis
Assurez-vous d'avoir Docker installé :
```bash
docker --version
docker-compose --version
```

Si Docker n'est pas installé, téléchargez-le depuis [docker.com](https://www.docker.com/get-started)

### Étape 2️⃣ : Créer le projet

```bash
# Créer le dossier
mkdir catlife-tracker
cd catlife-tracker

# Créer les sous-dossiers
mkdir -p server/uploads database/backups src/components
```

### Étape 3️⃣ : Créer les fichiers

Créez les fichiers suivants avec le contenu fourni dans les artifacts :

**Fichiers racine :**
- `docker-compose.yml`
- `Dockerfile`
- `package.json`
- `vite.config.js`
- `index.html`
- `.gitignore`
- `.env.example`

**Dossier server/ :**
- `server/index.js`
- `server/db.js`
- `server/backup.js`

**Dossier src/ :**
- `src/main.jsx`
- `src/App.jsx`
- `src/index.css`

**Dossier src/components/ :**
- `src/components/CatLifeTracker.jsx`
- `src/components/AddCatModal.jsx`
- `src/components/AddVaccinationModal.jsx`
- `src/components/AddTreatmentModal.jsx`
- `src/components/AddWeightModal.jsx`
- `src/components/AddPhotoModal.jsx`

**Dossier src/utils/ :**
- `src/utils/api.js`

**Fichier vide pour Git :**
```bash
touch server/uploads/.gitkeep
```

### Étape 4️⃣ : Lancer l'application

```bash
# Build et démarrage
docker-compose up --build

# Ou en arrière-plan
docker-compose up --build -d
```

### Étape 5️⃣ : Accéder à l'application

Ouvrez votre navigateur :
- **Application** : http://localhost:3000
- **API** : http://localhost:6000

## ✅ Vérification

Si tout fonctionne, vous devriez voir :
- ✅ Page d'accueil avec "CatLife Tracker" 
- ✅ Bouton "+ Nouveau chat"
- ✅ Tableau de bord avec statistiques
- ✅ Menu latéral avec navigation

## 🎯 Premiers pas

1. **Créer votre premier chat**
   - Cliquez sur "+ Nouveau chat"
   - Remplissez les informations
   - Sauvegardez

2. **Ajouter des données**
   - Onglet "Santé" → Vaccinations, Traitements
   - Onglet "Poids" → Enregistrer une pesée
   - Onglet "Photos" → Uploader des photos

3. **Consulter les statistiques**
   - Onglet "Statistiques" → Graphiques et analyses

## 🔧 Commandes utiles

```bash
# Voir les logs en temps réel
docker-compose logs -f

# Arrêter l'application
docker-compose down

# Redémarrer
docker-compose restart

# Backup manuel
docker-compose exec app npm run backup
```

## 🆘 Problèmes courants

### Port déjà utilisé
```bash
# Changer les ports dans docker-compose.yml
ports:
  - "3001:3000"  # Au lieu de 3000:3000
  - "6001:6000"  # Au lieu de 6000:6000
```

### Erreur de permissions
```bash
sudo chown -R $USER:$USER database/
sudo chown -R $USER:$USER server/uploads/
```

### Rebuild nécessaire
```bash
docker-compose down
docker-compose up --build
```

## 📚 Documentation complète

Pour plus d'informations, consultez le [README.md](README.md) complet.

---
