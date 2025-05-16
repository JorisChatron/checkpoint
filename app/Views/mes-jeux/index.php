<?php $this->extend('layouts/default'); ?>
<?php $this->section('content'); ?>

<h2>Mes Jeux</h2>
<button id="openModal" class="btn btn-primary">Ajouter un jeu</button>

<?php if (!empty($games)): ?>
<div class="carousel">
    <?php foreach ($games as $game): ?>
        <div class="carousel-card">
            <h3><?= esc($game['name']) ?></h3>
            <p><strong>Plateforme :</strong> <?= esc($game['platform']) ?></p>
            <p><strong>Année :</strong> <?= esc($game['release_date']) ?></p>
            <p><strong>Genre :</strong> <?= esc($game['category']) ?></p>
            <p><strong>Temps de jeu :</strong> <?= esc($game['play_time']) ?> h</p>
            <!-- Ajoute d'autres infos si besoin -->
        </div>
    <?php endforeach; ?>
</div>
<?php else: ?>
    <p>Vous n'avez pas encore ajouté de jeux.</p>
<?php endif; ?>

<!-- Modal -->
<div id="addGameModal" class="modal">
    <div class="modal-content">
        <button class="modal-close" id="closeModal">&times;</button>
        <h2>Ajouter un jeu</h2>
        <form id="addGameForm">
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

            <!-- Ligne pour Statut et Temps de jeu -->
            <div class="form-row-status">
                <div class="form-group">
                    <label for="status">Statut :</label>
                    <select name="status" id="status" class="form-control" required>
                        <option value="">Choisir un statut</option>
                        <option value="en cours">En cours</option>
                        <option value="termine">Terminé</option>
                        <option value="complete">Complété</option>
                        <option value="abandonne">Abandonné</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="playtime">Temps de jeu :</label>
                    <input type="text" name="playtime" id="playtime" class="form-control" placeholder="Temps de jeu (en h)">
                </div>
            </div>

            <div class="form-group">
                <label for="notes">Notes :</label>
                <textarea id="notes" name="notes" placeholder="Notes"></textarea>
            </div>

            <button type="submit">Ajouter le jeu</button>
        </form>
    </div>
</div>

<?php $this->endSection(); ?>