<?php
$this->extend('layouts/default');
$this->section('content');
?>
<section class="dashboard-home" style="max-width:1100px;margin:2.5rem auto 2rem auto;">
    <h1 style="color:#9B5DE5;text-align:center;font-size:2rem;margin-bottom:2.2rem;">Sorties de la semaine</h1>
    <div style="text-align:center;margin-bottom:2rem;">
        <?php
            $weekStr = $year . '-W' . str_pad($week, 2, '0', STR_PAD_LEFT);
            $startDate = new DateTime($start);
            $endDate = new DateTime($end);
        ?>
        <div style="display:inline-flex;align-items:center;gap:1rem;background:rgba(31,27,46,0.92);padding:1rem 1.5rem;border-radius:12px;box-shadow:0 2px 10px #7F39FB22;">
            <button id="openWeekPicker" class="home-btn" style="width:auto;font-size:1.1rem;padding:0.7rem 2.2rem;">Choisir une semaine</button>
            <input type="text" id="hiddenWeekInput" style="display:none;">
            <span style="color:#BB86FC;font-size:1.1rem;">|</span>
            <span style="color:#E0F7FA;font-size:1.1rem;">
                Du <b><?= $startDate->format('d/m/Y') ?></b> au <b><?= $endDate->format('d/m/Y') ?></b>
            </span>
        </div>
    </div>
    <div style="text-align:center;margin-bottom:2rem;">
        <span style="color:#BB86FC;font-size:1.2rem;vertical-align:middle;">
            Du <b><?= date('d/m/Y', strtotime($start)) ?></b> au <b><?= date('d/m/Y', strtotime($end)) ?></b>
        </span>
    </div>
    <?php if (!empty($error)): ?>
        <div class="text-danger" style="text-align:center;">Erreur : <?= esc($error) ?></div>
    <?php endif; ?>
    <?php
    // Récupération des genres présents dans les jeux de la semaine
    $allGenres = [];
    foreach ($games as $g) {
        if (!empty($g['genres'])) {
            foreach ($g['genres'] as $genre) {
                $allGenres[$genre['slug']] = $genre['name'];
            }
        }
    }
    // On trie les genres par ordre alpha
    asort($allGenres);
    ?>
    <form id="genre-filter-form" style="background:rgba(31,27,46,0.92);padding:1.2rem 1.5rem;border-radius:12px;box-shadow:0 2px 10px #7F39FB22;margin-bottom:2.2rem;max-width:900px;margin-left:auto;margin-right:auto;">
        <div style="display:flex;flex-wrap:wrap;gap:1.2rem;align-items:center;justify-content:center;">
            <?php foreach ($allGenres as $slug => $name): ?>
                <label style="color:#BB86FC;font-size:1.05rem;display:flex;align-items:center;gap:0.4em;">
                    <input type="checkbox" class="genre-checkbox" value="<?= esc($slug) ?>" <?= (stripos($name, 'adult') === false && stripos($name, 'adulte') === false) ? 'checked' : '' ?>>
                    <?= esc($name) ?>
                </label>
            <?php endforeach; ?>
            <button type="button" id="checkAllGenres" class="home-btn" style="width:auto;font-size:0.98em;padding:0.5em 1.2em;">Tout cocher</button>
            <button type="button" id="uncheckAllGenres" class="home-btn" style="width:auto;font-size:0.98em;padding:0.5em 1.2em;">Tout décocher</button>
        </div>
    </form>
    <div class="dashboard-row" style="flex-wrap:wrap;gap:2rem;justify-content:center;">
        <?php if (empty($games)): ?>
            <p style="color:#9B5DE5;text-align:center;width:100%;">Aucune sortie prévue pour cette semaine.</p>
        <?php else: ?>
            <?php foreach ($games as $game): ?>
                <div class="game-card calendrier-card" 
                     data-game-id="<?= esc($game['id']) ?>"
                     style="width:210px;height:320px;flex-direction:column;justify-content:flex-start;align-items:center;overflow:hidden;cursor:pointer;">
                    <img src="<?= !empty($game['background_image']) ? esc($game['background_image']) : base_url('images/default-cover.png') ?>" alt="<?= esc($game['name']) ?>" style="width:100%;height:180px;object-fit:cover;border-radius:10px 10px 0 0;">
                    <div style="padding:1rem 0.7rem 0.5rem 0.7rem;width:100%;text-align:center;">
                        <span style="display:block;font-weight:bold;color:#9B5DE5;font-size:1.08rem;text-shadow:0 2px 8px #000;letter-spacing:1px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">
                            <?= esc($game['name']) ?>
                        </span>
                        <span style="color:#BB86FC;font-size:1rem;display:block;margin-top:0.7rem;">
                            Sortie : <?= !empty($game['released']) ? date('d/m/Y', strtotime($game['released'])) : 'Date inconnue' ?>
                        </span>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
    <?php if ($count > 50): ?>
        <div style="text-align:center;margin-top:2.5rem;">
            <?php $nbPages = ceil($count/50); ?>
            <?php if ($page > 1): ?>
                <a href="<?= base_url('calendrier/'.$year.'/'.$week.'/page/'.($page-1)) ?>" class="home-btn" style="width:140px;font-size:1rem;margin-right:1.2rem;">&larr; Précédent</a>
            <?php endif; ?>
            <span style="color:#BB86FC;font-size:1.1rem;">Page <?= $page ?> / <?= $nbPages ?></span>
            <select id="pageSelector" style="width:120px;display:inline-block;margin:0 1.2rem;padding:0.5rem;background:rgba(31,27,46,0.92);color:#BB86FC;border:1px solid #7F39FB;border-radius:8px;cursor:pointer;">
                <?php for($i = 1; $i <= $nbPages; $i++): ?>
                    <option value="<?= $i ?>" <?= $i == $page ? 'selected' : '' ?>>Page <?= $i ?></option>
                <?php endfor; ?>
            </select>
            <?php if ($page < $nbPages): ?>
                <a href="<?= base_url('calendrier/'.$year.'/'.$week.'/page/'.($page+1)) ?>" class="home-btn" style="width:140px;font-size:1rem;margin-left:1.2rem;">Suivant &rarr;</a>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <!-- Modal détails jeu -->
    <div id="gameModal" class="modal">
        <div class="modal-content" id="gameModalContent" style="max-width:600px;position:relative;">
            <button class="modal-close" id="closeGameModal">&times;</button>
            <div id="gameModalBody" style="min-height:200px;text-align:center;">
                <span style="color:#BB86FC;">Chargement...</span>
            </div>
        </div>
    </div>
</section>
<!-- Flatpickr CSS/JS placés en haut pour garantir le chargement -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/plugins/weekSelect/weekSelect.js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/plugins/weekSelect/weekSelect.css">
<script>
// Gestion du sélecteur de semaine
function updateWeek(weekStr) {
    if(weekStr) {
        const [year, week] = weekStr.split('-W');
        window.location.href = `/checkpoint/public/calendrier/${year}/${week}`;
    }
}

// Gestion du sélecteur de page
var pageSelector = document.getElementById('pageSelector');
if (pageSelector) {
    pageSelector.addEventListener('change', function() {
        const selectedPage = this.value;
        const currentUrl = window.location.pathname;
        const baseUrl = currentUrl.split('/page/')[0];
        window.location.href = `${baseUrl}/page/${selectedPage}`;
    });
}

// Modal détails jeu RAWG
const modal = document.getElementById('gameModal');
const modalBody = document.getElementById('gameModalBody');
const closeModalBtn = document.getElementById('closeGameModal');

function openGameModal(gameId) {
    modal.classList.add('active');
    modalBody.innerHTML = '<span style="color:#BB86FC;">Chargement...</span>';
    fetch(`https://api.rawg.io/api/games/${gameId}?key=ff6f7941c211456c8806541638fdfaff`)
        .then(res => res.json())
        .then(game => {
            const desc = game.description_raw ? game.description_raw : '<i>Aucune description disponible.</i>';
            modalBody.innerHTML = `
                <img src="${game.background_image || '<?= base_url('images/default-cover.png') ?>'}" alt="${game.name}" style="width:220px;height:220px;object-fit:cover;border-radius:12px;box-shadow:0 2px 12px #7F39FB44;margin-bottom:1.2rem;">
                <h2 style="color:#9B5DE5;margin-bottom:0.7rem;">${game.name}</h2>
                <div style="color:#BB86FC;font-size:1.05rem;margin-bottom:0.7rem;">Sortie : ${game.released ? (new Date(game.released)).toLocaleDateString('fr-FR') : 'Date inconnue'}</div>
                <div id="game-desc" style="color:#E0F7FA;font-size:1rem;margin-bottom:1.2rem;max-height:120px;overflow:auto;">
                    ${desc}
                </div>
                <div style="color:#BB86FC;font-size:0.98rem;margin-bottom:0.5rem;">
                    <b>Développeur(s) :</b> ${game.developers && game.developers.length ? game.developers.map(d=>d.name).join(', ') : 'Inconnu'}<br>
                    <b>Éditeur(s) :</b> ${game.publishers && game.publishers.length ? game.publishers.map(d=>d.name).join(', ') : 'Inconnu'}<br>
                    <b>Plateformes :</b> ${game.platforms && game.platforms.length ? game.platforms.map(p=>p.platform.name).join(', ') : 'Inconnu'}<br>
                    <b>Genres :</b> ${game.genres && game.genres.length ? game.genres.map(g=>g.name).join(', ') : 'Inconnu'}
                </div>
                <a href="${game.website || '#'}" target="_blank" style="color:#00E5FF;text-decoration:underline;">Site officiel</a>
                <br><br>
                <button id="addToWishlistBtn" style="margin-top:1rem;padding:0.7rem 2.2rem;background:#7F39FB;color:#fff;border:none;border-radius:10px;font-size:1.1rem;cursor:pointer;">Ajouter à la wishlist</button>
                <div id="wishlistMsg" style="margin-top:1rem;font-size:1rem;"></div>
            `;
            // Ajout du handler pour le bouton wishlist
            setTimeout(function() {
                var btn = document.getElementById('addToWishlistBtn');
                if(btn) {
                    btn.onclick = function() {
                        btn.disabled = true;
                        btn.textContent = 'Ajout en cours...';
                        fetch('/checkpoint/public/wishlist/add', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            },
                            body: JSON.stringify({
                                game_id: game.id,
                                status: 'souhaité'
                            })
                        })
                        .then(r => r.json())
                        .then(data => {
                            document.getElementById('wishlistMsg').textContent = data.success ? 'Ajouté à la wishlist !' : (data.message || 'Erreur lors de l\'ajout.');
                            btn.style.display = 'none';
                        })
                        .catch(() => {
                            document.getElementById('wishlistMsg').textContent = 'Erreur lors de l\'ajout.';
                            btn.disabled = false;
                            btn.textContent = 'Ajouter à la wishlist';
                        });
                    }
                }
            }, 100);
        })
        .catch(() => {
            modalBody.innerHTML = '<span style="color:#FF6F61;">Erreur lors du chargement des infos du jeu.</span>';
        });
}

document.querySelectorAll('.calendrier-card').forEach(card => {
    card.addEventListener('click', function() {
        openGameModal(this.dataset.gameId);
    });
});
if(closeModalBtn && modal) {
    closeModalBtn.addEventListener('click', () => modal.classList.remove('active'));
    window.addEventListener('click', (e) => {
        if(e.target === modal) modal.classList.remove('active');
    });
}

// Filtrage par genre
function updateGenreFilter() {
    const checked = Array.from(document.querySelectorAll('.genre-checkbox:checked')).map(cb => cb.value);
    document.querySelectorAll('.calendrier-card').forEach(card => {
        const genres = (card.dataset.genres || '').split(',');
        const show = genres.some(g => checked.includes(g));
        card.style.display = show ? '' : 'none';
    });
}
document.querySelectorAll('.genre-checkbox').forEach(cb => {
    cb.addEventListener('change', updateGenreFilter);
});
document.getElementById('checkAllGenres').addEventListener('click', function() {
    document.querySelectorAll('.genre-checkbox').forEach(cb => { cb.checked = true; });
    updateGenreFilter();
});
document.getElementById('uncheckAllGenres').addEventListener('click', function() {
    document.querySelectorAll('.genre-checkbox').forEach(cb => { cb.checked = false; });
    updateGenreFilter();
});
// Ajout des genres sur chaque carte pour le filtrage
<?php foreach ($games as $game): ?>
    document.querySelector('.calendrier-card[data-game-id="<?= $game['id'] ?>"]').dataset.genres = "<?= isset($game['genres']) ? implode(',', array_map(function($g){return $g['slug'];}, $game['genres'])) : '' ?>";
<?php endforeach; ?>
// Filtrage initial
updateGenreFilter();

// Flatpickr pour choisir une semaine (mode semaine)
if (typeof flatpickr !== 'undefined') {
    flatpickr("#hiddenWeekInput", {
        dateFormat: "Y-m-d",
        defaultDate: "<?= $startDate->format('Y-m-d') ?>",
        plugins: [new weekSelect({})],
        onChange: function(selectedDates, dateStr, instance) {
            if(selectedDates.length) {
                // flatpickr weekSelect retourne le premier jour de la semaine sélectionnée
                const date = selectedDates[0];
                const year = date.getFullYear();
                const week = getWeekNumber(date);
                window.location.href = `/checkpoint/public/calendrier/${year}/${week}`;
            }
        },
        disableMobile: true,
        locale: {
            firstDayOfWeek: 1
        }
    });
    document.getElementById('openWeekPicker').addEventListener('click', function() {
        const fp = document.getElementById('hiddenWeekInput')._flatpickr;
        if(fp) fp.open();
        else alert('Le calendrier ne peut pas s\'ouvrir (flatpickr non initialisé).');
    });
} else {
    alert('Erreur : flatpickr n\'a pas pu être chargé.');
}
// Calcul du numéro de semaine ISO
function getWeekNumber(date) {
    const d = new Date(Date.UTC(date.getFullYear(), date.getMonth(), date.getDate()));
    const dayNum = d.getUTCDay() || 7;
    d.setUTCDate(d.getUTCDate() + 4 - dayNum);
    const yearStart = new Date(Date.UTC(d.getUTCFullYear(),0,1));
    return String(Math.ceil((((d - yearStart) / 86400000) + 1)/7)).padStart(2,'0');
}
</script>
<?php $this->endSection(); ?> 