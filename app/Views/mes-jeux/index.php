<?php $this->extend('layouts/default'); ?>
<?php $this->section('content'); ?>

<section class="dashboard-home">
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
        <div class="game-card-universal" data-game='<?= htmlspecialchars(json_encode($game, JSON_HEX_APOS | JSON_HEX_QUOT), ENT_QUOTES, 'UTF-8') ?>'>
            <?php if (!empty($game['cover'])): ?>
                <img src="<?= esc($game['cover']) ?>" alt="<?= esc($game['name']) ?>" class="card-image">
            <?php else: ?>
                <div class="game-cover-placeholder">
                    <div class="placeholder-title"><?= esc($game['name']) ?></div>
                    <div class="placeholder-text">Aucune jaquette</div>
                </div>
            <?php endif; ?>
            
            <!-- Overlay titre -->
            <div class="card-title-overlay">
                <span><?= esc($game['name']) ?></span>
            </div>
            
            <!-- Boutons d'action -->
            <div class="card-actions">
                <button type="button" class="btn-action delete" title="Supprimer" data-id="<?= $game['id'] ?>">✕</button>
                <button type="button" class="btn-action edit" title="Modifier" 
                        data-id="<?= $game['id'] ?>" 
                        data-status="<?= esc($game['status']) ?>" 
                        data-playtime="<?= esc($game['play_time']) ?>" 
                        data-notes="<?= esc($game['notes']) ?>" 
                        data-name="<?= esc($game['name']) ?>">✎</button>
            </div>
        </div>
    <?php endforeach; ?>
</div>
<?php else: ?>
    <p class="wishlist-empty-message">Vous n'avez pas encore ajouté de jeux.</p>
<?php endif; ?>

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
document.querySelectorAll('.dashboard-row .game-card-universal').forEach(card => {
    card.addEventListener('click', function(e) {
        // Empêche le clic sur les boutons d'action d'ouvrir le modal
        // Vérifie si l'élément cliqué ou un de ses parents est un bouton d'action
        if (e.target.classList.contains('btn-action') || e.target.closest('.btn-action')) {
            return;
        }

        // Récupération des données du jeu depuis l'attribut data-game
        const gameDataString = this.getAttribute('data-game');
        
        let gameData;
        try {
            gameData = JSON.parse(gameDataString);
        } catch (error) {
            console.error('JSON parse error:', error);
            console.error('Problematic string:', gameDataString);
            return;
        }
        
        let html = '';
        
        // Gestion de la jaquette ou du placeholder dans le modal
        if (gameData.cover) {
            html += `<img src="${gameData.cover}" alt="${gameData.name}" style="width:220px;height:220px;object-fit:cover;border-radius:12px;box-shadow:0 2px 12px #7F39FB44;margin-bottom:1.2rem;">`;
        } else {
            html += `
                <div style="width:220px;height:220px;margin:0 auto 1.2rem auto;background:linear-gradient(45deg, #1F1B2E, #2A1B3D);border-radius:10px;display:flex;flex-direction:column;justify-content:center;align-items:center;padding:0.5rem;box-sizing:border-box;text-align:center;border:2px solid #7F39FB;box-shadow:0 2px 8px #7F39FB44;">
                    <div style="color:#9B5DE5;font-size:1.2rem;font-weight:bold;margin-bottom:0.5rem;text-shadow:0 2px 8px rgba(0,0,0,0.5);letter-spacing:1px;line-height:1.2;">${gameData.name}</div>
                    <div style="color:#BB86FC;font-size:0.9rem;opacity:0.8;max-width:85%;line-height:1.3;text-align:center;">Aucune jaquette</div>
                </div>
            `;
        }
        
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
</script>

</section>
<?php $this->endSection(); ?>