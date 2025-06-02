<?php

$this->extend('layouts/default');
$this->section('content');
?>

<?php if (!session()->get('user_id')): ?>
<section class="dashboard-home" style="max-width:800px;margin:2.5rem auto 2rem auto;">
    <h1 style="color:#9B5DE5;text-align:center;font-size:2.2rem;margin-bottom:2.2rem;">Bienvenue sur Checkpoint !</h1>
    <p style="font-size:1.15rem;text-align:center;margin-bottom:2.5rem;color:#E0F7FA;">
        <strong>Checkpoint</strong> est votre espace personnel pour gérer, suivre et partager votre passion du jeu vidéo.<br>
        Collectionneur, joueur occasionnel ou hardcore gamer&nbsp;: organisez votre univers vidéoludique en toute simplicité&nbsp;!
    </p>
    <ul style="font-size:1.08rem;line-height:1.7;color:#BB86FC;margin-bottom:2.5rem;max-width:600px;margin-left:auto;margin-right:auto;">
        <li>📚 Lister tous vos jeux, toutes plateformes confondues</li>
        <li>🎮 Suivre votre progression (statut, temps de jeu, jeux terminés ou complétés)</li>
        <li>⭐ Créer votre Top 5 de jeux favoris</li>
        <li>📝 Gérer votre wishlist pour ne jamais oublier un titre à acheter ou à essayer</li>
        <li>🔎 Filtrer et rechercher facilement dans votre collection</li>
        <li>📊 Visualiser vos statistiques de joueur</li>
    </ul>
    <h2 style="color:#9B5DE5;text-align:center;margin:2.5rem 0 1.2rem 0;">Aperçu des fonctionnalités</h2>
    <div style="margin-bottom:2.5rem;">
        <h3 style="color:#BB86FC;text-align:center;font-size:1.3rem;margin-bottom:1.2rem;">Votre collection de jeux</h3>
        <img src="<?= base_url('images/demo-mesjeux.png') ?>" alt="Aperçu Mes Jeux" style="max-width:100%;border-radius:12px;box-shadow:0 4px 24px #7F39FB44;display:block;margin:0 auto 1.5rem auto;">
        <p style="text-align:center;color:#E0F7FA;">Gérez tous vos jeux, ajoutez-en de nouveaux, suivez votre progression, et classez-les selon vos envies.</p>
    </div>
    <div style="margin-bottom:2.5rem;">
        <h3 style="color:#BB86FC;text-align:center;font-size:1.3rem;margin-bottom:1.2rem;">Votre wishlist</h3>
        <img src="<?= base_url('images/demo-wishlist.png') ?>" alt="Aperçu Wishlist" style="max-width:100%;border-radius:12px;box-shadow:0 4px 24px #7F39FB44;display:block;margin:0 auto 1.5rem auto;">
        <p style="text-align:center;color:#E0F7FA;">Gardez une trace des jeux que vous souhaitez acquérir ou découvrir, et ne ratez plus jamais une sortie importante !</p>
    </div>
    <div class="home-btns-container">
        <a href="<?= base_url('register') ?>" class="home-btn">Créer un compte</a>
        <a href="<?= base_url('login') ?>" class="home-btn" style="background:linear-gradient(90deg,#00E5FF 80%,#9B5DE5 100%);color:#1E1E2F;border-color:#00E5FF;">Se connecter</a>
    </div>
</section>
<?php else: ?>
<section class="dashboard-home">
    <?php if (isset($username)): ?>
        <h1 style="color:#9B5DE5;text-align:center;font-size:2.2rem;margin-bottom:2.2rem;">Bienvenue, <?= esc($username) ?> !</h1>
    <?php endif; ?>
    <h2>Derniers jeux ajoutés</h2>
    <div class="dashboard-row" id="last-played-games">
        <?php foreach (($lastPlayedGames ?? []) as $game): ?>
            <div class="game-card" style="position:relative; padding:0;"
                data-name="<?= esc($game['name']) ?>"
                data-cover="<?= !empty($game['cover']) ? esc((strpos($game['cover'], 'http') === 0 ? $game['cover'] : base_url($game['cover']))): base_url('/public/images/default-cover.png') ?>"
                data-platform="<?= esc($game['platform'] ?? '') ?>"
                data-release="<?= esc($game['release_date'] ?? '') ?>"
                data-genre="<?= esc($game['category'] ?? '') ?>"
                data-status="<?= esc($game['status'] ?? '') ?>"
                data-playtime="<?= esc($game['play_time'] ?? '') ?>"
                data-notes="<?= esc($game['notes'] ?? '') ?>"
            >
                <?php
                    $cover = !empty($game['cover']) ? $game['cover'] : '';
                    $isExternal = (strpos($cover, 'http://') === 0 || strpos($cover, 'https://') === 0);
                ?>
                <?php if (!empty($cover)): ?>
                    <img src="<?= $isExternal ? $cover : base_url($cover) ?>" alt="<?= esc($game['name']) ?>" style="width:100%; height:100%; object-fit:cover; border-radius:10px; display:block;">
                <?php else: ?>
                    <div class="game-cover-placeholder size-large" style="width:100%; height:100%; border-radius:10px;">
                        <div class="placeholder-title"><?= esc($game['name']) ?></div>
                        <div class="placeholder-text">Aucune jaquette</div>
                    </div>
                <?php endif; ?>
                <div style="position:absolute;top:0;left:0;width:100%;z-index:2;text-align:center;">
                    <span style="display:block;padding:0.5rem 0 0.2rem 0;font-weight:bold;color:#9B5DE5;font-size:1.1rem;text-shadow:0 2px 8px #000;letter-spacing:1px;background:rgba(31,27,46,0.7);border-radius:12px 12px 0 0;">
                        <?= esc($game['name']) ?>
                    </span>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <h2>Mon top 5</h2>
    <div class="dashboard-row" id="top5-games">
        <?php foreach (($top5 ?? []) as $idx => $game): ?>
            <div class="game-card" style="position:relative; padding:0;"
                data-name="<?= esc($game['name']) ?>"
                data-cover="<?= !empty($game['cover']) ? esc((strpos($game['cover'], 'http') === 0 ? $game['cover'] : base_url($game['cover']))): base_url('/public/images/default-cover.png') ?>"
                data-platform="<?= esc($game['platform'] ?? '') ?>"
                data-release="<?= esc($game['release_date'] ?? '') ?>"
                data-genre="<?= esc($game['category'] ?? '') ?>"
                data-status="<?= esc($game['status'] ?? '') ?>"
                data-playtime="<?= esc($game['play_time'] ?? '') ?>"
                data-notes="<?= esc($game['notes'] ?? '') ?>"
            >
                <?php
                    $cover = !empty($game['cover']) ? $game['cover'] : '';
                    $isExternal = (strpos($cover, 'http://') === 0 || strpos($cover, 'https://') === 0);
                ?>
                <?php if (!empty($cover)): ?>
                    <img src="<?= $isExternal ? $cover : base_url($cover) ?>" alt="<?= esc($game['name']) ?>" style="width:100%; height:100%; object-fit:cover; border-radius:10px; display:block;">
                <?php else: ?>
                    <div class="game-cover-placeholder size-large" style="width:100%; height:100%; border-radius:10px;">
                        <div class="placeholder-title">#<?= $idx+1 ?> <?= esc($game['name']) ?></div>
                        <div class="placeholder-text">Aucune jaquette</div>
                    </div>
                <?php endif; ?>
                <div style="position:absolute;top:0;left:0;width:100%;z-index:2;text-align:center;">
                    <span style="display:block;padding:0.5rem 0 0.2rem 0;font-weight:bold;color:#9B5DE5;font-size:1.1rem;text-shadow:0 2px 8px #000;letter-spacing:1px;background:rgba(31,27,46,0.7);border-radius:12px 12px 0 0;">
                        #<?= $idx+1 ?> <?= esc($game['name']) ?>
                    </span>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <h2>Statistiques</h2>
    <div class="dashboard-stats">
        <div class="stat-card">Jeux possédés : <span><?= esc($stats['owned'] ?? 0) ?></span></div>
        <div class="stat-card">Jeux terminés : <span><?= esc($stats['finished'] ?? 0) ?></span></div>
        <div class="stat-card">Temps de jeu global : <span><?= esc($stats['playtime'] ?? '0h') ?></span></div>
        <div class="stat-card">Jeux attendus : <span><?= esc($stats['expected'] ?? 0) ?></span></div>
        <div class="stat-card">Jeux complétés : <span><?= esc($stats['completed'] ?? 0) ?></span></div>
    </div>
</section>
<?php endif; ?>

<!-- Modal aperçu jeu Accueil (identique à Mes Jeux) -->
<div id="gameViewModal" class="modal">
    <div class="modal-content" id="gameViewModalContent" style="max-width:600px;position:relative;">
        <button class="modal-close" id="closeGameViewModal">&times;</button>
        <div id="gameViewModalBody" style="min-height:200px;text-align:center;">
            <span style="color:#BB86FC;">Chargement...</span>
        </div>
    </div>
</div>
<script>
document.querySelectorAll('.dashboard-row .game-card').forEach(card => {
    card.addEventListener('click', function(e) {
        // Empêche le clic sur le bouton supprimer d'ouvrir le modal (sécurité, même si pas présent ici)
        if (e.target.classList.contains('btn-action')) return;
        const name = this.dataset.name || '';
        const cover = this.dataset.cover || '';
        const platform = this.dataset.platform || '';
        const release = this.dataset.release || '';
        const genre = this.dataset.genre || '';
        const status = this.dataset.status || '';
        const playtime = this.dataset.playtime || '';
        const notes = this.dataset.notes || '';
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
document.getElementById('closeGameViewModal').addEventListener('click', function() {
    document.getElementById('gameViewModal').classList.remove('active');
});
window.addEventListener('click', function(e) {
    const modal = document.getElementById('gameViewModal');
    if (e.target === modal) modal.classList.remove('active');
});
</script>

<?php $this->endSection(); ?>

