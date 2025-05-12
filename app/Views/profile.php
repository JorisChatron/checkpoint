<!-- filepath: /home/resu-50/www/formation.acc/checkpoint/app/Views/profile.php -->
<?= $this->extend('layouts/default') ?>

<?= $this->section('content') ?>
<h2>Mon Profil</h2>

<div class="profile-container">
    <img src="<?= base_url($user['profile_picture']) ?>" alt="Photo de profil" class="profile-picture">
    <form action="<?= base_url('profile/upload') ?>" method="post" enctype="multipart/form-data">
        <div class="file-upload">
            <label for="profile_picture" class="custom-file-label">Choisir un fichier</label>
            <input type="file" id="profile_picture" name="profile_picture" class="custom-file-input" accept="image/*">
        </div>
        <button type="submit">Mettre à jour</button>
    </form>
</div>
<?= $this->endSection() ?>