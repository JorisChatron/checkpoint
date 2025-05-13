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
                <label for="gameName">Nom du jeu :</label>
                <input type="text" id="gameName" name="gameName" class="form-control" placeholder="Nom du jeu" required>
            </div>
            <div class="form-group">
                <label for="platform">Plateforme :</label>
                <input type="text" id="platform" name="platform" class="form-control" placeholder="Plateforme" required>
            </div>
            <div class="form-group">
                <label for="releaseYear">Année de sortie :</label>
                <input type="number" id="releaseYear" name="releaseYear" class="form-control" placeholder="Année de sortie">
            </div>
            <button type="submit" class="btn btn-primary">Ajouter</button>
        </form>
    </div>
</div>

<?php $this->endSection(); ?>