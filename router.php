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
        if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
            $filename = time() . '_' . basename($_FILES['photo']['name']);
            $uploadPath = UPLOAD_DIR . $filename;
            if (move_uploaded_file($_FILES['photo']['tmp_name'], $uploadPath)) {
                $_POST['filename'] = $filename;
                addPhoto((int) $_POST['cat_id'], $_POST);
            }
        }
        header('Location: index.php?cat=' . (int) $_POST['cat_id'] . '&tab=photos');
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
