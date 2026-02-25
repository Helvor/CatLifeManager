<?php if ($selectedCat): ?>
    <div class="cat-banner">
        <div class="cat-banner-inner">
            <div>
                <h1><?= htmlspecialchars($selectedCat['name']) ?></h1>
                <div class="cat-banner-meta">
                    <?php if ($selectedCat['breed']): ?>
                        <span>
                            <svg xmlns="http://www.w3.org/2000/svg" width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/></svg>
                            <?= htmlspecialchars($selectedCat['breed']) ?>
                        </span>
                    <?php endif; ?>
                    <?php if ($selectedCat['birth_date']): ?>
                        <span>
                            <svg xmlns="http://www.w3.org/2000/svg" width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                            <?= calculateAge($selectedCat['birth_date']) ?> ans
                        </span>
                    <?php endif; ?>
                    <span>
                        <svg xmlns="http://www.w3.org/2000/svg" width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                        <?= htmlspecialchars($selectedCat['gender']) ?>
                    </span>
                    <?php if ($selectedCat['is_neutered']): ?>
                        <span>Stérilisé(e)</span>
                    <?php endif; ?>
                </div>
            </div>
            <div class="cat-banner-actions">
                <button onclick="showModal('editCatModal')" class="btn btn-ghost btn-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                        <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                    </svg>
                    Modifier
                </button>
                <button onclick="if(confirm('Supprimer ce chat définitivement ?')) { document.getElementById('deleteCatForm').submit(); }" class="btn btn-outline btn-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/>
                        <path d="M10 11v6"/><path d="M14 11v6"/>
                        <path d="M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/>
                    </svg>
                    Supprimer
                </button>
            </div>
        </div>
    </div>

    <form id="deleteCatForm" method="POST" style="display: none;">
        <input type="hidden" name="action" value="delete_cat">
        <input type="hidden" name="cat_id" value="<?= htmlspecialchars($selectedCatId ?? '') ?>">
    </form>

    <div class="stats-grid">
        <div class="stat-card blue">
            <div class="stat-card-icon">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/>
                    <line x1="6" y1="20" x2="6" y2="14"/>
                </svg>
            </div>
            <div class="stat-label">Poids actuel</div>
            <div class="stat-value"><?= !empty($weightRecords) ? end($weightRecords)['weight'] . ' kg' : '—' ?></div>
        </div>
        <div class="stat-card green">
            <div class="stat-card-icon">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                    <polyline points="22 4 12 14.01 9 11.01"/>
                </svg>
            </div>
            <div class="stat-label">Vaccinations</div>
            <div class="stat-value"><?= count($vaccinations) ?></div>
        </div>
        <div class="stat-card orange">
            <div class="stat-card-icon">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="3" width="18" height="18" rx="2" ry="2"/>
                    <circle cx="8.5" cy="8.5" r="1.5"/>
                    <polyline points="21 15 16 10 5 21"/>
                </svg>
            </div>
            <div class="stat-label">Photos</div>
            <div class="stat-value"><?= count($photos) ?></div>
        </div>
        <div class="stat-card red">
            <div class="stat-card-icon">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/>
                    <path d="M13.73 21a2 2 0 0 1-3.46 0"/>
                </svg>
            </div>
            <div class="stat-label">Rappels</div>
            <div class="stat-value"><?= count($reminders) ?></div>
        </div>
    </div>

    <?php if (!empty($reminders)): ?>
        <div class="card">
            <div class="card-header">
                <h2 class="card-title">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/>
                        <path d="M13.73 21a2 2 0 0 1-3.46 0"/>
                    </svg>
                    Rappels à venir
                </h2>
            </div>
            <?php foreach ($reminders as $reminder): ?>
                <div class="item red">
                    <div class="item-dot"></div>
                    <div class="item-body">
                        <div class="item-title"><?= htmlspecialchars($reminder['title']) ?></div>
                        <div class="item-text"><?= date('d/m/Y', strtotime($reminder['reminder_date'])) ?></div>
                    </div>
                    <form method="POST" style="display: inline; flex-shrink: 0;">
                        <input type="hidden" name="action" value="complete_reminder">
                        <input type="hidden" name="reminder_id" value="<?= $reminder['id'] ?>">
                        <input type="hidden" name="cat_id" value="<?= htmlspecialchars($selectedCatId ?? '') ?>">
                        <button type="submit" class="btn btn-sm btn-success">Fait</button>
                    </form>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <?php if (empty($reminders) && empty($vaccinations) && empty($weightRecords)): ?>
        <div class="card">
            <div class="empty-inline">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/>
                    <line x1="12" y1="16" x2="12.01" y2="16"/>
                </svg>
                <span>Aucune donnée pour le moment. Commencez par ajouter une vaccination ou un poids.</span>
            </div>
        </div>
    <?php endif; ?>
<?php endif; ?>
