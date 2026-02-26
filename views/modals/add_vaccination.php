<div id="addVaccinationModal" class="modal">
    <div class="modal-content">
        <span class="modal-handle"></span>
        <div class="modal-header">
            <h2 class="modal-title">Ajouter une vaccination</h2>
            <button class="modal-close" onclick="hideModal('addVaccinationModal')">&times;</button>
        </div>
        <form method="POST">
            <input type="hidden" name="action" value="add_vaccination">
            <input type="hidden" name="cat_id" value="<?= htmlspecialchars($selectedCatId ?? '') ?>">
            <?= csrfInput() ?>

            <div class="form-group">
                <label class="form-label">Type de vaccin *</label>
                <select name="vaccine_type" class="form-select" required>
                    <option value="">Sélectionner...</option>
                    <option value="Rage">Rage</option>
                    <option value="Typhus">Typhus</option>
                    <option value="Coryza">Coryza</option>
                    <option value="Leucose">Leucose</option>
                    <option value="Chlamydiose">Chlamydiose</option>
                </select>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Date *</label>
                    <input type="date" name="date" class="form-input" required value="<?= date('Y-m-d') ?>">
                </div>
                <div class="form-group">
                    <label class="form-label">Prochain rappel</label>
                    <input type="date" name="next_date" class="form-input">
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Vétérinaire</label>
                <input type="text" name="vet_name" class="form-input">
            </div>

            <div class="form-group">
                <label class="form-label">Notes</label>
                <textarea name="notes" class="form-textarea"></textarea>
            </div>

            <div class="form-actions">
                <button type="button" class="btn" onclick="hideModal('addVaccinationModal')">Annuler</button>
                <button type="submit" class="btn btn-success">Enregistrer</button>
            </div>
        </form>
    </div>
</div>
