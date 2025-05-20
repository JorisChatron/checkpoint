<?php $this->extend('layouts/default'); ?>
<?php $this->section('content'); ?>

<h2>Mes Jeux</h2>
<button id="openModal" class="btn btn-primary">Ajouter un jeu</button>

<!-- Barre de filtres -->
<form class="filters-bar" method="get" action="">
    <select name="platform" onchange="this.form.submit()">
        <option value="">Plateforme</option>
        <?php foreach ($platforms as $p): ?>
            <option value="<?= esc($p['platform']) ?>" <?= ($selectedPlatform == $p['platform']) ? 'selected' : '' ?>><?= esc($p['platform']) ?></option>
        <?php endforeach; ?>
    </select>
    <select name="status" onchange="this.form.submit()">
        <option value="">Statut</option>
        <?php foreach ($statuses as $s): ?>
            <?php if ($s['status']): ?>
                <option value="<?= esc($s['status']) ?>" <?= ($selectedStatus == $s['status']) ? 'selected' : '' ?>><?= esc(ucfirst($s['status'])) ?></option>
            <?php endif; ?>
        <?php endforeach; ?>
    </select>
    <select name="genre" onchange="this.form.submit()">
        <option value="">Genre</option>
        <?php foreach ($genres as $g): ?>
            <?php if ($g['category']): ?>
                <option value="<?= esc($g['category']) ?>" <?= ($selectedGenre == $g['category']) ? 'selected' : '' ?>><?= esc($g['category']) ?></option>
            <?php endif; ?>
        <?php endforeach; ?>
    </select>
    <noscript><button type="submit">Filtrer</button></noscript>
</form>

<?php if (!empty($games)): ?>
<div class="dashboard-row">
    <?php foreach ($games as $game): ?>
        <div class="game-card carousel-card">
            <div class="card-front">
                <div style="position:absolute;top:0;left:0;width:100%;z-index:2;text-align:center;">
                    <span style="display:block;padding:0.5rem 0 0.2rem 0;font-weight:bold;color:#9B5DE5;font-size:1.1rem;text-shadow:0 2px 8px #000;letter-spacing:1px;background:rgba(31,27,46,0.7);border-radius:12px 12px 0 0;">
                        <?= esc($game['name']) ?>
                    </span>
                </div>
                <button type="button" class="btn-action delete" title="Supprimer" data-id="<?= $game['id'] ?>">&times;</button>
                <div class="card-cover-container" style="height:100%;">
                    <img src="<?= esc(!empty($game['cover']) ? $game['cover'] : '/public/images/default-cover.png') ?>" alt="Jaquette" class="card-cover">
                </div>
            </div>
            <div class="card-back">
                <div style="padding: 1rem; color: #E0F7FA; width: 100%;">
                    <strong>Plateforme :</strong> <?= esc($game['platform']) ?><br>
                    <strong>Année :</strong> <?= esc($game['release_date']) ?><br>
                    <strong>Genre :</strong> <?= esc($game['category']) ?><br>
                    <strong>Statut :</strong> <?= esc($game['status']) ?><br>
                    <strong>Temps de jeu :</strong> <?= esc($game['play_time']) ?> h<br>
                    <strong>Notes :</strong> <?= esc($game['notes']) ?>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>
<?php else: ?>
    <p class="wishlist-empty-message">Vous n'avez pas encore ajouté de jeux.</p>
<?php endif; ?>

<!-- Modal -->
<div id="addGameModal" class="modal">
    <div class="modal-content">
        <button class="modal-close" id="closeModal">&times;</button>
        <h2>Ajouter un jeu</h2>
        <form id="addGameForm">
            <div class="form-group">
                <label for="searchGame">Recherchez votre jeu :</label>
                <input type="text" id="searchGame" name="searchGame" placeholder="Commencez à taper le nom du jeu..." required autocomplete="off">
                <ul id="suggestions" class="suggestions-list"></ul>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label for="platform">Plateforme :</label>
                    <input type="text" id="platform" name="platform" placeholder="Plateforme" required readonly>
                </div>
                <div class="form-group">
                    <label for="releaseYear">Année de sortie :</label>
                    <input type="text" id="releaseYear" name="releaseYear" placeholder="Année" readonly>
                </div>
            </div>
            <div class="form-group">
                <label for="genre">Genre :</label>
                <input type="text" id="genre" name="genre" placeholder="Genre" readonly>
            </div>
            <div class="form-group">
                <label for="cover">Jaquette :</label>
                <input type="text" id="cover" name="cover" placeholder="URL de la jaquette" readonly>
                <div class="form-preview cover-preview-container hidden" id="coverPreviewContainer">
                    <img id="coverPreview" src="" alt="Aperçu de la jaquette" class="hidden">
                    <span>Aperçu de la jaquette</span>
                </div>
            </div>
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