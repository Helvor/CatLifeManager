<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CatLife Tracker 🐱</title>
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
    </script>
</body>
</html>
