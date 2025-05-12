<?php helper('url'); ?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkpoint</title>

    <!-- Inclusion des fichiers CSS -->
    <link rel="stylesheet" href="<?= base_url('css/style.css') ?>">
    <link rel="icon" href="<?= base_url('favicon.ico') ?>" type="image/x-icon">

    <!-- Inclusion des fichiers JS -->
    <script src="<?= base_url('js/script.js') ?>" defer></script>
</head>
<body>
<header>
    <nav class="navbar">
        <!-- Section des liens principaux -->
        <div class="navbar-section navbar-links">
            <a href="<?= base_url() ?>">Accueil</a>
            <a href="<?= base_url('mes-jeux') ?>">Mes Jeux</a>
            <a href="<?= base_url('wishlist') ?>">Wishlist</a>
        </div>

        <!-- Logo centré -->
        <div class="logo-container">
            <a href="<?= base_url() ?>"> <!-- Lien vers la page d'accueil -->
                <img src="<?= base_url('images/logo.png') ?>" alt="Logo" class="logo">
            </a>
        </div>

        <!-- Menu burger -->
        <div class="burger-menu">
            <button class="burger" id="burger-button">
                <img src="<?= base_url(session()->get('profile_picture') ?? 'images/burger-icon.png') ?>" alt="Menu" class="burger-icon">
            </button>
            <div class="dropdown hidden" id="burger-dropdown">
                <ul>
                    <?php if (session()->get('user_id')) : ?>
                        <li><a href="<?= base_url('profile') ?>">Profil</a></li>
                        <li><a href="<?= site_url('logout') ?>">Déconnexion</a></li>
                    <?php else : ?>
                        <li><a href="<?= base_url('login') ?>">Connexion</a></li>
                        <li><a href="<?= base_url('register') ?>">Inscription</a></li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>
</header>
</body>
</html>


