<?php $this->extend('layouts/default'); ?>

<?php $this->section('content'); ?>

<section class="section">
    <h2>Connexion</h2>

    <!-- Affichage des erreurs de validation -->
    <?php if (isset($validation)) : ?>
        <div class="alert alert-danger">
            <?= $validation->listErrors() ?>
        </div>
    <?php endif; ?>

    <?php if (isset($error)) : ?>
        <div class="alert alert-danger">
            <?= $error ?>
        </div>
    <?php endif; ?>

    <form action="<?= site_url('login') ?>" method="post">
        <?= csrf_field() ?> <!-- Ajout du champ CSRF pour la sécurité -->

        <div class="form-group">
            <label for="username">Nom d'utilisateur :</label>
            <input type="text" name="username" id="username" class="form-control" value="<?= set_value('username') ?>" required>
            <!-- Affichage de l'erreur pour le champ username -->
            <?php if (isset($validation) && $validation->getError('username')): ?>
                <div class="text-danger"><?= $validation->getError('username') ?></div>
            <?php endif; ?>
        </div>

        <div class="form-group">
            <label for="password">Mot de passe :</label>
            <input type="password" name="password" id="password" class="form-control" required>
            <!-- Affichage de l'erreur pour le champ password -->
            <?php if (isset($validation) && $validation->getError('password')): ?>
                <div class="text-danger"><?= $validation->getError('password') ?></div>
            <?php endif; ?>
        </div>

        <button type="submit" class="btn btn-primary mt-3">Se connecter</button>
    </form>
</section>

<?php $this->endSection(); ?>

