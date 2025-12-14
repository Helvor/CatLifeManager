# 🐱 CatLife Tracker - Version PHP/SQLite

Application web simple pour suivre la santé et le bien-être de vos chats.

## ✨ Fonctionnalités

- 🐱 Gestion de plusieurs chats
- 💉 Carnet de santé (vaccinations, traitements)
- ⚖️ Suivi du poids
- 📸 Galerie photos avec tags
- 🔔 Rappels automatiques
- 📊 Statistiques
- 💾 Base de données SQLite

## 🚀 Installation rapide

### Prérequis
- PHP 7.4+ avec extension SQLite
- Un serveur web (Apache/Nginx) ou PHP built-in server

### Installation

1. **Télécharger les fichiers**
```bash
mkdir catlife-tracker
cd catlife-tracker
```

2. **Créer les fichiers**
Copiez le contenu des artifacts dans ces fichiers :
- `config.php`
- `database.php`
- `index.php`
- `style.css`

3. **Créer les dossiers**
```bash
mkdir uploads database
chmod 777 uploads database
```

4. **Lancer l'application**

Option 1 - Serveur PHP intégré :
```bash
php -S localhost:8000
```

Option 2 - Apache/Nginx :
Placez les fichiers dans votre répertoire web (htdocs, www, etc.)

5. **Accéder à l'application**
```
http://localhost:8000
```

## 📁 Structure des fichiers

```
catlife-tracker/
├── index.php        # Page principale + logique
├── database.php     # Gestion base de données
├── config.php       # Configuration
├── style.css        # Styles CSS
├── uploads/         # Photos uploadées
└── database/
    └── catlife.db  # Base SQLite (créée auto)
```

## 🎯 Utilisation

### 1. Ajouter un chat
- Cliquez sur "+ Nouveau chat"
- Remplissez les informations
- Enregistrez

### 2. Ajouter des données
- Sélectionnez votre chat
- Naviguez dans les onglets (Santé, Poids, Photos)
- Utilisez les boutons "+ Ajouter"

### 3. Consulter les statistiques
- Tableau de bord : vue d'ensemble
- Chaque onglet affiche les données détaillées

## 🔧 Configuration

Modifiez `config.php` pour :
- Changer l'emplacement de la base de données
- Modifier la taille max des uploads
- Ajuster le timezone

```php
define('DB_PATH', __DIR__ . '/database/catlife.db');
define('UPLOAD_DIR', __DIR__ . '/uploads/');
define('MAX_UPLOAD_SIZE', 10 * 1024 * 1024); // 10 MB
```

## 📊 Base de données

La base SQLite est créée automatiquement avec ces tables :
- `cats` - Profils des chats
- `vaccinations` - Historique des vaccins
- `treatments` - Traitements et médicaments
- `weight_records` - Suivi du poids
- `photos` - Galerie photos
- `reminders` - Rappels

## 🎨 Personnalisation

### Modifier les couleurs
Dans `style.css`, cherchez les gradients :
```css
background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
```

### Ajouter des types de vaccins/traitements
Dans `index.php`, modifiez les options des `<select>` dans les modals.

## 🐛 Dépannage

### Erreur "Unable to open database"
```bash
chmod 777 database/
chmod 666 database/catlife.db
```

### Upload de photos ne fonctionne pas
```bash
chmod 777 uploads/
```

### Page blanche
Vérifiez les erreurs PHP :
```bash
tail -f /var/log/php_errors.log
```

## 📝 Fonctionnalités futures possibles

- [ ] Export PDF des carnets de santé
- [ ] Multi-utilisateurs
- [ ] Graphiques de poids
- [ ] Notifications email
- [ ] API REST
- [ ] Application mobile
