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

<!-- Modal détails jeu (identique au calendrier) -->
<div id="gameModal" class="modal">
    <div class="modal-content" id="gameModalContent" style="max-width:600px;position:relative;">
        <button class="modal-close" id="closeGameModal">&times;</button>
        <div id="gameModalBody" style="min-height:200px;text-align:center;">
            <span style="color:#BB86FC;">Chargement...</span>
        </div>
    </div>
</div>

<!-- Modal d'ajout global -->
<div id="globalAddGameModal" class="modal">
    <div class="modal-content">
        <button class="modal-close" id="closeGlobalAddGameModal">&times;</button>
        <h2>Ajouter un jeu</h2>
        <form id="globalAddGameForm">
            <input type="hidden" id="global_addGame_game_id" name="game_id">
            <input type="hidden" id="global_addGame_searchGame" name="searchGame">
            <input type="hidden" id="global_addGame_platform" name="platform">
            <input type="hidden" id="global_addGame_releaseYear" name="releaseYear">
            <input type="hidden" id="global_addGame_genre" name="genre">
            <input type="hidden" id="global_addGame_cover" name="cover">
            <input type="hidden" id="global_addGame_developer" name="developer">
            <input type="hidden" id="global_addGame_publisher" name="publisher">
            
            <div class="form-group">
                <label>Jeu sélectionné :</label>
                <div id="global_selectedGameName" style="color: var(--secondary-color); font-weight: bold; margin: 0.5rem 0;"></div>
            </div>

            <div class="form-group">
                <label for="global_addGame_status">Statut :</label>
                <select name="status" id="global_addGame_status" class="form-control" required>
                    <option value="">Choisir un statut</option>
                    <option value="en cours">En cours</option>
                    <option value="termine">Terminé</option>
                    <option value="complete">Complété</option>
                    <option value="abandonne">Abandonné</option>
                </select>
            </div>

            <div class="form-group">
                <label for="global_addGame_playtime">Temps de jeu :</label>
                <input type="text" name="playtime" id="global_addGame_playtime" class="form-control" placeholder="Temps de jeu (en h)">
            </div>

            <div class="form-group">
                <label for="global_addGame_notes">Notes :</label>
                <textarea id="global_addGame_notes" name="notes" placeholder="Ajoutez vos notes sur ce jeu..."></textarea>
            </div>
            
            <button type="submit">Ajouter le jeu</button>
        </form>
    </div>
</div>

<script>window.CP_BASE_URL = "<?= base_url() ?>";</script>


