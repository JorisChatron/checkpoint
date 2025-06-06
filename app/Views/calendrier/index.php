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
    // Extraction des plateformes et genres depuis l'API RAWG
    $allPlatforms = [];
    $allGenres = [];
    
    foreach ($games as $game) {
        // Plateformes
        if (!empty($game['platforms'])) {
            foreach ($game['platforms'] as $platform) {
                $platformName = $platform['platform']['name'] ?? '';
                if ($platformName && !in_array($platformName, $allPlatforms)) {
                    $allPlatforms[] = $platformName;
                }
            }
        }
        
        // Genres
        if (!empty($game['genres'])) {
            foreach ($game['genres'] as $genre) {
                $genreName = $genre['name'] ?? '';
                if ($genreName && !in_array($genreName, $allGenres)) {
                    $allGenres[] = $genreName;
                }
            }
        }
    }
    
    // Tri alphabétique
    sort($allPlatforms);
    sort($allGenres);
    ?>
    
    <!-- Barre de filtres comme dans la wishlist -->
    <form class="filters-bar" id="calendarFilters">
        <select name="platform" id="platformFilter">
            <option value="">Plateforme</option>
            <?php foreach ($allPlatforms as $platform): ?>
                <option value="<?= esc($platform) ?>"><?= esc($platform) ?></option>
            <?php endforeach; ?>
        </select>
        <select name="genre" id="genreFilter">
            <option value="">Genre</option>
            <?php foreach ($allGenres as $genre): ?>
                <option value="<?= esc($genre) ?>"><?= esc($genre) ?></option>
            <?php endforeach; ?>
        </select>
    </form>
    
    <div style="text-align:center;margin-bottom:1.5rem;">
        <div class="calendar-search-container">
            <input type="text" id="searchGame" placeholder="Rechercher un jeu..." class="calendar-search-input">
            <button id="clearSearch" class="home-btn" style="width:auto;font-size:0.98rem;padding:0.7rem 1.2rem;">Effacer</button>
        </div>
    </div>
    <div class="dashboard-row">
        <?php if (empty($games)): ?>
            <p style="color:#9B5DE5;text-align:center;width:100%;grid-column: 1 / -1;">Aucune sortie prévue pour cette semaine.</p>
        <?php else: ?>
            <?php foreach ($games as $game): ?>
                <div class="game-card-universal" 
                     data-game-id="<?= esc($game['id']) ?>"
                     data-platforms="<?= esc(isset($game['platforms']) ? implode(',', array_map(function($p) { return $p['platform']['name']; }, $game['platforms'])) : '') ?>"
                     data-genres="<?= esc(isset($game['genres']) ? implode(',', array_map(function($g) { return $g['name']; }, $game['genres'])) : '') ?>">
                    <?php if (!empty($game['background_image'])): ?>
                        <img src="<?= esc($game['background_image']) ?>" 
                             alt="<?= esc($game['name']) ?>"
                             class="card-image">
                    <?php else: ?>
                        <div class="game-cover-placeholder">
                            <div class="placeholder-title"><?= esc($game['name']) ?></div>
                            <div class="placeholder-text">Aucune jaquette</div>
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
        setTimeout(() => {
            window.location.href = BASE_URL + 'login';
        }, 500);
        return false;
    }
    return true;
}

// Attendre que le DOM soit complètement chargé
document.addEventListener('DOMContentLoaded', function() {
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
});

// FONCTION 1: Gestion du changement de page
function initPageSelector() {
    const pageSelector = document.getElementById('pageSelector');
    if (pageSelector) {
        pageSelector.addEventListener('change', function() {
            const selectedPage = this.value;
            const currentUrl = window.location.pathname;
            const basePath = currentUrl.replace(/\/page\/\d+$/, '');
            window.location.href = basePath + '/page/' + selectedPage;
        });
    }
}

// FONCTION 2: Modal de détails des jeux
function initGameDetailsModal() {
    const gameModal = document.getElementById('gameModal');
    const gameModalBody = document.getElementById('gameModalBody');
    const closeGameModal = document.getElementById('closeGameModal');
    
    if (!gameModal || !gameModalBody) {
        return;
    }
    
    // Fonction pour ouvrir le modal de détails du jeu
    window.openGameModal = function(gameId) {
        gameModalBody.innerHTML = '<span style="color:#BB86FC;">Chargement...</span>';
        gameModal.classList.add('active');
        
        fetch(`https://api.rawg.io/api/games/${gameId}?key=ff6f7941c211456c8806541638fdfaff`)
            .then(res => res.json())
            .then(game => {
                gameModalBody.innerHTML = `
                    ${game.background_image ? `<img src="${game.background_image}" alt="${game.name}" style="width:220px;height:220px;object-fit:cover;border-radius:12px;box-shadow:0 2px 12px #7F39FB44;margin-bottom:1.2rem;">` : createInlineCoverPlaceholder(game.name)}
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
                            if (!checkAuthAndRedirect('ajouter un jeu à votre wishlist')) {
                                return;
                            }
                            
                            gameModal.classList.remove('active');
                            
                            const gameData = {
                                game_id: game.id,
                                searchGame: game.name || 'Jeu sans nom',
                                platform: (game.platforms && game.platforms.length && game.platforms[0].platform && game.platforms[0].platform.name) ? game.platforms[0].platform.name : 'Inconnue',
                                releaseYear: extractYear(game.released),
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
                                    // Succès silencieux
                                } else {
                                    if (data.error && data.error.includes('non connecté')) {
                                        setTimeout(() => {
                                            window.location.href = BASE_URL + 'login';
                                        }, 500);
                                    } else {
                                        console.error(data.error || data.message || 'Une erreur est survenue');
                                    }
                                }
                            })
                            .catch(error => {
                                console.error(error);
                            });
                        }
                    }
                    
                    if (myGamesBtn) {
                        myGamesBtn.onclick = function() {
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
}

// FONCTION 3: Modal d'ajout à Mes Jeux
function initAddGameModal() {
    const addGameModal = document.getElementById('addGameModal');
    const closeAddGameModal = document.getElementById('closeAddGameModal');
    
    if (!addGameModal) {
        return;
    }
    
    // Fonction pour ouvrir le modal d'ajout depuis les données RAWG
    window.openAddGameModalFromRawg = function(game) {
        if (!checkAuthAndRedirect('ajouter un jeu à votre collection')) {
            return;
        }

        // Remplir les champs cachés
        const elements = {
            'addGame_searchGame': game.name || 'Jeu sans nom',
            'addGame_game_id': game.id || '',
            'addGame_platform': '',
            'addGame_releaseYear': extractYear(game.released),
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
            
            if (elements.addGame_cover) {
                selectedGameCover.src = elements.addGame_cover;
                selectedGameCover.style.display = 'block';
                const placeholder = gamePreview.querySelector('.game-cover-placeholder');
                if (placeholder) placeholder.style.display = 'none';
            } else {
                selectedGameCover.style.display = 'none';
                let placeholder = gamePreview.querySelector('.game-cover-placeholder');
                if (!placeholder) {
                    // Créer le placeholder en utilisant la fonction utilitaire
                    placeholder = createSmallCoverPlaceholder(elements.addGame_searchGame);
                    selectedGameCover.parentNode.insertBefore(placeholder, selectedGameCover);
                } else {
                    placeholder.style.display = 'flex';
                    placeholder.querySelector('.placeholder-title').textContent = elements.addGame_searchGame;
                }
            }
            
            selectedGameName.textContent = elements.addGame_searchGame;
            
            const details = [];
            if (platform) details.push(platform);
            if (elements.addGame_releaseYear) details.push(elements.addGame_releaseYear);
            if (elements.addGame_genre) details.push(elements.addGame_genre);
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
                    addGameModal.classList.remove('active');
                    setTimeout(() => location.reload(), 300);
                } else {
                    if (data.error && data.error.includes('non connecté')) {
                        setTimeout(() => {
                            window.location.href = BASE_URL + 'login';
                        }, 500);
                    } else {
                        console.error(data.error || data.message || 'Erreur lors de l\'ajout');
                    }
                }
                isSubmitting = false;
            })
            .catch(error => {
                console.error('Erreur lors de l\'ajout');
                isSubmitting = false;
            });
        });
    }
    
    // Initialiser la recherche dans le modal
    initCalendarGameSearch();
}

// FONCTION 4: Recherche et filtrage  
function initSearchAndFilters() {
    // A. Recherche de jeux dans le calendrier
    const searchInput = document.getElementById('searchGame');
    const clearSearchBtn = document.getElementById('clearSearch');
    const cards = document.querySelectorAll('.game-card-universal');
    
    function filterGames() {
        const searchTerm = searchInput ? searchInput.value.toLowerCase().trim() : '';
        const selectedPlatform = document.getElementById('platformFilter')?.value || '';
        const selectedGenre = document.getElementById('genreFilter')?.value || '';
        
        let visibleCount = 0;
        
        cards.forEach(card => {
            const titleElement = card.querySelector('.card-name') || card.querySelector('.placeholder-title');
            if (!titleElement) return;
            
            const gameName = titleElement.textContent.toLowerCase().trim();
            const cardPlatforms = (card.dataset.platforms || '').split(',');
            const cardGenres = (card.dataset.genres || '').split(',');
            
            const matchesSearch = searchTerm === '' || gameName.includes(searchTerm);
            const matchesPlatform = selectedPlatform === '' || cardPlatforms.some(p => p.trim() === selectedPlatform);
            const matchesGenre = selectedGenre === '' || cardGenres.some(g => g.trim() === selectedGenre);
            
            const shouldShow = matchesSearch && matchesPlatform && matchesGenre;
            
            card.style.display = shouldShow ? 'block' : 'none';
            if (shouldShow) visibleCount++;
        });
        
        // Message si aucun résultat
        const dashboard = document.querySelector('.dashboard-row');
        let noResultMsg = dashboard.querySelector('.no-results-message');
        
        if (visibleCount === 0) {
            if (!noResultMsg) {
                noResultMsg = document.createElement('p');
                noResultMsg.className = 'no-results-message';
                noResultMsg.style.cssText = 'color:#9B5DE5;text-align:center;width:100%;grid-column: 1 / -1;margin:2rem 0;font-size:1.2rem;';
                noResultMsg.textContent = 'Aucun jeu ne correspond aux filtres sélectionnés.';
                dashboard.appendChild(noResultMsg);
            }
            noResultMsg.style.display = 'block';
        } else {
            if (noResultMsg) {
                noResultMsg.style.display = 'none';
            }
        }
    }
    
    // Event listeners pour la recherche
    if (searchInput) {
        searchInput.addEventListener('input', filterGames);
        searchInput.addEventListener('blur', filterGames);
    }
    
    if (clearSearchBtn) {
        clearSearchBtn.addEventListener('click', function() {
            if (searchInput) {
                searchInput.value = '';
                filterGames();
            }
        });
    }
    
    // Event listeners pour les filtres
    const platformFilter = document.getElementById('platformFilter');
    const genreFilter = document.getElementById('genreFilter');
    
    if (platformFilter) {
        platformFilter.addEventListener('change', filterGames);
    }
    
    if (genreFilter) {
        genreFilter.addEventListener('change', filterGames);
    }
    
    // Filtrage initial
    filterGames();
}

// FONCTION 5: Flatpickr pour sélection de semaine
function initWeekPicker() {
    if (typeof flatpickr === 'undefined') {
        return;
    }
    
    const hiddenInput = document.getElementById('hiddenWeekInput');
    if (!hiddenInput) {
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
                window.location.href = `/checkpoint/public/calendrier/${year}/${week}`;
            }
        },
        disableMobile: true,
        locale: {
            firstDayOfWeek: 1
        }
    });
    
    const openWeekPickerBtn = document.getElementById('openWeekPicker');
    if (openWeekPickerBtn) {
        openWeekPickerBtn.addEventListener('click', function(e) {
            e.preventDefault();
            const fp = hiddenInput._flatpickr;
            if(fp) {
                fp.open();
            }
        });
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

// Gestion de la recherche dans le modal "Ajouter un jeu"
function initCalendarGameSearch() {
    const searchInput = document.getElementById('addGame_searchGame');
    const suggestionsList = document.getElementById('addGame_suggestions');
    
    if (!searchInput || !suggestionsList) {
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
                'addGame_releaseYear': extractYear(gameDetails.released || game.released),
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
                        placeholder = createSmallCoverPlaceholder(fields.addGame_searchGame);
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
            // Erreur silencieuse
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
                suggestionsList.innerHTML = '<li>Erreur lors de la recherche</li>';
            }
        }, 300);
    });

    document.addEventListener('click', (e) => {
        if (!searchInput.contains(e.target) && !suggestionsList.contains(e.target)) {
            suggestionsList.innerHTML = '';
        }
    });
}
</script>
<?php $this->endSection(); ?> 