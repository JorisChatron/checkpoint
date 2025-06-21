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
            
            <!-- Info overlay en bas avec titre et statut -->
            <div class="card-info-overlay">
                <div class="card-name">
                    <?= esc($game['name']) ?>
                </div>
                <div class="card-date">
                    <?= esc(ucfirst($game['status'])) ?><?= !empty($game['play_time']) ? ' • ' . $game['play_time'] . 'h' : '' ?>
                </div>
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

<!-- Modal aperçu jeu Mes Jeux -->
<div id="gameViewModal" class="modal">
    <div class="modal-content" id="gameViewModalContent" style="max-width:600px;position:relative;">
        <button class="modal-close" id="closeGameViewModal">&times;</button>
        <div id="gameViewModalBody" style="min-height:200px;text-align:center;">
            <span style="color:#BB86FC;">Chargement...</span>
        </div>
    </div>
</div>

<script>
document.querySelectorAll('.dashboard-row .game-card-universal').forEach(card => {
    card.addEventListener('click', function(e) {
        // Empêche le clic sur les boutons d'action d'ouvrir le modal
        if (e.target.classList.contains('btn-action') || e.target.closest('.btn-action')) {
            return;
        }

        // Récupération des données du jeu depuis l'attribut data-game
        const gameDataString = this.getAttribute('data-game');
        
        let gameData;
        try {
            gameData = JSON.parse(gameDataString);
        } catch (error) {
            return; // Erreur silencieuse
        }
        
        let html = '';
        
        // Gestion de la jaquette ou du placeholder dans le modal
        if (gameData.cover) {
            html += `<img src="${gameData.cover}" alt="${gameData.name}" style="width:220px;height:220px;object-fit:cover;border-radius:12px;box-shadow:0 2px 12px #7F39FB44;margin-bottom:1.2rem;">`;
        } else {
            html += createInlineCoverPlaceholder(gameData.name);
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
        
        const gameId = this.getAttribute('data-id');
        const gameName = this.getAttribute('data-name');
        const status = this.getAttribute('data-status');
        const playtime = this.getAttribute('data-playtime');
        const notes = this.getAttribute('data-notes');
        
        document.getElementById('editGameId').value = gameId;
        document.getElementById('editGameName').value = gameName;
        document.getElementById('editStatus').value = status || '';
        document.getElementById('editPlaytime').value = playtime || '';
        document.getElementById('editNotes').value = notes || '';
        
        document.getElementById('editGameModal').classList.add('active');
    });
});

// Fermeture des modaux
document.getElementById('closeEditModal').addEventListener('click', function() {
    document.getElementById('editGameModal').classList.remove('active');
});

document.getElementById('closeGameViewModal').addEventListener('click', function() {
    document.getElementById('gameViewModal').classList.remove('active');
});

window.addEventListener('click', function(e) {
    const editModal = document.getElementById('editGameModal');
    const viewModal = document.getElementById('gameViewModal');
    if (e.target === editModal) editModal.classList.remove('active');
    if (e.target === viewModal) viewModal.classList.remove('active');
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
            document.getElementById('editGameModal').classList.remove('active');
            setTimeout(() => location.reload(), 300);
        }
    });
});

// Redirection du bouton "Ajouter un jeu" vers la barre de recherche navbar
document.getElementById('openModal').addEventListener('click', function(e) {
    e.preventDefault();
    
    const navbarSearchInput = document.getElementById('navbarGameSearchInput');
    if (navbarSearchInput) {
        navbarSearchInput.focus();
        
        // Scroll vers le haut pour s'assurer que la navbar est visible
        window.scrollTo({
            top: 0,
            behavior: 'smooth'
        });
        
        // Effet visuel pour attirer l'attention
        navbarSearchInput.style.boxShadow = '0 0 10px #7F39FB';
        setTimeout(() => {
            navbarSearchInput.style.boxShadow = '';
        }, 2000);
    }
});

// Fonction locale pour le placeholder (OBLIGATOIRE)
function createInlineCoverPlaceholder(title) {
    return `
        <div style="width:220px;height:220px;background:linear-gradient(45deg,#1F1B2E,#2A1B3D);border-radius:12px;display:flex;flex-direction:column;justify-content:center;align-items:center;margin:0 auto 1.2rem;border:2px solid #7F39FB;">
            <div style="color:#9B5DE5;font-weight:bold;text-align:center;padding:1rem;font-size:1.2rem;">${title}</div>
            <div style="color:#BB86FC;opacity:0.8;font-size:1rem;">Aucune jaquette</div>
        </div>
    `;
}
</script>

</section>
<?php $this->endSection(); ?>