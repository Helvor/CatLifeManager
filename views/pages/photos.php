<div class="card">
    <div class="card-header">
        <h2 class="card-title">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">
                <rect x="3" y="3" width="18" height="18" rx="2" ry="2"/>
                <circle cx="8.5" cy="8.5" r="1.5"/>
                <polyline points="21 15 16 10 5 21"/>
            </svg>
            Galerie photos
        </h2>
        <button class="btn btn-warning btn-sm" onclick="showModal('addPhotoModal')">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">
                <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
            </svg>
            Ajouter
        </button>
    </div>

    <?php if (!empty($_GET['upload_error'])): ?>
        <div class="alert-error">
            Échec de l'upload : <?= htmlspecialchars($_GET['upload_error']) ?>
        </div>
    <?php endif; ?>

    <?php if (empty($photos)): ?>
        <div class="empty-inline">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">
                <rect x="3" y="3" width="18" height="18" rx="2" ry="2"/>
                <circle cx="8.5" cy="8.5" r="1.5"/>
                <polyline points="21 15 16 10 5 21"/>
            </svg>
            <span>Aucune photo pour le moment</span>
            <button class="btn btn-ghost btn-sm" onclick="showModal('addPhotoModal')">Ajouter la première photo</button>
        </div>
    <?php else: ?>
        <div class="photo-masonry">
            <?php foreach ($photos as $photo): ?>
                <div class="photo-item"
                     onclick="openLightbox('uploads/<?= htmlspecialchars($photo['filename']) ?>', '<?= htmlspecialchars($photo['title'] ?: '', ENT_QUOTES, 'UTF-8') ?>')">

                    <img src="uploads/<?= htmlspecialchars($photo['filename']) ?>"
                         alt="<?= htmlspecialchars($photo['title'] ?: '') ?>"
                         loading="lazy">

                    <div class="photo-overlay">
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

                    <form method="POST" onclick="event.stopPropagation();" data-async>
                        <input type="hidden" name="action" value="delete_photo">
                        <input type="hidden" name="photo_id" value="<?= $photo['id'] ?>">
                        <input type="hidden" name="cat_id" value="<?= htmlspecialchars($selectedCatId ?? '') ?>">
                        <?= csrfInput() ?>
                        <button type="submit" class="photo-delete-btn"
                                onclick="return confirm('Supprimer cette photo ?')">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="3 6 5 6 21 6"/>
                                <path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/>
                                <path d="M10 11v6"/><path d="M14 11v6"/>
                            </svg>
                        </button>
                    </form>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
