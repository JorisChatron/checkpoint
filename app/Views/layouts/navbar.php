<?php helper('url'); ?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Checkpoint</title>

<link rel="stylesheet" href="<?= base_url('css/style.css') ?>">


<script src="<?= base_url('public/js/script.js') ?>"></script>
</head>
<body>
<header>
  <nav class="navbar">
    <div class="navbar-section navbar-links">
      <a href="<?= base_url() ?>">Accueil</a>
      <a href="<?= base_url('profile') ?>">Profil</a>
      <a href="<?= base_url('wishlist') ?>">Wishlist</a>
      <a href="<?= base_url('avis') ?>">Avis</a>
    </div>

    <div class="logo-container">
      <img src="<?= base_url('images/logo.png') ?>" alt="Logo" class="logo">
    </div>

    <div class="navbar-section auth-links">
      <a href="<?= base_url('login') ?>">Connexion</a>
      <a href="<?= base_url('register') ?>">Inscription</a>
    </div>

    <button class="burger">☰</button>
  </nav>
</header>

