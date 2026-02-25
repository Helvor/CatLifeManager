# CatLife Manager

Application web pour suivre la santé, le poids et les souvenirs de vos chats.
Installable sur iPhone via Safari (PWA) — aucune App Store requise.

---

## Fonctionnalités

- Gestion de plusieurs chats (profil, vétérinaire, puce électronique)
- Carnet de santé : vaccinations et traitements avec dates de rappel
- Suivi du poids dans le temps
- Galerie photos avec tags et localisation
- Rappels personnalisables
- Tableau de bord avec statistiques
- PWA installable sur iOS (Add to Home Screen)
- Support hors-ligne basique via Service Worker

---

## Stack technique

| Composant | Technologie |
|-----------|-------------|
| Backend   | PHP 8.2     |
| Base de données | SQLite 3 (PDO) |
| Frontend  | HTML/CSS/JS vanilla |
| Serveur   | Apache (Docker) ou PHP built-in |
| HTTPS     | Caddy (optionnel, requis pour le PWA en prod) |

---

## Installation

### Option 1 — Serveur local PHP (développement)

**Prérequis :** PHP 8.2+ avec les extensions `pdo_sqlite` et `gd`

```bash
git clone <url-du-repo> catlife
cd catlife

# Générer les icônes PWA (nécessite l'extension GD)
php scripts/generate_icons.php

# Créer les dossiers de données
mkdir -p uploads database
chmod 777 uploads database

# Lancer le serveur
php -S localhost:8000
```

Accéder à : `http://localhost:8000`

> **Note :** En HTTP local, le Service Worker et l'installation PWA ne sont pas disponibles.
> Utilisez l'option Docker + Caddy pour tester le PWA complet.

---

### Option 2 — Docker (recommandé)

**Prérequis :** Docker et Docker Compose

```bash
git clone <url-du-repo> catlife
cd catlife

docker-compose up --build
```

Accéder à : `http://localhost:8000`

Les données sont persistées dans des dossiers locaux (`database/` et `uploads/`).

---

### Option 3 — Docker + HTTPS via Caddy (production / PWA iOS)

L'installation PWA sur iOS requiert HTTPS. Caddy gère les certificats automatiquement.

**Pour un nom de domaine réel (Let's Encrypt) :**

```bash
APP_DOMAIN=catlife.example.com \
  docker-compose -f docker-compose.yml -f docker-compose.https.yml up -d
```

**Pour tester en HTTPS en local (CA locale Caddy) :**

```bash
APP_DOMAIN=localhost \
  docker-compose -f docker-compose.yml -f docker-compose.https.yml up
```

> Caddy installe sa CA locale dans le trust store de votre OS automatiquement.

**Installer l'app sur iPhone :**
1. Ouvrir l'URL dans Safari
2. Toucher le bouton Partager
3. Sélectionner "Sur l'écran d'accueil"

---

## Structure du projet

```
catlife/
├── index.php                    # Point d'entrée, chargement des données
├── router.php                   # Dispatcher des actions POST
├── database.php                 # Couche d'accès aux données (PDO/SQLite)
├── config.php                   # Configuration (chemins, timezone, upload)
├── style.css                    # Styles globaux
├── manifest.json                # Web App Manifest (PWA)
├── sw.js                        # Service Worker (cache offline)
├── Caddyfile                    # Config Caddy pour HTTPS
├── docker-compose.yml           # Docker Compose (HTTP)
├── docker-compose.https.yml     # Overlay Caddy pour HTTPS
├── dockerfile                   # Image PHP 8.2 + Apache + GD
├── scripts/
│   └── generate_icons.php       # Génère les icônes PNG via GD
├── icons/                       # Icônes PWA générées (192, 512, apple-touch…)
├── views/
│   ├── layout.php               # Template HTML principal
│   ├── partials/
│   │   ├── header.php           # En-tête de navigation
│   │   └── sidebar.php          # Sélecteur de chat + onglets
│   ├── pages/
│   │   ├── dashboard.php        # Tableau de bord
│   │   ├── health.php           # Vaccinations et traitements
│   │   ├── weight.php           # Historique du poids
│   │   ├── photos.php           # Galerie photos
│   │   └── empty.php            # État vide (aucun chat)
│   └── modals/
│       ├── add_cat.php          # Formulaire nouveau chat
│       ├── edit_cat.php         # Formulaire modification chat
│       ├── add_vaccination.php  # Ajout d'un vaccin
│       ├── add_treatment.php    # Ajout d'un traitement
│       ├── add_weight.php       # Saisie du poids
│       └── add_photo.php        # Upload photo
├── uploads/                     # Photos uploadées (ignoré par git)
└── database/
    └── catlife.db               # Base SQLite (créée automatiquement, ignorée par git)
```

---

## Configuration

Modifier `config.php` selon votre environnement :

```php
define('DB_PATH',         __DIR__ . '/database/catlife.db');
define('UPLOAD_DIR',      __DIR__ . '/uploads/');
define('MAX_UPLOAD_SIZE', 10 * 1024 * 1024); // 10 Mo par défaut
date_default_timezone_set('Europe/Brussels');
```

---

## Base de données

La base SQLite est créée automatiquement au premier démarrage.

| Table | Contenu |
|-------|---------|
| `cats` | Profils des chats (nom, race, date de naissance, vétérinaire…) |
| `vaccinations` | Vaccins avec date et prochain rappel |
| `treatments` | Traitements (vermifuges, antipuces…) |
| `weight_records` | Historique des pesées |
| `photos` | Métadonnées des photos uploadées |
| `reminders` | Rappels personnalisés |

Toutes les tables sont liées à `cats` par `cat_id` avec suppression en cascade.

---

## Génération des icônes

Les icônes PWA sont générées par `scripts/generate_icons.php` à l'aide de l'extension GD.
Elles sont committées dans `icons/` pour éviter de devoir relancer le script à chaque clone.

Pour les régénérer (après modification du design) :

```bash
php scripts/generate_icons.php
```

Fichiers produits : `icon-192.png`, `icon-512.png`, `icon-maskable.png`,
`apple-touch-icon.png`, `apple-touch-icon-152.png`, `apple-touch-icon-120.png`.

---

## Dépannage

**Erreur "Unable to open database"**
```bash
chmod 777 database/
```

**Les photos ne s'uploadent pas**
```bash
chmod 777 uploads/
```

**Page blanche**
```bash
# Activer les erreurs PHP (dev uniquement)
php -S localhost:8000 -d display_errors=1
```

**Les icônes PWA ne s'affichent pas**
```bash
php scripts/generate_icons.php
```

---

## Feuille de route

Voir [ROADMAP.md](ROADMAP.md) pour le détail complet des évolutions prévues.

Prochaines grandes étapes :
1. **Authentification** — email/password, Google et Apple Sign In
2. **UI/UX 2026** — refonte visuelle, dark mode, navigation mobile en bas d'écran
3. **Formulaires asynchrones** — fin des rechargements de page complets
4. **Export PDF** — carnet de santé imprimable
5. **Rappels par email** — notifications automatiques via cron

---

## Sécurité

- Les requêtes SQL utilisent des prepared statements (PDO) — protection contre l'injection SQL
- Les sorties HTML sont échappées avec `htmlspecialchars()` — protection XSS
- L'accès au dossier `scripts/` est bloqué par `.htaccess`

> L'application ne dispose pas encore d'authentification. Elle est conçue pour un usage
> local ou en accès restreint. Ne pas exposer sur internet sans avoir d'abord implémenté
> la Phase 2 du ROADMAP.

---

## Licence

Projet personnel — tous droits réservés.
