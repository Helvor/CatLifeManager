<div id="addPhotoModal" class="modal">
    <div class="modal-content">
        <span class="modal-handle"></span>
        <div class="modal-header">
            <h2 class="modal-title">Ajouter une photo</h2>
            <button class="modal-close" onclick="hideModal('addPhotoModal')">&times;</button>
        </div>
        <form method="POST" enctype="multipart/form-data" data-async>
            <input type="hidden" name="action" value="add_photo">
            <input type="hidden" name="cat_id" value="<?= htmlspecialchars($selectedCatId ?? '') ?>">
            <?= csrfInput() ?>

            <div class="form-group">
                <label class="form-label">Photo *</label>
                <input type="file" name="photo" class="form-input" accept="image/*" required>
            </div>

            <div class="form-group">
                <label class="form-label">Titre</label>
                <input type="text" name="title" class="form-input">
            </div>

            <div class="form-group">
                <label class="form-label">Tags (séparés par des virgules)</label>
                <input type="text" name="tags" class="form-input" placeholder="Joyeux, Mignon, Sommeil">
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Date *</label>
                    <input type="date" name="date" class="form-input" required value="<?= date('Y-m-d') ?>">
                </div>
                <div class="form-group">
                    <label class="form-label">Lieu</label>
                    <input type="text" name="location" class="form-input">
                </div>
            </div>

            <div class="form-actions">
                <button type="button" class="btn" onclick="hideModal('addPhotoModal')">Annuler</button>
                <button type="submit" class="btn btn-warning">Ajouter</button>
            </div>
        </form>
    </div>
</div>
