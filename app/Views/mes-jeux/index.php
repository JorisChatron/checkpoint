<?php

$this->extend('layouts/default');
$this->section('content');
?>

<h2>Mes Jeux</h2>
<p>Vous n'avez pas encore ajouté de jeux.</p>
<button id="openModal" class="btn btn-primary">Ajouter un jeu</button>

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
                    <select id="status" name="status">
                        <option value="En cours">En cours</option>
                        <option value="Terminé">Terminé</option>
                        <option value="Complété">Complété</option>
                        <option value="Abandonné">Abandonné</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="playtime">Temps de jeu :</label>
                    <input type="number" id="playtime" name="playtime" placeholder="Temps de jeu (en heures)">
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