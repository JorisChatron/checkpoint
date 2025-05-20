<?php $this->extend('layouts/default'); ?>

<?php $this->section('content'); ?>

<section class="auth-section">
    <h2 class="auth-title">Connexion</h2>

    <form action="<?= site_url('login') ?>" method="post" class="auth-form">
        <?= csrf_field() ?> <!-- Ajout du champ CSRF pour la sécurité -->

        <div class="form-group">
            <label for="username">Nom d'utilisateur :</label>
            <input type="text" name="username" id="username" class="form-control" value="<?= set_value('username') ?>" required autocomplete="username">
            <?php if (isset($validation) && $validation->getError('username')): ?>
                <div class="text-danger"><?= $validation->getError('username') ?></div>
            <?php endif; ?>
        </div>

        <div class="form-group">
            <label for="password">Mot de passe :</label>
            <input type="password" name="password" id="password" class="form-control" required autocomplete="current-password">
            <?php if (isset($validation) && $validation->getError('password')): ?>
                <div class="text-danger"><?= $validation->getError('password') ?></div>
            <?php endif; ?>
        </div>

        <?php if (isset($error)) : ?>
            <div class="text-danger" style="margin-bottom:1rem; text-align:center; font-weight:bold;"><?= $error ?></div>
        <?php endif; ?>

        <button type="submit" class="btn btn-primary mt-3" style="width:100%;">Se connecter</button>
        <div class="auth-link">Pas encore de compte ? <a href="<?= site_url('register') ?>">Inscription</a></div>
    </form>
</section>

<?php $this->endSection(); ?>

