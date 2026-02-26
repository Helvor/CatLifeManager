<!DOCTYPE html>
<html lang="fr" data-theme="system">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>CatLife Manager</title>

    <!-- PWA manifest -->
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#6c5ce7">

    <!-- iOS PWA support -->
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="CatLife">

    <!-- iOS app icons -->
    <link rel="apple-touch-icon" href="/icons/apple-touch-icon.png">
    <link rel="apple-touch-icon" sizes="152x152" href="/icons/apple-touch-icon-152.png">
    <link rel="apple-touch-icon" sizes="120x120" href="/icons/apple-touch-icon-120.png">

    <!-- Google Fonts: Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="style.css">

    <!-- Apply saved theme before render to avoid flash -->
    <script>
        (function() {
            const saved = localStorage.getItem('theme');
            if (saved) document.documentElement.dataset.theme = saved;
        })();
    </script>
</head>
<body>
    <?php require __DIR__ . '/partials/header.php'; ?>

    <div class="container main-container">
        <?php if (empty($cats)): ?>
            <?php require __DIR__ . '/pages/empty.php'; ?>
        <?php else: ?>
            <div class="layout">
                <?php require __DIR__ . '/partials/sidebar.php'; ?>

                <main class="content">
                    <?php
                    if ($activeTab === 'dashboard') {
                        require __DIR__ . '/pages/dashboard.php';
                    } elseif ($activeTab === 'health') {
                        require __DIR__ . '/pages/health.php';
                    } elseif ($activeTab === 'weight') {
                        require __DIR__ . '/pages/weight.php';
                    } elseif ($activeTab === 'photos') {
                        require __DIR__ . '/pages/photos.php';
                    } else {
                        require __DIR__ . '/pages/dashboard.php';
                    }
                    ?>
                </main>
            </div>
        <?php endif; ?>
    </div>

    <?php if (!empty($cats)): ?>
        <?php require __DIR__ . '/partials/bottom_nav.php'; ?>
    <?php endif; ?>

    <!-- Modals -->
    <?php require __DIR__ . '/modals/add_cat.php'; ?>
    <?php require __DIR__ . '/modals/edit_cat.php'; ?>
    <?php require __DIR__ . '/modals/add_vaccination.php'; ?>
    <?php require __DIR__ . '/modals/add_treatment.php'; ?>
    <?php require __DIR__ . '/modals/add_weight.php'; ?>
    <?php require __DIR__ . '/modals/add_photo.php'; ?>

    <!-- Toast container -->
    <div id="toast-container"></div>

    <!-- Lightbox -->
    <div id="lightbox" class="lightbox" onclick="closeLightbox()">
        <button class="lightbox-close" onclick="closeLightbox()">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
        </button>
        <img id="lightbox-img" src="" alt="">
    </div>

    <script>
        /* --- Modal helpers --- */
        function showModal(id) {
            document.getElementById(id).classList.add('active');
            document.body.style.overflow = 'hidden';
        }
        function hideModal(id) {
            document.getElementById(id).classList.remove('active');
            document.body.style.overflow = '';
        }
        document.querySelectorAll('.modal').forEach(modal => {
            modal.addEventListener('click', function(e) {
                if (e.target === this) hideModal(this.id);
            });
        });
        document.addEventListener('keydown', e => {
            if (e.key === 'Escape') {
                document.querySelectorAll('.modal.active').forEach(m => hideModal(m.id));
                closeLightbox();
            }
        });

        /* --- Toast notifications --- */
        function toast(message, type = 'success') {
            const el = Object.assign(document.createElement('div'), {
                className: `toast toast--${type}`,
                textContent: message
            });
            document.getElementById('toast-container').appendChild(el);
            requestAnimationFrame(() => el.classList.add('toast--visible'));
            setTimeout(() => {
                el.classList.remove('toast--visible');
                el.addEventListener('transitionend', () => el.remove());
            }, 3000);
        }

        /* --- Lightbox --- */
        function openLightbox(src, alt) {
            const lb = document.getElementById('lightbox');
            const img = document.getElementById('lightbox-img');
            img.src = src;
            img.alt = alt || '';
            lb.classList.add('active');
            document.body.style.overflow = 'hidden';
        }
        function closeLightbox() {
            document.getElementById('lightbox').classList.remove('active');
            document.body.style.overflow = '';
        }

        /* --- Dark mode toggle --- */
        function toggleTheme() {
            const html = document.documentElement;
            const current = html.dataset.theme;
            const isDark = current === 'dark' || (current !== 'light' && window.matchMedia('(prefers-color-scheme: dark)').matches);
            const next = isDark ? 'light' : 'dark';
            html.dataset.theme = next;
            localStorage.setItem('theme', next);
            updateThemeIcon();
        }
        function updateThemeIcon() {
            const btn = document.getElementById('theme-toggle-btn');
            if (!btn) return;
            const html = document.documentElement;
            const current = html.dataset.theme;
            const isDark = current === 'dark' || (current !== 'light' && window.matchMedia('(prefers-color-scheme: dark)').matches);
            btn.innerHTML = isDark
                ? '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="5"/><line x1="12" y1="1" x2="12" y2="3"/><line x1="12" y1="21" x2="12" y2="23"/><line x1="4.22" y1="4.22" x2="5.64" y2="5.64"/><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"/><line x1="1" y1="12" x2="3" y2="12"/><line x1="21" y1="12" x2="23" y2="12"/><line x1="4.22" y1="19.78" x2="5.64" y2="18.36"/><line x1="18.36" y1="5.64" x2="19.78" y2="4.22"/></svg>'
                : '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/></svg>';
        }
        updateThemeIcon();

        /* --- Service Worker --- */
        if ('serviceWorker' in navigator) {
            navigator.serviceWorker.register('/sw.js');
        }
    </script>
    <script src="/async-forms.js"></script>
</body>
</html>
