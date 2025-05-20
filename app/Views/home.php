<?php

$this->extend('layouts/default');
$this->section('content');
?>

<section class="dashboard-home">
    <h2>Derniers jeux joués</h2>
    <div class="dashboard-row" id="last-played-games">
        <?php foreach (($lastPlayedGames ?? []) as $game): ?>
            <div class="game-card">
                <?php if (!empty($game['cover'])): ?>
                    <img src="<?= base_url($game['cover']) ?>" alt="<?= esc($game['name']) ?>" style="max-width:60px; max-height:60px; border-radius:8px; margin-right:10px;">
                <?php endif; ?>
                <span><?= esc($game['name']) ?></span>
            </div>
        <?php endforeach; ?>
    </div>

    <h2>Mon top 5</h2>
    <div class="dashboard-row" id="top5-games">
        <?php foreach (($top5 ?? []) as $game): ?>
            <div class="game-card">
                <?php if (!empty($game['cover'])): ?>
                    <img src="<?= base_url($game['cover']) ?>" alt="<?= esc($game['name']) ?>" style="max-width:60px; max-height:60px; border-radius:8px; margin-right:10px;">
                <?php endif; ?>
                <span><?= esc($game['name']) ?></span>
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

