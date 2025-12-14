<?php
require_once 'database.php';

// Traitement des actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    switch ($action) {
        case 'add_cat':
            addCat($_POST);
            header('Location: index.php');
            exit;
            
        case 'update_cat':
            updateCat($_POST['cat_id'], $_POST);
            header('Location: index.php?cat=' . $_POST['cat_id']);
            exit;
            
        case 'delete_cat':
            deleteCat($_POST['cat_id']);
            header('Location: index.php');
            exit;
            
        case 'add_vaccination':
            addVaccination($_POST['cat_id'], $_POST);
            header('Location: index.php?cat=' . $_POST['cat_id'] . '&tab=health');
            exit;
            
        case 'add_treatment':
            addTreatment($_POST['cat_id'], $_POST);
            header('Location: index.php?cat=' . $_POST['cat_id'] . '&tab=health');
            exit;
            
        case 'add_weight':
            addWeight($_POST['cat_id'], $_POST);
            header('Location: index.php?cat=' . $_POST['cat_id'] . '&tab=weight');
            exit;
            
        case 'add_photo':
            if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
                $filename = time() . '_' . basename($_FILES['photo']['name']);
                $uploadPath = UPLOAD_DIR . $filename;
                
                if (move_uploaded_file($_FILES['photo']['tmp_name'], $uploadPath)) {
                    $_POST['filename'] = $filename;
                    addPhoto($_POST['cat_id'], $_POST);
                }
            }
            header('Location: index.php?cat=' . $_POST['cat_id'] . '&tab=photos');
            exit;
        
	case 'delete_photo':
            deletePhoto($_POST['photo_id']);
	    header('Location: index.php?cat=' , $_POST['cat_id'] . '&tab=photos');
	    exit;

        case 'add_reminder':
            addReminder($_POST['cat_id'], $_POST);
            header('Location: index.php?cat=' . $_POST['cat_id']);
            exit;
            
        case 'complete_reminder':
            completeReminder($_POST['reminder_id']);
            header('Location: index.php?cat=' . $_POST['cat_id']);
            exit;
    }
}

// Récupérer les données
$cats = getAllCats();
$selectedCatId = $_GET['cat'] ?? ($cats[0]['id'] ?? null);
$activeTab = $_GET['tab'] ?? 'dashboard';
$selectedCat = $selectedCatId ? getCatById($selectedCatId) : null;

$vaccinations = $selectedCatId ? getVaccinations($selectedCatId) : [];
$treatments = $selectedCatId ? getTreatments($selectedCatId) : [];
$weightRecords = $selectedCatId ? getWeightRecords($selectedCatId) : [];
$photos = $selectedCatId ? getPhotos($selectedCatId) : [];
$reminders = $selectedCatId ? getReminders($selectedCatId) : [];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CatLife Tracker 🐱</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <!-- Header -->
    <header class="header">
        <div class="container">
            <div class="header-content">
                <div class="logo">
                    <span class="logo-icon">🐾</span>
                    <h1 class="logo-text">CatLife Tracker</h1>
                </div>
                <button class="btn btn-primary" onclick="showModal('addCatModal')">+ Nouveau chat</button>
            </div>
        </div>
    </header>

    <div class="container main-container">
        <?php if (empty($cats)): ?>
            <!-- Aucun chat -->
            <div class="empty-state">
                <div class="empty-icon">🐱</div>
                <h2>Aucun chat enregistré</h2>
                <p>Commencez par ajouter votre premier chat</p>
                <button class="btn btn-primary btn-lg" onclick="showModal('addCatModal')">+ Ajouter mon premier chat</button>
            </div>
        <?php else: ?>
            <div class="layout">
                <!-- Sidebar -->
                <aside class="sidebar">
                    <!-- Sélection du chat -->
                    <div class="cat-selector">
                        <label>Chat sélectionné</label>
                        <select onchange="window.location.href='?cat=' + this.value" class="form-select">
                            <?php foreach ($cats as $cat): ?>
                                <option value="<?= $cat['id'] ?>" <?= $cat['id'] == $selectedCatId ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($cat['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <!-- Navigation -->
                    <nav class="nav">
                        <a href="?cat=<?= $selectedCatId ?>&tab=dashboard" class="nav-item <?= $activeTab === 'dashboard' ? 'active' : '' ?>">
                            <span>📊</span> Tableau de bord
                        </a>
                        <a href="?cat=<?= $selectedCatId ?>&tab=health" class="nav-item <?= $activeTab === 'health' ? 'active' : '' ?>">
                            <span>❤️</span> Santé
                        </a>
                        <a href="?cat=<?= $selectedCatId ?>&tab=weight" class="nav-item <?= $activeTab === 'weight' ? 'active' : '' ?>">
                            <span>⚖️</span> Poids
                        </a>
                        <a href="?cat=<?= $selectedCatId ?>&tab=photos" class="nav-item <?= $activeTab === 'photos' ? 'active' : '' ?>">
                            <span>📸</span> Photos
                        </a>
                    </nav>
                </aside>

                <!-- Main Content -->
                <main class="content">
                    <?php if ($activeTab === 'dashboard'): ?>
                        <!-- Dashboard -->
                        <?php if ($selectedCat): ?>
                            <div class="cat-banner">
                                <div style="display: flex; justify-content: space-between; align-items: start;">
                                    <div>
                                        <h1><?= htmlspecialchars($selectedCat['name']) ?> 🐱</h1>
                                        <p>
                                            <?= htmlspecialchars($selectedCat['breed'] ?: 'Race non spécifiée') ?> • 
                                            <?= calculateAge($selectedCat['birth_date']) ?> ans • 
                                            <?= htmlspecialchars($selectedCat['gender']) ?>
                                        </p>
                                    </div>
                                    <div style="display: flex; gap: 8px;">
                                        <button onclick="showModal('editCatModal')" class="btn btn-sm" style="background: rgba(255,255,255,0.2); color: white;">
                                            ✏️ Modifier
                                        </button>
                                        <button onclick="if(confirm('Voulez-vous vraiment supprimer ce chat ?')) { document.getElementById('deleteCatForm').submit(); }" class="btn btn-sm btn-danger">
                                            🗑️ Supprimer
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <form id="deleteCatForm" method="POST" style="display: none;">
                                <input type="hidden" name="action" value="delete_cat">
                                <input type="hidden" name="cat_id" value="<?= $selectedCatId ?>">
                            </form>

                            <div class="stats-grid">
                                <div class="stat-card blue">
                                    <div class="stat-label">Poids actuel</div>
                                    <div class="stat-value"><?= !empty($weightRecords) ? end($weightRecords)['weight'] : '0' ?> kg</div>
                                </div>
                                <div class="stat-card green">
                                    <div class="stat-label">Vaccinations</div>
                                    <div class="stat-value"><?= count($vaccinations) ?></div>
                                </div>
                                <div class="stat-card orange">
                                    <div class="stat-label">Photos</div>
                                    <div class="stat-value"><?= count($photos) ?></div>
                                </div>
                                <div class="stat-card red">
                                    <div class="stat-label">Rappels</div>
                                    <div class="stat-value"><?= count($reminders) ?></div>
                                </div>
                            </div>

                            <?php if (!empty($reminders)): ?>
                                <div class="card">
                                    <div class="card-header">
                                        <h2 class="card-title">🔔 Rappels à venir</h2>
                                    </div>
                                    <?php foreach ($reminders as $reminder): ?>
                                        <div class="item red">
                                            <div style="display: flex; justify-content: space-between; align-items: center;">
                                                <div>
                                                    <div class="item-title"><?= htmlspecialchars($reminder['title']) ?></div>
                                                    <div class="item-text"><?= date('d/m/Y', strtotime($reminder['reminder_date'])) ?></div>
                                                </div>
                                                <form method="POST" style="display: inline;">
                                                    <input type="hidden" name="action" value="complete_reminder">
                                                    <input type="hidden" name="reminder_id" value="<?= $reminder['id'] ?>">
                                                    <input type="hidden" name="cat_id" value="<?= $selectedCatId ?>">
                                                    <button type="submit" class="btn btn-sm btn-success">Fait</button>
                                                </form>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        <?php endif; ?>

                    <?php elseif ($activeTab === 'health'): ?>
                        <!-- Santé -->
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

                    <?php elseif ($activeTab === 'weight'): ?>
                        <!-- Poids -->
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

                    <?php elseif ($activeTab === 'photos'): ?>
                        <!-- Photos -->
                        <div class="card">
                            <div class="card-header">
                                <h2 class="card-title">📸 Galerie photos</h2>
                                <button class="btn btn-warning" onclick="showModal('addPhotoModal')">+ Ajouter</button>
                            </div>
                            <?php if (empty($photos)): ?>
                                <p style="text-align: center; color: #9ca3af; padding: 40px;">Aucune photo pour le moment</p>
                            <?php else: ?>
                                <div class="photo-grid">
                                    <?php foreach ($photos as $photo): ?>
                                        <div class="photo-item" style="position: relative;">
                                            <img src="uploads/<?= htmlspecialchars($photo['filename']) ?>" alt="<?= htmlspecialchars($photo['title']) ?>">
					    <!-- Bouton supprimer -->
                                            <form method="POST" style="position:absolute; top:6px; right:6px;">
            					<input type="hidden" name="action" value="delete_photo">
				                <input type="hidden" name="photo_id" value="<?= $photo['id'] ?>">
                			 	<input type="hidden" name="cat_id" value="<?= $selectedCatId ?>">
						<input type="hidden" name="tab" value="photos">
						<button type="submit"
  				                    class="btn btn-danger btn-sm"
				                    onclick="return confirm('Supprimer cette photo ?')">
					                🗑
				                </button>
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
                    <?php endif; ?>
                </main>
            </div>
        <?php endif; ?>
    </div>

    <!-- Modals -->
    <!-- Modal Ajouter Chat -->
    <div id="addCatModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title">Ajouter un nouveau chat</h2>
                <button class="modal-close" onclick="hideModal('addCatModal')">&times;</button>
            </div>
            <form method="POST">
                <input type="hidden" name="action" value="add_cat">
                
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

                <h3 style="margin: 24px 0 16px;">Vétérinaire</h3>

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

    <!-- Modal Modifier Chat -->
    <div id="editCatModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title">Modifier <?= htmlspecialchars($selectedCat['name'] ?? '') ?></h2>
                <button class="modal-close" onclick="hideModal('editCatModal')">&times;</button>
            </div>
            <form method="POST">
                <input type="hidden" name="action" value="update_cat">
                <input type="hidden" name="cat_id" value="<?= $selectedCatId ?>">
                
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
                        <input type="date" name="birth_date" class="form-input" value="<?= $selectedCat['birth_date'] ?? '' ?>">
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

    <!-- Modal Vaccination -->
    <div id="addVaccinationModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title">Ajouter une vaccination</h2>
                <button class="modal-close" onclick="hideModal('addVaccinationModal')">&times;</button>
            </div>
            <form method="POST">
                <input type="hidden" name="action" value="add_vaccination">
                <input type="hidden" name="cat_id" value="<?= $selectedCatId ?>">
                
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

    <!-- Modal Traitement -->
    <div id="addTreatmentModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title">Ajouter un traitement</h2>
                <button class="modal-close" onclick="hideModal('addTreatmentModal')">&times;</button>
            </div>
            <form method="POST">
                <input type="hidden" name="action" value="add_treatment">
                <input type="hidden" name="cat_id" value="<?= $selectedCatId ?>">
                
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

    <!-- Modal Poids -->
    <div id="addWeightModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title">Ajouter une pesée</h2>
                <button class="modal-close" onclick="hideModal('addWeightModal')">&times;</button>
            </div>
            <form method="POST">
                <input type="hidden" name="action" value="add_weight">
                <input type="hidden" name="cat_id" value="<?= $selectedCatId ?>">
                
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

    <!-- Modal Photo -->
    <div id="addPhotoModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title">Ajouter une photo</h2>
                <button class="modal-close" onclick="hideModal('addPhotoModal')">&times;</button>
            </div>
            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="action" value="add_photo">
                <input type="hidden" name="cat_id" value="<?= $selectedCatId ?>">

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

    <script>
        function showModal(id) {
            document.getElementById(id).classList.add('active');
        }
        
        function hideModal(id) {
            document.getElementById(id).classList.remove('active');
        }
        
        // Fermer modal en cliquant à l'extérieur
        document.querySelectorAll('.modal').forEach(modal => {
            modal.addEventListener('click', function(e) {
                if (e.target === this) {
                    hideModal(this.id);
                }
            });
        });
    </script>
</body>
</html>
