/* =================== Gaming Library - JavaScript Optimisé =================== */

// Configuration globale
const CONFIG = {
    API_KEY: 'ff6f7941c211456c8806541638fdfaff',
    RAWG_BASE_URL: 'https://api.rawg.io/api/games',
    SEARCH_DELAY: 250,
    BASE_URL: window.CP_BASE_URL || '/checkpoint/public/'
};

// État global
const state = {
    openDropdown: null,
    searchTimeout: null
};

// =================== INITIALISATION =================== 
document.addEventListener('DOMContentLoaded', () => {
    initializeApp();
});

function initializeApp() {
    initBurgerMenu();
    initForms();
    initCards();
    initNavbarSearch();
    initModals();
}

// =================== UTILITAIRES =================== 
function debounce(func, wait) {
    return function executedFunction(...args) {
        clearTimeout(state.searchTimeout);
        state.searchTimeout = setTimeout(() => func(...args), wait);
    };
}

function showError(message) {
    console.error(message);
    // Possibilité d'ajouter une notification visuelle ici
}

function showSuccess(message) {
    console.log(message);
    // Possibilité d'ajouter une notification visuelle ici
}

// =================== GESTION DU MENU BURGER =================== 
function initBurgerMenu() {
    const burger = document.querySelector('.burger');
    const dropdown = document.getElementById('burger-dropdown');
    
    if (!burger || !dropdown) return;

    // Initialisation
    closeDropdown(dropdown);
    
    // Événements
    burger.addEventListener('click', (e) => toggleDropdown(e, dropdown));
    document.addEventListener('click', (e) => handleOutsideClick(e, burger, dropdown));
    document.addEventListener('keydown', (e) => handleEscapeKey(e, dropdown));
    
    // Fermer au clic sur les liens
    dropdown.querySelectorAll('a').forEach(link => {
        link.addEventListener('click', () => closeDropdown(dropdown));
    });
}

function toggleDropdown(e, dropdown) {
    e.preventDefault();
    e.stopPropagation();
    
    const isActive = dropdown.classList.contains('active');
    isActive ? closeDropdown(dropdown) : openDropdown(dropdown);
}

function openDropdown(dropdown) {
    dropdown.classList.add('active');
    Object.assign(dropdown.style, {
        opacity: '1',
        visibility: 'visible',
        pointerEvents: 'auto',
        transform: 'translateY(0)'
    });
    state.openDropdown = dropdown;
}

function closeDropdown(dropdown) {
    dropdown.classList.remove('active');
    Object.assign(dropdown.style, {
        opacity: '0',
        visibility: 'hidden',
        pointerEvents: 'none',
        transform: 'translateY(-10px)'
    });
    state.openDropdown = null;
}

function handleOutsideClick(e, burger, dropdown) {
    if (!burger.contains(e.target) && !dropdown.contains(e.target) && state.openDropdown) {
        closeDropdown(dropdown);
    }
}

function handleEscapeKey(e, dropdown) {
    if (e.key === 'Escape' && state.openDropdown) {
        closeDropdown(dropdown);
    }
}

// =================== GESTION DES FORMULAIRES =================== 
function initForms() {
    const form = document.getElementById('addGameForm');
    if (!form) return;

    form.addEventListener('submit', handleFormSubmit);
}

async function handleFormSubmit(e) {
    e.preventDefault();
    
    const formData = new FormData(e.target);
    const isWishlist = window.location.pathname.includes('wishlist');
    const endpoint = `${CONFIG.BASE_URL}${isWishlist ? 'wishlist' : 'mes-jeux'}/add`;
    
    const gameData = Object.fromEntries(formData);
    
    try {
        const response = await apiRequest(endpoint, 'POST', gameData);
        
        if (response.success) {
            showSuccess('Jeu ajouté avec succès');
            setTimeout(() => location.reload(), 300);
        } else {
            showError(response.error || response.message || 'Erreur lors de l\'ajout');
        }
    } catch (error) {
        showError('Erreur réseau lors de l\'ajout');
    }
}

// =================== GESTION DES CARTES =================== 
function initCards() {
    // Boutons de suppression
    document.querySelectorAll('.btn-action.delete').forEach(button => {
        button.addEventListener('click', handleDeleteCard);
    });
    
    // Cartes cliquables (pour les modales d'aperçu)
    document.querySelectorAll('.dashboard-row .game-card-universal').forEach(card => {
        card.addEventListener('click', handleCardClick);
    });
}

async function handleDeleteCard(e) {
    e.preventDefault();
    e.stopPropagation();
    
    if (!confirm('Êtes-vous sûr de vouloir supprimer ce jeu ?')) return;

    const gameId = e.target.getAttribute('data-id');
    if (!gameId) {
        showError('ID du jeu non trouvé');
        return;
    }

    const isWishlist = window.location.pathname.includes('wishlist');
    const endpoint = `${CONFIG.BASE_URL}${isWishlist ? 'wishlist' : 'mes-jeux'}/delete/${gameId}`;

    try {
        const response = await apiRequest(endpoint, 'POST');

        if (response.success) {
            const card = e.target.closest('.game-card-universal');
            if (card) {
                card.remove();
                checkEmptyPage();
                showSuccess('Jeu supprimé avec succès');
            }
        } else {
            showError(response.error || 'Erreur lors de la suppression');
        }
    } catch (error) {
        showError('Erreur réseau lors de la suppression');
    }
}

function handleCardClick(e) {
    // Empêcher l'ouverture du modal si on clique sur un bouton
    if (e.target.classList.contains('btn-action')) return;
    
    const cardData = extractCardData(e.currentTarget);
    openGameViewModal(cardData);
}

function extractCardData(card) {
    return {
        name: card.dataset.name || '',
        cover: card.dataset.cover || '',
        platform: card.dataset.platform || '',
        release: card.dataset.release || '',
        genre: card.dataset.genre || '',
        status: card.dataset.status || '',
        playtime: card.dataset.playtime || '',
        notes: card.dataset.notes || ''
    };
}

function checkEmptyPage() {
    const container = document.querySelector('.dashboard-row');
    const cards = document.querySelectorAll('.game-card-universal');
    
    if (container && cards.length === 0) {
        const isWishlist = window.location.pathname.includes('wishlist');
        const messageClass = isWishlist ? 'wishlist-empty-message' : 'games-empty-message';
        const messageText = isWishlist ? 'Votre wishlist est vide.' : 'Vous n\'avez aucun jeu.';
        container.innerHTML = `<p class="${messageClass}">${messageText}</p>`;
    }
}

// =================== RECHERCHE NAVBAR =================== 
function initNavbarSearch() {
    const input = document.getElementById('navbarGameSearchInput');
    const suggestions = document.getElementById('navbarGameSuggestions');

    if (!input || !suggestions) return;

    const debouncedSearch = debounce(performSearch, CONFIG.SEARCH_DELAY);
    
    input.addEventListener('input', (e) => {
        const query = e.target.value.trim();
        if (query.length < 2) {
            hideSuggestions(suggestions);
            return;
        }
        debouncedSearch(query, suggestions);
    });

    document.addEventListener('click', (e) => {
        if (!input.contains(e.target) && !suggestions.contains(e.target)) {
            hideSuggestions(suggestions);
        }
    });
}

async function performSearch(query, suggestions) {
    try {
        const url = `${CONFIG.RAWG_BASE_URL}?key=${CONFIG.API_KEY}&search=${encodeURIComponent(query)}`;
        const response = await fetch(url);
        const data = await response.json();
        
        renderSuggestions(data.results || [], suggestions);
    } catch (error) {
        renderErrorSuggestion(suggestions);
    }
}

function renderSuggestions(games, suggestions) {
    suggestions.innerHTML = '';
    
    if (games.length === 0) {
        suggestions.innerHTML = '<li style="color:#BB86FC;padding:0.5rem;">Aucun résultat</li>';
    } else {
        games.slice(0, 8).forEach(game => {
            const li = createSuggestionItem(game);
            suggestions.appendChild(li);
        });
    }
    
    showSuggestions(suggestions);
}

function createSuggestionItem(game) {
    const li = document.createElement('li');
    li.textContent = game.name;
    Object.assign(li.style, {
        cursor: 'pointer',
        fontStyle: 'italic',
        color: '#00E5FF'
    });
    
    li.addEventListener('click', () => {
        openGameModal(game.id);
        hideSuggestions(document.getElementById('navbarGameSuggestions'));
        document.getElementById('navbarGameSearchInput').value = '';
    });
    
    return li;
}

function renderErrorSuggestion(suggestions) {
    suggestions.innerHTML = '<li style="color:#BB86FC;padding:0.5rem;">Erreur</li>';
    showSuggestions(suggestions);
}

function showSuggestions(suggestions) {
    suggestions.style.display = 'block';
}

function hideSuggestions(suggestions) {
    suggestions.style.display = 'none';
    suggestions.innerHTML = '';
}

// =================== GESTION DES MODALES =================== 
function initModals() {
    initGameModal();
    initGameViewModal();
}

function initGameModal() {
    const modal = document.getElementById('gameModal');
    const closeBtn = document.getElementById('closeGameModal');
    
    if (!modal || !closeBtn) return;
    
    closeBtn.addEventListener('click', () => closeModal(modal));
    modal.addEventListener('click', (e) => {
        if (e.target === modal) closeModal(modal);
    });
}

function initGameViewModal() {
    const modal = document.getElementById('gameViewModal');
    const closeBtn = document.getElementById('closeGameViewModal');
    
    if (!modal || !closeBtn) return;
    
    closeBtn.addEventListener('click', () => closeModal(modal));
    modal.addEventListener('click', (e) => {
        if (e.target === modal) closeModal(modal);
    });
}

async function openGameModal(gameId) {
    const modal = document.getElementById('gameModal');
    const modalBody = document.getElementById('gameModalBody');
    
    if (!modal || !modalBody) return;
    
    modalBody.innerHTML = '<span style="color:#BB86FC;">Chargement...</span>';
    openModal(modal);
    
    try {
        const response = await fetch(`${CONFIG.RAWG_BASE_URL}/${gameId}?key=${CONFIG.API_KEY}`);
        const game = await response.json();
        renderGameModalContent(game, modalBody);
    } catch (error) {
        modalBody.innerHTML = '<span style="color:#FF6F61;">Erreur lors du chargement des infos du jeu.</span>';
    }
}

function openGameViewModal(cardData) {
    const modal = document.getElementById('gameViewModal');
    const modalBody = document.getElementById('gameViewModalBody');
    
    if (!modal || !modalBody) return;
    
    const html = createGameViewContent(cardData);
    modalBody.innerHTML = html;
    openModal(modal);
}

function createGameViewContent(data) {
    let html = '';
    if (data.cover) {
        html += `<img src="${data.cover}" alt="${data.name}" style="width:220px;height:220px;object-fit:cover;border-radius:12px;box-shadow:0 2px 12px #7F39FB44;margin-bottom:1.2rem;">`;
    }
    html += `<h2 style="color:#9B5DE5;margin-bottom:0.7rem;">${data.name}</h2>`;
    html += `<div style="color:#BB86FC;font-size:1.05rem;margin-bottom:0.7rem;">Plateforme : ${data.platform || 'Inconnue'}<br>Année : ${data.release || 'Inconnue'}<br>Genre : ${data.genre || 'Inconnu'}</div>`;
    html += `<div style="color:#E0F7FA;font-size:1rem;margin-bottom:1.2rem;">Statut : ${data.status || 'Inconnu'}<br>Temps de jeu : ${data.playtime || '0'} h</div>`;
    html += `<div style="color:#BB86FC;font-size:0.98rem;margin-bottom:0.5rem;"><b>Notes :</b> ${data.notes || '<i>Aucune note</i>'}</div>`;
    return html;
}

function renderGameModalContent(game, modalBody) {
    const coverImage = game.background_image ? 
        `<img src="${game.background_image}" alt="${game.name}" style="width:220px;height:220px;object-fit:cover;border-radius:12px;box-shadow:0 2px 12px #7F39FB44;margin-bottom:1.2rem;">` :
        createInlineCoverPlaceholder(game.name);
    
    const platforms = game.platforms?.length ? game.platforms.map(p => p.platform.name).join(', ') : 'Inconnue';
    const year = extractYear(game.released) || 'Inconnue';
    const genres = game.genres?.length ? game.genres.map(g => g.name).join(', ') : 'Inconnu';
    const developers = game.developers?.length ? game.developers.map(d => d.name).join(', ') : 'Inconnu';
    const publishers = game.publishers?.length ? game.publishers.map(p => p.name).join(', ') : 'Inconnu';
    const description = game.description_raw ? 
        `<div style="color:#E0F7FA;font-size:1rem;margin-bottom:1.2rem;max-height:120px;overflow:auto;">${game.description_raw}</div>` : '';
    
    modalBody.innerHTML = `
        ${coverImage}
        <h2 style="color:#9B5DE5;margin-bottom:0.7rem;">${game.name}</h2>
        <div style="color:#BB86FC;font-size:1.05rem;margin-bottom:0.7rem;">
            Plateforme : ${platforms}<br>
            Année : ${year}<br>
            Genre : ${genres}
        </div>
        <div style="color:#BB86FC;font-size:1.05rem;margin-bottom:0.7rem;">
            Développeur : ${developers}
        </div>
        <div style="color:#BB86FC;font-size:1.05rem;margin-bottom:1.2rem;">
            Éditeur : ${publishers}
        </div>
        ${description}
        <div style="margin-top:1.5rem;text-align:center;">
            <button onclick="addGameFromRawg(${JSON.stringify(game).replace(/"/g, '&quot;')}, 'mes-jeux')" class="home-btn" style="margin:0 0.5rem 0.5rem 0;">Ajouter à mes jeux</button>
            <button onclick="addGameFromRawg(${JSON.stringify(game).replace(/"/g, '&quot;')}, 'wishlist')" class="home-btn" style="background:linear-gradient(90deg,#00E5FF 80%,#9B5DE5 100%);color:#1E1E2F;border-color:#00E5FF;">Ajouter à la wishlist</button>
        </div>
    `;
}

function openModal(modal) {
    modal.classList.add('active');
}

function closeModal(modal) {
    modal.classList.remove('active');
}

// =================== AJOUT DE JEUX DEPUIS RAWG =================== 
async function addGameFromRawg(rawgGame, targetList) {
    const gameData = {
        game_id: rawgGame.id,
        searchGame: rawgGame.name || 'Jeu sans nom',
        platform: rawgGame.platforms?.[0]?.platform?.name || 'Inconnue',
        releaseYear: extractYear(rawgGame.released),
        genre: rawgGame.genres?.map(g => g.name).join(', ') || '',
        cover: rawgGame.background_image || '',
        developer: rawgGame.developers?.map(d => d.name).join(', ') || '',
        publisher: rawgGame.publishers?.map(p => p.name).join(', ') || ''
    };
    
    if (targetList === 'mes-jeux') {
        gameData.status = 'en cours';
    }

    try {
        const endpoint = `${CONFIG.BASE_URL}${targetList}/add`;
        const response = await apiRequest(endpoint, 'POST', gameData);
        
        if (response.success) {
            closeModal(document.getElementById('gameModal'));
            showSuccess(`Jeu ajouté à ${targetList === 'mes-jeux' ? 'mes jeux' : 'la wishlist'}`);
            
            // Recharger si on est sur la bonne page
            if (window.location.pathname.includes(targetList)) {
                setTimeout(() => location.reload(), 300);
            }
        } else {
            showError(response.error || response.message || 'Erreur lors de l\'ajout');
        }
    } catch (error) {
        showError('Erreur réseau lors de l\'ajout');
    }
}

// Fonctions globales pour compatibilité avec les boutons inline
window.addToMyGamesFromRawg = (rawgGame) => addGameFromRawg(rawgGame, 'mes-jeux');
window.addToWishlistFromRawg = (rawgGame) => addGameFromRawg(rawgGame, 'wishlist');

// =================== UTILITAIRES API =================== 
async function apiRequest(url, method = 'GET', data = null) {
    const options = {
        method,
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    };
    
    if (data && method !== 'GET') {
        options.headers['Content-Type'] = 'application/json';
        options.body = JSON.stringify(data);
    }
    
    const response = await fetch(url, options);
    return await response.json();
}

// =================== UTILITAIRES POUR LES JEUX =================== 
// Fonction utilitaire pour extraire l'année d'une date
function extractYear(dateString) {
    return dateString ? dateString.split('-')[0] : '';
}

// Constante pour le texte uniforme des jaquettes manquantes
const NO_COVER_TEXT = 'Aucune jaquette';

// Fonction utilitaire pour créer un placeholder de jaquette uniforme (pour les vues)
function createGameCoverPlaceholder(gameName, size = 'normal') {
    const sizeClasses = {
        'small': 'size-small',
        'normal': '',
        'large': 'size-large'
    };
    
    return `
        <div class="game-cover-placeholder ${sizeClasses[size] || ''}">
            <div class="placeholder-title">${gameName || 'Jeu sans nom'}</div>
            <div class="placeholder-text">${NO_COVER_TEXT}</div>
        </div>
    `;
}

// Fonction utilitaire pour créer un placeholder inline (pour les modals)
function createInlineCoverPlaceholder(gameName, width = '220px', height = '220px') {
    return `
        <div style="width:${width};height:${height};margin:0 auto 1.2rem auto;background:linear-gradient(45deg, #1F1B2E, #2A1B3D);border-radius:10px;display:flex;flex-direction:column;justify-content:center;align-items:center;padding:0.5rem;box-sizing:border-box;text-align:center;border:2px solid #7F39FB;box-shadow:0 2px 8px #7F39FB44;">
            <div style="color:#9B5DE5;font-size:1.2rem;font-weight:bold;margin-bottom:0.5rem;text-shadow:0 2px 8px rgba(0,0,0,0.5);letter-spacing:1px;line-height:1.2;">${gameName || 'Jeu sans nom'}</div>
            <div style="color:#BB86FC;font-size:0.9rem;opacity:0.8;max-width:85%;line-height:1.3;text-align:center;">${NO_COVER_TEXT}</div>
        </div>
    `;
}

// Fonction utilitaire pour créer un placeholder small (pour les aperçus de modals)
function createSmallCoverPlaceholder(gameName) {
    const placeholderHTML = createGameCoverPlaceholder(gameName, 'small');
    const tempDiv = document.createElement('div');
    tempDiv.innerHTML = placeholderHTML;
    const placeholder = tempDiv.firstElementChild;
    placeholder.style.cssText = 'width: 60px; height: 60px; border-radius: 8px; border: 2px solid var(--secondary-color); margin-right: 1rem; display: flex; flex-direction: column; justify-content: center; align-items: center;';
    return placeholder;
}