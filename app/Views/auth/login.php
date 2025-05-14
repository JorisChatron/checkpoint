<?php $this->extend('layouts/default'); ?>

<?php $this->section('content'); ?>

<section class="section">
    <h2>Connexion</h2>

    <form action="<?= site_url('login') ?>" method="post">
        <?= csrf_field() ?> <!-- Ajout du champ CSRF pour la sécurité -->

        <div class="form-group">
            <label for="username">Nom d'utilisateur :</label>
            <input type="text" name="username" id="username" class="form-control" value="<?= set_value('username') ?>" required>
            <?php if (isset($validation) && $validation->getError('username')): ?>
                <div class="text-danger"><?= $validation->getError('username') ?></div>
            <?php endif; ?>
        </div>

        <div class="form-group">
            <label for="password">Mot de passe :</label>
            <input type="password" name="password" id="password" class="form-control" required>
            <?php if (isset($validation) && $validation->getError('password')): ?>
                <div class="text-danger"><?= $validation->getError('password') ?></div>
            <?php endif; ?>
        </div>

        <?php if (isset($error)) : ?>
            <div class="text-danger"><?= $error ?></div>
        <?php endif; ?>

        <button type="submit" class="btn btn-primary mt-3">Se connecter</button>
    </form>
</section>

<?php $this->endSection(); ?>

