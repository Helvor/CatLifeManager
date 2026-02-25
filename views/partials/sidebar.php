<aside class="sidebar">
    <div class="sidebar-section">
        <div class="cat-selector">
            <span class="cat-selector-label">Chat sélectionné</span>
            <select onchange="window.location.href='?cat=' + this.value + '&tab=<?= htmlspecialchars($activeTab) ?>'" class="form-select">
                <?php foreach ($cats as $cat): ?>
                    <option value="<?= $cat['id'] ?>" <?= $cat['id'] == $selectedCatId ? 'selected' : '' ?>>
                        <?= htmlspecialchars($cat['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>

    <div class="sidebar-section">
        <nav class="nav">
            <a href="?cat=<?= htmlspecialchars($selectedCatId ?? '') ?>&tab=dashboard"
               class="nav-item <?= $activeTab === 'dashboard' ? 'active' : '' ?>">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/>
                    <rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/>
                </svg>
                Tableau de bord
            </a>
            <a href="?cat=<?= htmlspecialchars($selectedCatId ?? '') ?>&tab=health"
               class="nav-item <?= $activeTab === 'health' ? 'active' : '' ?>">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/>
                </svg>
                Santé
            </a>
            <a href="?cat=<?= htmlspecialchars($selectedCatId ?? '') ?>&tab=weight"
               class="nav-item <?= $activeTab === 'weight' ? 'active' : '' ?>">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/>
                    <line x1="6" y1="20" x2="6" y2="14"/>
                </svg>
                Poids
            </a>
            <a href="?cat=<?= htmlspecialchars($selectedCatId ?? '') ?>&tab=photos"
               class="nav-item <?= $activeTab === 'photos' ? 'active' : '' ?>">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="3" width="18" height="18" rx="2" ry="2"/>
                    <circle cx="8.5" cy="8.5" r="1.5"/>
                    <polyline points="21 15 16 10 5 21"/>
                </svg>
                Photos
            </a>
        </nav>
    </div>

    <div class="sidebar-section">
        <button class="sidebar-add-btn" onclick="showModal('addCatModal')">
            <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
            </svg>
            Nouveau chat
        </button>
    </div>
</aside>
