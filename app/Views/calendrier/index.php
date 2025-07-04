<?php
$this->extend('layouts/default');
$this->section('content');
?>

<!-- ===== SECTION PRINCIPALE DU CALENDRIER ===== -->
<!-- Cette section affiche les sorties de jeux pour une semaine donnée avec navigation et filtres -->

<section class="dashboard-home" style="max-width:1100px;margin:2.5rem auto 2rem auto;">
    <!-- Titre principal de la page -->
    <h1 style="color:#9B5DE5;text-align:center;font-size:2rem;margin-bottom:2.2rem;">Sorties de la semaine</h1>
    
    <!-- ===== NAVIGATION ET SÉLECTION DE SEMAINE ===== -->
    <div style="text-align:center;margin-bottom:2rem;">
        <?php
            // Formatage de la semaine pour l'affichage (ex: 2024-W01)
            $weekStr = $year . '-W' . str_pad($week, 2, '0', STR_PAD_LEFT);
            $startDate = new DateTime($start);
            $endDate = new DateTime($end);
            
            // ===== CALCUL DE LA SEMAINE PRÉCÉDENTE =====
            // Gestion des changements d'année pour la navigation
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
            
            // ===== CALCUL DE LA SEMAINE SUIVANTE =====
            // Gestion des changements d'année pour la navigation
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
        
        <!-- ===== CONTENEUR DE SÉLECTION DE SEMAINE ===== -->
        <div class="week-selector-container">
            <!-- Navigation entre les semaines -->
            <div class="week-navigation">
                <!-- Bouton pour aller à la semaine précédente -->
                <a href="<?= base_url('calendrier/'.$prevYear.'/'.$prevWeek) ?>" 
                   class="home-btn week-nav-btn prev" 
                   title="Semaine précédente">
                    &#8249;
                </a>
                
                <!-- Bouton pour ouvrir le sélecteur de semaine -->
                <button id="openWeekPicker" class="home-btn week-picker-btn" style="width:auto;font-size:1.1rem;padding:0.7rem 2.2rem;">
                    Choisir une semaine
                </button>
                <!-- Champ caché pour le sélecteur de semaine Flatpickr -->
                <input type="text" id="hiddenWeekInput" style="display:none;">
                
                <!-- Séparateur visuel -->
                <span class="week-separator" style="color:#BB86FC;font-size:1.1rem;">|</span>
                
                <!-- Bouton pour aller à la semaine suivante -->
                <a href="<?= base_url('calendrier/'.$nextYear.'/'.$nextWeek) ?>" 
                   class="home-btn week-nav-btn next" 
                   title="Semaine suivante">
                    &#8250;
                </a>
            </div>
            
            <!-- Affichage de la période sélectionnée -->
            <span class="week-period-text" style="color:#E0F7FA;font-size:1.1rem;">
                Du <b><?= $startDate->format('d/m/Y') ?></b> au <b><?= $endDate->format('d/m/Y') ?></b>
            </span>
        </div>
    </div>
    
    <!-- ===== AFFICHAGE DES ERREURS ===== -->
    <!-- Affichage des erreurs éventuelles (API indisponible, etc.) -->
    <?php if (!empty($error)): ?>
        <div class="text-danger" style="text-align:center;">Erreur : <?= esc($error) ?></div>
    <?php endif; ?>
    
    <?php
    // ===== EXTRACTION DES FILTRES DEPUIS L'API RAWG =====
    // Récupération des plateformes et genres uniques depuis les jeux affichés
    $allPlatforms = [];
    $allGenres = [];
    
    foreach ($games as $game) {
        // Extraction des plateformes depuis les données RAWG
        if (!empty($game['platforms'])) {
            foreach ($game['platforms'] as $platform) {
                $platformName = $platform['platform']['name'] ?? '';
                if ($platformName && !in_array($platformName, $allPlatforms)) {
                    $allPlatforms[] = $platformName;
                }
            }
        }
        
        // Extraction des genres depuis les données RAWG
        if (!empty($game['genres'])) {
            foreach ($game['genres'] as $genre) {
                $genreName = $genre['name'] ?? '';
                if ($genreName && !in_array($genreName, $allGenres)) {
                    $allGenres[] = $genreName;
                }
            }
        }
    }
    
    // Tri alphabétique des filtres pour une meilleure UX
    sort($allPlatforms);
    sort($allGenres);
    ?>
    
    <!-- ===== BARRE DE FILTRES ===== -->
    <!-- Filtres par plateforme et genre pour affiner les résultats -->
    <form class="filters-bar" id="calendarFilters">
        <!-- Filtre par plateforme -->
        <select name="platform" id="platformFilter">
            <option value="">Plateforme</option>
            <?php foreach ($allPlatforms as $platform): ?>
                <option value="<?= esc($platform) ?>"><?= esc($platform) ?></option>
            <?php endforeach; ?>
        </select>
        
        <!-- Filtre par genre -->
        <select name="genre" id="genreFilter">
            <option value="">Genre</option>
            <?php foreach ($allGenres as $genre): ?>
                <option value="<?= esc($genre) ?>"><?= esc($genre) ?></option>
            <?php endforeach; ?>
        </select>
    </form>
    
    <!-- ===== BARRE DE RECHERCHE ===== -->
    <!-- Recherche textuelle dans les noms de jeux -->
    <div style="text-align:center;margin-bottom:1.5rem;">
        <div class="calendar-search-container">
            <input type="text" id="searchGame" placeholder="Rechercher un jeu..." class="calendar-search-input">
            <button id="clearSearch" class="home-btn" style="width:auto;font-size:0.98rem;padding:0.7rem 1.2rem;">
                Effacer
            </button>
        </div>
    </div>
    
    <!-- ===== AFFICHAGE DES JEUX ===== -->
    <!-- Grille des cartes de jeux avec gestion du cas vide -->
    <div class="dashboard-row">
        <?php if (empty($games)): ?>
            <!-- Message si aucun jeu n'est trouvé pour cette semaine -->
            <p style="color:#9B5DE5;text-align:center;width:100%;grid-column: 1 / -1;">
                Aucune sortie prévue pour cette semaine.
            </p>
        <?php else: ?>
            <!-- Boucle d'affichage des cartes de jeux -->
            <?php foreach ($games as $game): ?>
                <!-- Carte de jeu avec données pour le filtrage -->
                <div class="game-card-universal" 
                     data-game-id="<?= esc($game['id']) ?>"
                     data-platforms="<?= esc(isset($game['platforms']) ? implode(',', array_map(function($p) { return $p['platform']['name']; }, $game['platforms'])) : '') ?>"
                     data-genres="<?= esc(isset($game['genres']) ? implode(',', array_map(function($g) { return $g['name']; }, $game['genres'])) : '') ?>">
                    
                    <!-- ===== IMAGE DE COUVERTURE ===== -->
                    <?php if (!empty($game['background_image'])): ?>
                        <!-- Image de couverture depuis l'API RAWG -->
                        <img src="<?= esc($game['background_image']) ?>" 
                             alt="<?= esc($game['name']) ?>"
                             class="card-image">
                    <?php else: ?>
                        <!-- Placeholder si aucune image n'est disponible -->
                        <div class="game-cover-placeholder">
                            <div class="placeholder-title"><?= esc($game['name']) ?></div>
                            <div class="placeholder-text">Aucune jaquette</div>
                        </div>
                    <?php endif; ?>
                    
                    <!-- ===== INFORMATIONS DU JEU ===== -->
                    <!-- Overlay avec nom et date de sortie -->
                    <div class="card-info-overlay">
                        <div class="card-name">
                            <?= esc($game['name']) ?>
                        </div>
                        <div class="card-date">
                            <?= !empty($game['released']) ? date('d/m/Y', strtotime($game['released'])) : 'Date inconnue' ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
    
    <!-- ===== PAGINATION ===== -->
    <!-- Navigation entre les pages si plus de 50 jeux -->
    <?php if ($count > 50): ?>
        <div style="text-align:center;margin-top:2.5rem;">
            <?php $nbPages = ceil($count/50); ?>
            
            <!-- Bouton page précédente -->
            <?php if ($page > 1): ?>
                <a href="<?= base_url('calendrier/'.$year.'/'.$week.'/page/'.($page-1)) ?>" 
                   class="home-btn" 
                   style="width:140px;font-size:1rem;margin-right:1.2rem;">
                    &larr; Précédent
                </a>
            <?php endif; ?>
            
            <!-- Indicateur de page actuelle -->
            <span style="color:#BB86FC;font-size:1.1rem;">Page <?= $page ?> / <?= $nbPages ?></span>
            
            <!-- Sélecteur de page -->
            <select id="pageSelector" 
                    style="width:120px;display:inline-block;margin:0 1.2rem;padding:0.5rem;background:rgba(31,27,46,0.92);color:#BB86FC;border:1px solid #7F39FB;border-radius:8px;cursor:pointer;">
                <?php for($i = 1; $i <= $nbPages; $i++): ?>
                    <option value="<?= $i ?>" <?= $i == $page ? 'selected' : '' ?>>
                        Page <?= $i ?>
                    </option>
                <?php endfor; ?>
            </select>
            
            <!-- Bouton page suivante -->
            <?php if ($page < $nbPages): ?>
                <a href="<?= base_url('calendrier/'.$year.'/'.$week.'/page/'.($page+1)) ?>" 
                   class="home-btn" 
                   style="width:140px;font-size:1rem;margin-left:1.2rem;">
                    Suivant &rarr;
                </a>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <!-- ===== MODAL DÉTAILS JEU ===== -->
    <!-- Modal pour afficher les détails d'un jeu au clic -->
    <div id="gameModal" class="modal">
        <div class="modal-content" id="gameModalContent" style="max-width:600px;position:relative;">
            <button class="modal-close" id="closeGameModal">&times;</button>
            <div id="gameModalBody" style="min-height:200px;text-align:center;">
                <span style="color:#BB86FC;">Chargement...</span>
            </div>
        </div>
    </div>
</section>

<!-- ===== MODAL AJOUT À LA COLLECTION ===== -->
<!-- Modal pour ajouter un jeu à la collection "Mes Jeux" -->
<div id="addGameModal" class="modal">
    <div class="modal-content">
        <button class="modal-close" id="closeAddGameModal">&times;</button>
        <h2>Ajouter un jeu</h2>
        
        <!-- Formulaire d'ajout de jeu -->
        <form id="addGameCalendarForm">
            <!-- ===== CHAMPS CACHÉS POUR LES INFORMATIONS AUTOMATIQUES ===== -->
            <!-- Ces champs sont remplis automatiquement depuis l'API RAWG -->
            <input type="hidden" id="addGame_game_id" name="game_id">
            <input type="hidden" id="addGame_platform" name="platform">
            <input type="hidden" id="addGame_releaseYear" name="releaseYear">
            <input type="hidden" id="addGame_genre" name="genre">
            <input type="hidden" id="addGame_cover" name="cover">
            <input type="hidden" id="addGame_developer" name="developer">
            <input type="hidden" id="addGame_publisher" name="publisher">
            
            <!-- ===== CHAMP DE RECHERCHE DE JEU ===== -->
            <!-- Recherche de jeu avec autocomplétion depuis l'API RAWG -->
            <div class="form-group">
                <label for="addGame_searchGame">Recherchez votre jeu :</label>
                <input type="text" 
                       id="addGame_searchGame" 
                       name="searchGame" 
                       placeholder="Commencez à taper le nom du jeu..." 
                       required 
                       autocomplete="off">
                <!-- Liste des suggestions de jeux -->
                <ul id="addGame_suggestions" class="suggestions-list"></ul>
            </div>

            <!-- ===== APERÇU DU JEU SÉLECTIONNÉ ===== -->
            <!-- Affichage des informations du jeu sélectionné -->
            <div class="form-group" id="addGame_gamePreview" style="display: none;">
                <div style="background: var(--background-dark); border: 2px solid var(--primary-color); border-radius: 10px; padding: 1rem; display: flex; align-items: center; gap: 1rem; margin-bottom: 1rem;">
                    <!-- Image de couverture du jeu sélectionné -->
                    <img id="addGame_selectedGameCover" 
                         src="" 
                         alt="Jaquette" 
                         style="width: 60px; height: 60px; object-fit: cover; border-radius: 8px; border: 2px solid var(--secondary-color);">
                    <div>
                        <!-- Nom du jeu sélectionné -->
                        <div id="addGame_selectedGameName" style="color: var(--secondary-color); font-weight: bold; margin-bottom: 0.3rem;"></div>
                        <!-- Détails du jeu (plateforme, année, genre) -->
                        <div id="addGame_selectedGameDetails" style="color: var(--text-color); font-size: 0.9rem;"></div>
                    </div>
                </div>
            </div>

            <!-- ===== CHAMPS VISIBLES POUR L'UTILISATEUR ===== -->
            <!-- Statut et temps de jeu -->
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
                    <input type="text" 
                           name="playtime" 
                           id="addGame_playtime" 
                           class="form-control" 
                           placeholder="Temps de jeu (en h)">
                </div>
            </div>
            
            <!-- Notes personnelles -->
            <div class="form-group">
                <label for="addGame_notes">Notes :</label>
                <textarea id="addGame_notes" 
                          name="notes" 
                          placeholder="Ajoutez vos notes sur ce jeu..."></textarea>
            </div>
            
            <!-- Bouton de soumission -->
            <button type="submit">Ajouter le jeu</button>
        </form>
    </div>
</div>

<!-- ===== RESSOURCES EXTERNES ===== -->
<!-- CSS et JS de Flatpickr pour le sélecteur de semaine -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/plugins/weekSelect/weekSelect.js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/plugins/weekSelect/weekSelect.css">

<!-- ===== SCRIPT JAVASCRIPT PRINCIPAL ===== -->
<script>
// ===== VARIABLES GLOBALES =====
// État de connexion de l'utilisateur et URL de base
const IS_USER_LOGGED_IN = <?= session()->get('user_id') ? 'true' : 'false' ?>;
const BASE_URL = '<?= base_url() ?>';

// ===== FONCTION DE VÉRIFICATION D'AUTHENTIFICATION =====
// Vérifie si l'utilisateur est connecté et redirige si nécessaire
function checkAuthAndRedirect(action = 'effectuer cette action') {
    if (!IS_USER_LOGGED_IN) {
        setTimeout(() => {
            window.location.href = BASE_URL + 'login';
        }, 500);
        return false;
    }
    return true;
}

// ===== INITIALISATION AU CHARGEMENT DE LA PAGE =====
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

// ===== FONCTION 1: GESTION DU CHANGEMENT DE PAGE =====
// Gère la navigation entre les pages de résultats
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

// ===== FONCTION 2: MODAL DE DÉTAILS DES JEUX =====
// Gère l'affichage des détails d'un jeu au clic
function initGameDetailsModal() {
    const gameModal = document.getElementById('gameModal');
    const gameModalBody = document.getElementById('gameModalBody');
    const closeGameModal = document.getElementById('closeGameModal');
    
    if (!gameModal || !gameModalBody) {
        return;
    }
    
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

// ===== FONCTION 3: MODAL D'AJOUT À MES JEUX =====
// Gère l'ajout de jeux à la collection personnelle
function initAddGameModal() {
    const addGameModal = document.getElementById('addGameModal');
    const closeAddGameModal = document.getElementById('closeAddGameModal');
    
    if (!addGameModal) {
        return;
    }
    
    // Note: La fonction openAddGameModalFromRawg est maintenant définie globalement dans script.js
    
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
    
    // ===== GESTION DU FORMULAIRE D'AJOUT =====
    const addGameForm = document.getElementById('addGameCalendarForm');
    if (addGameForm) {
        let isSubmitting = false;
        
        addGameForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Vérification de l'authentification
            if (!checkAuthAndRedirect('ajouter un jeu à votre collection')) {
                return;
            }
            
            if (isSubmitting) return;
            isSubmitting = true;
            
            // Préparation des données du formulaire
            const formData = new FormData(addGameForm);
            const jsonData = {};
            formData.forEach((value, key) => {
                jsonData[key] = value;
            });
            
            // Envoi de la requête d'ajout
            fetch(BASE_URL + 'mes-jeux/add', {
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
                        // Erreur lors de l'ajout - message déjà géré côté serveur
                    }
                }
                isSubmitting = false;
            })
            .catch(error => {
                // Erreur de réseau ou autre
                isSubmitting = false;
            });
        });
    }
    
    // Initialiser la recherche dans le modal
    initCalendarGameSearch();
}

// ===== FONCTION 4: RECHERCHE ET FILTRAGE =====
// Gère la recherche textuelle et les filtres par plateforme/genre
function initSearchAndFilters() {
    // A. Recherche de jeux dans le calendrier
    const searchInput = document.getElementById('searchGame');
    const clearSearchBtn = document.getElementById('clearSearch');
    const cards = document.querySelectorAll('.game-card-universal');
    
    // ===== FONCTION DE FILTRAGE =====
    function filterGames() {
        const searchTerm = searchInput ? searchInput.value.toLowerCase().trim() : '';
        const selectedPlatform = document.getElementById('platformFilter')?.value || '';
        const selectedGenre = document.getElementById('genreFilter')?.value || '';
        
        let visibleCount = 0;
        
        // Parcours de toutes les cartes de jeux
        cards.forEach(card => {
            const titleElement = card.querySelector('.card-name') || card.querySelector('.placeholder-title');
            if (!titleElement) return;
            
            const gameName = titleElement.textContent.toLowerCase().trim();
            const cardPlatforms = (card.dataset.platforms || '').split(',');
            const cardGenres = (card.dataset.genres || '').split(',');
            
            // Vérification des critères de filtrage
            const matchesSearch = searchTerm === '' || gameName.includes(searchTerm);
            const matchesPlatform = selectedPlatform === '' || cardPlatforms.some(p => p.trim() === selectedPlatform);
            const matchesGenre = selectedGenre === '' || cardGenres.some(g => g.trim() === selectedGenre);
            
            const shouldShow = matchesSearch && matchesPlatform && matchesGenre;
            
            // Affichage/masquage des cartes
            card.style.display = shouldShow ? 'block' : 'none';
            if (shouldShow) visibleCount++;
        });
        
        // ===== GESTION DU MESSAGE "AUCUN RÉSULTAT" =====
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
    
    // ===== EVENT LISTENERS POUR LA RECHERCHE =====
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
    
    // ===== EVENT LISTENERS POUR LES FILTRES =====
    const platformFilter = document.getElementById('platformFilter');
    const genreFilter = document.getElementById('genreFilter');
    
    if (platformFilter) {
        platformFilter.addEventListener('change', filterGames);
    }
    
    if (genreFilter) {
        genreFilter.addEventListener('change', filterGames);
    }
    
    // Filtrage initial au chargement
    filterGames();
}

// ===== FONCTION 5: FLATPICKR POUR SÉLECTION DE SEMAINE =====
// Initialise le sélecteur de semaine avec Flatpickr
function initWeekPicker() {
    if (typeof flatpickr === 'undefined') {
        return;
    }
    
    const hiddenInput = document.getElementById('hiddenWeekInput');
    if (!hiddenInput) {
        return;
    }
    
    // Configuration de Flatpickr pour la sélection de semaine
    const weekPicker = flatpickr(hiddenInput, {
        dateFormat: "Y-m-d",
        defaultDate: "<?= $startDate->format('Y-m-d') ?>",
        plugins: [new weekSelect({})],
        onChange: function(selectedDates, dateStr, instance) {
            if(selectedDates.length) {
                const date = selectedDates[0];
                const year = date.getFullYear();
                const week = getWeekNumber(date);
                window.location.href = `${BASE_URL}calendrier/${year}/${week}`;
            }
        },
        disableMobile: true,
        locale: {
            firstDayOfWeek: 1
        }
    });
    
    // Event listener pour ouvrir le sélecteur
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

// ===== FONCTION UTILITAIRE: CALCUL DU NUMÉRO DE SEMAINE ISO =====
// Calcule le numéro de semaine ISO pour une date donnée
function getWeekNumber(date) {
    const d = new Date(Date.UTC(date.getFullYear(), date.getMonth(), date.getDate()));
    const dayNum = d.getUTCDay() || 7;
    d.setUTCDate(d.getUTCDate() + 4 - dayNum);
    const yearStart = new Date(Date.UTC(d.getUTCFullYear(),0,1));
    return String(Math.ceil((((d - yearStart) / 86400000) + 1)/7)).padStart(2,'0');
}

// ===== FONCTION 6: RECHERCHE DANS LE MODAL "AJOUTER UN JEU" =====
// Gère la recherche et l'autocomplétion dans le modal d'ajout
function initCalendarGameSearch() {
    const searchInput = document.getElementById('addGame_searchGame');
    const suggestionsList = document.getElementById('addGame_suggestions');
    
    if (!searchInput || !suggestionsList) {
        return;
    }

    const API_KEY = window.CP_RAWG_API_KEY || '';
    let searchTimeout;

    // ===== FONCTION DE MISE À JOUR DES CHAMPS =====
    // Remplit automatiquement les champs avec les données du jeu sélectionné
    const updateGameFields = async (game) => {
        try {
            // Récupération des détails complets du jeu depuis l'API RAWG
            const detailResponse = await fetch(`https://api.rawg.io/api/games/${game.id}?key=${API_KEY}`);
            const gameDetails = await detailResponse.json();
            
            // Mapping des données vers les champs du formulaire
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

            // Mise à jour des champs cachés
            Object.entries(fields).forEach(([id, value]) => {
                const element = document.getElementById(id);
                if (element) element.value = value;
            });

            // ===== GESTION DE L'APERÇU DU JEU SÉLECTIONNÉ =====
            const gamePreview = document.getElementById('addGame_gamePreview');
            const selectedGameCover = document.getElementById('addGame_selectedGameCover');
            const selectedGameName = document.getElementById('addGame_selectedGameName');
            const selectedGameDetails = document.getElementById('addGame_selectedGameDetails');

            if (gamePreview && selectedGameCover && selectedGameName && selectedGameDetails) {
                gamePreview.style.display = 'block';
                
                // Gestion de l'image de couverture
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
                
                // Mise à jour des informations affichées
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

    // ===== EVENT LISTENER POUR LA RECHERCHE =====
    searchInput.addEventListener('input', (e) => {
        clearTimeout(searchTimeout);
        const query = e.target.value.trim();
        
        if (query.length < 2) {
            suggestionsList.innerHTML = '';
            return;
        }

        // Recherche avec délai pour éviter trop de requêtes
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

    // ===== FERMETURE DES SUGGESTIONS =====
    // Ferme la liste des suggestions quand on clique ailleurs
    document.addEventListener('click', (e) => {
        if (!searchInput.contains(e.target) && !suggestionsList.contains(e.target)) {
            suggestionsList.innerHTML = '';
        }
    });
}
</script>

<?php $this->endSection(); ?> 