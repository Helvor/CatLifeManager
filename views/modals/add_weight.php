<div id="addWeightModal" class="modal">
    <div class="modal-content">
        <span class="modal-handle"></span>
        <div class="modal-header">
            <h2 class="modal-title">Ajouter une pesée</h2>
            <button class="modal-close" onclick="hideModal('addWeightModal')">&times;</button>
        </div>
        <form method="POST">
            <input type="hidden" name="action" value="add_weight">
            <input type="hidden" name="cat_id" value="<?= htmlspecialchars($selectedCatId ?? '') ?>">

            <div class="form-group">
                <label class="form-label">Poids (kg) *</label>
                <input type="number" step="0.01" name="weight" class="form-input" required>
            </div>

            <div class="form-group">
                <label class="form-label">Date *</label>
                <input type="date" name="date" class="form-input" required value="<?= date('Y-m-d') ?>">
            </div>

            <div class="form-group">
                <label class="form-label">Notes</label>
                <textarea name="notes" class="form-textarea"></textarea>
            </div>

            <div class="form-actions">
                <button type="button" class="btn" onclick="hideModal('addWeightModal')">Annuler</button>
                <button type="submit" class="btn btn-info">Enregistrer</button>
            </div>
        </form>
    </div>
</div>
