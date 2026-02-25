<?php

$action = $_POST['action'] ?? '';

switch ($action) {
    case 'add_cat':
        addCat($_POST);
        header('Location: index.php');
        exit;

    case 'update_cat':
        updateCat((int) $_POST['cat_id'], $_POST);
        header('Location: index.php?cat=' . (int) $_POST['cat_id']);
        exit;

    case 'delete_cat':
        deleteCat((int) $_POST['cat_id']);
        header('Location: index.php');
        exit;

    case 'add_vaccination':
        addVaccination((int) $_POST['cat_id'], $_POST);
        header('Location: index.php?cat=' . (int) $_POST['cat_id'] . '&tab=health');
        exit;

    case 'add_treatment':
        addTreatment((int) $_POST['cat_id'], $_POST);
        header('Location: index.php?cat=' . (int) $_POST['cat_id'] . '&tab=health');
        exit;

    case 'add_weight':
        addWeight((int) $_POST['cat_id'], $_POST);
        header('Location: index.php?cat=' . (int) $_POST['cat_id'] . '&tab=weight');
        exit;

    case 'add_photo':
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
            $code = $_FILES['photo']['error'] ?? -1;
            $uploadError = $phpErrors[$code] ?? 'Erreur inconnue (code ' . $code . ').';
        } else {
            $filename  = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '_', basename($_FILES['photo']['name']));
            $uploadPath = UPLOAD_DIR . $filename;
            if (move_uploaded_file($_FILES['photo']['tmp_name'], $uploadPath)) {
                $_POST['filename'] = $filename;
                addPhoto((int) $_POST['cat_id'], $_POST);
            } else {
                $uploadError = 'move_uploaded_file() a échoué — vérifiez les permissions du dossier uploads/.';
            }
        }
        $redirect = 'index.php?cat=' . (int) $_POST['cat_id'] . '&tab=photos';
        if ($uploadError) {
            $redirect .= '&upload_error=' . urlencode($uploadError);
        }
        header('Location: ' . $redirect);
        exit;

    case 'delete_photo':
        deletePhoto((int) $_POST['photo_id']);
        header('Location: index.php?cat=' . (int) $_POST['cat_id'] . '&tab=photos');
        exit;

    case 'add_reminder':
        addReminder((int) $_POST['cat_id'], $_POST);
        header('Location: index.php?cat=' . (int) $_POST['cat_id']);
        exit;

    case 'complete_reminder':
        completeReminder((int) $_POST['reminder_id']);
        header('Location: index.php?cat=' . (int) $_POST['cat_id']);
        exit;
}
