<?php helper('url'); ?>
<header>
    <nav class="navbar">
        <!-- Bloc flex principal -->
        <div class="navbar-flex" style="display:flex;align-items:center;width:100%;gap:1.5rem;">
            <!-- Liens principaux (desktop seulement) -->
            <div class="navbar-section navbar-links navbar-desktop-only" style="flex:0 0 auto;">
                <a href="<?= base_url() ?>">Accueil</a>
                <?php if (session()->get('user_id')) : ?>
                <a href="<?= base_url('mes-jeux') ?>">Mes Jeux</a>
                <a href="<?= base_url('wishlist') ?>">Wishlist</a>
                <?php endif; ?>
                <a href="<?= base_url('calendrier') ?>">Calendrier</a>
            </div>

            <!-- Logo (centré sur desktop, à gauche sur mobile) -->
            <div class="logo-container" style="flex:0 0 auto;">
                <a href="<?= base_url() ?>">
                    <img src="<?= base_url('images/logo.png') ?>" alt="Logo" class="logo">
                </a>
            </div>

            <!-- Barre de recherche jeux (desktop : centrée, mobile : entre logo et burger) -->
            <form class="navbar-search" id="navbarGameSearchForm" autocomplete="off" style="flex:1 1 0;max-width:340px;min-width:180px;">
                <input type="text" id="navbarGameSearchInput" placeholder="Rechercher un jeu...">
                <ul id="navbarGameSuggestions" class="navbar-suggestions"></ul>
            </form>

            <!-- Menu burger (photo profil) -->
            <div class="burger-menu" style="flex:0 0 auto;">
                <button class="burger" id="burger-button">
                    <img src="<?= base_url(session()->get('profile_picture') ?? 'images/burger-icon.png') ?>" alt="Menu" class="burger-icon">
                </button>
                <div class="dropdown hidden" id="burger-dropdown">
                    <ul>
                        <li class="navbar-mobile-only"><a href="<?= base_url() ?>">Accueil</a></li>
                        <?php if (session()->get('user_id')) : ?>
                            <li class="navbar-mobile-only"><a href="<?= base_url('mes-jeux') ?>">Mes Jeux</a></li>
                            <li class="navbar-mobile-only"><a href="<?= base_url('wishlist') ?>">Wishlist</a></li>
                        <?php endif; ?>
                        <li class="navbar-mobile-only"><a href="<?= base_url('calendrier') ?>">Calendrier</a></li>
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
        </div>
    </nav>
</header>

<!-- Modal aperçu jeu global (navbar) -->
<div id="navbarGameModal" class="modal">
    <div class="modal-content" id="navbarGameModalContent" style="max-width:600px;position:relative;">
        <button class="modal-close" id="closeNavbarGameModal">&times;</button>
        <div id="navbarGameModalBody" style="min-height:200px;text-align:center;">
            <span style="color:#BB86FC;">Chargement...</span>
        </div>
        <div id="navbarGameModalActions" style="margin-top:1.5rem;text-align:center;display:none;"></div>
    </div>
</div>

<script>window.CP_BASE_URL = "<?= base_url() ?>";</script>


