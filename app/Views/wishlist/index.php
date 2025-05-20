<?php

$this->extend('layouts/default');
$this->section('content');
?>

<h2>Ma Wishlist</h2>
<button id="openModal" class="btn btn-primary">Ajouter un jeu</button>

<!-- Barre de filtres -->
<form class="filters-bar" method="get" action="">
    <select name="platform" onchange="this.form.submit()">
        <option value="">Plateforme</option>
        <?php foreach ($platforms as $p): ?>
            <option value="<?= esc($p['platform']) ?>" <?= ($selectedPlatform == $p['platform']) ? 'selected' : '' ?>><?= esc($p['platform']) ?></option>
        <?php endforeach; ?>
    </select>
    <select name="status" onchange="this.form.submit()">
        <option value="">Statut</option>
        <?php foreach ($statuses as $s): ?>
            <option value="<?= esc($s['status']) ?>" <?= ($selectedStatus == $s['status']) ? 'selected' : '' ?>><?= esc($s['status']) ?></option>
        <?php endforeach; ?>
    </select>
    <select name="genre" onchange="this.form.submit()">
        <option value="">Genre</option>
        <?php foreach ($genres as $g): ?>
            <option value="<?= esc($g['category']) ?>" <?= ($selectedGenre == $g['category']) ? 'selected' : '' ?>><?= esc($g['category']) ?></option>
        <?php endforeach; ?>
    </select>
</form>

<?php if (!empty($wishlist)): ?>
<div class="wishlist-carousel">
    <?php foreach ($wishlist as $game): ?>
        <div class="wishlist-card">
            <img src="<?= esc(!empty($game['cover']) ? $game['cover'] : '/public/images/default-cover.png') ?>" 
                 alt="<?= esc($game['name']) ?>" 
                 class="card-cover">
            
            <div class="card-actions">
                <form class="delete-game-form" data-id="<?= $game['id'] ?>">
                    <button type="submit" class="btn-action delete" title="Supprimer">&times;</button>
                </form>
            </div>

            <div class="card-info">
                <h3 class="card-title"><?= esc($game['name']) ?></h3>
                <div class="card-details">
                    <div class="card-detail">
                        <strong>Plateforme</strong>
                        <?= esc($game['platform']) ?>
                    </div>
                    <div class="card-detail">
                        <strong>Année</strong>
                        <?= esc($game['release_date']) ?>
                    </div>
                    <div class="card-detail">
                        <strong>Genre</strong>
                        <?= esc($game['category']) ?>
                    </div>
                    <div class="card-detail">
                        <strong>Statut</strong>
                        <?= esc($game['status']) ?>
                    </div>
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

            <!-- Statut -->
            <div class="form-group">
                <label for="status">Statut :</label>
                <select name="status" id="status" required>
                    <option value="">Sélectionnez un statut</option>
                    <option value="souhaité">Souhaité</option>
                    <option value="acheté">Acheté</option>
                    <option value="joué">Joué</option>
                </select>
            </div>

            <button type="submit">Ajouter à ma wishlist</button>
        </form>
    </div>
</div>

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
        
        const coverPreview = document.getElementById('coverPreview');
        if (game.cover) {
            coverPreview.src = game.cover;
            coverPreview.classList.remove('hidden');
        } else {
            coverPreview.classList.add('hidden');
        }
        
        suggestionsList.innerHTML = '';
    }
});
</script>

<?php $this->endSection(); ?>
