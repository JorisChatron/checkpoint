<?php

$this->extend('layouts/default');
$this->section('content');
?>

<h2>Avis des joueurs</h2>
<ul>
<?php foreach ($reviews as $review): ?>
    <li>
        <strong><?= esc($review['username']) ?></strong> a noté
        <em><?= esc($review['name']) ?></em> : <?= esc($review['rating']) ?>/10
        <br>Commentaire : <?= esc($review['comment']) ?>
    </li>
<?php endforeach; ?>
</ul>

<?php $this->endSection(); ?>
