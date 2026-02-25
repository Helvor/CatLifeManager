<div class="card">
    <div class="card-header">
        <h2 class="card-title">📸 Galerie photos</h2>
        <button class="btn btn-warning" onclick="showModal('addPhotoModal')">+ Ajouter</button>
    </div>
    <?php if (!empty($_GET['upload_error'])): ?>
        <div style="background:#fee2e2;color:#b91c1c;border:1px solid #fca5a5;border-radius:8px;padding:12px 16px;margin:12px;">
            Échec de l'upload : <?= htmlspecialchars($_GET['upload_error']) ?>
        </div>
    <?php endif; ?>
    <?php if (empty($photos)): ?>
        <p style="text-align: center; color: #9ca3af; padding: 40px;">Aucune photo pour le moment</p>
    <?php else: ?>
        <div class="photo-grid">
            <?php foreach ($photos as $photo): ?>
                <div class="photo-item" style="position: relative;">
                    <img src="uploads/<?= htmlspecialchars($photo['filename']) ?>" alt="<?= htmlspecialchars($photo['title']) ?>">
                    <form method="POST" style="position: absolute; top: 6px; right: 6px;">
                        <input type="hidden" name="action" value="delete_photo">
                        <input type="hidden" name="photo_id" value="<?= $photo['id'] ?>">
                        <input type="hidden" name="cat_id" value="<?= htmlspecialchars($selectedCatId ?? '') ?>">
                        <button type="submit" class="btn btn-danger btn-sm"
                                onclick="return confirm('Supprimer cette photo ?')">🗑</button>
                    </form>
                    <div class="photo-info">
                        <?php if ($photo['title']): ?>
                            <div class="photo-title"><?= htmlspecialchars($photo['title']) ?></div>
                        <?php endif; ?>
                        <?php if ($photo['tags']): ?>
                            <div class="photo-tags">
                                <?php foreach (explode(',', $photo['tags']) as $tag): ?>
                                    <span class="tag"><?= htmlspecialchars(trim($tag)) ?></span>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                        <div class="photo-date"><?= date('d/m/Y', strtotime($photo['date'])) ?></div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
