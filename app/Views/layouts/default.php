<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title ?? 'Checkpoint') ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= base_url('public/css/style.css') ?>">
    <script defer>
        document.addEventListener('DOMContentLoaded', () => {
            const burger = document.querySelector('.burger');
            burger?.addEventListener('click', () => {
                document.querySelector('header')?.classList.toggle('active');
            });
        });
    </script>
</head>
<body>

    <?= $this->include('layouts/navbar') ?>

    <main>
        <?= $this->renderSection('content') ?>
    </main>

</body>
</html>

