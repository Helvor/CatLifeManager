<div class="card">
    <div class="card-header">
        <h2 class="card-title">💉 Vaccinations</h2>
        <button class="btn btn-success" onclick="showModal('addVaccinationModal')">+ Ajouter</button>
    </div>
    <?php if (empty($vaccinations)): ?>
        <p style="text-align: center; color: #9ca3af; padding: 40px;">Aucune vaccination enregistrée</p>
    <?php else: ?>
        <?php foreach ($vaccinations as $vacc): ?>
            <div class="item green">
                <div class="item-title"><?= htmlspecialchars($vacc['vaccine_type']) ?></div>
                <div class="item-text">Date: <?= date('d/m/Y', strtotime($vacc['date'])) ?></div>
                <?php if ($vacc['next_date']): ?>
                    <div class="item-text">Rappel: <?= date('d/m/Y', strtotime($vacc['next_date'])) ?></div>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<div class="card">
    <div class="card-header">
        <h2 class="card-title">💊 Traitements</h2>
        <button class="btn btn-info" onclick="showModal('addTreatmentModal')">+ Ajouter</button>
    </div>
    <?php if (empty($treatments)): ?>
        <p style="text-align: center; color: #9ca3af; padding: 40px;">Aucun traitement enregistré</p>
    <?php else: ?>
        <?php foreach ($treatments as $treatment): ?>
            <div class="item blue">
                <div class="item-title"><?= htmlspecialchars($treatment['treatment_type']) ?></div>
                <?php if ($treatment['product_name']): ?>
                    <div class="item-text"><?= htmlspecialchars($treatment['product_name']) ?></div>
                <?php endif; ?>
                <div class="item-text">Dernier: <?= date('d/m/Y', strtotime($treatment['date'])) ?></div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>
