<?php

$this->extend('layouts/default');
$this->section('content');
?>

<section class="dashboard-home">
<h2>Ma Wishlist</h2>
<button id="openModal" class="btn btn-primary">Ajouter un jeu</button>

<!-- Barre de filtres -->
<form class="filters-bar" method="get" action="">
    <select name="platform" onchange="this.form.submit()">
        <option value="" <?= ($selectedPlatform === null || $selectedPlatform === '') ? 'selected' : '' ?>>Plateforme</option>
        <?php foreach ($platforms as $p): ?>
            <option value="<?= esc($p['platform']) ?>" <?= ($selectedPlatform === $p['platform']) ? 'selected' : '' ?>><?= esc($p['platform']) ?></option>
        <?php endforeach; ?>
    </select>
    <select name="genre" onchange="this.form.submit()">
        <option value="" <?= ($selectedGenre === null || $selectedGenre === '') ? 'selected' : '' ?>>Genre</option>
        <?php foreach ($genres as $g): ?>
            <option value="<?= esc($g['category']) ?>" <?= ($selectedGenre === $g['category']) ? 'selected' : '' ?>><?= esc($g['category']) ?></option>
        <?php endforeach; ?>
    </select>
</form>

<?php if (!empty($wishlist)): ?>
<div class="dashboard-row">
    <?php foreach ($wishlist as $game): ?>
        <div class="game-card-universal">
            <?php if (!empty($game['cover'])): ?>
                <img src="<?= esc($game['cover']) ?>"
                     alt="<?= esc($game['name']) ?>"
                     class="card-image">
            <?php else: ?>
                <div class="game-cover-placeholder">
                    <div class="placeholder-title"><?= esc($game['name']) ?></div>
                    <div class="placeholder-text">Aucune jaquette</div>
                </div>
            <?php endif; ?>
            
            <!-- Boutons d'action -->
            <div class="card-actions">
                <button type="button" class="btn-action delete" title="Supprimer de la wishlist" data-id="<?= $game['id'] ?>">✕</button>
                <button type="button" 
                        class="btn-action transfer" 
                        title="Déplacer vers Mes Jeux" 
                        data-id="<?= $game['id'] ?>"
                        data-name="<?= esc($game['name']) ?>"
                        data-platform="<?= esc($game['platform'] ?? '') ?>"
                        data-year="<?= esc($game['release_date'] ?? '') ?>"
                        data-genre="<?= esc($game['category'] ?? '') ?>"
                        data-cover="<?= esc($game['cover'] ?? '') ?>"
                        data-developer="<?= esc($game['developer'] ?? '') ?>"
                        data-publisher="<?= esc($game['publisher'] ?? '') ?>"
                        data-game-id="<?= esc($game['game_id'] ?? '') ?>">➤</button>
            </div>
            
            <!-- Détails simples comme le calendrier : nom + date de sortie -->
            <div class="card-info-overlay">
                <div class="card-name">
                    <?= esc($game['name']) ?>
                </div>
                <div class="card-date">
                    <?= esc(!empty($game['release_date']) ? date('d/m/Y', strtotime($game['release_date'])) : 'Date inconnue') ?>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>
<?php else: ?>
    <p class="wishlist-empty-message">Votre wishlist est vide.</p>
<?php endif; ?>

<!-- Modal d'ajout à la collection Mes Jeux (identique au calendrier) -->
<div id="addGameModal" class="modal">
    <div class="modal-content">
        <button class="modal-close" id="closeAddGameModal">&times;</button>
        <h2>Ajouter un jeu</h2>
        <form id="addGameWishlistForm">
            <!-- Champs cachés pour les informations automatiques -->
            <input type="hidden" id="addGame_game_id" name="game_id">
            <input type="hidden" id="addGame_platform" name="platform">
            <input type="hidden" id="addGame_releaseYear" name="releaseYear">
            <input type="hidden" id="addGame_genre" name="genre">
            <input type="hidden" id="addGame_cover" name="cover">
            <input type="hidden" id="addGame_developer" name="developer">
            <input type="hidden" id="addGame_publisher" name="publisher">
            <input type="hidden" id="addGame_searchGame" name="searchGame">
            <input type="hidden" id="wishlist_item_id" name="wishlist_id">
            
            <!-- Aperçu du jeu sélectionné -->
            <div class="form-group" id="addGame_gamePreview" style="display: none;">
                <div style="background: var(--background-dark); border: 2px solid var(--primary-color); border-radius: 10px; padding: 1rem; display: flex; align-items: center; gap: 1rem; margin-bottom: 1rem;">
                    <img id="addGame_selectedGameCover" 
                         src="" 
                         alt="Jaquette" 
                         style="width: 60px; height: 60px; object-fit: cover; border-radius: 8px; border: 2px solid var(--secondary-color);">
                    <div>
                        <div id="addGame_selectedGameName" style="color: var(--secondary-color); font-weight: bold; margin-bottom: 0.3rem;"></div>
                        <div id="addGame_selectedGameDetails" style="color: var(--text-color); font-size: 0.9rem;"></div>
                    </div>
                </div>
            </div>

            <!-- Champs visibles pour l'utilisateur -->
            <div class="form-row-status">
                <div class="form-group">
                    <label for="addGame_status">Statut :</label>
                    <select name="status" id="addGame_status" class="form-control" required>
                        <option value="">Choisir un statut</option>
                        <option value="en cours">En cours</option>
                        <option value="termine">Terminé</option>
                        <option value="complete">Complété</option>
                        <option value="abandonne">Abandonné</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="addGame_playtime">Temps de jeu :</label>
                    <input type="text" name="playtime" id="addGame_playtime" class="form-control" placeholder="Temps de jeu (en h)">
                </div>
            </div>
            <div class="form-group">
                <label for="addGame_notes">Notes :</label>
                <textarea id="addGame_notes" name="notes" placeholder="Ajoutez vos notes sur ce jeu..."></textarea>
            </div>
            <button type="submit">Ajouter à mes jeux</button>
        </form>
    </div>
</div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Gestion des boutons de suppression optimisée
    document.querySelectorAll('.btn-action.delete').forEach(button => {
        button.addEventListener('click', async (e) => {
            e.preventDefault();
            if (!confirm('Êtes-vous sûr de vouloir supprimer ce jeu de votre wishlist ?')) return;

            const gameId = button.getAttribute('data-id');
            if (!gameId) return;

            try {
                const result = await GameUtils.apiCall(`/checkpoint/public/wishlist/delete/${gameId}`, 'POST');
                if (result.success) {
                    const card = button.closest('.game-card-universal');
                    if (card) {
                        card.remove();
                        GameUtils.showSuccess('Jeu supprimé de la wishlist !');
                        checkEmptyPage();
                    }
                } else {
                    GameUtils.showError('Erreur lors de la suppression');
                }
            } catch (error) {
                GameUtils.showError('Erreur de connexion');
            }
        });
    });

    // Gestion des boutons de transfert vers "Mes Jeux"
    document.querySelectorAll('.btn-action.transfer').forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            // Récupération des données depuis les attributs data-*
            const wishlistId = this.getAttribute('data-id');
            const gameName = this.getAttribute('data-name');
            const platform = this.getAttribute('data-platform');
            const year = this.getAttribute('data-year');
            const genre = this.getAttribute('data-genre');
            const cover = this.getAttribute('data-cover');
            const developer = this.getAttribute('data-developer');
            const publisher = this.getAttribute('data-publisher');
            const gameId = this.getAttribute('data-game-id');
            
            // Remplissage des champs cachés
            document.getElementById('addGame_game_id').value = gameId || '';
            document.getElementById('addGame_platform').value = platform || '';
            document.getElementById('addGame_releaseYear').value = year ? year.split('-')[0] : '';
            document.getElementById('addGame_genre').value = genre || '';
            document.getElementById('addGame_cover').value = cover || '';
            document.getElementById('addGame_developer').value = developer || '';
            document.getElementById('addGame_publisher').value = publisher || '';
            document.getElementById('addGame_searchGame').value = gameName;
            document.getElementById('wishlist_item_id').value = wishlistId;
            
            // Affichage de l'aperçu du jeu
            const gamePreview = document.getElementById('addGame_gamePreview');
            const selectedGameCover = document.getElementById('addGame_selectedGameCover');
            const selectedGameName = document.getElementById('addGame_selectedGameName');
            const selectedGameDetails = document.getElementById('addGame_selectedGameDetails');
            
            // Afficher l'aperçu
            gamePreview.style.display = 'block';
            
            // Jaquette - utiliser le placeholder si pas d'image
            if (cover) {
                selectedGameCover.src = cover;
                selectedGameCover.style.display = 'block';
                // Cacher le placeholder s'il existe
                const placeholder = gamePreview.querySelector('.game-cover-placeholder');
                if (placeholder) placeholder.style.display = 'none';
            } else {
                selectedGameCover.style.display = 'none';
                // Afficher le placeholder
                let placeholder = gamePreview.querySelector('.game-cover-placeholder');
                if (!placeholder) {
                    placeholder = document.createElement('div');
                    placeholder.className = 'game-cover-placeholder size-small';
                    placeholder.style.cssText = 'width: 60px; height: 60px; border-radius: 8px; border: 2px solid var(--secondary-color); margin-right: 1rem;';
                    placeholder.innerHTML = `
                        <div class="placeholder-title">${gameName}</div>
                    `;
                    selectedGameCover.parentNode.insertBefore(placeholder, selectedGameCover);
                } else {
                    placeholder.style.display = 'flex';
                    placeholder.querySelector('.placeholder-title').textContent = gameName;
                }
            }
            
            selectedGameName.textContent = gameName;
            
            // Détails (plateforme, année, genre)
            const details = [];
            if (platform) details.push(platform);
            if (year) details.push(year.split('-')[0]);
            if (genre) details.push(genre);
            selectedGameDetails.textContent = details.join(' • ');
            
            // Réinitialiser les champs du formulaire
            document.getElementById('addGame_status').value = '';
            document.getElementById('addGame_playtime').value = '';
            document.getElementById('addGame_notes').value = '';
            
            // Ouvrir le modal d'ajout
            ModalUtils.open('addGameModal');
        });
    });

    // Gestion du formulaire d'ajout optimisée
    document.getElementById('addGameWishlistForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const wishlistId = document.getElementById('wishlist_item_id').value;
        
        // Ajouter l'ID de wishlist aux données
        const formData = new FormData(this);
        const jsonData = FormUtils.formDataToJson(formData);
        jsonData.wishlist_id = wishlistId;
        
        try {
            const result = await GameUtils.apiCall('/checkpoint/public/wishlist/transfer', 'POST', jsonData);
            if (result.success) {
                ModalUtils.close('addGameModal');
                GameUtils.showSuccess('Jeu transféré vers Mes Jeux !');
                
                // Supprimer la carte de la wishlist
                const transferBtn = document.querySelector(`[data-id="${wishlistId}"].transfer`);
                if (transferBtn) {
                    const card = transferBtn.closest('.game-card-universal');
                    if (card) {
                        card.remove();
                        checkEmptyPage();
                    }
                }
                
                setTimeout(() => location.reload(), 300);
            } else {
                GameUtils.showError('Erreur lors du transfert');
            }
        } catch (error) {
            GameUtils.showError('Erreur de connexion');
        }
    });

    // Configuration des modals avec utilitaires
    ModalUtils.setupAutoClose('addGameModal', 'closeAddGameModal');

    // Redirection du bouton "Ajouter un jeu" vers la barre de recherche navbar
    document.getElementById('openModal').addEventListener('click', function(e) {
        e.preventDefault();
        
        // Focus sur la barre de recherche navbar
        const navbarSearchInput = document.getElementById('navbarGameSearchInput');
        if (navbarSearchInput) {
            navbarSearchInput.focus();
            
            // Scroll vers le haut pour s'assurer que la navbar est visible
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
            
            // Optionnel : ajouter un effet visuel pour attirer l'attention
            navbarSearchInput.style.boxShadow = '0 0 10px #7F39FB';
            setTimeout(() => {
                navbarSearchInput.style.boxShadow = '';
            }, 2000);
        }
    });

    // Fonction pour vérifier si la page est vide
    function checkEmptyPage() {
        const gameCards = document.querySelectorAll('.game-card-universal');
        if (gameCards.length === 0) {
            setTimeout(() => location.reload(), 300);
        }
    }
});
</script>

<?php $this->endSection(); ?>
