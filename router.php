<?php

// ─── CSRF ────────────────────────────────────────────────────────────────────
if (!verifyCsrfToken($_POST['_token'] ?? '')) {
    http_response_code(403);
    die('Requête invalide (CSRF). Veuillez recharger la page et réessayer.');
}

// ─── Ownership helper ────────────────────────────────────────────────────────

/**
 * Vérifie que le cat_id soumis appartient à l'utilisateur courant.
 * Termine la requête avec 403 si ce n'est pas le cas (IDOR mitigation).
 */
function assertCatOwnership(int $catId): void
{
    global $currentUser;
    if ($catId <= 0 || !catBelongsToUser($catId, $currentUser['id'])) {
        http_response_code(403);
        die('Accès refusé : ce chat ne vous appartient pas.');
    }
}

$action = $_POST['action'] ?? '';

switch ($action) {
    case 'add_cat':
        $_POST['user_id'] = $currentUser['id'];
        addCat($_POST);
        header('Location: index.php');
        exit;

    case 'update_cat':
        assertCatOwnership((int) $_POST['cat_id']);
        updateCat((int) $_POST['cat_id'], $_POST);
        header('Location: index.php?cat=' . (int) $_POST['cat_id']);
        exit;

    case 'delete_cat':
        assertCatOwnership((int) $_POST['cat_id']);
        deleteCat((int) $_POST['cat_id']);
        header('Location: index.php');
        exit;

    case 'add_vaccination':
        assertCatOwnership((int) $_POST['cat_id']);
        addVaccination((int) $_POST['cat_id'], $_POST);
        header('Location: index.php?cat=' . (int) $_POST['cat_id'] . '&tab=health');
        exit;

    case 'add_treatment':
        assertCatOwnership((int) $_POST['cat_id']);
        addTreatment((int) $_POST['cat_id'], $_POST);
        header('Location: index.php?cat=' . (int) $_POST['cat_id'] . '&tab=health');
        exit;

    case 'add_weight':
        assertCatOwnership((int) $_POST['cat_id']);
        addWeight((int) $_POST['cat_id'], $_POST);
        header('Location: index.php?cat=' . (int) $_POST['cat_id'] . '&tab=weight');
        exit;

    case 'add_photo':
        assertCatOwnership((int) $_POST['cat_id']);

        $uploadError = null;

        if (!isset($_FILES['photo']) || $_FILES['photo']['error'] !== UPLOAD_ERR_OK) {
            $phpErrors = [
                UPLOAD_ERR_INI_SIZE   => 'Le fichier dépasse la limite du serveur (upload_max_filesize).',
                UPLOAD_ERR_FORM_SIZE  => 'Le fichier dépasse la limite du formulaire.',
                UPLOAD_ERR_PARTIAL    => 'Le fichier n\'a été que partiellement reçu.',
                UPLOAD_ERR_NO_FILE    => 'Aucun fichier sélectionné.',
                UPLOAD_ERR_NO_TMP_DIR => 'Dossier temporaire manquant.',
                UPLOAD_ERR_CANT_WRITE => 'Impossible d\'écrire le fichier sur le disque.',
                UPLOAD_ERR_EXTENSION  => 'Upload bloqué par une extension PHP.',
            ];
            $code        = $_FILES['photo']['error'] ?? -1;
            $uploadError = $phpErrors[$code] ?? 'Erreur inconnue (code ' . $code . ').';
        } else {
            // ── Vérification taille côté serveur ──────────────────────────
            if ($_FILES['photo']['size'] > MAX_UPLOAD_SIZE) {
                $uploadError = 'Fichier trop volumineux (maximum 10 Mo).';
            } else {
                // ── Validation MIME réelle (finfo, pas juste l'extension) ──
                $allowedMimes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
                $mimeExtMap   = [
                    'image/jpeg' => 'jpg',
                    'image/png'  => 'png',
                    'image/gif'  => 'gif',
                    'image/webp' => 'webp',
                ];

                $finfo = finfo_open(FILEINFO_MIME_TYPE);
                $mime  = finfo_file($finfo, $_FILES['photo']['tmp_name']);
                finfo_close($finfo);

                if (!in_array($mime, $allowedMimes, true)) {
                    $uploadError = 'Type de fichier non autorisé. Seules les images JPEG, PNG, GIF et WebP sont acceptées.';
                } else {
                    // ── Nom de fichier aléatoire (imprévisible) ────────────
                    $ext      = $mimeExtMap[$mime];
                    $filename = bin2hex(random_bytes(16)) . '.' . $ext;

                    $uploadPath = UPLOAD_DIR . $filename;
                    if (move_uploaded_file($_FILES['photo']['tmp_name'], $uploadPath)) {
                        $_POST['filename'] = $filename;
                        addPhoto((int) $_POST['cat_id'], $_POST);
                    } else {
                        $uploadError = 'move_uploaded_file() a échoué — vérifiez les permissions du dossier uploads/.';
                    }
                }
            }
        }

        $redirect = 'index.php?cat=' . (int) $_POST['cat_id'] . '&tab=photos';
        if ($uploadError) {
            $redirect .= '&upload_error=' . urlencode($uploadError);
        }
        header('Location: ' . $redirect);
        exit;

    case 'delete_photo':
        assertCatOwnership((int) $_POST['cat_id']);
        // Double vérification IDOR : la photo appartient bien au chat de l'utilisateur
        if (!photoBelongsToUser((int) $_POST['photo_id'], $currentUser['id'])) {
            http_response_code(403);
            die('Accès refusé.');
        }
        deletePhoto((int) $_POST['photo_id']);
        header('Location: index.php?cat=' . (int) $_POST['cat_id'] . '&tab=photos');
        exit;

    case 'add_reminder':
        assertCatOwnership((int) $_POST['cat_id']);
        addReminder((int) $_POST['cat_id'], $_POST);
        header('Location: index.php?cat=' . (int) $_POST['cat_id']);
        exit;

    case 'complete_reminder':
        assertCatOwnership((int) $_POST['cat_id']);
        completeReminder((int) $_POST['reminder_id']);
        header('Location: index.php?cat=' . (int) $_POST['cat_id']);
        exit;
}
