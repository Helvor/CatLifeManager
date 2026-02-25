<div class="card">
    <div class="card-header">
        <h2 class="card-title">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">
                <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/>
            </svg>
            Vaccinations
        </h2>
        <button class="btn btn-success btn-sm" onclick="showModal('addVaccinationModal')">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">
                <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
            </svg>
            Ajouter
        </button>
    </div>
    <?php if (empty($vaccinations)): ?>
        <div class="empty-inline">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">
                <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/>
            </svg>
            <span>Aucune vaccination enregistrée</span>
            <button class="btn btn-ghost btn-sm" onclick="showModal('addVaccinationModal')">Ajouter le premier vaccin</button>
        </div>
    <?php else: ?>
        <?php foreach ($vaccinations as $vacc): ?>
            <div class="item green">
                <div class="item-dot"></div>
                <div class="item-body">
                    <div class="item-title"><?= htmlspecialchars($vacc['vaccine_type']) ?></div>
                    <div class="item-text">
                        Administré le <?= date('d/m/Y', strtotime($vacc['date'])) ?>
                        <?php if ($vacc['next_date']): ?>
                            · Rappel : <?= date('d/m/Y', strtotime($vacc['next_date'])) ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<div class="card">
    <div class="card-header">
        <h2 class="card-title">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">
                <path d="M19 14c1.49-1.46 3-3.21 3-5.5A5.5 5.5 0 0 0 16.5 3c-1.76 0-3 .5-4.5 2-1.5-1.5-2.74-2-4.5-2A5.5 5.5 0 0 0 2 8.5c0 2.3 1.5 4.05 3 5.5l7 7Z"/>
            </svg>
            Traitements
        </h2>
        <button class="btn btn-info btn-sm" onclick="showModal('addTreatmentModal')">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">
                <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
            </svg>
            Ajouter
        </button>
    </div>
    <?php if (empty($treatments)): ?>
        <div class="empty-inline">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">
                <path d="M19 14c1.49-1.46 3-3.21 3-5.5A5.5 5.5 0 0 0 16.5 3c-1.76 0-3 .5-4.5 2-1.5-1.5-2.74-2-4.5-2A5.5 5.5 0 0 0 2 8.5c0 2.3 1.5 4.05 3 5.5l7 7Z"/>
            </svg>
            <span>Aucun traitement enregistré</span>
            <button class="btn btn-ghost btn-sm" onclick="showModal('addTreatmentModal')">Ajouter le premier traitement</button>
        </div>
    <?php else: ?>
        <?php foreach ($treatments as $treatment): ?>
            <div class="item blue">
                <div class="item-dot"></div>
                <div class="item-body">
                    <div class="item-title"><?= htmlspecialchars($treatment['treatment_type']) ?></div>
                    <div class="item-text">
                        <?php if ($treatment['product_name']): ?>
                            <?= htmlspecialchars($treatment['product_name']) ?> ·
                        <?php endif; ?>
                        Dernier : <?= date('d/m/Y', strtotime($treatment['date'])) ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>
