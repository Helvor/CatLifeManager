<header class="header">
    <div class="container">
        <div class="header-content">
            <div class="logo">
                <div class="logo-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M20.42 4.58a5.4 5.4 0 0 0-7.65 0l-.77.78-.77-.78a5.4 5.4 0 0 0-7.65 7.65l8.42 8.42 8.42-8.42a5.4 5.4 0 0 0 0-7.65z"/>
                    </svg>
                </div>
                <span class="logo-text">CatLife</span>
            </div>
            <div class="header-actions">
                <button id="theme-toggle-btn" class="theme-toggle" onclick="toggleTheme()" title="Basculer le thème" aria-label="Basculer le mode sombre/clair"></button>
                <?php if (isset($currentUser)): ?>
                    <span class="header-user">
                        <?= htmlspecialchars($currentUser['name'], ENT_QUOTES, 'UTF-8') ?>
                    </span>
                    <form method="POST" action="/logout.php" style="display:inline;margin:0;">
                        <?= csrfInput() ?>
                        <button type="submit" class="btn btn-sm" title="Se déconnecter">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
                        </button>
                    </form>
                <?php endif; ?>
                <button class="btn btn-primary btn-sm" onclick="showModal('addCatModal')">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
                    </svg>
                    Nouveau chat
                </button>
            </div>
        </div>
    </div>
</header>
