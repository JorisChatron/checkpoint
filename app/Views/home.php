<?php

$this->extend('layouts/default');
$this->section('content');
?>

<section class="section">
    <h2>Bienvenue sur ma page d'accueil</h2>
    <p>Découvrez une collection de jeux passionnants et explorez mes recommandations personnelles.</p>
</section>
<section class="section">
    <h2>Jeux en Vedette</h2>
    <p>Cliquez sur les cartes pour plus de détails.</p>
    <!-- Ajoutez ici des cartes de jeux cliquables -->
</section>
<section class="section">
    <h2>Mon Top 5 des Jeux</h2>
    <ul>
        <li>Jeu 1</li>
        <li>Jeu 2</li>
        <li>Jeu 3</li>
        <li>Jeu 4</li>
        <li>Jeu 5</li>
    </ul>
</section>

<?php $this->endSection(); ?>

