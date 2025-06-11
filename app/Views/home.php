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
            <div class="game-card-universal" 
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
                    <img src="<?= $isExternal ? $cover : base_url($cover) ?>" alt="<?= esc($game['name']) ?>" class="card-image">
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
                        <?= esc($game['status']) ?><?= !empty($game['play_time']) ? ' • ' . $game['play_time'] . 'h' : '' ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <h2>Mon top 5</h2>
    <div class="dashboard-row" id="top5-games">
        <?php foreach (($top5 ?? []) as $idx => $game): ?>
            <div class="game-card-universal" 
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
                    <img src="<?= $isExternal ? $cover : base_url($cover) ?>" alt="<?= esc($game['name']) ?>" class="card-image">
                <?php else: ?>
                    <div class="game-cover-placeholder">
                        <div class="placeholder-title">#<?= $idx+1 ?> <?= esc($game['name']) ?></div>
                        <div class="placeholder-text">Aucune jaquette</div>
                    </div>
                <?php endif; ?>
                
                <!-- Info overlay en bas avec titre et rang -->
                <div class="card-info-overlay">
                    <div class="card-name">
                        <?= esc($game['name']) ?>
                    </div>
                    <div class="card-date">
                        #<?= $idx+1 ?><?= !empty($game['play_time']) ? ' • ' . $game['play_time'] . 'h' : '' ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <h2>Statistiques</h2>
    <div class="dashboard-stats">
        <div class="stat-card">Jeux possédés : <span><?= esc($stats['owned'] ?? 0) ?></span></div>
        <div class="stat-card">Jeux terminés : <span><?= esc($stats['finished'] ?? 0) ?></span></div>
        <div class="stat-card">Temps de jeu global : <span><?= esc($stats['playtime'] ?? '0h') ?></span></div>
        <div class="stat-card">Jeux souhaités : <span><?= esc($stats['wishlist'] ?? 0) ?></span></div>
        <div class="stat-card">Jeux complétés : <span><?= esc($stats['completed'] ?? 0) ?></span></div>
    </div>
</section>
<?php endif; ?>

<!-- Modal aperçu jeu Accueil -->
<div id="gameViewModal" class="modal">
    <div class="modal-content" id="gameViewModalContent" style="max-width:600px;position:relative;">
        <button class="modal-close" id="closeGameViewModal">&times;</button>
        <div id="gameViewModalBody" style="min-height:200px;text-align:center;">
            <span style="color:#BB86FC;">Chargement...</span>
        </div>
    </div>
</div>

<?php $this->endSection(); ?>

