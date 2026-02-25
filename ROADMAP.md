# CatLife Manager — Product Roadmap

> Last updated: 2026-02-25
> Current version: 0.2
> Stack: PHP 8.2 · SQLite · Vanilla JS · Custom CSS

---

## Vision

Transform CatLife Manager from a local single-user tool into a polished,
multi-user web app — installable on iOS as a PWA, protected by modern
authentication, and delightful to use in 2026.

---

## Phase 1 — PWA & iOS Installability

**Goal:** Make the app installable on iOS via "Add to Home Screen" without
touching the App Store.

### 1.1 Web App Manifest

Create `public/manifest.json`:

```json
{
  "name": "CatLife Manager",
  "short_name": "CatLife",
  "start_url": "/",
  "display": "standalone",
  "background_color": "#f8f7ff",
  "theme_color": "#6c5ce7",
  "orientation": "portrait",
  "icons": [
    { "src": "/icons/icon-192.png", "sizes": "192x192", "type": "image/png" },
    { "src": "/icons/icon-512.png", "sizes": "512x512", "type": "image/png" },
    { "src": "/icons/icon-maskable.png", "sizes": "512x512", "type": "image/png", "purpose": "maskable" }
  ]
}
```

Link it in `<head>`:

```html
<link rel="manifest" href="/manifest.json">
```

### 1.2 Apple-Specific Meta Tags

iOS Safari has its own set of meta tags needed for a proper PWA feel:

```html
<!-- Required for standalone mode on iOS -->
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
<meta name="apple-mobile-web-app-title" content="CatLife">

<!-- App icons for iOS -->
<link rel="apple-touch-icon" sizes="180x180" href="/icons/apple-touch-icon.png">
<link rel="apple-touch-icon" sizes="152x152" href="/icons/apple-touch-icon-152.png">
<link rel="apple-touch-icon" sizes="120x120" href="/icons/apple-touch-icon-120.png">

<!-- Splash screens (generated for each iPhone resolution) -->
<link rel="apple-touch-startup-image" href="/icons/splash-1170x2532.png"
      media="(device-width: 390px) and (-webkit-device-pixel-ratio: 3)">
```

### 1.3 Service Worker (Offline Support)

Create `public/sw.js` to cache the app shell for offline access:

```js
const CACHE = 'catlife-v1';
const PRECACHE = ['/', '/style.css', '/icons/icon-192.png'];

self.addEventListener('install', e =>
  e.waitUntil(caches.open(CACHE).then(c => c.addAll(PRECACHE)))
);

self.addEventListener('fetch', e =>
  e.respondWith(caches.match(e.request).then(r => r || fetch(e.request)))
);
```

Register it in `layout.php`:

```html
<script>
  if ('serviceWorker' in navigator)
    navigator.serviceWorker.register('/sw.js');
</script>
```

### 1.4 HTTPS Requirement

iOS requires HTTPS for PWA installation. Options:
- Local dev: use `mkcert` to generate a local certificate
- Production: Caddy (auto-HTTPS) or Nginx + Let's Encrypt via Certbot
- Docker: add a Caddy reverse-proxy service in `docker-compose.yml`

### 1.5 Viewport & Safe Areas

Handle iPhone notch/dynamic island properly:

```html
<meta name="viewport" content="width=device-width, initial-scale=1,
      viewport-fit=cover">
```

```css
/* In style.css */
.header {
  padding-top: env(safe-area-inset-top);
}
.bottom-nav {
  padding-bottom: env(safe-area-inset-bottom);
}
```

---

## Phase 2 — Authentication

**Goal:** Secure the app with multi-user support and three sign-in methods.

### 2.1 Database Changes

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

### 2.2 Session Management

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

### 2.3 Email / Password Authentication

- Hash passwords with `password_hash($pass, PASSWORD_ARGON2ID)`
- Verify with `password_verify()`
- Add a CSRF token to every form (one hidden `<input name="_token">`)
- Rate-limit login attempts (track in a `login_attempts` table)
- Add a "Forgot password" flow with a time-limited token sent by email

Recommended library: `PHPMailer/PHPMailer` for transactional email.

### 2.4 Google OAuth 2.0

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

### 2.5 Apple Sign In

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

### 2.6 Login UI

Create `views/pages/login.php` and `views/pages/register.php` with:
- Clean centered card layout
- Email/password fields
- "Continue with Google" button (Google brand colors)
- "Sign in with Apple" button (black, Apple's brand guidelines)
- Link between login ↔ register

---

## Phase 3 — UX & UI Modernization

**Goal:** Bring the visual design up to 2026 standards.

### 3.1 Honest Assessment of the Current UI

The current design has a solid foundation but several issues typical of 2022–2023 UI:

| Issue | Impact |
|-------|--------|
| Heavy purple gradients everywhere | Feels dated; gradients should be subtle accents, not backgrounds |
| Emoji as navigation icons | Inconsistent rendering across OS, low fidelity on OLED screens |
| Single-column sidebar layout | Wastes space on desktop, unusable on small phones |
| Full-page POST reloads | Every action causes a jarring white flash |
| No dark mode | Uncomfortable at night; expected by most users in 2026 |
| No micro-animations | Feels static and unresponsive to touch |
| No loading/skeleton states | Data appears abruptly with no feedback |
| Form modals are bare | Long scrollable modals with no visual grouping |
| No data visualization | Weight table is hard to read vs. a chart |
| Typography is generic | System font stack with no personality |

### 3.2 New Design Direction: "Soft Pet App" (2026)

Replace the heavy gradients with a calm, airy design language:

**Color Palette**

```css
:root {
  /* Primary */
  --color-lavender:    #6c5ce7;
  --color-lavender-10: #f0eeff;
  --color-lavender-20: #dcd8ff;

  /* Neutrals */
  --color-base:    #fafaf9;
  --color-surface: #ffffff;
  --color-border:  #e8e6f0;
  --color-text:    #1a1625;
  --color-muted:   #7c7a8e;

  /* Semantics */
  --color-success: #00b894;
  --color-warning: #fdcb6e;
  --color-danger:  #d63031;
  --color-info:    #0984e3;

  /* Dark mode (prefers-color-scheme: dark) */
  --color-base-dark:    #13111c;
  --color-surface-dark: #1e1b2e;
  --color-border-dark:  #2d2a3e;
  --color-text-dark:    #f0eeff;
}
```

**Typography**

Switch from pure system fonts to a font pairing:

```html
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap"
      rel="stylesheet">
```

```css
body {
  font-family: 'Plus Jakarta Sans', system-ui, sans-serif;
  font-size: 15px;
  line-height: 1.6;
  letter-spacing: -0.01em;
}
h1, h2, h3 { letter-spacing: -0.03em; font-weight: 700; }
```

### 3.3 Layout Changes

**Desktop** (≥ 1024px)
- Collapsible sidebar, 240px wide, icon + label in collapsed state
- Content area with max-width 1100px, centered
- Top navigation bar is removed in favor of sidebar-only navigation
- Stats displayed as a 4-column grid of compact "glass cards"

**Mobile** (< 768px, iOS PWA)
- **Bottom tab bar** instead of sidebar (matches iOS app conventions)
- 5 tabs: Dashboard · Santé · Poids · Photos · Profil
- Full-screen modals slide up from the bottom (iOS sheet pattern)
- Swipe-left on cards to reveal Delete/Edit actions
- Pull-to-refresh gesture on list views

```
┌──────────────────────────────┐
│  🐾 Luna              ···   │  ← Header: cat name + menu
├──────────────────────────────┤
│                              │
│  Today's reminders   ────   │
│  ┌──────────────────────┐   │
│  │ 💉 Vaccin rage        │   │
│  │ Dans 3 jours          │   │
│  └──────────────────────┘   │
│                              │
│  Dernière visite ──────────  │
│  Poids       5.2 kg  (+0.3) │
│                              │
├──────────────────────────────┤
│ 🏠  ❤️  ⚖️  📸  👤        │  ← Bottom tab bar
└──────────────────────────────┘
```

**Icons**
Replace emoji with a proper SVG icon library. Options:
- `Lucide` (MIT, SVG sprites, tree-shakeable) — recommended
- `Phosphor Icons` (more styles, pet-friendly variants)

### 3.4 Micro-interactions & Animation

```css
/* Base transition system */
:root {
  --ease-spring: cubic-bezier(0.34, 1.56, 0.64, 1);
  --ease-out:    cubic-bezier(0.16, 1, 0.3, 1);
  --duration-fast: 120ms;
  --duration-base: 220ms;
}

/* Card hover */
.card:hover {
  transform: translateY(-2px);
  box-shadow: 0 8px 24px rgba(108, 92, 231, 0.12);
  transition: transform var(--duration-base) var(--ease-spring),
              box-shadow var(--duration-base) var(--ease-out);
}

/* Modal sheet */
.modal-sheet {
  animation: slide-up var(--duration-base) var(--ease-spring);
}

@keyframes slide-up {
  from { transform: translateY(100%); opacity: 0; }
  to   { transform: translateY(0);    opacity: 1; }
}

/* Button press */
button:active {
  transform: scale(0.97);
  transition: transform 80ms ease;
}
```

### 3.5 Skeleton Loading States

Replace the blank states during page loads with skeleton cards:

```html
<!-- views/partials/skeleton_card.php -->
<div class="card skeleton">
  <div class="skeleton-line w-60"></div>
  <div class="skeleton-line w-40 mt-2"></div>
</div>
```

```css
.skeleton-line {
  height: 12px;
  border-radius: 6px;
  background: linear-gradient(90deg, var(--color-border) 25%,
              var(--color-surface) 50%, var(--color-border) 75%);
  background-size: 200% 100%;
  animation: shimmer 1.4s infinite;
}
@keyframes shimmer { to { background-position: -200% 0; } }
```

### 3.6 Toast Notifications

Replace full-page reloads with inline feedback using a small toast system
(vanilla JS, no library needed):

```js
// public/toast.js
function toast(message, type = 'success') {
  const el = Object.assign(document.createElement('div'), {
    className: `toast toast--${type}`,
    textContent: message
  });
  document.body.appendChild(el);
  requestAnimationFrame(() => el.classList.add('toast--visible'));
  setTimeout(() => el.remove(), 3000);
}
```

### 3.7 Weight Chart

Replace the weight table with a sparkline chart using Chart.js (45 kB gzip):

```html
<canvas id="weight-chart" height="120"></canvas>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4/dist/chart.umd.min.js"></script>
<script>
  new Chart(document.getElementById('weight-chart'), {
    type: 'line',
    data: {
      labels: <?= json_encode($weightLabels) ?>,
      datasets: [{
        data: <?= json_encode($weightData) ?>,
        borderColor: '#6c5ce7',
        tension: 0.4,
        pointRadius: 4,
        fill: true,
        backgroundColor: 'rgba(108, 92, 231, 0.08)'
      }]
    },
    options: { plugins: { legend: { display: false } }, scales: { y: { beginAtZero: false } } }
  });
</script>
```

### 3.8 Photo Gallery Upgrade

Replace the simple grid with a masonry/lightbox layout:
- CSS `columns` for masonry (no JS needed for layout)
- Tap a photo to open a full-screen overlay with swipe navigation
- Pinch-to-zoom on iOS

### 3.9 Dark Mode

```css
@media (prefers-color-scheme: dark) {
  :root {
    --color-base:    var(--color-base-dark);
    --color-surface: var(--color-surface-dark);
    --color-border:  var(--color-border-dark);
    --color-text:    var(--color-text-dark);
  }
}
```

Also expose a manual toggle stored in `localStorage`:

```js
document.documentElement.dataset.theme =
  localStorage.getItem('theme') ?? 'system';
```

### 3.10 Empty States with Illustrations

Replace plain "Aucun enregistrement" text with friendly illustrations:
- Use `undraw.co` SVG illustrations (free, customizable primary color)
- Show a relevant CTA button in each empty state
- Examples: "Ajouter le premier vaccin", "Enregistrer le premier poids"

---

## Phase 4 — Feature Improvements

### 4.1 Async Form Submissions (No Page Reloads)

Convert all modal forms to use `fetch()`:

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

Update `router.php` to return JSON when the `Accept: application/json` header
is present (progressive enhancement — still works without JS).

### 4.2 Health Record Export (PDF)

Use `dompdf/dompdf` (PHP library, no external service needed) to generate a
printable carnet de santé:
- Cat profile, photo, microchip
- All vaccinations and next booster dates
- Treatment history
- Vet contact info
- QR code linking to the app

### 4.3 Email Reminders

Use a cron job + PHPMailer to send reminder emails:

```
# crontab
0 8 * * * php /var/www/html/cron/send_reminders.php
```

The script queries for reminders due today or tomorrow and sends a digest email
to the cat owner.

### 4.4 REST API

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

### 4.5 Multi-Cat Quick Switcher

On iOS, implement a swipeable card carousel at the top of the dashboard so users
with multiple cats can switch without using the dropdown:

```
← 🐱 Luna   |   Milo 🐱 →
```

### 4.6 Shareable Health QR Code

Generate a public, read-only URL per cat (with a random token):
`/share/{random_token}` — shows vaccinations and vet info to a vet or
pet sitter without requiring login.

---

## Phase 5 — Infrastructure & Developer Experience

### 5.1 Introduce Composer

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

### 5.2 Docker Improvements

Update `docker-compose.yml` to add:
- **Caddy** reverse proxy (automatic HTTPS, required for PWA/OAuth)
- **Named volumes** for uploads and database
- **Healthcheck** on the PHP container

```yaml
services:
  caddy:
    image: caddy:2-alpine
    ports: ["80:80", "443:443"]
    volumes:
      - ./Caddyfile:/etc/caddy/Caddyfile
      - caddy_data:/data

  app:
    build: .
    volumes:
      - uploads:/var/www/html/uploads
      - db:/var/www/html/database
    healthcheck:
      test: ["CMD", "curl", "-f", "http://localhost/health"]
      interval: 30s

volumes:
  caddy_data:
  uploads:
  db:
```

### 5.3 Security Hardening

- [ ] Add CSRF token middleware for all POST forms
- [ ] Set `SameSite=Lax; Secure; HttpOnly` on session cookie
- [ ] Move database out of webroot
- [ ] Add Content-Security-Policy header
- [ ] Validate and sanitize file upload MIME type (not just extension)
- [ ] Add rate limiting on auth endpoints (IP-based, tracked in SQLite)
- [ ] Log auth events (logins, failures, password resets)

### 5.4 Testing

Introduce a basic test suite with PHPUnit:
- Unit tests for `database.php` functions
- Integration tests for auth flows
- End-to-end smoke test with a headless browser (Playwright)

---

## Suggested Implementation Order

| # | Phase | Effort | Value |
|---|-------|--------|-------|
| 1 | PWA + iOS meta tags | Low | High — immediate install on iOS |
| 2 | Dark mode + bottom nav on mobile | Low | High — biggest visible win |
| 3 | Email authentication | Medium | High — multi-user support |
| 4 | Google OAuth | Medium | High — removes password friction |
| 5 | Apple Sign In | Medium | Required for iOS audience |
| 6 | UI redesign (colors, typography, icons) | Medium | High — 2026 look & feel |
| 7 | Toast notifications + async forms | Medium | High — removes jarring reloads |
| 8 | Weight chart | Low | Medium — data visualization |
| 9 | PDF export | Low | Medium — practical utility |
| 10 | Email reminders | Medium | Medium — engagement driver |
| 11 | REST API | High | Medium — future-proofing |
| 12 | Security hardening | Medium | Critical — before going public |

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
