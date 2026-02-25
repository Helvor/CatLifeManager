<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <!-- viewport-fit=cover is required for iOS safe-area-inset support -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>CatLife Tracker</title>

    <!-- PWA manifest -->
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#6c5ce7">

    <!-- iOS PWA support -->
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="CatLife">

    <!-- iOS app icons (Safari ignores the manifest for apple-touch-icon) -->
    <link rel="apple-touch-icon" href="/icons/apple-touch-icon.png">
    <link rel="apple-touch-icon" sizes="152x152" href="/icons/apple-touch-icon-152.png">
    <link rel="apple-touch-icon" sizes="120x120" href="/icons/apple-touch-icon-120.png">

    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php require __DIR__ . '/partials/header.php'; ?>

    <div class="container main-container">
        <?php if (empty($cats)): ?>
            <?php require __DIR__ . '/pages/empty.php'; ?>
        <?php else: ?>
            <div class="layout">
                <?php require __DIR__ . '/partials/sidebar.php'; ?>

                <main class="content">
                    <?php
                    if ($activeTab === 'dashboard') {
                        require __DIR__ . '/pages/dashboard.php';
                    } elseif ($activeTab === 'health') {
                        require __DIR__ . '/pages/health.php';
                    } elseif ($activeTab === 'weight') {
                        require __DIR__ . '/pages/weight.php';
                    } elseif ($activeTab === 'photos') {
                        require __DIR__ . '/pages/photos.php';
                    } else {
                        require __DIR__ . '/pages/dashboard.php';
                    }
                    ?>
                </main>
            </div>
        <?php endif; ?>
    </div>

    <?php require __DIR__ . '/modals/add_cat.php'; ?>
    <?php require __DIR__ . '/modals/edit_cat.php'; ?>
    <?php require __DIR__ . '/modals/add_vaccination.php'; ?>
    <?php require __DIR__ . '/modals/add_treatment.php'; ?>
    <?php require __DIR__ . '/modals/add_weight.php'; ?>
    <?php require __DIR__ . '/modals/add_photo.php'; ?>

    <script>
        function showModal(id) {
            document.getElementById(id).classList.add('active');
        }

        function hideModal(id) {
            document.getElementById(id).classList.remove('active');
        }

        document.querySelectorAll('.modal').forEach(modal => {
            modal.addEventListener('click', function(e) {
                if (e.target === this) {
                    hideModal(this.id);
                }
            });
        });

        // Register service worker for PWA / offline support
        if ('serviceWorker' in navigator) {
            navigator.serviceWorker.register('/sw.js');
        }
    </script>
</body>
</html>
