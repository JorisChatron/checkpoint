<?php
$this->extend('layouts/default');
$this->section('content');
?>
<link rel="stylesheet" href="<?= base_url('css/default-cover.css') ?>">

<section class="dashboard-home" style="max-width:1100px;margin:2.5rem auto 2rem auto;">
    <h1 style="color:#9B5DE5;text-align:center;font-size:2rem;margin-bottom:2.2rem;">Sorties de la semaine</h1>
    <div style="text-align:center;margin-bottom:2rem;">
        <?php
            $weekStr = $year . '-W' . str_pad($week, 2, '0', STR_PAD_LEFT);
            $startDate = new DateTime($start);
            $endDate = new DateTime($end);
            
            // Calcul de la semaine précédente avec gestion des changements d'année
            $prevWeek = $week - 1;
            $prevYear = $year;
            if ($prevWeek < 1) {
                $prevYear = $year - 1;
                // Calculer le nombre de semaines dans l'année précédente
                $tempDate = new DateTime($prevYear . '-12-31');
                $prevWeek = (int)$tempDate->format('W');
                // Correction pour le cas où le 31 décembre fait partie de la semaine 1 de l'année suivante
                if ($prevWeek == 1) {
                    $tempDate->modify('-7 days');
                    $prevWeek = (int)$tempDate->format('W');
                }
            }
            
            // Calcul de la semaine suivante avec gestion des changements d'année
            $nextWeek = $week + 1;
            $nextYear = $year;
            // Calculer le nombre de semaines dans l'année courante
            $tempDate = new DateTime($year . '-12-31');
            $maxWeeksInCurrentYear = (int)$tempDate->format('W');
            // Correction pour le cas où le 31 décembre fait partie de la semaine 1 de l'année suivante
            if ($maxWeeksInCurrentYear == 1) {
                $tempDate->modify('-7 days');
                $maxWeeksInCurrentYear = (int)$tempDate->format('W');
            }
            
            if ($nextWeek > $maxWeeksInCurrentYear) {
                $nextWeek = 1;
                $nextYear = $year + 1;
            }
        ?>
        <div class="week-selector-container">
            <!-- Ligne de navigation de semaine -->
            <div class="week-navigation">
                <!-- Bouton semaine précédente -->
                <a href="<?= base_url('calendrier/'.$prevYear.'/'.$prevWeek) ?>" 
                   class="home-btn week-nav-btn" 
                   style="width:45px;height:45px;display:flex;align-items:center;justify-content:center;font-size:1.4rem;padding:0;border-radius:50%;background:linear-gradient(45deg,#7F39FB,#9B5DE5);color:#fff;text-decoration:none;transition:transform 0.2s,box-shadow 0.2s;" 
                   title="Semaine précédente"
                   onmouseover="this.style.transform='scale(1.05)';this.style.boxShadow='0 4px 16px #7F39FB44';"
                   onmouseout="this.style.transform='scale(1)';this.style.boxShadow='0 2px 8px #7F39FB22';">
                    &#8249;
                </a>
                
                <button id="openWeekPicker" class="home-btn week-picker-btn" style="width:auto;font-size:1.1rem;padding:0.7rem 2.2rem;">Choisir une semaine</button>
                <input type="text" id="hiddenWeekInput" style="display:none;">
                
                <span class="week-separator" style="color:#BB86FC;font-size:1.1rem;">|</span>
                
                <!-- Bouton semaine suivante -->
                <a href="<?= base_url('calendrier/'.$nextYear.'/'.$nextWeek) ?>" 
                   class="home-btn week-nav-btn" 
                   style="width:45px;height:45px;display:flex;align-items:center;justify-content:center;font-size:1.4rem;padding:0;border-radius:50%;background:linear-gradient(45deg,#00E5FF,#BB86FC);color:#1E1E2F;text-decoration:none;transition:transform 0.2s,box-shadow 0.2s;" 
                   title="Semaine suivante"
                   onmouseover="this.style.transform='scale(1.05)';this.style.boxShadow='0 4px 16px #00E5FF44';"
                   onmouseout="this.style.transform='scale(1)';this.style.boxShadow='0 2px 8px #00E5FF22';">
                    &#8250;
                </a>
            </div>
            
            <!-- Texte de la période -->
            <span class="week-period-text" style="color:#E0F7FA;font-size:1.1rem;">
                Du <b><?= $startDate->format('d/m/Y') ?></b> au <b><?= $endDate->format('d/m/Y') ?></b>
            </span>
        </div>
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
    <div style="text-align:center;margin-bottom:1.5rem;">
        <div class="calendar-search-container">
            <input type="text" id="searchGame" placeholder="Rechercher un jeu..." class="calendar-search-input">
            <button id="clearSearch" class="home-btn" style="width:auto;font-size:0.98rem;padding:0.7rem 1.2rem;">Effacer</button>
        </div>
    </div>
    <form id="genre-filter-form" style="background:rgba(31,27,46,0.92);padding:1.2rem 1.5rem;border-radius:12px;box-shadow:0 2px 10px #7F39FB22;margin-bottom:2.2rem;max-width:900px;margin-left:auto;margin-right:auto;">
        <div class="genre-filter-box">
            <div class="checkbox-list">
                <?php foreach ($allGenres as $slug => $name): ?>
                    <label style="color:#BB86FC;font-size:1.05rem;display:flex;align-items:center;gap:0.4em;">
                        <input type="checkbox" class="genre-checkbox" value="<?= esc($slug) ?>" <?= (stripos($name, 'adult') === false && stripos($name, 'adulte') === false) ? 'checked' : '' ?>>
                        <?= esc($name) ?>
                    </label>
                <?php endforeach; ?>
            </div>
            <div class="button-row">
                <button type="button" id="checkAllGenres" class="home-btn" style="width:auto;font-size:0.98em;padding:0.5em 1.2em;">Tout cocher</button>
                <button type="button" id="uncheckAllGenres" class="home-btn" style="width:auto;font-size:0.98em;padding:0.5em 1.2em;">Tout décocher</button>
            </div>
        </div>
    </form>
    <div class="dashboard-row">
        <?php if (empty($games)): ?>
            <p style="color:#9B5DE5;text-align:center;width:100%;grid-column: 1 / -1;">Aucune sortie prévue pour cette semaine.</p>
        <?php else: ?>
            <?php foreach ($games as $game): ?>
                <div class="game-card-universal" 
                     data-game-id="<?= esc($game['id']) ?>">
                    <?php if (!empty($game['background_image'])): ?>
                        <img src="<?= esc($game['background_image']) ?>" 
                             alt="<?= esc($game['name']) ?>"
                             class="card-image">
                    <?php else: ?>
                        <div class="game-cover-placeholder">
                            <div class="placeholder-title"><?= esc($game['name']) ?></div>
                            <div class="placeholder-text">Jaquette indisponible</div>
                        </div>
                    <?php endif; ?>
                    
                    <!-- Info overlay en bas avec date de sortie -->
                    <div class="card-info-overlay">
                        <div class="card-name">
                            <?= esc($game['name']) ?>
                        </div>
                        <div class="card-date">
                            Sortie : <?= !empty($game['released']) ? date('d/m/Y', strtotime($game['released'])) : 'Date inconnue' ?>
                        </div>
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

<!-- Modal d'ajout à la collection Mes Jeux (copié de mes-jeux/index.php) -->
<div id="addGameModal" class="modal">
    <div class="modal-content">
        <button class="modal-close" id="closeAddGameModal">&times;</button>
        <h2>Ajouter un jeu</h2>
        <form id="addGameCalendarForm">
            <!-- Champs cachés pour les informations automatiques -->
            <input type="hidden" id="addGame_game_id" name="game_id">
            <input type="hidden" id="addGame_platform" name="platform">
            <input type="hidden" id="addGame_releaseYear" name="releaseYear">
            <input type="hidden" id="addGame_genre" name="genre">
            <input type="hidden" id="addGame_cover" name="cover">
            <input type="hidden" id="addGame_developer" name="developer">
            <input type="hidden" id="addGame_publisher" name="publisher">
            
            <!-- Recherche de jeu - VISIBLE -->
            <div class="form-group">
                <label for="addGame_searchGame">Recherchez votre jeu :</label>
                <input type="text" id="addGame_searchGame" name="searchGame" placeholder="Commencez à taper le nom du jeu..." required autocomplete="off">
                <ul id="addGame_suggestions" class="suggestions-list"></ul>
            </div>

            <!-- Aperçu du jeu sélectionné -->
            <div class="form-group" id="addGame_gamePreview" style="display: none;">
                <div style="background: var(--background-dark); border: 2px solid var(--primary-color); border-radius: 10px; padding: 1rem; display: flex; align-items: center; gap: 1rem; margin-bottom: 1rem;">
                    <img id="addGame_selectedGameCover" 
                         src="" 
                         alt="Jaquette" 
                         style="width: 60px; height: 60px; object-fit: cover; border-radius: 8px; border: 2px solid var(--secondary-color);">
                    <div>
                        <div id="addGame_selectedGameName" style="color: var(--secondary-color); font-weight: bold; margin-bottom: 0.3rem;"></div>
                        <div id="addGame_selectedGameDetails" style="color: var(--text-color); font-size: 0.9rem;"></div>
                    </div>
                </div>
            </div>

            <!-- Champs visibles pour l'utilisateur -->
            <div class="form-row-status">
                <div class="form-group">
                    <label for="addGame_status">Statut :</label>
                    <select name="status" id="addGame_status" class="form-control" required>
                        <option value="">Choisir un statut</option>
                        <option value="en cours">En cours</option>
                        <option value="termine">Terminé</option>
                        <option value="complete">Complété</option>
                        <option value="abandonne">Abandonné</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="addGame_playtime">Temps de jeu :</label>
                    <input type="text" name="playtime" id="addGame_playtime" class="form-control" placeholder="Temps de jeu (en h)">
                </div>
            </div>
            <div class="form-group">
                <label for="addGame_notes">Notes :</label>
                <textarea id="addGame_notes" name="notes" placeholder="Ajoutez vos notes sur ce jeu..."></textarea>
            </div>
            <button type="submit">Ajouter le jeu</button>
        </form>
    </div>
</div>

<!-- Flatpickr CSS/JS placés en haut pour garantir le chargement -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/plugins/weekSelect/weekSelect.js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/plugins/weekSelect/weekSelect.css">
<script>
// Variables globales
const IS_USER_LOGGED_IN = <?= session()->get('user_id') ? 'true' : 'false' ?>;
const BASE_URL = '<?= base_url() ?>';

// Fonction pour vérifier la connexion et rediriger si nécessaire
function checkAuthAndRedirect(action = 'effectuer cette action') {
    if (!IS_USER_LOGGED_IN) {
        showToast('info', `Vous devez être connecté pour ${action}`);
        setTimeout(() => {
            window.location.href = BASE_URL + 'login';
        }, 1500);
        return false;
    }
    return true;
}

// Toast notifications
function showToast(type, message) {
    let container = document.getElementById('toast-container');
    if (!container) {
        container = document.createElement('div');
        container.id = 'toast-container';
        container.style.cssText = 'position:fixed;top:30px;right:30px;z-index:9999;display:flex;flex-direction:column;gap:1rem;pointer-events:none;';
        document.body.appendChild(container);
    }

    const toast = document.createElement('div');
    toast.className = `toast toast-${type}`;
    toast.style.cssText = `
        min-width:220px;max-width:350px;background:var(--primary-color);color:#fff;border-radius:12px;
        box-shadow:0 4px 16px #7F39FB55;padding:1.1rem 1.5rem;font-family:'Orbitron',sans-serif;
        font-size:1.05rem;font-weight:500;letter-spacing:0.5px;margin-bottom:0.5rem;
        opacity:0;transform:translateY(-20px) scale(0.98);
        animation:toastIn 0.5s cubic-bezier(0.23,1,0.32,1) forwards;pointer-events:auto;
        display:flex;align-items:center;gap:0.7rem;
    `;
    
    if (type === 'success') {
        toast.style.background = 'linear-gradient(90deg, #7F39FB 80%, #00E5FF 100%)';
    } else if (type === 'error') {
        toast.style.background = 'linear-gradient(90deg, #7F39FB 80%, #FF6F61 100%)';
    } else if (type === 'info') {
        toast.style.background = 'linear-gradient(90deg, #7F39FB 80%, #9B5DE5 100%)';
    }
    
    toast.innerHTML = message;
    container.appendChild(toast);

    setTimeout(() => {
        toast.style.opacity = '0';
        toast.style.transform = 'translateY(-20px) scale(0.98)';
        setTimeout(() => container.removeChild(toast), 400);
    }, 2600);
}

// Attendre que le DOM soit complètement chargé
document.addEventListener('DOMContentLoaded', function() {
    console.log('=== DÉBUT INITIALISATION CALENDRIER ===');
    
    // INITIALISATION 1: Gestion du changement de page
    initPageSelector();
    
    // INITIALISATION 2: Modal de détails des jeux
    initGameDetailsModal();
    
    // INITIALISATION 3: Modal d'ajout à Mes Jeux
    initAddGameModal();
    
    // INITIALISATION 4: Recherche et filtrage
    initSearchAndFilters();
    
    // INITIALISATION 5: Flatpickr pour sélection de semaine
    initWeekPicker();
    
    console.log('=== FIN INITIALISATION CALENDRIER ===');
});

// FONCTION 1: Gestion du changement de page
function initPageSelector() {
    console.log('1. Initialisation du sélecteur de page...');
    const pageSelector = document.getElementById('pageSelector');
    if (pageSelector) {
        pageSelector.addEventListener('change', function() {
            const selectedPage = this.value;
            const currentUrl = window.location.pathname;
            const basePath = currentUrl.replace(/\/page\/\d+$/, '');
            window.location.href = basePath + '/page/' + selectedPage;
        });
        console.log('✓ Sélecteur de page initialisé');
    } else {
        console.log('- Pas de sélecteur de page sur cette page');
    }
}

// FONCTION 2: Modal de détails des jeux
function initGameDetailsModal() {
    console.log('2. Initialisation du modal de détails...');
    const gameModal = document.getElementById('gameModal');
    const gameModalBody = document.getElementById('gameModalBody');
    const closeGameModal = document.getElementById('closeGameModal');
    
    if (!gameModal || !gameModalBody) {
        console.error('✗ Éléments du modal de détails non trouvés');
        return;
    }
    
    // Fonction pour ouvrir le modal de détails du jeu
    window.openGameModal = function(gameId) {
        console.log('Ouverture modal détails pour jeu:', gameId);
        gameModalBody.innerHTML = '<span style="color:#BB86FC;">Chargement...</span>';
        gameModal.classList.add('active');
        
        fetch(`https://api.rawg.io/api/games/${gameId}?key=ff6f7941c211456c8806541638fdfaff`)
            .then(res => res.json())
            .then(game => {
                gameModalBody.innerHTML = `
                    ${game.background_image ? `<img src="${game.background_image}" alt="${game.name}" style="width:220px;height:220px;object-fit:cover;border-radius:12px;box-shadow:0 2px 12px #7F39FB44;margin-bottom:1.2rem;">` : `
                        <div style="width:220px;height:220px;margin:0 auto 1.2rem auto;background:linear-gradient(45deg, #1F1B2E, #2A1B3D);border-radius:10px;display:flex;flex-direction:column;justify-content:center;align-items:center;padding:0.5rem;box-sizing:border-box;text-align:center;border:2px solid #7F39FB;box-shadow:0 2px 8px #7F39FB44;">
                            <div style="color:#9B5DE5;font-size:1.2rem;font-weight:bold;margin-bottom:0.5rem;text-shadow:0 2px 8px rgba(0,0,0,0.5);letter-spacing:1px;line-height:1.2;">${game.name}</div>
                            <div style="color:#BB86FC;font-size:0.9rem;opacity:0.8;max-width:85%;line-height:1.3;text-align:center;">Aucune jaquette</div>
                        </div>
                    `}
                    <h2 style="color:#9B5DE5;margin-bottom:0.7rem;">${game.name}</h2>
                    <div style="color:#BB86FC;font-size:1.05rem;margin-bottom:0.7rem;">
                        Plateforme : ${game.platforms && game.platforms.length ? game.platforms.map(p=>p.platform.name).join(', ') : 'Inconnues'}<br>
                        Année : ${game.released || 'Inconnue'}<br>
                        Genre : ${game.genres && game.genres.length ? game.genres.map(g=>g.name).join(', ') : 'Inconnus'}
                    </div>
                    ${game.developers && game.developers.length ? `<div style="color:#BB86FC;font-size:1.05rem;margin-bottom:0.7rem;">Développeur : ${game.developers.map(d=>d.name).join(', ')}</div>` : ''}
                    ${game.publishers && game.publishers.length ? `<div style="color:#BB86FC;font-size:1.05rem;margin-bottom:0.7rem;">Éditeur : ${game.publishers.map(p=>p.name).join(', ')}</div>` : ''}
                    <div style="color:#E0F7FA;font-size:1rem;margin-bottom:1.5rem;max-height:120px;overflow:auto;">
                        ${game.description_raw || '<i>Aucune description disponible.</i>'}
                    </div>
                    <button id="addToWishlistBtn" style="margin-top:1rem;padding:0.7rem 2.2rem;background:#7F39FB;color:#fff;border:none;border-radius:10px;font-size:1.1rem;cursor:pointer;">Ajouter à la wishlist</button>
                    <button id="addToMyGamesBtn" style="margin-top:1rem;margin-left:1rem;padding:0.7rem 2.2rem;background:#00E5FF;color:#1E1E2F;border:none;border-radius:10px;font-size:1.1rem;cursor:pointer;">Ajouter à mes jeux</button>
                    <div id="wishlistMsg" style="margin-top:1rem;font-size:1rem;"></div>
                `;
                
                // Gestion des boutons avec délai pour s'assurer qu'ils existent
                setTimeout(function() {
                    const wishlistBtn = document.getElementById('addToWishlistBtn');
                    const myGamesBtn = document.getElementById('addToMyGamesBtn');
                    
                    if (wishlistBtn) {
                        wishlistBtn.onclick = function() {
                            console.log('Clic sur bouton wishlist');
                            if (!checkAuthAndRedirect('ajouter un jeu à votre wishlist')) {
                                return;
                            }
                            
                            gameModal.classList.remove('active');
                            
                            const gameData = {
                                game_id: game.id,
                                searchGame: game.name || 'Jeu sans nom',
                                platform: (game.platforms && game.platforms.length && game.platforms[0].platform && game.platforms[0].platform.name) ? game.platforms[0].platform.name : 'Inconnue',
                                releaseYear: game.released ? game.released.split('-')[0] : '',
                                genre: (game.genres && game.genres.length) ? game.genres.map(g => g.name).join(', ') : '',
                                cover: game.background_image || '',
                                developer: (game.developers && game.developers.length) ? game.developers.map(d => d.name).join(', ') : '',
                                publisher: (game.publishers && game.publishers.length) ? game.publishers.map(p => p.name).join(', ') : ''
                            };

                            fetch('/checkpoint/public/wishlist/add', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-Requested-With': 'XMLHttpRequest'
                                },
                                body: JSON.stringify(gameData)
                            })
                            .then(res => res.json())
                            .then(data => {
                                if (data.success) {
                                    showToast('success', 'Jeu ajouté à votre wishlist avec succès !');
                                } else {
                                    if (data.error && data.error.includes('non connecté')) {
                                        showToast('info', 'Vous devez être connecté pour ajouter un jeu à votre wishlist');
                                        setTimeout(() => {
                                            window.location.href = BASE_URL + 'login';
                                        }, 1500);
                                    } else {
                                        showToast('error', data.error || data.message || 'Une erreur est survenue');
                                    }
                                }
                            })
                            .catch(error => {
                                console.error(error);
                                showToast('error', 'Erreur lors de l\'ajout à la wishlist');
                            });
                        }
                    }
                    
                    if (myGamesBtn) {
                        myGamesBtn.onclick = function() {
                            console.log('Clic sur bouton mes jeux');
                            if (!checkAuthAndRedirect('ajouter un jeu à votre collection')) {
                                return;
                            }
                            
                            gameModal.classList.remove('active');
                            openAddGameModalFromRawg(game);
                        }
                    }
                }, 100);
            })
            .catch(() => {
                gameModalBody.innerHTML = '<span style="color:#FF6F61;">Erreur lors du chargement des infos du jeu.</span>';
            });
    };
    
    // Event listeners pour fermer le modal
    if (closeGameModal) {
        closeGameModal.addEventListener('click', () => gameModal.classList.remove('active'));
    }
    
    gameModal.addEventListener('click', (e) => {
        if (e.target === gameModal) gameModal.classList.remove('active');
    });
    
    // Attacher les event listeners aux cartes de jeux
    document.querySelectorAll('.game-card-universal').forEach(card => {
        card.addEventListener('click', function() {
            const gameId = this.dataset.gameId;
            if (gameId) {
                openGameModal(gameId);
            }
        });
    });
    
    console.log('✓ Modal de détails initialisé');
}

// FONCTION 3: Modal d'ajout à Mes Jeux
function initAddGameModal() {
    console.log('3. Initialisation du modal d\'ajout...');
    const addGameModal = document.getElementById('addGameModal');
    const closeAddGameModal = document.getElementById('closeAddGameModal');
    
    if (!addGameModal) {
        console.error('✗ Modal d\'ajout non trouvé');
        return;
    }
    
    // Fonction pour ouvrir le modal d'ajout depuis les données RAWG
    window.openAddGameModalFromRawg = function(game) {
        console.log('Ouverture modal ajout pour:', game.name);
        
        if (!checkAuthAndRedirect('ajouter un jeu à votre collection')) {
            return;
        }

        // Remplir les champs cachés
        const elements = {
            'addGame_searchGame': game.name || 'Jeu sans nom',
            'addGame_game_id': game.id || '',
            'addGame_platform': '',
            'addGame_releaseYear': game.released ? game.released.split('-')[0] : '',
            'addGame_genre': (game.genres && game.genres.length) ? game.genres.map(g => g.name).join(', ') : '',
            'addGame_cover': game.background_image || '',
            'addGame_developer': (game.developers && game.developers.length) ? game.developers.map(d => d.name).join(', ') : '',
            'addGame_publisher': (game.publishers && game.publishers.length) ? game.publishers.map(p => p.name).join(', ') : ''
        };
        
        // Gestion spéciale pour platform
        let platform = 'Inconnue';
        if (game.platforms && Array.isArray(game.platforms) && game.platforms.length > 0) {
            const plat = game.platforms[0];
            if (plat && plat.platform && plat.platform.name) {
                platform = plat.platform.name;
            } else if (plat && plat.name) {
                platform = plat.name;
            }
        }
        elements['addGame_platform'] = platform;
        
        // Remplir tous les champs
        Object.entries(elements).forEach(([id, value]) => {
            const element = document.getElementById(id);
            if (element) {
                element.value = value;
            } else {
                console.warn(`Élément ${id} non trouvé`);
            }
        });
        
        // Gérer l'aperçu du jeu sélectionné
        const gamePreview = document.getElementById('addGame_gamePreview');
        const selectedGameCover = document.getElementById('addGame_selectedGameCover');
        const selectedGameName = document.getElementById('addGame_selectedGameName');
        const selectedGameDetails = document.getElementById('addGame_selectedGameDetails');

        if (gamePreview && selectedGameCover && selectedGameName && selectedGameDetails) {
            gamePreview.style.display = 'block';
            
            // Jaquette - utiliser le placeholder si pas d'image
            if (game.background_image) {
                selectedGameCover.src = game.background_image;
                selectedGameCover.style.display = 'block';
                const placeholder = gamePreview.querySelector('.game-cover-placeholder');
                if (placeholder) placeholder.style.display = 'none';
            } else {
                selectedGameCover.style.display = 'none';
                let placeholder = gamePreview.querySelector('.game-cover-placeholder');
                if (!placeholder) {
                    placeholder = document.createElement('div');
                    placeholder.className = 'game-cover-placeholder size-small';
                    placeholder.style.cssText = 'width: 60px; height: 60px; border-radius: 8px; border: 2px solid var(--secondary-color); margin-right: 1rem;';
                    placeholder.innerHTML = `<div class="placeholder-title">${game.name || 'Jeu sans nom'}</div>`;
                    selectedGameCover.parentNode.insertBefore(placeholder, selectedGameCover);
                } else {
                    placeholder.style.display = 'flex';
                    placeholder.querySelector('.placeholder-title').textContent = game.name || 'Jeu sans nom';
                }
            }
            
            selectedGameName.textContent = game.name || 'Jeu sans nom';
            
            const details = [];
            if (platform) details.push(platform);
            if (game.released) details.push(game.released.split('-')[0]);
            if (game.genres && game.genres.length) details.push(game.genres.map(g => g.name).join(', '));
            selectedGameDetails.textContent = details.join(' • ');
        }
        
        // Réinitialiser les champs utilisateur
        const userFields = ['addGame_status', 'addGame_playtime', 'addGame_notes'];
        userFields.forEach(id => {
            const element = document.getElementById(id);
            if (element) element.value = '';
        });
        
        // Ouvrir le modal
        addGameModal.classList.add('active');
    };
    
    // Event listeners pour fermer le modal
    if (closeAddGameModal) {
        closeAddGameModal.addEventListener('click', () => {
            addGameModal.classList.remove('active');
        });
    }
    
    addGameModal.addEventListener('click', (event) => {
        if (event.target === addGameModal) {
            addGameModal.classList.remove('active');
        }
    });
    
    // Gestion du formulaire
    const addGameForm = document.getElementById('addGameCalendarForm');
    if (addGameForm) {
        let isSubmitting = false;
        
        addGameForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            if (!checkAuthAndRedirect('ajouter un jeu à votre collection')) {
                return;
            }
            
            if (isSubmitting) return;
            isSubmitting = true;
            
            const formData = new FormData(addGameForm);
            const jsonData = {};
            formData.forEach((value, key) => {
                jsonData[key] = value;
            });
            
            fetch('/checkpoint/public/mes-jeux/add', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify(jsonData)
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    showToast('success', 'Jeu ajouté à votre collection !');
                    addGameModal.classList.remove('active');
                } else {
                    if (data.error && data.error.includes('non connecté')) {
                        showToast('info', 'Vous devez être connecté pour ajouter un jeu à votre collection');
                        setTimeout(() => {
                            window.location.href = BASE_URL + 'login';
                        }, 1500);
                    } else {
                        showToast('error', data.error || data.message || 'Erreur lors de l\'ajout');
                    }
                }
                isSubmitting = false;
            })
            .catch(error => {
                showToast('error', 'Erreur lors de l\'ajout');
                isSubmitting = false;
            });
        });
    }
    
    // Initialiser la recherche dans le modal
    initCalendarGameSearch();
    
    console.log('✓ Modal d\'ajout initialisé');
}

// FONCTION 4: Recherche et filtrage  
function initSearchAndFilters() {
    console.log('4. Initialisation de la recherche et des filtres...');
    
    // A. Recherche de jeux dans le calendrier
    const searchInput = document.getElementById('searchGame');
    const clearSearchBtn = document.getElementById('clearSearch');
    const cards = document.querySelectorAll('.game-card-universal');
    
    console.log('Éléments de recherche trouvés:', {
        searchInput: !!searchInput,
        clearSearchBtn: !!clearSearchBtn,
        cardsCount: cards.length
    });
    
    function filterGames() {
        const searchTerm = searchInput ? searchInput.value.toLowerCase().trim() : '';
        console.log('Filtrage avec terme:', searchTerm);
        
        let visibleCount = 0;
        
        cards.forEach((card, index) => {
            const titleSpan = card.querySelector('span[style*="font-weight:bold"]');
            if (!titleSpan) {
                console.warn(`Carte ${index}: titleSpan non trouvé`);
                return;
            }
            
            const gameName = titleSpan.textContent.toLowerCase().trim();
            const genres = (card.dataset.genres || '').split(',');
            const checkedGenres = Array.from(document.querySelectorAll('.genre-checkbox:checked')).map(cb => cb.value);
            
            const matchesSearch = searchTerm === '' || gameName.includes(searchTerm);
            const matchesGenres = checkedGenres.length === 0 || genres.some(g => checkedGenres.includes(g));
            
            const shouldShow = matchesSearch && matchesGenres;
            
            card.style.display = shouldShow ? '' : 'none';
            
            if (shouldShow) visibleCount++;
        });
        
        console.log(`Jeux visibles après filtrage: ${visibleCount}/${cards.length}`);
    }
    
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            console.log('Input event - valeur:', this.value);
            filterGames();
        });
        
        searchInput.addEventListener('blur', function() {
            console.log('Blur event - valeur:', this.value);
            filterGames();
        });
    } else {
        console.error('✗ searchInput non trouvé - ID: searchGame');
    }
    
    if (clearSearchBtn) {
        clearSearchBtn.addEventListener('click', function() {
            console.log('Clic sur bouton effacer');
            if (searchInput) {
                searchInput.value = '';
                filterGames();
            }
        });
    } else {
        console.error('✗ clearSearchBtn non trouvé - ID: clearSearch');
    }
    
    // B. Filtrage par genre
    const genreCheckboxes = document.querySelectorAll('.genre-checkbox');
    const checkAllBtn = document.getElementById('checkAllGenres');
    const uncheckAllBtn = document.getElementById('uncheckAllGenres');
    
    genreCheckboxes.forEach(cb => {
        cb.addEventListener('change', filterGames);
    });
    
    if (checkAllBtn) {
        checkAllBtn.addEventListener('click', function() {
            genreCheckboxes.forEach(cb => { cb.checked = true; });
            filterGames();
        });
    }
    
    if (uncheckAllBtn) {
        uncheckAllBtn.addEventListener('click', function() {
            genreCheckboxes.forEach(cb => { cb.checked = false; });
            filterGames();
        });
    }
    
    // C. Ajout des genres sur chaque carte pour le filtrage
    <?php foreach ($games as $game): ?>
        const card_<?= $game['id'] ?> = document.querySelector('.game-card-universal[data-game-id="<?= $game['id'] ?>"]');
        if (card_<?= $game['id'] ?>) {
            card_<?= $game['id'] ?>.dataset.genres = "<?= isset($game['genres']) ? implode(',', array_map(function($g){return $g['slug'];}, $game['genres'])) : '' ?>";
        }
    <?php endforeach; ?>
    
    // Filtrage initial
    filterGames();
    
    console.log('✓ Recherche et filtres initialisés');
}

// FONCTION 5: Flatpickr pour sélection de semaine
function initWeekPicker() {
    console.log('5. Initialisation du sélecteur de semaine...');
    
    // Vérifier que flatpickr est chargé
    if (typeof flatpickr === 'undefined') {
        console.error('✗ flatpickr n\'a pas pu être chargé.');
        const openWeekPickerBtn = document.getElementById('openWeekPicker');
        if (openWeekPickerBtn) {
            openWeekPickerBtn.addEventListener('click', function() {
                showToast('error', 'Le sélecteur de semaine n\'est pas disponible. Flatpickr non chargé.');
            });
        }
        return;
    }
    
    // Initialisation de flatpickr
    try {
        const hiddenInput = document.getElementById('hiddenWeekInput');
        if (!hiddenInput) {
            console.error('✗ Input caché pour flatpickr non trouvé');
            return;
        }
        
        const weekPicker = flatpickr(hiddenInput, {
            dateFormat: "Y-m-d",
            defaultDate: "<?= $startDate->format('Y-m-d') ?>",
            plugins: [new weekSelect({})],
            onChange: function(selectedDates, dateStr, instance) {
                if(selectedDates.length) {
                    const date = selectedDates[0];
                    const year = date.getFullYear();
                    const week = getWeekNumber(date);
                    console.log(`Semaine sélectionnée: ${year}-W${week}`);
                    window.location.href = `/checkpoint/public/calendrier/${year}/${week}`;
                }
            },
            disableMobile: true,
            locale: {
                firstDayOfWeek: 1
            }
        });
        
        // Event listener pour le bouton d'ouverture
        const openWeekPickerBtn = document.getElementById('openWeekPicker');
        if (openWeekPickerBtn) {
            openWeekPickerBtn.addEventListener('click', function(e) {
                e.preventDefault();
                console.log('Ouverture du sélecteur de semaine...');
                
                try {
                    const fp = hiddenInput._flatpickr;
                    if(fp) {
                        fp.open();
                        console.log('✓ Sélecteur de semaine ouvert');
                    } else {
                        console.error('✗ flatpickr instance non trouvée');
                        showToast('error', 'Le calendrier ne peut pas s\'ouvrir (flatpickr non initialisé).');
                    }
                } catch (error) {
                    console.error('Erreur lors de l\'ouverture:', error);
                    showToast('error', 'Erreur lors de l\'ouverture du sélecteur de semaine.');
                }
            });
            console.log('✓ Bouton sélecteur de semaine configuré');
        } else {
            console.error('✗ Bouton openWeekPicker non trouvé');
        }
        
        console.log('✓ Flatpickr initialisé avec succès');
        
    } catch (error) {
        console.error('✗ Erreur lors de l\'initialisation de flatpickr:', error);
        const openWeekPickerBtn = document.getElementById('openWeekPicker');
        if (openWeekPickerBtn) {
            openWeekPickerBtn.addEventListener('click', function() {
                showToast('error', 'Erreur lors de l\'initialisation du sélecteur de semaine.');
            });
        }
    }
}

// Calcul du numéro de semaine ISO
function getWeekNumber(date) {
    const d = new Date(Date.UTC(date.getFullYear(), date.getMonth(), date.getDate()));
    const dayNum = d.getUTCDay() || 7;
    d.setUTCDate(d.getUTCDate() + 4 - dayNum);
    const yearStart = new Date(Date.UTC(d.getUTCFullYear(),0,1));
    return String(Math.ceil((((d - yearStart) / 86400000) + 1)/7)).padStart(2,'0');
}

// Gestion de la recherche dans le modal "Ajouter un jeu" du calendrier
function initCalendarGameSearch() {
    console.log('Initialisation de la recherche modal...');
    const searchInput = document.getElementById('addGame_searchGame');
    const suggestionsList = document.getElementById('addGame_suggestions');
    
    if (!searchInput || !suggestionsList) {
        console.warn('Éléments de recherche modal non trouvés');
        return;
    }

    const API_KEY = 'ff6f7941c211456c8806541638fdfaff';
    let searchTimeout;

    const updateGameFields = async (game) => {
        try {
            const detailResponse = await fetch(`https://api.rawg.io/api/games/${game.id}?key=${API_KEY}`);
            const gameDetails = await detailResponse.json();
            
            const fields = {
                'addGame_searchGame': gameDetails.name || game.name,
                'addGame_platform': gameDetails.platforms?.[0]?.platform?.name || game.platforms?.[0]?.platform?.name || '',
                'addGame_releaseYear': (gameDetails.released || game.released)?.split('-')[0] || '',
                'addGame_genre': gameDetails.genres?.map(g => g.name).join(', ') || game.genres?.map(g => g.name).join(', ') || '',
                'addGame_cover': gameDetails.background_image || game.background_image || '',
                'addGame_game_id': gameDetails.id || game.id || '',
                'addGame_developer': gameDetails.developers?.map(d => d.name).join(', ') || '',
                'addGame_publisher': gameDetails.publishers?.map(p => p.name).join(', ') || ''
            };

            Object.entries(fields).forEach(([id, value]) => {
                const element = document.getElementById(id);
                if (element) element.value = value;
            });

            // Gérer l'aperçu du jeu sélectionné
            const gamePreview = document.getElementById('addGame_gamePreview');
            const selectedGameCover = document.getElementById('addGame_selectedGameCover');
            const selectedGameName = document.getElementById('addGame_selectedGameName');
            const selectedGameDetails = document.getElementById('addGame_selectedGameDetails');

            if (gamePreview && selectedGameCover && selectedGameName && selectedGameDetails) {
                gamePreview.style.display = 'block';
                
                if (fields.addGame_cover) {
                    selectedGameCover.src = fields.addGame_cover;
                    selectedGameCover.style.display = 'block';
                    const placeholder = gamePreview.querySelector('.game-cover-placeholder');
                    if (placeholder) placeholder.style.display = 'none';
                } else {
                    selectedGameCover.style.display = 'none';
                    let placeholder = gamePreview.querySelector('.game-cover-placeholder');
                    if (!placeholder) {
                        placeholder = document.createElement('div');
                        placeholder.className = 'game-cover-placeholder size-small';
                        placeholder.style.cssText = 'width: 60px; height: 60px; border-radius: 8px; border: 2px solid var(--secondary-color); margin-right: 1rem;';
                        placeholder.innerHTML = `<div class="placeholder-title">${fields.addGame_searchGame}</div>`;
                        selectedGameCover.parentNode.insertBefore(placeholder, selectedGameCover);
                    } else {
                        placeholder.style.display = 'flex';
                        placeholder.querySelector('.placeholder-title').textContent = fields.addGame_searchGame;
                    }
                }
                
                selectedGameName.textContent = fields.addGame_searchGame;
                
                const details = [];
                if (fields.addGame_platform) details.push(fields.addGame_platform);
                if (fields.addGame_releaseYear) details.push(fields.addGame_releaseYear);
                if (fields.addGame_genre) details.push(fields.addGame_genre);
                selectedGameDetails.textContent = details.join(' • ');
            }

        } catch (error) {
            console.error('Erreur lors de la récupération des détails:', error);
        }
    };

    searchInput.addEventListener('input', (e) => {
        clearTimeout(searchTimeout);
        const query = e.target.value.trim();
        
        if (query.length < 2) {
            suggestionsList.innerHTML = '';
            return;
        }

        searchTimeout = setTimeout(async () => {
            try {
                const response = await fetch(`https://api.rawg.io/api/games?key=${API_KEY}&search=${query}`);
                const data = await response.json();
                
                suggestionsList.innerHTML = '';
                if (data.results?.length) {
                    data.results.forEach(game => {
                        const li = document.createElement('li');
                        li.textContent = game.name;
                        li.addEventListener('click', async () => {
                            await updateGameFields(game);
                            suggestionsList.innerHTML = '';
                        });
                        suggestionsList.appendChild(li);
                    });
                } else {
                    suggestionsList.innerHTML = '<li>Aucun résultat trouvé</li>';
                }
            } catch (error) {
                console.error('Erreur recherche:', error);
                suggestionsList.innerHTML = '<li>Erreur lors de la recherche</li>';
            }
        }, 300);
    });

    document.addEventListener('click', (e) => {
        if (!searchInput.contains(e.target) && !suggestionsList.contains(e.target)) {
            suggestionsList.innerHTML = '';
        }
    });
    
    console.log('✓ Recherche modal initialisée');
}
</script>
<?php $this->endSection(); ?> 