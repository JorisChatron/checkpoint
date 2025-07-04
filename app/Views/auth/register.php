<?php $this->extend('layouts/default'); ?>

<?php $this->section('content'); ?>

<!-- ===== SECTION D'INSCRIPTION ===== -->
<!-- Cette section contient le formulaire d'inscription avec validation et gestion d'erreurs -->

<section class="auth-section">
    <!-- Titre principal de la page d'inscription -->
    <h2 class="auth-title">Inscription</h2>

    <!-- ===== FORMULAIRE D'INSCRIPTION ===== -->
    <!-- Formulaire POST vers la route 'register' avec protection CSRF -->
    <form action="<?= site_url('register') ?>" method="post" class="auth-form">
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

        <!-- ===== CHAMP ADRESSE EMAIL ===== -->
        <!-- Groupe de formulaire pour l'adresse email avec validation -->
        <div class="form-group">
            <!-- Label explicite pour l'accessibilité -->
            <label for="email">Adresse email :</label>
            
            <!-- Champ de saisie de l'adresse email -->
            <input type="email" id="email" name="email" required autocomplete="email" value="<?= old('email') ?>">
            
            <!-- ===== AFFICHAGE DES ERREURS DE VALIDATION ===== -->
            <!-- Affiche les erreurs de validation spécifiques au champ email -->
            <?php if (isset($validation) && $validation->getError('email')): ?>
                <div class="text-danger"><?= $validation->getError('email') ?></div>
            <?php endif; ?>
        </div>

        <!-- ===== CHAMP MOT DE PASSE ===== -->
        <!-- Groupe de formulaire pour le mot de passe avec validation -->
        <div class="form-group">
            <!-- Label explicite pour l'accessibilité -->
            <label for="password">Mot de passe :</label>
            
            <!-- Champ de saisie du mot de passe (masqué) -->
            <input type="password" id="password" name="password" required autocomplete="new-password">
            
            <!-- ===== AFFICHAGE DES ERREURS DE VALIDATION ===== -->
            <!-- Affiche les erreurs de validation spécifiques au champ password -->
            <?php if (isset($validation) && $validation->getError('password')): ?>
                <div class="text-danger"><?= $validation->getError('password') ?></div>
            <?php endif; ?>
        </div>

        <!-- ===== BOUTON DE SOUMISSION ===== -->
        <!-- Bouton pour soumettre le formulaire d'inscription -->
        <button type="submit" class="btn btn-primary mt-3" style="width:100%;">
            S'inscrire
        </button>
        
        <!-- ===== LIEN VERS LA CONNEXION ===== -->
        <!-- Lien pour rediriger vers la page de connexion -->
        <div class="auth-link">
            Déjà inscrit ? <a href="<?= site_url('login') ?>">Connexion</a>
        </div>
    </form>
</section>

<?php $this->endSection(); ?>




