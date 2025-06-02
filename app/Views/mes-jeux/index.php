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
        <div class="game-card carousel-card">
                <div class="card-front">
                <div style="position:absolute;top:0;left:0;width:100%;z-index:2;text-align:center;">
                    <span style="display:block;padding:0.5rem 0 0.2rem 0;font-weight:bold;color:#9B5DE5;font-size:1.1rem;text-shadow:0 2px 8px #000;letter-spacing:1px;background:rgba(31,27,46,0.7);border-radius:12px 12px 0 0;">
                        <?= esc($game['name']) ?>
                    </span>
                    </div>
                <button type="button" class="btn-action delete" title="Supprimer" data-id="<?= $game['id'] ?>">&times;</button>
                <button type="button" class="btn-action edit" title="Modifier" data-id="<?= $game['id'] ?>" data-status="<?= esc($game['status']) ?>" data-playtime="<?= esc($game['play_time']) ?>" data-notes="<?= esc($game['notes']) ?>" data-name="<?= esc($game['name']) ?>">✏️</button>
                <div class="card-cover-container" style="height:100%;">
                    <img src="<?= esc(!empty($game['cover']) ? $game['cover'] : '/public/images/default-cover.png') ?>" alt="Jaquette" class="card-cover">
                </div>
                </div>
                <div class="card-back">
                <div style="padding: 1rem; color: #E0F7FA; width: 100%;">
                    <strong>Plateforme :</strong> <?= esc($game['platform']) ?><br>
                    <strong>Année :</strong> <?= esc($game['release_date']) ?><br>
                    <strong>Genre :</strong> <?= esc($game['category']) ?><br>
                    <strong>Statut :</strong> <?= esc($game['status']) ?><br>
                    <strong>Temps de jeu :</strong> <?= esc($game['play_time']) ?> h<br>
                    <strong>Notes :</strong> <?= esc($game['notes']) ?>
                </div>
            </div>
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
            <input type="hidden" id="game_id" name="game_id">
            <input type="hidden" id="developer" name="developer">
            <input type="hidden" id="publisher" name="publisher">
            <div class="form-group">
                <label for="searchGame">Recherchez votre jeu :</label>
                <input type="text" id="searchGame" name="searchGame" placeholder="Commencez à taper le nom du jeu..." required autocomplete="off">
                <ul id="suggestions" class="suggestions-list"></ul>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label for="platform">Plateforme :</label>
                    <input type="text" id="platform" name="platform" placeholder="Plateforme" required readonly>
                </div>
                <div class="form-group">
                    <label for="releaseYear">Année de sortie :</label>
                    <input type="text" id="releaseYear" name="releaseYear" placeholder="Année" readonly>
                </div>
            </div>
            <div class="form-group">
                <label for="genre">Genre :</label>
                <input type="text" id="genre" name="genre" placeholder="Genre" readonly>
            </div>
            <div class="form-group">
                    <label for="cover">Jaquette :</label>
                    <input type="text" id="cover" name="cover" placeholder="URL de la jaquette" readonly>
                <div class="form-preview cover-preview-container hidden" id="coverPreviewContainer">
                    <img id="coverPreview" src="" alt="Aperçu de la jaquette" class="hidden">
                    <span>Aperçu de la jaquette</span>
                </div>
            </div>
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
                <textarea id="notes" name="notes" placeholder="Notes"></textarea>
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
        // Empêche le clic sur le bouton supprimer d'ouvrir le modal
        if (e.target.classList.contains('btn-action')) return;

        const name = this.querySelector('.card-front span').textContent.trim();
        const cover = this.querySelector('.card-cover').getAttribute('src');
        const backDiv = this.querySelector('.card-back > div');
        const platform = backDiv ? backDiv.innerHTML.match(/Plateforme :<\/strong> ([^<]*)<br>/)?.[1] : '';
        const release = backDiv ? backDiv.innerHTML.match(/Année :<\/strong> ([^<]*)<br>/)?.[1] : '';
        const genre = backDiv ? backDiv.innerHTML.match(/Genre :<\/strong> ([^<]*)<br>/)?.[1] : '';
        const status = backDiv ? backDiv.innerHTML.match(/Statut :<\/strong> ([^<]*)<br>/)?.[1] : '';
        const playtime = backDiv ? backDiv.innerHTML.match(/Temps de jeu :<\/strong> ([^<]*) h<br>/)?.[1] : '';
        const notes = backDiv ? backDiv.innerHTML.match(/Notes :<\/strong> ([^<]*)$/)?.[1] : '';

        let html = '';
        html += cover ? `<img src="${cover}" alt="${name}" style="width:220px;height:220px;object-fit:cover;border-radius:12px;box-shadow:0 2px 12px #7F39FB44;margin-bottom:1.2rem;">` : '';
        html += `<h2 style=\"color:#9B5DE5;margin-bottom:0.7rem;\">${name}</h2>`;
        html += `<div style=\"color:#BB86FC;font-size:1.05rem;margin-bottom:0.7rem;\">Plateforme : ${platform || 'Inconnue'}<br>Année : ${release || 'Inconnue'}<br>Genre : ${genre || 'Inconnu'}</div>`;
        html += `<div style=\"color:#E0F7FA;font-size:1rem;margin-bottom:1.2rem;\">Statut : ${status || 'Inconnu'}<br>Temps de jeu : ${playtime || '0'} h</div>`;
        html += `<div style=\"color:#BB86FC;font-size:0.98rem;margin-bottom:0.5rem;\"><b>Notes :</b> ${notes || '<i>Aucune note</i>'}</div>`;

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