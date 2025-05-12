<?= $this->extend('layouts/default') ?>

<?= $this->section('content') ?>
<h2>Mon Profil</h2>

<div class="profile-container">
    <!-- L'image de profil sera remplacée par la preview -->
    <img id="preview" src="<?= base_url($user['profile_picture']) ?>" alt="Photo de profil" class="profile-picture" style="max-width: 100px; max-height: 100px; border-radius: 50%; object-fit: cover;">
    <form action="<?= base_url('profile/upload') ?>" method="post" enctype="multipart/form-data">
        <div class="file-upload">
            <label for="profile_picture" class="custom-file-label">Choisir un fichier</label>
            <input type="file" id="profile_picture" name="profile_picture" class="custom-file-input" accept="image/*">
        </div>
        <button type="submit">Mettre à jour</button>
    </form>
</div>

<!-- JavaScript pour la preview -->
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const fileInput = document.getElementById('profile_picture');
        const preview = document.getElementById('preview');

        fileInput.addEventListener('change', (event) => {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();

                reader.onload = (e) => {
                    preview.src = e.target.result; // Met à jour la source de l'image
                };

                reader.readAsDataURL(file); // Lit le fichier comme une URL de données
            }
        });
    });
</script>
<?= $this->endSection() ?>