<?php

$this->extend('layouts/default');
$this->section('content');
?>

<h2>Mes Jeux</h2>
<p>Vous n'avez pas encore ajouté de jeux.</p>
<button onclick="window.location.href='<?= base_url('mes-jeux/ajouter') ?>'">Ajouter un jeu</button>

<?php $this->endSection(); ?>