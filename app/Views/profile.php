<?php

$this->extend('layouts/default');
$this->section('content');
?>

<h2>Profil de <?= esc($user['username']) ?></h2>
<p>Email : <?= esc($user['email']) ?></p>

<?php $this->endSection(); ?>

