<?= $this->extend('layouts/default') ?>

<?= $this->section('content') ?>
<h2>Mon Profil</h2>

<div class="profile-container">
    <!-- L'image de profil sera remplacée par la preview -->
    <img id="preview" src="<?= base_url($user['profile_picture']) ?>" alt="Photo de profil" class="profile-picture" style="max-width: 100px; max-height: 100px; border-radius: 50%; object-fit: cover;">
    <form action="<?= base_url('profile/upload') ?>" method="post" enctype="multipart/form-data">
        <div class="file-upload">
            <label for="profile_picture" class="custom-file-label">Choisir un fichier</label>
            <input type="file" id="profile_picture" name="profile_picture" class="custom-file-input" accept="image/*">
        </div>
        <button type="submit">Mettre à jour</button>
    </form>
</div>

<h3 class="profile-section-title">Statistiques</h3>
<div class="dashboard-stats">
    <div class="stat-card">
        <span><?= esc($stats['nbGames']) ?></span><br>Jeux possédés
    </div>
    <div class="stat-card">
        <span><?= esc($stats['nbWishlist']) ?></span><br>En wishlist
    </div>
    <div class="stat-card">
        <span><?= esc($stats['totalPlayTime'] ?? 0) ?></span><br>Heures de jeu
    </div>
    <div class="stat-card">
        <span><?= esc($stats['nbFinished']) ?></span><br>Jeux terminés
    </div>
</div>

<h3 class="profile-section-title">Mon Top 5</h3>
<div class="dashboard-row" id="top5-profile">
    <?php if (!empty($top5)): ?>
        <?php foreach ($top5 as $game): ?>
            <div class="game-card" draggable="true" data-id="<?= $game['id'] ?>">
                <?php if (!empty($game['cover'])): ?>
                    <img src="<?= base_url($game['cover']) ?>" alt="<?= esc($game['name']) ?>" style="max-width:60px; max-height:60px; border-radius:8px; margin-right:10px;">
                <?php endif; ?>
                <div>
                    <span style="font-weight:bold; color:#9B5DE5;">#<?= esc($game['position']) ?></span> <span><?= esc($game['name']) ?></span><br>
                    <span style="font-size:0.95em; color:#BB86FC;">[<?= esc($game['platform']) ?>, <?= esc($game['release_date']) ?>, <?= esc($game['category']) ?>]</span>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p style="color:#9B5DE5;">Aucun jeu dans le top 5 pour l'instant.</p>
    <?php endif; ?>
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
    });
</script>
<?= $this->endSection() ?>