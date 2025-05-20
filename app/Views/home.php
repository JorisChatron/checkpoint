<?php

$this->extend('layouts/default');
$this->section('content');
?>

<section class="dashboard-home">
    <h2>Derniers jeux ajoutés</h2>
    <div class="dashboard-row" id="last-played-games">
        <?php foreach (($lastPlayedGames ?? []) as $game): ?>
            <div class="game-card" style="position:relative; padding:0;">
                <?php
                    $cover = !empty($game['cover']) ? $game['cover'] : '/public/images/default-cover.png';
                    $isExternal = (strpos($cover, 'http://') === 0 || strpos($cover, 'https://') === 0);
                ?>
                <img src="<?= $isExternal ? $cover : base_url($cover) ?>" alt="<?= esc($game['name']) ?>" style="width:100%; height:100%; object-fit:cover; border-radius:10px; display:block;">
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
            <div class="game-card" style="position:relative; padding:0;">
                <?php
                    $cover = !empty($game['cover']) ? $game['cover'] : '/public/images/default-cover.png';
                    $isExternal = (strpos($cover, 'http://') === 0 || strpos($cover, 'https://') === 0);
                ?>
                <img src="<?= $isExternal ? $cover : base_url($cover) ?>" alt="<?= esc($game['name']) ?>" style="width:100%; height:100%; object-fit:cover; border-radius:10px; display:block;">
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

<?php $this->endSection(); ?>

