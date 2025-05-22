<?php helper('url'); ?>
<header>
    <nav class="navbar">
        <!-- Section des liens principaux (desktop uniquement) -->
        <div class="navbar-section navbar-links navbar-desktop-only">
            <a href="<?= base_url() ?>">Accueil</a>
            <?php if (session()->get('user_id')) : ?>
            <a href="<?= base_url('mes-jeux') ?>">Mes Jeux</a>
            <a href="<?= base_url('wishlist') ?>">Wishlist</a>
                <a href="<?= base_url('calendrier') ?>">Calendrier</a>
            <?php endif; ?>
        </div>

        <!-- Logo centré -->
        <div class="logo-container">
            <a href="<?= base_url() ?>"> <!-- Toujours la page d'accueil publique -->
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
                    <li class="navbar-mobile-only"><a href="<?= base_url() ?>">Accueil</a></li>
                    <?php if (session()->get('user_id')) : ?>
                        <li class="navbar-mobile-only"><a href="<?= base_url('mes-jeux') ?>">Mes Jeux</a></li>
                        <li class="navbar-mobile-only"><a href="<?= base_url('wishlist') ?>">Wishlist</a></li>
                        <li class="navbar-mobile-only"><a href="<?= base_url('calendrier') ?>">Calendrier</a></li>
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


