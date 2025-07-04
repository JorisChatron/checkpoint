<?php $this->extend('layouts/default'); ?>

<?php $this->section('content'); ?>

<!-- ===== SECTION D'AUTHENTIFICATION ===== -->
<!-- Cette section contient le formulaire de connexion avec validation et gestion d'erreurs -->

<section class="auth-section">
    <!-- Titre principal de la page de connexion -->
    <h2 class="auth-title">Connexion</h2>
      
    <!-- ===== FORMULAIRE DE CONNEXION ===== -->
    <!-- Formulaire POST vers la route 'login' avec protection CSRF -->
    <form action="<?= site_url('login') ?>" method="post" class="auth-form">
        <?= csrf_field() ?> <!-- Protection CSRF pour sécuriser le formulaire contre les attaques cross-site -->

        <!-- ===== CHAMP NOM D'UTILISATEUR ===== -->
        <!-- Groupe de formulaire pour le nom d'utilisateur avec validation -->
        <div class="form-group">
            <!-- Label explicite pour l'accessibilité -->
            <label for="username">Nom d'utilisateur :</label>
            
            <!-- Champ de saisie du nom d'utilisateur -->
            <input type="text" id="username" name="username" required autocomplete="username" value="<?= old('username') ?>">
            
            <!-- ===== AFFICHAGE DES ERREURS DE VALIDATION ===== -->
            <!-- Affiche les erreurs de validation spécifiques au champ username -->
            <?php if (isset($validation) && $validation->getError('username')): ?>
                <div class="text-danger"><?= $validation->getError('username') ?></div>
            <?php endif; ?>
        </div>

        <!-- ===== CHAMP MOT DE PASSE ===== -->
        <!-- Groupe de formulaire pour le mot de passe avec validation -->
        <div class="form-group">
            <!-- Label explicite pour l'accessibilité -->
            <label for="password">Mot de passe :</label>
            
            <!-- Champ de saisie du mot de passe (masqué) -->
            <input type="password" id="password" name="password" required autocomplete="current-password">
            
            <!-- ===== AFFICHAGE DES ERREURS DE VALIDATION ===== -->
            <!-- Affiche les erreurs de validation spécifiques au champ password -->
            <?php if (isset($validation) && $validation->getError('password')): ?>
                <div class="text-danger"><?= $validation->getError('password') ?></div>
            <?php endif; ?>
        </div>

        <!-- ===== AFFICHAGE DES ERREURS GÉNÉRALES ===== -->
        <!-- Affiche les erreurs de connexion (identifiants incorrects) -->
        <?php if (isset($error)) : ?>
            <div class="text-danger" style="margin-bottom:1rem; text-align:center; font-weight:bold;">
                <?= $error ?>
            </div>
        <?php endif; ?>

        <!-- ===== BOUTON DE SOUMISSION ===== -->
        <!-- Bouton pour soumettre le formulaire de connexion -->
        <button type="submit" class="btn btn-primary mt-3" style="width:100%;">
            Se connecter
        </button>
        
        <!-- ===== LIEN VERS L'INSCRIPTION ===== -->
        <!-- Lien pour rediriger vers la page d'inscription -->
        <div class="auth-link">
            Pas encore de compte ? <a href="<?= site_url('register') ?>">Inscription</a>
        </div>
    </form>
</section>

<?php $this->endSection(); ?> 

