# CatLife Manager — Product Roadmap

> Last updated: 2026-02-26
> Current version: 0.3
> Stack: PHP 8.2 · SQLite · Vanilla JS · Custom CSS

---

## Légende

| Symbole | Signification |
|---------|---------------|
| ✅ | Terminé |
| ⚠️ | Partiel |
| ❌ | Non démarré |

---

## Vision

Transform CatLife Manager from a local single-user tool into a polished,
multi-user web app — installable on iOS as a PWA, protected by modern
authentication, and delightful to use in 2026.

---

## ✅ Phase 1 — PWA & iOS Installability

**Statut : Complète**

### ✅ 1.1 Web App Manifest

`manifest.json` créé et lié dans `<head>`.

### ✅ 1.2 Apple-Specific Meta Tags

Meta tags iOS (`apple-mobile-web-app-capable`, status bar, titre, icônes
`apple-touch-icon`) dans `views/layout.php`.

### ✅ 1.3 Service Worker (Offline Support)

`sw.js` implémenté avec pré-cache de l'app shell, stratégie cache-first
et nettoyage des anciens caches.

### ✅ 1.4 HTTPS Requirement

`Caddyfile` + `docker-compose.https.yml` ajoutés pour reverse proxy avec
HTTPS automatique.

### ✅ 1.5 Viewport & Safe Areas

Meta viewport `viewport-fit=cover` + `env(safe-area-inset-*)` dans `style.css`.

---

## ❌ Phase 2 — Authentication

**Statut : Non démarrée — priorité suivante**

**Goal:** Secure the app with multi-user support and three sign-in methods.

### ❌ 2.1 Database Changes

Add a `users` table and link cats to users:

```sql
CREATE TABLE users (
  id           INTEGER PRIMARY KEY AUTOINCREMENT,
  email        TEXT UNIQUE NOT NULL,
  name         TEXT,
  avatar_url   TEXT,
  password_hash TEXT,         -- NULL for OAuth-only users
  provider     TEXT NOT NULL DEFAULT 'email',  -- 'email' | 'google' | 'apple'
  provider_id  TEXT,          -- OAuth subject identifier
  created_at   DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Add to cats table:
ALTER TABLE cats ADD COLUMN user_id INTEGER REFERENCES users(id) ON DELETE CASCADE;
```

### ❌ 2.2 Session Management

Use PHP native sessions secured with regeneration:

```php
// auth.php
session_start();
session_regenerate_id(true);

function currentUser(): ?array {
    return $_SESSION['user'] ?? null;
}

function requireAuth(): void {
    if (!currentUser()) {
        header('Location: /login');
        exit;
    }
}
```

Add `requireAuth()` at the top of `index.php`.

### ❌ 2.3 Email / Password Authentication

- Hash passwords with `password_hash($pass, PASSWORD_ARGON2ID)`
- Verify with `password_verify()`
- Add a CSRF token to every form (one hidden `<input name="_token">`)
- Rate-limit login attempts (track in a `login_attempts` table)
- Add a "Forgot password" flow with a time-limited token sent by email

Recommended library: `PHPMailer/PHPMailer` for transactional email.

### ❌ 2.4 Google OAuth 2.0

Recommended library: `league/oauth2-google`

Flow:
1. User clicks "Continue with Google"
2. Redirect to Google authorization URL
3. Google redirects back to `/auth/callback/google?code=...`
4. Exchange code for tokens, fetch profile from `https://openidconnect.googleapis.com/v1/userinfo`
5. Upsert user record, set session

```php
// composer.json additions
"league/oauth2-google": "^4.0"
```

Google Cloud Console setup:
- Authorized redirect URIs: `https://yourdomain.com/auth/callback/google`
- Scopes: `email`, `profile`, `openid`

### ❌ 2.5 Apple Sign In

Apple Sign In is required if you want to offer any social login on iOS.

Recommended library: `patrickbussell/apple-sign-in-php` or implement manually
using Apple's JWT-based flow.

Flow:
1. User clicks "Sign in with Apple"
2. Redirect to `https://appleid.apple.com/auth/authorize`
3. Apple POST to `/auth/callback/apple` with a JWT `id_token`
4. Verify JWT signature against Apple's public keys (JWKS endpoint)
5. Extract `sub` (stable user ID) and `email`

Apple Developer Console setup:
- Register an App ID with "Sign in with Apple" capability
- Create a Service ID (used as `client_id`)
- Generate a private key (.p8 file) for client secret JWT

> Note: Apple only sends the email on the **first** sign-in. Store it immediately.

### ❌ 2.6 Login UI

Create `views/pages/login.php` and `views/pages/register.php` with:
- Clean centered card layout
- Email/password fields
- "Continue with Google" button (Google brand colors)
- "Sign in with Apple" button (black, Apple's brand guidelines)
- Link between login ↔ register

---

## ✅ Phase 3 — UX & UI Modernization

**Statut : Complète**

### ✅ 3.1 Nouveau système de couleurs

Palette "Soft Pet App" 2026 implémentée dans `style.css` :
variables CSS `--color-lavender`, `--color-base`, `--color-surface`, etc.

### ✅ 3.2 Typographie

`Plus Jakarta Sans` (Google Fonts) + hiérarchie typographique définie.

### ✅ 3.3 Layout Mobile (Bottom Tab Bar)

`views/partials/bottom_nav.php` — 5 onglets : Dashboard · Santé · Poids ·
Photos · Profil. Sidebar cachée sur mobile.

### ✅ 3.4 Micro-interactions & Animations

Transitions spring, slide-up des modals, press scale sur les boutons.

### ✅ 3.5 Skeleton Loading States

Skeleton cards avec animation shimmer dans `style.css`.

### ✅ 3.6 Toast Notifications

Système toast vanilla JS dans `views/layout.php` avec `#toast-container`.
Types : `success`, `error`, `info`, `warning`.

### ✅ 3.7 Weight Chart (Chart.js)

Graphique sparkline dans `views/pages/weight.php` avec support dark mode.

### ⚠️ 3.8 Photo Gallery Upgrade

Grille simple en place. Lightbox plein écran et swipe navigation **non implémentés**.

### ✅ 3.9 Dark Mode

`prefers-color-scheme: dark` automatique + toggle manuel stocké dans
`localStorage`. Bouton toggle dans le header.

### ✅ 3.10 Empty States

États vides avec illustration et CTA dans les pages sans données.

### ✅ 3.11 SVG Icons (Lucide)

Remplacement des emoji par des icônes Lucide SVG dans toute l'interface.

---

## ⚠️ Phase 4 — Feature Improvements

**Statut : Partielle**

### ❌ 4.1 Async Form Submissions (No Page Reloads)

Les formulaires utilisent encore des POST avec redirection complète.
À convertir avec `fetch()` :

```js
form.addEventListener('submit', async (e) => {
  e.preventDefault();
  const res = await fetch(form.action, {
    method: 'POST',
    body: new FormData(form)
  });
  const { ok, message } = await res.json();
  toast(message, ok ? 'success' : 'error');
  if (ok) { modal.close(); refreshSection(); }
});
```

Mettre à jour `router.php` pour retourner du JSON si
`Accept: application/json` est présent.

### ❌ 4.2 Health Record Export (PDF)

Use `dompdf/dompdf` (PHP library, no external service needed) to generate a
printable carnet de santé:
- Cat profile, photo, microchip
- All vaccinations and next booster dates
- Treatment history
- Vet contact info
- QR code linking to the app

### ❌ 4.3 Email Reminders

Use a cron job + PHPMailer to send reminder emails:

```
# crontab
0 8 * * * php /var/www/html/cron/send_reminders.php
```

The script queries for reminders due today or tomorrow and sends a digest email
to the cat owner.

### ❌ 4.4 REST API

Add a thin JSON API layer so the app can power a native app later:

```
GET    /api/cats
POST   /api/cats
GET    /api/cats/{id}
PUT    /api/cats/{id}
DELETE /api/cats/{id}
GET    /api/cats/{id}/vaccinations
POST   /api/cats/{id}/vaccinations
...
```

Authenticate API requests with Bearer tokens (JWT, signed with `firebase/php-jwt`).

### ❌ 4.5 Multi-Cat Quick Switcher

On iOS, implement a swipeable card carousel at the top of the dashboard so users
with multiple cats can switch without using the dropdown:

```
← 🐱 Luna   |   Milo 🐱 →
```

### ❌ 4.6 Shareable Health QR Code

Generate a public, read-only URL per cat (with a random token):
`/share/{random_token}` — shows vaccinations and vet info to a vet or
pet sitter without requiring login.

### ❌ 4.7 Photo Lightbox & Swipe Navigation

Remplacer la grille photo simple par :
- CSS `columns` pour layout masonry (pas de JS)
- Overlay plein écran au tap avec navigation swipe
- Pinch-to-zoom sur iOS

---

## ⚠️ Phase 5 — Infrastructure & Developer Experience

**Statut : Partielle**

### ✅ 5.2 Docker Improvements (Caddy)

`Caddyfile` + `docker-compose.https.yml` avec reverse proxy Caddy pour
HTTPS automatique.

### ❌ 5.1 Introduce Composer

```bash
composer require league/oauth2-google
composer require league/oauth2-client
composer require firebase/php-jwt
composer require phpmailer/phpmailer
composer require dompdf/dompdf
composer require vlucas/phpdotenv
```

Move credentials to `.env` (managed by `phpdotenv`):

```
APP_URL=https://catlife.example.com
DB_PATH=/var/www/data/catlife.db
GOOGLE_CLIENT_ID=...
GOOGLE_CLIENT_SECRET=...
APPLE_CLIENT_ID=...
APPLE_TEAM_ID=...
APPLE_KEY_ID=...
APPLE_PRIVATE_KEY_PATH=...
SMTP_HOST=smtp.mailgun.org
SMTP_USER=...
SMTP_PASS=...
```

### ❌ 5.3 Security Hardening

- [ ] Add CSRF token middleware for all POST forms
- [ ] Set `SameSite=Lax; Secure; HttpOnly` on session cookie
- [ ] Move database out of webroot
- [ ] Add Content-Security-Policy header
- [ ] Validate and sanitize file upload MIME type (not just extension)
- [ ] Add rate limiting on auth endpoints (IP-based, tracked in SQLite)
- [ ] Log auth events (logins, failures, password resets)

### ❌ 5.4 Testing

Introduce a basic test suite with PHPUnit:
- Unit tests for `database.php` functions
- Integration tests for auth flows
- End-to-end smoke test with a headless browser (Playwright)

---

## Ordre d'implémentation suggéré

| # | Phase | Statut | Effort | Valeur |
|---|-------|--------|--------|--------|
| 1 | PWA + iOS meta tags | ✅ Fait | Low | High |
| 2 | Dark mode + bottom nav mobile | ✅ Fait | Low | High |
| 3 | UI redesign (couleurs, typo, icônes) | ✅ Fait | Medium | High |
| 4 | Toast notifications | ✅ Fait | Low | High |
| 5 | Weight chart | ✅ Fait | Low | Medium |
| 6 | **Email authentication** | ❌ À faire | Medium | High |
| 7 | **Composer + .env** | ❌ À faire | Low | High |
| 8 | **Google OAuth** | ❌ À faire | Medium | High |
| 9 | **Apple Sign In** | ❌ À faire | Medium | Requis iOS |
| 10 | **Async forms (fetch)** | ❌ À faire | Medium | High |
| 11 | **Security hardening (CSRF, etc.)** | ❌ À faire | Medium | Critique |
| 12 | Photo lightbox & swipe | ❌ À faire | Low | Medium |
| 13 | PDF export carnet de santé | ❌ À faire | Low | Medium |
| 14 | Email reminders (cron) | ❌ À faire | Medium | Medium |
| 15 | REST API + JWT | ❌ À faire | High | Medium |
| 16 | Multi-cat quick switcher | ❌ À faire | Low | Medium |
| 17 | QR code partageable | ❌ À faire | Low | Medium |
| 18 | Tests (PHPUnit + Playwright) | ❌ À faire | High | Élevé |

---

## Prochaines étapes immédiates (v0.4)

Les 3 tâches à traiter en priorité :

### 1. Introduire Composer & `.env`

Installer Composer dans le Dockerfile et ajouter `vlucas/phpdotenv`.
Déplacer toutes les constantes hardcodées (`DB_PATH`, futurs tokens OAuth)
vers un fichier `.env` ignoré par git.

### 2. Authentification email/password

- Créer la table `users` dans `database.php`
- Ajouter `auth.php` avec `requireAuth()` / `currentUser()`
- Créer `views/pages/login.php` et `views/pages/register.php`
- Brancher `requireAuth()` dans `index.php`
- Ajouter un token CSRF à tous les formulaires POST

### 3. Soumissions de formulaires async (fetch)

Convertir les modals en fetch + JSON pour supprimer les rechargements complets.
Mettre à jour `router.php` pour retourner du JSON en plus des redirections.

---

## Open Questions

1. **Framework migration?** Staying on vanilla PHP is fine for simplicity, but
   a micro-framework like `Slim 4` or `Laravel` would speed up Phase 2–4 significantly.
   Consider it if the codebase grows beyond ~2,000 lines.

2. **Database migration?** SQLite is great for a personal tool. If going
   multi-user with concurrent writes, consider migrating to PostgreSQL.

3. **Language of the UI?** Currently French only. Would an i18n layer be worth
   adding, or is this always a French-first app?

4. **Hosting target?** VPS (Docker), PaaS (Railway, Render, Fly.io), or
   self-hosted (Raspberry Pi)? This affects the HTTPS and email strategy.
