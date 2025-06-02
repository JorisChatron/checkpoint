<?php $this->extend('layouts/default'); ?>
<?php $this->section('content'); ?>

<h2>Mes Jeux</h2>
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
            <?php if ($s['status']): ?>
                <option value="<?= esc($s['status']) ?>" <?= ($selectedStatus == $s['status']) ? 'selected' : '' ?>><?= esc(ucfirst($s['status'])) ?></option>
            <?php endif; ?>
        <?php endforeach; ?>
    </select>
    <select name="genre" onchange="this.form.submit()">
        <option value="">Genre</option>
        <?php foreach ($genres as $g): ?>
            <?php if ($g['category']): ?>
                <option value="<?= esc($g['category']) ?>" <?= ($selectedGenre == $g['category']) ? 'selected' : '' ?>><?= esc($g['category']) ?></option>
            <?php endif; ?>
        <?php endforeach; ?>
    </select>
    <noscript><button type="submit">Filtrer</button></noscript>
</form>

<?php if (!empty($games)): ?>
<div class="dashboard-row">
    <?php foreach ($games as $game): ?>
        <div class="game-card carousel-card" style="cursor: pointer;" data-game='<?= json_encode($game) ?>'>
            <div style="position:absolute;top:0;left:0;width:100%;z-index:2;text-align:center;">
                <span style="display:block;padding:0.5rem 0 0.2rem 0;font-weight:bold;color:#9B5DE5;font-size:1.1rem;text-shadow:0 2px 8px #000;letter-spacing:1px;background:rgba(31,27,46,0.7);border-radius:12px 12px 0 0;">
                    <?= esc($game['name']) ?>
                </span>
            </div>
            <button type="button" class="btn-action delete" title="Supprimer" data-id="<?= $game['id'] ?>" style="position:absolute;left:50%;top:75%;transform:translate(-60px, -50%);width:32px;height:32px;border-radius:50%;background:rgba(31,27,46,0.4);color:#BB86FC;border:2px solid #7F39FB;z-index:10;display:flex;align-items:center;justify-content:center;font-size:1.3rem;opacity:0;pointer-events:none;transition:opacity 0.2s, background 0.2s;">&times;</button>
            <button type="button" class="btn-action edit" title="Modifier" data-id="<?= $game['id'] ?>" data-status="<?= esc($game['status']) ?>" data-playtime="<?= esc($game['play_time']) ?>" data-notes="<?= esc($game['notes']) ?>" data-name="<?= esc($game['name']) ?>" style="position:absolute;left:50%;top:75%;transform:translate(28px, -50%);width:32px;height:32px;border-radius:50%;background:rgba(31,27,46,0.4);color:#BB86FC;border:2px solid #00E5FF;z-index:10;display:flex;align-items:center;justify-content:center;font-size:1.1rem;opacity:0;pointer-events:none;transition:opacity 0.2s, background 0.2s;">✏️</button>
            <img src="<?= esc(!empty($game['cover']) ? $game['cover'] : '/public/images/default-cover.png') ?>" alt="Jaquette" style="width:100%;height:100%;object-fit:cover;border-radius:12px;">
        </div>
    <?php endforeach; ?>
</div>
<?php else: ?>
    <p class="wishlist-empty-message">Vous n'avez pas encore ajouté de jeux.</p>
<?php endif; ?>

<!-- Modal -->
<div id="addGameModal" class="modal">
    <div class="modal-content">
        <button class="modal-close" id="closeModal">&times;</button>
        <h2>Ajouter un jeu</h2>
        <form id="addGameForm">
            <!-- Champs cachés pour les informations automatiques -->
            <input type="hidden" id="game_id" name="game_id">
            <input type="hidden" id="platform" name="platform">
            <input type="hidden" id="releaseYear" name="releaseYear">
            <input type="hidden" id="genre" name="genre">
            <input type="hidden" id="cover" name="cover">
            <input type="hidden" id="developer" name="developer">
            <input type="hidden" id="publisher" name="publisher">
            
            <!-- Recherche de jeu - VISIBLE -->
            <div class="form-group">
                <label for="searchGame">Recherchez votre jeu :</label>
                <input type="text" id="searchGame" name="searchGame" placeholder="Commencez à taper le nom du jeu..." required autocomplete="off">
                <ul id="suggestions" class="suggestions-list"></ul>
            </div>

            <!-- Aperçu du jeu sélectionné -->
            <div class="form-group" id="gamePreview" style="display: none;">
                <div style="background: var(--background-dark); border: 2px solid var(--primary-color); border-radius: 10px; padding: 1rem; display: flex; align-items: center; gap: 1rem; margin-bottom: 1rem;">
                    <img id="selectedGameCover" 
                         src="" 
                         alt="Jaquette" 
                         style="width: 60px; height: 60px; object-fit: cover; border-radius: 8px; border: 2px solid var(--secondary-color);">
                    <div>
                        <div id="selectedGameName" style="color: var(--secondary-color); font-weight: bold; margin-bottom: 0.3rem;"></div>
                        <div id="selectedGameDetails" style="color: var(--text-color); font-size: 0.9rem;"></div>
                    </div>
                </div>
            </div>

            <!-- Champs visibles pour l'utilisateur -->
            <div class="form-row-status">
                <div class="form-group">
                    <label for="status">Statut :</label>
                    <select name="status" id="status" class="form-control" required>
                        <option value="">Choisir un statut</option>
                        <option value="en cours">En cours</option>
                        <option value="termine">Terminé</option>
                        <option value="complete">Complété</option>
                        <option value="abandonne">Abandonné</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="playtime">Temps de jeu :</label>
                    <input type="text" name="playtime" id="playtime" class="form-control" placeholder="Temps de jeu (en h)">
                </div>
            </div>
            <div class="form-group">
                <label for="notes">Notes :</label>
                <textarea id="notes" name="notes" placeholder="Ajoutez vos notes sur ce jeu..."></textarea>
            </div>
            <button type="submit">Ajouter le jeu</button>
        </form>
    </div>
</div>

<!-- Modal aperçu jeu Mes Jeux -->
<div id="gameViewModal" class="modal">
    <div class="modal-content" id="gameViewModalContent" style="max-width:600px;position:relative;">
        <button class="modal-close" id="closeGameViewModal">&times;</button>
        <div id="gameViewModalBody" style="min-height:200px;text-align:center;">
            <span style="color:#BB86FC;">Chargement...</span>
        </div>
    </div>
</div>

<!-- Modal modification jeu -->
<div id="editGameModal" class="modal">
    <div class="modal-content">
        <button class="modal-close" id="closeEditModal">&times;</button>
        <h2>Modifier le jeu</h2>
        <form id="editGameForm">
            <input type="hidden" id="editGameId" name="gameId">
            
            <div class="form-group">
                <label for="editGameName">Nom du jeu :</label>
                <input type="text" id="editGameName" name="gameName" readonly style="background: rgba(31,27,46,0.5); color: #BB86FC;">
            </div>

            <div class="form-group">
                <label for="editStatus">Statut :</label>
                <select name="status" id="editStatus" class="form-control" required>
                    <option value="">Choisir un statut</option>
                    <option value="en cours">En cours</option>
                    <option value="termine">Terminé</option>
                    <option value="complete">Complété</option>
                    <option value="abandonne">Abandonné</option>
                </select>
            </div>

            <div class="form-group">
                <label for="editPlaytime">Temps de jeu :</label>
                <input type="text" name="playtime" id="editPlaytime" class="form-control" placeholder="Temps de jeu (en h)">
            </div>

            <div class="form-group">
                <label for="editNotes">Notes :</label>
                <textarea id="editNotes" name="notes" placeholder="Notes"></textarea>
            </div>

            <button type="submit">Modifier le jeu</button>
        </form>
    </div>
</div>

<script>
document.querySelectorAll('.dashboard-row .game-card').forEach(card => {
    card.addEventListener('click', function(e) {
        // Empêche le clic sur les boutons d'action d'ouvrir le modal
        if (e.target.classList.contains('btn-action')) return;

        // Récupération des données du jeu depuis l'attribut data-game
        const gameData = JSON.parse(this.getAttribute('data-game'));
        
        let html = '';
        html += gameData.cover ? `<img src="${gameData.cover}" alt="${gameData.name}" style="width:220px;height:220px;object-fit:cover;border-radius:12px;box-shadow:0 2px 12px #7F39FB44;margin-bottom:1.2rem;">` : '';
        html += `<h2 style=\"color:#9B5DE5;margin-bottom:0.7rem;\">${gameData.name}</h2>`;
        html += `<div style=\"color:#BB86FC;font-size:1.05rem;margin-bottom:0.7rem;\">Plateforme : ${gameData.platform || 'Inconnue'}<br>Année : ${gameData.release_date || 'Inconnue'}<br>Genre : ${gameData.category || 'Inconnu'}</div>`;
        
        if (gameData.developer && gameData.developer.trim() !== '') {
            html += `<div style=\"color:#BB86FC;font-size:1.05rem;margin-bottom:0.7rem;\">Développeur : ${gameData.developer}</div>`;
        }
        if (gameData.publisher && gameData.publisher.trim() !== '') {
            html += `<div style=\"color:#BB86FC;font-size:1.05rem;margin-bottom:0.7rem;\">Éditeur : ${gameData.publisher}</div>`;
        }
        
        html += `<div style=\"color:#E0F7FA;font-size:1rem;margin-bottom:1.2rem;\">Statut : ${gameData.status || 'Inconnu'}<br>Temps de jeu : ${gameData.play_time || '0'} h</div>`;
        html += `<div style=\"color:#BB86FC;font-size:0.98rem;margin-bottom:0.5rem;\"><b>Notes :</b> ${gameData.notes || '<i>Aucune note</i>'}</div>`;

        document.getElementById('gameViewModalBody').innerHTML = html;
        document.getElementById('gameViewModal').classList.add('active');
    });
});

// Gestion des boutons modifier
document.querySelectorAll('.btn-action.edit').forEach(button => {
    button.addEventListener('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        // Récupération des données depuis les attributs data-*
        const gameId = this.getAttribute('data-id');
        const gameName = this.getAttribute('data-name');
        const status = this.getAttribute('data-status');
        const playtime = this.getAttribute('data-playtime');
        const notes = this.getAttribute('data-notes');
        
        // Remplissage du modal
        document.getElementById('editGameId').value = gameId;
        document.getElementById('editGameName').value = gameName;
        document.getElementById('editStatus').value = status || '';
        document.getElementById('editPlaytime').value = playtime || '';
        document.getElementById('editNotes').value = notes || '';
        
        // Ouverture du modal
        document.getElementById('editGameModal').classList.add('active');
    });
});

// Fermeture du modal de modification
document.getElementById('closeEditModal').addEventListener('click', function() {
    document.getElementById('editGameModal').classList.remove('active');
});

// Fermeture du modal en cliquant à l'extérieur
window.addEventListener('click', function(e) {
    const editModal = document.getElementById('editGameModal');
    if (e.target === editModal) editModal.classList.remove('active');
});

// Gestion du formulaire de modification
document.getElementById('editGameForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const gameId = document.getElementById('editGameId').value;
    
    const jsonData = {};
    formData.forEach((value, key) => {
        jsonData[key] = value;
    });
    
    fetch(`/checkpoint/public/mes-jeux/edit/${gameId}`, {
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
            showToast('success', 'Jeu modifié avec succès !');
            document.getElementById('editGameModal').classList.remove('active');
            setTimeout(() => location.reload(), 1200);
        } else {
            showToast('error', data.error || 'Erreur lors de la modification');
        }
    })
    .catch(error => {
        console.error('Erreur:', error);
        showToast('error', 'Erreur lors de la modification');
    });
});

document.getElementById('closeGameViewModal').addEventListener('click', function() {
    document.getElementById('gameViewModal').classList.remove('active');
});
window.addEventListener('click', function(e) {
    const modal = document.getElementById('gameViewModal');
    if (e.target === modal) modal.classList.remove('active');
});
</script>

<?php $this->endSection(); ?>