<div id="addCatModal" class="modal">
    <div class="modal-content">
        <span class="modal-handle"></span>
        <div class="modal-header">
            <h2 class="modal-title">Ajouter un chat</h2>
            <button class="modal-close" onclick="hideModal('addCatModal')">&times;</button>
        </div>
        <form method="POST" data-async>
            <input type="hidden" name="action" value="add_cat">
            <?= csrfInput() ?>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Nom *</label>
                    <input type="text" name="name" class="form-input" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Race</label>
                    <input type="text" name="breed" class="form-input">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Date de naissance</label>
                    <input type="date" name="birth_date" class="form-input">
                </div>
                <div class="form-group">
                    <label class="form-label">Sexe</label>
                    <select name="gender" class="form-select">
                        <option value="Mâle">Mâle</option>
                        <option value="Femelle">Femelle</option>
                    </select>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Couleur</label>
                    <input type="text" name="color" class="form-input">
                </div>
                <div class="form-group">
                    <label class="form-label">N° puce</label>
                    <input type="text" name="microchip_number" class="form-input">
                </div>
            </div>

            <div class="form-group">
                <label class="form-checkbox">
                    <input type="checkbox" name="is_neutered" value="1">
                    <span>Stérilisé(e)</span>
                </label>
            </div>

            <div class="modal-section-title">Vétérinaire</div>

            <div class="form-group">
                <label class="form-label">Clinique</label>
                <input type="text" name="vet_clinic" class="form-input">
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Téléphone</label>
                    <input type="tel" name="vet_phone" class="form-input">
                </div>
                <div class="form-group">
                    <label class="form-label">Email</label>
                    <input type="email" name="vet_email" class="form-input">
                </div>
            </div>

            <div class="form-actions">
                <button type="button" class="btn" onclick="hideModal('addCatModal')">Annuler</button>
                <button type="submit" class="btn btn-primary">Enregistrer</button>
            </div>
        </form>
    </div>
</div>
