<?php if ($selectedCat): ?>
    <div class="cat-banner">
        <div style="display: flex; justify-content: space-between; align-items: start;">
            <div>
                <h1><?= htmlspecialchars($selectedCat['name']) ?> 🐱</h1>
                <p>
                    <?= htmlspecialchars($selectedCat['breed'] ?: 'Race non spécifiée') ?> •
                    <?= calculateAge($selectedCat['birth_date']) ?> ans •
                    <?= htmlspecialchars($selectedCat['gender']) ?>
                </p>
            </div>
            <div style="display: flex; gap: 8px;">
                <button onclick="showModal('editCatModal')" class="btn btn-sm" style="background: rgba(255,255,255,0.2); color: white;">
                    ✏️ Modifier
                </button>
                <button onclick="if(confirm('Voulez-vous vraiment supprimer ce chat ?')) { document.getElementById('deleteCatForm').submit(); }" class="btn btn-sm btn-danger">
                    🗑️ Supprimer
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
            <div class="stat-label">Poids actuel</div>
            <div class="stat-value"><?= !empty($weightRecords) ? end($weightRecords)['weight'] : '0' ?> kg</div>
        </div>
        <div class="stat-card green">
            <div class="stat-label">Vaccinations</div>
            <div class="stat-value"><?= count($vaccinations) ?></div>
        </div>
        <div class="stat-card orange">
            <div class="stat-label">Photos</div>
            <div class="stat-value"><?= count($photos) ?></div>
        </div>
        <div class="stat-card red">
            <div class="stat-label">Rappels</div>
            <div class="stat-value"><?= count($reminders) ?></div>
        </div>
    </div>

    <?php if (!empty($reminders)): ?>
        <div class="card">
            <div class="card-header">
                <h2 class="card-title">🔔 Rappels à venir</h2>
            </div>
            <?php foreach ($reminders as $reminder): ?>
                <div class="item red">
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <div>
                            <div class="item-title"><?= htmlspecialchars($reminder['title']) ?></div>
                            <div class="item-text"><?= date('d/m/Y', strtotime($reminder['reminder_date'])) ?></div>
                        </div>
                        <form method="POST" style="display: inline;">
                            <input type="hidden" name="action" value="complete_reminder">
                            <input type="hidden" name="reminder_id" value="<?= $reminder['id'] ?>">
                            <input type="hidden" name="cat_id" value="<?= htmlspecialchars($selectedCatId ?? '') ?>">
                            <button type="submit" class="btn btn-sm btn-success">Fait</button>
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
<?php endif; ?>
