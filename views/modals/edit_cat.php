<div id="editCatModal" class="modal">
    <div class="modal-content">
        <span class="modal-handle"></span>
        <div class="modal-header">
            <h2 class="modal-title">Modifier <?= htmlspecialchars($selectedCat['name'] ?? '') ?></h2>
            <button class="modal-close" onclick="hideModal('editCatModal')">&times;</button>
        </div>
        <form method="POST">
            <input type="hidden" name="action" value="update_cat">
            <input type="hidden" name="cat_id" value="<?= htmlspecialchars($selectedCatId ?? '') ?>">

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Nom *</label>
                    <input type="text" name="name" class="form-input" value="<?= htmlspecialchars($selectedCat['name'] ?? '') ?>" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Race</label>
                    <input type="text" name="breed" class="form-input" value="<?= htmlspecialchars($selectedCat['breed'] ?? '') ?>">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Date de naissance</label>
                    <input type="date" name="birth_date" class="form-input" value="<?= htmlspecialchars($selectedCat['birth_date'] ?? '') ?>">
                </div>
                <div class="form-group">
                    <label class="form-label">Sexe</label>
                    <select name="gender" class="form-select">
                        <option value="Mâle" <?= ($selectedCat['gender'] ?? '') === 'Mâle' ? 'selected' : '' ?>>Mâle</option>
                        <option value="Femelle" <?= ($selectedCat['gender'] ?? '') === 'Femelle' ? 'selected' : '' ?>>Femelle</option>
                    </select>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Couleur</label>
                    <input type="text" name="color" class="form-input" value="<?= htmlspecialchars($selectedCat['color'] ?? '') ?>">
                </div>
                <div class="form-group">
                    <label class="form-label">N° puce</label>
                    <input type="text" name="microchip_number" class="form-input" value="<?= htmlspecialchars($selectedCat['microchip_number'] ?? '') ?>">
                </div>
            </div>

            <div class="form-group">
                <label class="form-checkbox">
                    <input type="checkbox" name="is_neutered" value="1" <?= ($selectedCat['is_neutered'] ?? 0) ? 'checked' : '' ?>>
                    <span>Stérilisé(e)</span>
                </label>
            </div>

            <h3 style="margin: 24px 0 16px;">Vétérinaire</h3>

            <div class="form-group">
                <label class="form-label">Clinique</label>
                <input type="text" name="vet_clinic" class="form-input" value="<?= htmlspecialchars($selectedCat['vet_clinic'] ?? '') ?>">
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Téléphone</label>
                    <input type="tel" name="vet_phone" class="form-input" value="<?= htmlspecialchars($selectedCat['vet_phone'] ?? '') ?>">
                </div>
                <div class="form-group">
                    <label class="form-label">Email</label>
                    <input type="email" name="vet_email" class="form-input" value="<?= htmlspecialchars($selectedCat['vet_email'] ?? '') ?>">
                </div>
            </div>

            <div class="form-actions">
                <button type="button" class="btn" onclick="hideModal('editCatModal')">Annuler</button>
                <button type="submit" class="btn btn-primary">Enregistrer</button>
            </div>
        </form>
    </div>
</div>
