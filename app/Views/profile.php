<?= $this->extend('layouts/default') ?>

<?= $this->section('content') ?>
<h2>Mon Profil</h2>

<div class="profile-container">
    <!-- L'image de profil sera remplacée par la preview -->
    <img id="preview" src="<?= base_url($user['profile_picture']) ?>" alt="Photo de profil" class="profile-picture" style="max-width: 100px; max-height: 100px; border-radius: 50%; object-fit: cover; margin-bottom: 1.2rem;">
    <form action="<?= base_url('profile/upload') ?>" method="post" enctype="multipart/form-data" style="display:flex;flex-direction:column;align-items:center;width:100%;max-width:320px;margin:0 auto;">
        <div class="file-upload" style="width:100%;margin-bottom:0.7rem;">
            <label for="profile_picture" class="custom-file-label" style="width:100%;box-sizing:border-box;">Choisir un fichier</label>
            <input type="file" id="profile_picture" name="profile_picture" class="custom-file-input" accept="image/*" style="width:100%;">
        </div>
        <button type="submit" style="width:100%;box-sizing:border-box;">Mettre à jour</button>
    </form>
</div>

<h3 class="profile-section-title">Statistiques</h3>
<div class="dashboard-stats">
    <div class="stat-card">Jeux possédés : <span><?= esc($stats['nbGames'] ?? 0) ?></span></div>
    <div class="stat-card">Jeux terminés : <span><?= esc($stats['nbFinished'] ?? 0) ?></span></div>
    <div class="stat-card">Temps de jeu global : <span><?= esc($stats['totalPlayTime'] ?? '0h') ?></span></div>
    <div class="stat-card">Jeux attendus : <span><?= esc($stats['nbExpected'] ?? 0) ?></span></div>
    <div class="stat-card">Jeux complétés : <span><?= esc($stats['nbCompleted'] ?? 0) ?></span></div>
</div>

<h3 class="profile-section-title">Mon Top 5</h3>
<button id="openTop5Modal" class="btn btn-primary" style="margin-bottom:1.2rem;">Choisir mon top 5</button>
<div class="dashboard-row" id="top5-profile">
    <?php if (!empty($top5)): ?>
        <?php foreach ($top5 as $game): ?>
            <?php
                $cover = !empty($game['cover']) ? $game['cover'] : '/public/images/default-cover.png';
                $isExternal = (strpos($cover, 'http://') === 0 || strpos($cover, 'https://') === 0);
            ?>
            <div class="game-card" style="position:relative; padding:0;">
                <img src="<?= $isExternal ? $cover : base_url($cover) ?>" alt="<?= esc($game['name']) ?>" style="width:100%; height:100%; object-fit:cover; border-radius:10px; display:block;">
                <div style="position:absolute;top:0;left:0;width:100%;z-index:2;text-align:center;">
                    <span style="display:block;padding:0.5rem 0 0.2rem 0;font-weight:bold;color:#9B5DE5;font-size:1.1rem;text-shadow:0 2px 8px #000;letter-spacing:1px;background:rgba(31,27,46,0.7);border-radius:12px 12px 0 0;">
                        #<?= esc($game['position']) ?> <?= esc($game['name']) ?>
                    </span>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p style="color:#9B5DE5;">Aucun jeu dans le top 5 pour l'instant.</p>
    <?php endif; ?>
</div>

<!-- Modal de sélection du top 5 -->
<div id="top5Modal" class="modal">
    <div class="modal-content" style="max-width:500px;">
        <button class="modal-close" id="closeTop5Modal">&times;</button>
        <h2>Choisissez vos 5 jeux favoris</h2>
        <form id="top5Form">
            <div style="max-height:320px;overflow-y:auto;">
                <?php foreach ($allGames as $game): ?>
                    <?php
                        $cover = !empty($game['cover']) ? $game['cover'] : '/public/images/default-cover.png';
                        $isExternal = (strpos($cover, 'http://') === 0 || strpos($cover, 'https://') === 0);
                    ?>
                    <div style="display:flex;align-items:center;margin-bottom:0.7rem;">
                        <input type="checkbox" name="top5[]" value="<?= $game['id'] ?>" id="game<?= $game['id'] ?>" class="top5-checkbox" style="margin-right:10px;">
                        <img src="<?= $isExternal ? $cover : base_url($cover) ?>" alt="<?= esc($game['name']) ?>" style="width:40px;height:40px;object-fit:cover;border-radius:6px;margin-right:10px;">
                        <label for="game<?= $game['id'] ?>" style="cursor:pointer;">
                            <span class="top5-position" style="font-weight:bold;color:#00E5FF;margin-right:7px;"></span>
                            <?= esc($game['name']) ?> <span style="color:#BB86FC;font-size:0.95em;">[<?= esc($game['platform']) ?>]</span>
                        </label>
                    </div>
                <?php endforeach; ?>
            </div>
            <div style="margin-top:1.2rem;text-align:center;">
                <button type="submit" class="btn btn-primary">Valider</button>
            </div>
        </form>
    </div>
</div>

<!-- JavaScript pour la preview -->
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const fileInput = document.getElementById('profile_picture');
        const preview = document.getElementById('preview');
        const uploadForm = document.querySelector('form[action*="profile/upload"]');

        // Gestion de l'upload de photo
        uploadForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            
            fetch(this.action, {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    showToast('success', 'Photo de profil mise à jour avec succès !');
                    setTimeout(() => location.reload(), 1200);
                } else {
                    showToast('error', data.error || 'Erreur lors de la mise à jour de la photo');
                }
            })
            .catch(error => {
                showToast('error', 'Erreur lors de l\'envoi de la photo');
            });
        });

        fileInput.addEventListener('change', (event) => {
            const file = event.target.files[0];
            if (file) {
                if (file.size > 5 * 1024 * 1024) { // 5MB max
                    showToast('error', 'La taille du fichier ne doit pas dépasser 5MB');
                    fileInput.value = '';
                    return;
                }
                const reader = new FileReader();
                reader.onload = (e) => {
                    preview.src = e.target.result;
                };
                reader.readAsDataURL(file);
            }
        });

        // Drag & drop pour le top 5
        const top5 = document.getElementById('top5-profile');
        let dragged;
        let hasChanged = false;

        if (top5) {
            top5.querySelectorAll('.game-card').forEach(card => {
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
                
                card.addEventListener('dragover', (e) => {
                    e.preventDefault();
                });
                
                card.addEventListener('drop', (e) => {
                    e.preventDefault();
                    if (dragged && dragged !== card) {
                        if (Array.from(top5.children).indexOf(dragged) < Array.from(top5.children).indexOf(card)) {
                            card.after(dragged);
                        } else {
                            card.before(dragged);
                        }
                        hasChanged = true;
                    }
                });
            });
        }

        // Sauvegarde l'ordre du top 5
        function saveTop5Order() {
            const order = Array.from(top5.querySelectorAll('.game-card')).map(card => card.dataset.id);
            
            fetch('/checkpoint/public/profile/updateTop5', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: 'order[]=' + order.join('&order[]=')
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    showToast('success', 'Top 5 mis à jour avec succès !');
                } else {
                    showToast('error', 'Erreur lors de la mise à jour du top 5');
                    setTimeout(() => location.reload(), 1200); // Recharge en cas d'erreur
                }
            })
            .catch(error => {
                showToast('error', 'Erreur lors de la sauvegarde du top 5');
                setTimeout(() => location.reload(), 1200);
            });
        }

        // Modal top 5
        const openTop5Modal = document.getElementById('openTop5Modal');
        const closeTop5Modal = document.getElementById('closeTop5Modal');
        const top5Modal = document.getElementById('top5Modal');
        const top5Form = document.getElementById('top5Form');

        if(openTop5Modal && closeTop5Modal && top5Modal) {
            openTop5Modal.addEventListener('click', () => {
                top5Modal.classList.add('active');
            });
            closeTop5Modal.addEventListener('click', () => {
                top5Modal.classList.remove('active');
            });
            window.addEventListener('click', (e) => {
                if(e.target === top5Modal) top5Modal.classList.remove('active');
            });
        }

        // Limite à 5 cases cochées
        const checkboxes = document.querySelectorAll('.top5-checkbox');
        checkboxes.forEach(cb => {
            cb.addEventListener('change', () => {
                const checked = document.querySelectorAll('.top5-checkbox:checked');
                if(checked.length > 5) {
                    cb.checked = false;
                    showToast('error', 'Vous ne pouvez choisir que 5 jeux.');
                }
            });
        });

        // Soumission du top 5
        if(top5Form) {
            top5Form.addEventListener('submit', function(e) {
                e.preventDefault();
                const checked = Array.from(document.querySelectorAll('.top5-checkbox:checked')).map(cb => cb.value);
                if(checked.length !== 5) {
                    showToast('error', 'Veuillez sélectionner exactement 5 jeux.');
                    return;
                }
                fetch('/checkpoint/public/profile/setTop5', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({ top5: checked })
                })
                .then(res => res.json())
                .then(data => {
                    if(data.success) {
                        showToast('success', 'Top 5 mis à jour !');
                        setTimeout(() => location.reload(), 1200);
                    } else {
                        showToast('error', data.error || 'Erreur lors de la mise à jour du top 5');
                    }
                })
                .catch(() => showToast('error', 'Erreur lors de la requête.'));
            });
        }

        // Ajout JS pour afficher la position de sélection du top 5
        function updateTop5Positions() {
            const checked = Array.from(document.querySelectorAll('.top5-checkbox:checked'));
            document.querySelectorAll('.top5-position').forEach(span => span.textContent = '');
            checked.forEach((cb, idx) => {
                const label = cb.parentElement.querySelector('.top5-position');
                if(label) label.textContent = `#${idx+1}`;
            });
        }
        document.querySelectorAll('.top5-checkbox').forEach(cb => {
            cb.addEventListener('change', updateTop5Positions);
        });
    });
</script>
<?= $this->endSection() ?>