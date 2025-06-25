<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title ?? 'Checkpoint') ?></title>
    <link rel="icon" type="image/png" href="<?= base_url('images/icon.png') ?>" sizes="32x32">
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= base_url('css/style.css') ?>">
</head>
<body>
    <?= $this->include('layouts/navbar') ?>

    <main>
        <?= $this->renderSection('content') ?>
    </main>
    <!-- JavaScript optimisé avec utilitaires centralisés -->
    <script>
        window.CP_BASE_URL = '<?= base_url() ?>';
        window.CP_RAWG_API_KEY = '<?= getenv('RAWG_API_KEY') ?>';
    </script>
    <script src="<?= base_url('js/script.js') ?>" defer></script>
</body>
</html>

