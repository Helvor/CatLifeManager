<?php
require_once 'config.php';
require_once 'database.php';

// Handle POST actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once 'router.php';
}

// Load data for rendering
$cats          = getAllCats();
$selectedCatId = $_GET['cat'] ?? ($cats[0]['id'] ?? null);
$activeTab     = $_GET['tab'] ?? 'dashboard';
$selectedCat   = $selectedCatId ? getCatById($selectedCatId) : null;

$vaccinations  = $selectedCatId ? getVaccinations($selectedCatId) : [];
$treatments    = $selectedCatId ? getTreatments($selectedCatId) : [];
$weightRecords = $selectedCatId ? getWeightRecords($selectedCatId) : [];
$photos        = $selectedCatId ? getPhotos($selectedCatId) : [];
$reminders     = $selectedCatId ? getReminders($selectedCatId) : [];

require_once 'views/layout.php';
