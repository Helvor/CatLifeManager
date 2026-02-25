<div class="card">
    <div class="card-header">
        <h2 class="card-title">⚖️ Suivi du poids</h2>
        <button class="btn btn-info" onclick="showModal('addWeightModal')">+ Ajouter</button>
    </div>
    <?php if (empty($weightRecords)): ?>
        <p style="text-align: center; color: #9ca3af; padding: 40px;">Aucune pesée enregistrée</p>
    <?php else: ?>
        <?php foreach (array_reverse($weightRecords) as $record): ?>
            <div class="item blue" style="display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <div class="item-title"><?= $record['weight'] ?> kg</div>
                    <div class="item-text"><?= date('d/m/Y', strtotime($record['date'])) ?></div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>
