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
                <button type="button" class="btn-action transfer" title="Déplacer vers Mes Jeux" data-id="<?= $game['id'] ?>">➤</button>
            </div>
            
            <!-- Détails simples comme le calendrier : nom + date de sortie -->
            <div class="card-info-overlay">
                <div class="card-name">
                    <?= esc($game['name']) ?>
                </div>
                <div class="card-date">
                    Sortie : <?= esc(!empty($game['release_date']) ? date('d/m/Y', strtotime($game['release_date'])) : 'Date inconnue') ?>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>
<?php else: ?>
    <p class="wishlist-empty-message">Votre wishlist est vide.</p>
<?php endif; ?>

<!-- Modal -->
<div id="addGameModal" class="modal">
    <div class="modal-content">
        <button class="modal-close" id="closeModal">&times;</button>
        <h2>Ajouter un jeu à ma wishlist</h2>
        <form id="addGameForm" action="/checkpoint/public/wishlist/add" method="POST">
            <?= csrf_field() ?>
            <input type="hidden" id="game_id" name="game_id">
            <input type="hidden" id="developer" name="developer">
            <input type="hidden" id="publisher" name="publisher">
            
            <!-- Recherche de jeu -->
            <div class="form-group">
                <label for="searchGame">Recherchez votre jeu :</label>
                <input type="text" 
                       id="searchGame" 
                       name="searchGame" 
                       placeholder="Commencez à taper le nom du jeu..." 
                       required 
                       autocomplete="off">
                <ul id="suggestions" class="suggestions-list"></ul>
            </div>

            <!-- Informations du jeu -->
            <div class="form-row">
                <div class="form-group">
                    <label for="platform">Plateforme :</label>
                    <input type="text" 
                           id="platform" 
                           name="platform" 
                           placeholder="Plateforme" 
                           required 
                           readonly>
                </div>
                <div class="form-group">
                    <label for="releaseYear">Année de sortie :</label>
                    <input type="text" 
                           id="releaseYear" 
                           name="releaseYear" 
                           placeholder="Année" 
                           readonly>
                </div>
            </div>

            <div class="form-group">
                <label for="genre">Genre :</label>
                <input type="text" 
                       id="genre" 
                       name="genre" 
                       placeholder="Genre" 
                       readonly>
            </div>

            <!-- Aperçu de la jaquette -->
            <div class="form-group">
                <label for="cover">Jaquette :</label>
                <input type="text" 
                       id="cover" 
                       name="cover" 
                       placeholder="URL de la jaquette" 
                       readonly>
                <div class="form-preview cover-preview-container hidden" id="coverPreviewContainer">
                    <img id="coverPreview" 
                         src="" 
                         alt="Aperçu de la jaquette" 
                         class="hidden">
                    <span>Aperçu de la jaquette</span>
                </div>
            </div>

            <button type="submit">Ajouter à ma wishlist</button>
        </form>
    </div>
</div>

<!-- Modal de transfert vers "Mes Jeux" -->
<div id="transferGameModal" class="modal">
    <div class="modal-content">
        <button class="modal-close" id="closeTransferModal">&times;</button>
        <h2>Basculer vers mes jeux</h2>
        <form id="transferGameForm">
            <!-- Champs cachés pour les informations du jeu -->
            <input type="hidden" id="transfer_game_id" name="game_id">
            <input type="hidden" id="transfer_wishlist_id" name="wishlist_id">
            <input type="hidden" id="transfer_searchGame" name="searchGame">
            <input type="hidden" id="transfer_platform" name="platform">
            <input type="hidden" id="transfer_releaseYear" name="releaseYear">
            <input type="hidden" id="transfer_genre" name="genre">
            <input type="hidden" id="transfer_cover" name="cover">
            <input type="hidden" id="transfer_developer" name="developer">
            <input type="hidden" id="transfer_publisher" name="publisher">
            
            <!-- Aperçu du jeu à transférer -->
            <div class="form-group" id="transfer_gamePreview">
                <div style="background: var(--background-dark); border: 2px solid var(--primary-color); border-radius: 10px; padding: 1rem; display: flex; align-items: center; gap: 1rem; margin-bottom: 1rem;">
                    <img id="transfer_selectedGameCover" 
                         src="" 
                         alt="Jaquette" 
                         style="width: 60px; height: 60px; object-fit: cover; border-radius: 8px; border: 2px solid var(--secondary-color);">
                    <div>
                        <div id="transfer_selectedGameName" style="color: var(--secondary-color); font-weight: bold; margin-bottom: 0.3rem;"></div>
                        <div id="transfer_selectedGameDetails" style="color: var(--text-color); font-size: 0.9rem;"></div>
                    </div>
                </div>
            </div>

            <!-- Champs visibles pour l'utilisateur -->
            <div class="form-row-status">
                <div class="form-group">
                    <label for="transfer_status">Statut :</label>
                    <select name="status" id="transfer_status" class="form-control" required>
                        <option value="">Choisir un statut</option>
                        <option value="en cours">En cours</option>
                        <option value="termine">Terminé</option>
                        <option value="complete">Complété</option>
                        <option value="abandonne">Abandonné</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="transfer_playtime">Temps de jeu :</label>
                    <input type="text" name="playtime" id="transfer_playtime" class="form-control" placeholder="Temps de jeu (en h)">
                </div>
            </div>
            <div class="form-group">
                <label for="transfer_notes">Notes :</label>
                <textarea id="transfer_notes" name="notes" placeholder="Ajoutez vos notes sur ce jeu..."></textarea>
            </div>
            <button type="submit">Basculer vers mes jeux</button>
        </form>
    </div>
</div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('addGameModal');
    const openModalBtn = document.getElementById('openModal');
    const closeModalBtn = document.getElementById('closeModal');
    const searchInput = document.getElementById('searchGame');
    const suggestionsList = document.getElementById('suggestions');

    // Ouvrir le modal
    openModalBtn.addEventListener('click', () => {
        modal.classList.add('active');
        searchInput.focus();
    });

    // Fermer le modal
    closeModalBtn.addEventListener('click', () => {
        modal.classList.remove('active');
    });

    // Fermer le modal en cliquant en dehors
    modal.addEventListener('click', (e) => {
        if (e.target === modal) {
            modal.classList.remove('active');
        }
    });

    // Gestion de la recherche
    let searchTimeout;
    searchInput.addEventListener('input', (e) => {
        clearTimeout(searchTimeout);
        const query = e.target.value.trim();
        
        if (query.length < 2) {
            suggestionsList.innerHTML = '';
            return;
        }

        searchTimeout = setTimeout(() => {
            fetch(`/checkpoint/public/api/search?q=${encodeURIComponent(query)}`)
                .then(response => response.json())
                .then(data => {
                    suggestionsList.innerHTML = '';
                    data.forEach(game => {
                        const li = document.createElement('li');
                        li.textContent = `${game.name} (${game.platform})`;
                        li.addEventListener('click', () => selectGame(game));
                        suggestionsList.appendChild(li);
                    });
                })
                .catch(error => console.error('Erreur:', error));
        }, 300);
    });

    // Sélection d'un jeu
    function selectGame(game) {
        searchInput.value = game.name;
        document.getElementById('platform').value = game.platform;
        document.getElementById('releaseYear').value = game.release_date;
        document.getElementById('genre').value = game.category;
        document.getElementById('cover').value = game.cover;
        document.getElementById('game_id').value = game.id;
        document.getElementById('developer').value = game.developer || '';
        document.getElementById('publisher').value = game.publisher || '';
        
        const coverPreview = document.getElementById('coverPreview');
        if (game.cover) {
            coverPreview.src = game.cover;
            coverPreview.classList.remove('hidden');
        } else {
            coverPreview.classList.add('hidden');
        }
        
        suggestionsList.innerHTML = '';
    }

    // Gestion des boutons de suppression
    document.querySelectorAll('.btn-action.delete').forEach(button => {
        button.addEventListener('click', async (e) => {
            e.preventDefault();
            if (!confirm('Êtes-vous sûr de vouloir supprimer ce jeu de votre wishlist ?')) return;

            const gameId = button.getAttribute('data-id');
            if (!gameId) {
                showToast('error', 'ID du jeu non trouvé');
                return;
            }

            try {
                const response = await fetch(`/checkpoint/public/wishlist/delete/${gameId}`, {
                    method: 'POST',
                    headers: {'X-Requested-With': 'XMLHttpRequest'}
                });
                const data = await response.json();

                if (data.success) {
                    const card = button.closest('.game-card-universal');
                    if (card) {
                        card.remove();
                        checkEmptyWishlist();
                    }
                    showToast('success', 'Jeu supprimé de votre wishlist !');
                } else {
                    showToast('error', data.error || 'Une erreur est survenue lors de la suppression');
                }
            } catch (error) {
                showToast('error', 'Une erreur est survenue lors de la suppression');
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
            document.getElementById('transfer_wishlist_id').value = wishlistId;
            document.getElementById('transfer_game_id').value = gameId || '';
            document.getElementById('transfer_searchGame').value = gameName;
            document.getElementById('transfer_platform').value = platform || '';
            document.getElementById('transfer_releaseYear').value = year ? new Date(year).getFullYear() : '';
            document.getElementById('transfer_genre').value = genre || '';
            document.getElementById('transfer_cover').value = cover || '';
            document.getElementById('transfer_developer').value = developer || '';
            document.getElementById('transfer_publisher').value = publisher || '';
            
            // Affichage de l'aperçu du jeu
            const gamePreview = document.getElementById('transfer_gamePreview');
            const selectedGameCover = document.getElementById('transfer_selectedGameCover');
            const selectedGameName = document.getElementById('transfer_selectedGameName');
            const selectedGameDetails = document.getElementById('transfer_selectedGameDetails');
            
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
            if (year) details.push(new Date(year).getFullYear());
            if (genre) details.push(genre);
            selectedGameDetails.textContent = details.join(' • ');
            
            // Réinitialiser les champs du formulaire
            document.getElementById('transfer_status').value = '';
            document.getElementById('transfer_playtime').value = '';
            document.getElementById('transfer_notes').value = '';
            
            // Ouvrir le modal de transfert
            document.getElementById('transferGameModal').classList.add('active');
        });
    });

    // Fermeture du modal de transfert
    document.getElementById('closeTransferModal').addEventListener('click', function() {
        document.getElementById('transferGameModal').classList.remove('active');
    });

    // Fermeture du modal de transfert en cliquant à l'extérieur
    window.addEventListener('click', function(e) {
        const transferModal = document.getElementById('transferGameModal');
        if (e.target === transferModal) transferModal.classList.remove('active');
    });

    // Gestion du formulaire de transfert
    document.getElementById('transferGameForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const wishlistId = document.getElementById('transfer_wishlist_id').value;
        
        const jsonData = {};
        formData.forEach((value, key) => {
            jsonData[key] = value;
        });
        
        // Ajout de l'ID de la wishlist pour la suppression
        jsonData.wishlist_id = wishlistId;
        
        fetch('/checkpoint/public/wishlist/transfer', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify(jsonData)
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                showToast('success', 'Jeu transféré vers votre collection !');
                document.getElementById('transferGameModal').classList.remove('active');
                
                // Supprimer la carte de la wishlist
                const transferBtn = document.querySelector(`[data-id="${wishlistId}"].transfer`);
                if (transferBtn) {
                    const card = transferBtn.closest('.game-card-universal');
                    if (card) {
                        card.remove();
                        checkEmptyWishlist();
                    }
                }
            } else {
                showToast('error', data.error || 'Erreur lors du transfert');
            }
        })
        .catch(error => {
            console.error('Erreur:', error);
            showToast('error', 'Erreur lors du transfert');
        });
    });

    function checkEmptyWishlist() {
        const container = document.querySelector('.dashboard-row');
        const cards = document.querySelectorAll('.game-card-universal');
        
        if (container && cards.length === 0) {
            container.innerHTML = '<p class="wishlist-empty-message">Votre wishlist est vide.</p>';
        }
    }
});
</script>

<?php $this->endSection(); ?>
