<aside class="sidebar">
    <div class="cat-selector">
        <label>Chat sélectionné</label>
        <select onchange="window.location.href='?cat=' + this.value" class="form-select">
            <?php foreach ($cats as $cat): ?>
                <option value="<?= $cat['id'] ?>" <?= $cat['id'] == $selectedCatId ? 'selected' : '' ?>>
                    <?= htmlspecialchars($cat['name']) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <nav class="nav">
        <a href="?cat=<?= htmlspecialchars($selectedCatId ?? '') ?>&tab=dashboard"
           class="nav-item <?= $activeTab === 'dashboard' ? 'active' : '' ?>">
            <span>📊</span> Tableau de bord
        </a>
        <a href="?cat=<?= htmlspecialchars($selectedCatId ?? '') ?>&tab=health"
           class="nav-item <?= $activeTab === 'health' ? 'active' : '' ?>">
            <span>❤️</span> Santé
        </a>
        <a href="?cat=<?= htmlspecialchars($selectedCatId ?? '') ?>&tab=weight"
           class="nav-item <?= $activeTab === 'weight' ? 'active' : '' ?>">
            <span>⚖️</span> Poids
        </a>
        <a href="?cat=<?= htmlspecialchars($selectedCatId ?? '') ?>&tab=photos"
           class="nav-item <?= $activeTab === 'photos' ? 'active' : '' ?>">
            <span>📸</span> Photos
        </a>
    </nav>
</aside>
