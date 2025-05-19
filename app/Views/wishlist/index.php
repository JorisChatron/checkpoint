<?php

$this->extend('layouts/default');
$this->section('content');
?>

<h2>Ma Wishlist</h2>
<button id="openModal" class="btn btn-primary">Ajouter un jeu</button>

<?php if (!empty($wishlist)): ?>
<div class="carousel">
    <?php foreach ($wishlist as $game): ?>
        <div class="carousel-card">
            <div class="card-inner">
                <div class="card-front">
                    <div class="game-title-overlay"><?= esc($game['name']) ?></div>
                    <div class="card-actions">
                        <form class="delete-game-form" data-id="<?= $game['id'] ?>">
                            <button type="submit" class="btn btn-danger btn-delete-card" title="Supprimer">&times;</button>
                        </form>
                    </div>
                    <img src="<?= esc(!empty($game['cover']) ? $game['cover'] : '/public/images/default-cover.png') ?>" alt="Jaquette" class="carousel-cover">
                </div>
                <div class="card-back">
                    <div class="card-back-content">
                        <p><strong>Plateforme :</strong> <?= esc($game['platform']) ?></p>
                        <p><strong>Année :</strong> <?= esc($game['release_date']) ?></p>
                        <p><strong>Genre :</strong> <?= esc($game['category']) ?></p>
                        <p><strong>Statut :</strong> <?= esc($game['status']) ?></p>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>
<?php else: ?>
    <p>Votre wishlist est vide.</p>
<?php endif; ?>

<!-- Modal -->
<div id="addGameModal" class="modal">
    <div class="modal-content">
        <button class="modal-close" id="closeModal">&times;</button>
        <h2>Ajouter un jeu à ma wishlist</h2>
        <form id="addGameForm" action="/checkpoint/public/wishlist/add" method="POST">
            <?= csrf_field() ?>
            <div class="form-group">
                <label for="searchGame">Recherchez votre jeu :</label>
                <input type="text" id="searchGame" name="searchGame" placeholder="Recherchez votre jeu" required>
                <!-- Conteneur pour les suggestions -->
                <ul id="suggestions" class="suggestions-list"></ul>
            </div>

            <!-- Ligne pour Plateforme et Année de sortie -->
            <div class="form-row">
                <div class="form-group">
                    <label for="platform">Plateforme :</label>
                    <input type="text" id="platform" name="platform" placeholder="Plateforme" required>
                </div>
                <div class="form-group">
                    <label for="releaseYear">Année de sortie :</label>
                    <input type="text" id="releaseYear" name="releaseYear" placeholder="Année de sortie" readonly>
                </div>
            </div>

            <div class="form-group">
                <label for="genre">Genre :</label>
                <input type="text" id="genre" name="genre" placeholder="Genre" readonly>
            </div>

            <div class="form-group form-row">
                <div class="form-field">
                    <label for="cover">Jaquette :</label>
                    <input type="text" id="cover" name="cover" placeholder="URL de la jaquette" readonly>
                </div>
                <div class="form-preview">
                    <img id="coverPreview" src="" alt="Preview de la jaquette" class="hidden">
                </div>
            </div>

            <div class="form-group">
                <label for="status">Statut :</label>
                <select name="status" id="status" class="form-control" required>
                    <option value="souhaité">Souhaité</option>
                    <option value="acheté">Acheté</option>
                    <option value="joué">Joué</option>
                </select>
            </div>

            <button type="submit">Ajouter à ma wishlist</button>
        </form>
    </div>
</div>

<?php $this->endSection(); ?>
