<?= $this->extend('layouts/default') ?>

<?= $this->section('content') ?>
<h2>Mon Profil</h2>

<div class="profile-container">
    <!-- L'image de profil sera remplacée par la preview -->
    <img id="preview" src="<?= base_url($user['profile_picture']) ?>" alt="Photo de profil" class="profile-picture">
    <form action="<?= base_url('profile/upload') ?>" method="post" enctype="multipart/form-data" class="profile-form">
        <div class="file-upload">
            <input type="file" id="profile_picture" name="profile_picture" class="custom-file-input" accept="image/*">
            <label for="profile_picture" class="custom-file-label">Choisir un fichier</label>
        </div>
        <button type="submit" class="full-width">Mettre à jour</button>
    </form>
</div>

<h3 class="profile-section-title">Statistiques</h3>
<div class="dashboard-stats">
    <div class="stat-card">Jeux possédés : <span><?= esc($stats['nbGames'] ?? 0) ?></span></div>
    <div class="stat-card">Jeux terminés : <span><?= esc($stats['nbFinished'] ?? 0) ?></span></div>
    <div class="stat-card">Temps de jeu global : <span><?= esc($stats['totalPlayTime'] ?? '0h') ?></span></div>
    <div class="stat-card">Jeux souhaités : <span><?= esc($stats['nbWishlist'] ?? 0) ?></span></div>
    <div class="stat-card">Jeux complétés : <span><?= esc($stats['nbCompleted'] ?? 0) ?></span></div>
</div>

<h3 class="profile-section-title">Mon Top 5</h3>
<button id="openTop5Modal" class="btn btn-primary" style="margin-bottom:1.2rem;">Choisir mon top 5</button>
<div class="dashboard-row" id="top5-profile">
    <?php if (!empty($top5)): ?>
        <?php foreach ($top5 as $game): ?>
            <?php
                $cover = !empty($game['cover']) ? $game['cover'] : '';
                $isExternal = (strpos($cover, 'http://') === 0 || strpos($cover, 'https://') === 0);
            ?>
            <div class="game-card-universal" data-id="<?= $game['id'] ?>" draggable="true">
                <?php if (!empty($cover)): ?>
                    <img src="<?= $isExternal ? $cover : base_url($cover) ?>" alt="<?= esc($game['name']) ?>" class="card-image">
                <?php else: ?>
                    <div class="game-cover-placeholder">
                        <div class="placeholder-title"><?= esc($game['name']) ?></div>
                        <div class="placeholder-text">Aucune jaquette</div>
                    </div>
                <?php endif; ?>
                
                <div class="card-info-overlay">
                    <div class="card-name"><?= esc($game['name']) ?></div>
                    <div class="card-date">#<?= $game['position'] ?><?= !empty($game['play_time']) ? ' • ' . $game['play_time'] . 'h' : '' ?></div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p class="empty-top5-message">Aucun jeu dans le top 5 pour l'instant.</p>
    <?php endif; ?>
</div>

<!-- Modal de sélection du top 5 -->
<div id="top5Modal" class="modal">
    <div class="modal-content top5-modal-content">
        <button class="modal-close" id="closeTop5Modal">&times;</button>
        <h2>Choisissez vos 5 jeux favoris</h2>
        <form id="top5Form">
            <div class="top5-games-container">
                <?php foreach ($allGames as $game): ?>
                    <?php
                        $cover = !empty($game['cover']) ? $game['cover'] : '';
                        $isExternal = (strpos($cover, 'http://') === 0 || strpos($cover, 'https://') === 0);
                    ?>
                    <div class="top5-game-item">
                        <input type="checkbox" name="top5[]" value="<?= $game['id'] ?>" id="game<?= $game['id'] ?>" class="top5-checkbox">
                        <?php if (!empty($cover)): ?>
                            <img src="<?= $isExternal ? $cover : base_url($cover) ?>" alt="<?= esc($game['name']) ?>" class="top5-game-cover">
                        <?php else: ?>
                            <div class="top5-game-cover-placeholder">
                                <div class="placeholder-title">?</div>
                            </div>
                        <?php endif; ?>
                        <label for="game<?= $game['id'] ?>" class="top5-game-label">
                            <span class="top5-position"></span>
                            <?= esc($game['name']) ?> <span class="top5-platform">[<?= esc($game['platform']) ?>]</span>
                        </label>
                    </div>
                <?php endforeach; ?>
            </div>
            <div class="top5-submit-container">
                <button type="submit" class="btn btn-primary">Valider</button>
            </div>
        </form>
    </div>
</div>

<h3 class="profile-section-title">Préférences</h3>
<form id="adultFilterForm" class="adult-filter-form">
    <label class="adult-filter-label">
        <input type="checkbox" id="showAdultCheckbox" name="show_adult" value="1" <?= session()->get('show_adult') ? 'checked' : '' ?>>
        Afficher le contenu adulte (18+)
    </label>
</form>

<!-- JavaScript pour la preview -->
<script>
    document.addEventListener('DOMContentLoaded', () => {
        // Gestion upload photo optimisée avec les utilitaires
        const fileInput = document.getElementById('profile_picture');
        const preview = document.getElementById('preview');
        const uploadForm = document.querySelector('form[action*="profile/upload"]');

        // Gestion du formulaire d'upload
        uploadForm?.addEventListener('submit', async (e) => {
            e.preventDefault();
            try {
                const result = await GameUtils.apiCall(e.target.action, 'POST', new FormData(e.target));
                if (result.success) {
                    GameUtils.showSuccess('Photo mise à jour !');
                    setTimeout(() => location.reload(), 300);
                } else {
                    GameUtils.showError(result.error || 'Erreur lors de la mise à jour');
                }
            } catch (error) {
                GameUtils.showError('Erreur réseau');
            }
        });

        // Preview de l'image (garder le code existant car utilise une classe)
        fileInput?.addEventListener('change', (event) => {
            const file = event.target.files[0];
            const label = document.querySelector('.custom-file-label');
            
            if (file) {
                if (file.size > 5 * 1024 * 1024) {
                    GameUtils.showError('Fichier trop volumineux (max 5MB)');
                    fileInput.value = '';
                    label.textContent = 'Choisir un fichier';
                    return;
                }
                label.textContent = file.name.length > 20 ? file.name.substring(0, 17) + '...' : file.name;
                const reader = new FileReader();
                reader.onload = (e) => preview.src = e.target.result;
                reader.readAsDataURL(file);
            } else {
                label.textContent = 'Choisir un fichier';
            }
        });

        // Drag & drop pour le top 5 optimisé
        const top5 = document.getElementById('top5-profile');
        let dragged, hasChanged = false;

        top5?.querySelectorAll('.game-card-universal').forEach(card => {
            card.addEventListener('dragstart', (e) => {
                dragged = card;
                card.style.opacity = '0.5';
            });
            
            card.addEventListener('dragend', (e) => {
                card.style.opacity = '';
                if (hasChanged) {
                    saveTop5Order();
                    hasChanged = false;
                }
            });
            
            card.addEventListener('dragover', (e) => e.preventDefault());
            
            card.addEventListener('drop', (e) => {
                e.preventDefault();
                if (dragged && dragged !== card) {
                    const cards = Array.from(top5.children);
                    if (cards.indexOf(dragged) < cards.indexOf(card)) {
                        card.after(dragged);
                    } else {
                        card.before(dragged);
                    }
                    hasChanged = true;
                }
            });
        });

        async function saveTop5Order() {
            const order = Array.from(top5.querySelectorAll('.game-card-universal')).map(card => card.dataset.id);
            try {
                const result = await fetch('/checkpoint/public/profile/updateTop5', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded', 'X-Requested-With': 'XMLHttpRequest' },
                    body: 'order[]=' + order.join('&order[]=')
                });
                const data = await result.json();
                if (!data.success) setTimeout(() => location.reload(), 300);
            } catch (error) {
                setTimeout(() => location.reload(), 300);
            }
        }

        // Modal top 5 optimisé avec utilitaires
        const openBtn = document.getElementById('openTop5Modal');
        
        openBtn?.addEventListener('click', () => ModalUtils.open('top5Modal'));
        ModalUtils.setupAutoClose('top5Modal', 'closeTop5Modal');

        // Top 5 selection optimisé
        let clickOrder = [];
        
        function updateTop5Positions() {
            document.querySelectorAll('.top5-position').forEach(span => span.textContent = '');
            clickOrder.forEach((gameId, idx) => {
                const checkbox = document.querySelector(`input[value="${gameId}"]`);
                if (checkbox?.checked) {
                    const label = checkbox.parentElement.querySelector('.top5-position');
                    if (label) label.textContent = `#${idx+1}`;
                }
            });
        }
        
        document.querySelectorAll('.top5-checkbox').forEach(cb => {
            cb.addEventListener('change', function() {
                const gameId = this.value;
                
                if (this.checked) {
                    if (document.querySelectorAll('.top5-checkbox:checked').length > 5) {
                        this.checked = false;
                        showError('Maximum 5 jeux');
                        return;
                    }
                    if (!clickOrder.includes(gameId)) clickOrder.push(gameId);
                } else {
                    clickOrder = clickOrder.filter(id => id !== gameId);
                }
                updateTop5Positions();
            });
        });

        // Gestion du formulaire top 5
        const top5Form = document.getElementById('top5Form');
        top5Form?.addEventListener('submit', async (e) => {
            e.preventDefault();
            if (clickOrder.length !== 5) {
                GameUtils.showError('Sélectionnez exactement 5 jeux');
                return;
            }
            
            try {
                const result = await GameUtils.apiCall('/checkpoint/public/profile/setTop5', 'POST', { top5: clickOrder });
                if (result.success) {
                    GameUtils.showSuccess('Top 5 mis à jour !');
                    setTimeout(() => location.reload(), 300);
                } else {
                    GameUtils.showError(result.error || 'Erreur mise à jour top 5');
                }
            } catch (error) {
                GameUtils.showError('Erreur réseau');
            }
        });

        // Préférence adulte optimisée
        document.getElementById('showAdultCheckbox')?.addEventListener('change', async function() {
            try {
                // Utilisation du format fetch classique car envoi form-encoded
                const result = await fetch('<?= base_url('profile/toggleAdult') ?>', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded', 'X-Requested-With': 'XMLHttpRequest' },
                    body: 'show_adult=' + (this.checked ? '1' : '0')
                });
                const data = await result.json();
                if (data.success) {
                    GameUtils.showSuccess('Préférence mise à jour !');
                    setTimeout(() => location.reload(), 300);
                } else {
                    GameUtils.showError('Erreur sauvegarde préférence');
                }
            } catch (error) {
                GameUtils.showError('Erreur réseau');
            }
        });
    });
</script>
<?= $this->endSection() ?>