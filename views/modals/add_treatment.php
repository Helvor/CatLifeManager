<div id="addTreatmentModal" class="modal">
    <div class="modal-content">
        <span class="modal-handle"></span>
        <div class="modal-header">
            <h2 class="modal-title">Ajouter un traitement</h2>
            <button class="modal-close" onclick="hideModal('addTreatmentModal')">&times;</button>
        </div>
        <form method="POST">
            <input type="hidden" name="action" value="add_treatment">
            <input type="hidden" name="cat_id" value="<?= htmlspecialchars($selectedCatId ?? '') ?>">
            <?= csrfInput() ?>

            <div class="form-group">
                <label class="form-label">Type de traitement *</label>
                <select name="treatment_type" class="form-select" required>
                    <option value="">Sélectionner...</option>
                    <option value="Vermifuge">Vermifuge</option>
                    <option value="Antipuce">Antipuce</option>
                    <option value="Anti-tiques">Anti-tiques</option>
                    <option value="Antibiotique">Antibiotique</option>
                    <option value="Autre">Autre</option>
                </select>
            </div>

            <div class="form-group">
                <label class="form-label">Nom du produit</label>
                <input type="text" name="product_name" class="form-input">
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Date *</label>
                    <input type="date" name="date" class="form-input" required value="<?= date('Y-m-d') ?>">
                </div>
                <div class="form-group">
                    <label class="form-label">Prochain traitement</label>
                    <input type="date" name="next_date" class="form-input">
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Dosage</label>
                <input type="text" name="dosage" class="form-input">
            </div>

            <div class="form-group">
                <label class="form-label">Notes</label>
                <textarea name="notes" class="form-textarea"></textarea>
            </div>

            <div class="form-actions">
                <button type="button" class="btn" onclick="hideModal('addTreatmentModal')">Annuler</button>
                <button type="submit" class="btn btn-info">Enregistrer</button>
            </div>
        </form>
    </div>
</div>
