<?php helper('url'); ?>
<header>
    <nav class="navbar">
        <!-- Structure desktop (originale) -->
        <div class="navbar-flex">
            <!-- Section gauche : liens principaux (desktop seulement) -->
            <div class="navbar-section navbar-links navbar-desktop-only">
                <a href="<?= base_url() ?>">Accueil</a>
                <?php if (session()->get('user_id')) : ?>
                <a href="<?= base_url('mes-jeux') ?>">Mes Jeux</a>
                <a href="<?= base_url('wishlist') ?>">Wishlist</a>
                <?php endif; ?>
                <a href="<?= base_url('calendrier') ?>">Calendrier</a>
            </div>

            <!-- Logo centré (desktop) / à gauche (mobile) -->
            <div class="logo-container">
                <a href="<?= base_url() ?>">
                    <img src="<?= base_url('images/logo.png') ?>" alt="Logo" class="logo">
                </a>
            </div>

            <!-- Section droite : barre de recherche + menu burger -->
            <div class="navbar-right-section">
                <!-- Barre de recherche -->
                <form class="navbar-search" id="navbarGameSearchForm" autocomplete="off">
                    <input type="text" id="navbarGameSearchInput" placeholder="Rechercher un jeu...">
                    <ul id="navbarGameSuggestions" class="navbar-suggestions"></ul>
                </form>

                <!-- Menu burger -->
                <div class="burger-menu">
                    <button class="burger" id="burger-button">
                        <img src="<?= base_url(session()->get('profile_picture') ?? 'images/burger-icon.png') ?>" alt="Menu" class="burger-icon">
                    </button>
                    <div class="dropdown" id="burger-dropdown">
                        <ul>
                            <!-- Liens mobile uniquement dans le dropdown -->
                            <li class="navbar-mobile-only"><a href="<?= base_url() ?>">Accueil</a></li>
                            <?php if (session()->get('user_id')) : ?>
                                <li class="navbar-mobile-only"><a href="<?= base_url('mes-jeux') ?>">Mes Jeux</a></li>
                                <li class="navbar-mobile-only"><a href="<?= base_url('wishlist') ?>">Wishlist</a></li>
                            <?php endif; ?>
                            <li class="navbar-mobile-only"><a href="<?= base_url('calendrier') ?>">Calendrier</a></li>
                            <!-- Liens communs (profil, connexion, etc.) -->
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


