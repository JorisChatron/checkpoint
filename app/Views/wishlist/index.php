<?php

$this->extend('layouts/default');
$this->section('content');
?>

<h2>Ma Wishlist</h2>
<ul>
<?php foreach ($wishlist as $item): ?>
    <li><?= esc($item['name']) ?> (<?= esc($item['platform']) ?>) - <?= esc($item['status']) ?></li>
<?php endforeach; ?>
</ul>

<?php $this->endSection(); ?>
