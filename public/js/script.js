/* =================== Gaming Library - JavaScript Optimisé =================== */

const CONFIG = {
    API_KEY: 'ff6f7941c211456c8806541638fdfaff',
    BASE_URL: window.CP_BASE_URL || '',
    SEARCH_DELAY: 300,
    MIN_SEARCH_LENGTH: 2
};

// Classe principale pour gérer l'application
class GameLibraryApp {
    constructor() {
        this.initEventListeners();
        this.dropdownTimeout = null;
    }

    initEventListeners() {
        document.addEventListener('DOMContentLoaded', () => {
            this.initDropdownMenu();
            this.initNavbarSearch();
            this.initModalSystem();
            this.initGameModals();
            this.initGameActions();
        });
    }

    initDropdownMenu() {
        const burger = document.querySelector('.burger');
        const dropdown = document.querySelector('.dropdown');
        
        if (!burger || !dropdown) return;

        burger.addEventListener('click', (e) => {
            e.stopPropagation();
            dropdown.classList.toggle('active');
        });

        document.addEventListener('click', (e) => {
            if (!dropdown.contains(e.target) && !burger.contains(e.target)) {
                dropdown.classList.remove('active');
            }
        });
    }

    showMessage(message, type = 'info') {
        // Affichage silencieux des messages
        if (type === 'error') {
            // Gérer les erreurs sans console.error
        }
    }

    initNavbarSearch() {
        const searchInput = document.getElementById('navbarGameSearchInput');
        const suggestionsContainer = document.getElementById('navbarGameSuggestions');
        
        if (!searchInput || !suggestionsContainer) return;

        let searchTimeout;

        searchInput.addEventListener('input', (e) => {
            clearTimeout(searchTimeout);
            const query = e.target.value.trim();

            if (query.length < CONFIG.MIN_SEARCH_LENGTH) {
                this.hideSuggestions(suggestionsContainer);
                return;
            }

            searchTimeout = setTimeout(() => {
                this.searchGames(query, suggestionsContainer);
            }, CONFIG.SEARCH_DELAY);
        });

        document.addEventListener('click', (e) => {
            if (!searchInput.contains(e.target) && !suggestionsContainer.contains(e.target)) {
                this.hideSuggestions(suggestionsContainer);
            }
        });
    }

    async searchGames(query, container) {
        const response = await fetch(`https://api.rawg.io/api/games?key=${CONFIG.API_KEY}&search=${encodeURIComponent(query)}&page_size=8`);
        const data = await response.json();
        this.displaySuggestions(data.results || [], container);
    }

    displaySuggestions(games, container) {
        if (games.length === 0) {
            container.innerHTML = '<li style="padding:8px 12px;color:#BB86FC;">Aucun résultat trouvé</li>';
            container.style.display = 'block';
            return;
        }

        container.innerHTML = games.map(game => {
            const released = game.released ? new Date(game.released).getFullYear() : 'Année inconnue';
            const platform = game.platforms?.[0]?.platform?.name || 'Plateforme inconnue';
            
            return `
                <li onclick="openGameModal(${game.id})" style="padding:8px 12px;cursor:pointer;border-bottom:1px solid #2D2742;">
                    <div style="font-weight:bold;color:#9B5DE5;">${game.name}</div>
                    <small style="color:#BB86FC;">${platform} • ${released}</small>
                </li>
            `;
        }).join('');
        
        container.style.display = 'block';
    }

    hideSuggestions(container) {
        container.style.display = 'none';
        container.innerHTML = '';
    }

    initModalSystem() {
        document.addEventListener('click', (e) => {
            if (e.target.classList.contains('modal')) {
                e.target.classList.remove('active');
            }
            
            if (e.target.classList.contains('modal-close')) {
                const modal = e.target.closest('.modal');
                if (modal) modal.classList.remove('active');
            }
        });
    }

    initGameModals() {
        window.openGameModal = (gameId) => {
            const modal = document.getElementById('gameModal');
            const modalBody = document.getElementById('gameModalBody');
            
            if (!modal || !modalBody) return;
            
            modalBody.innerHTML = '<div style="text-align:center;color:#BB86FC;padding:2rem;">Chargement...</div>';
            modal.classList.add('active');
            
            this.loadGameDetails(gameId, modalBody);
        };
    }

    async loadGameDetails(gameId, container) {
        const response = await fetch(`https://api.rawg.io/api/games/${gameId}?key=${CONFIG.API_KEY}`);
        const game = await response.json();
        this.renderGameModal(game, container);
    }

    renderGameModal(game, container) {
        const coverHtml = game.background_image 
            ? `<img src="${game.background_image}" alt="${game.name}" style="width:200px;height:200px;object-fit:cover;border-radius:10px;margin-bottom:1rem;">`
            : this.createInlineCoverPlaceholder(game.name);

        container.innerHTML = `
            ${coverHtml}
            <h2 style="color:#9B5DE5;margin-bottom:1rem;">${game.name}</h2>
            <div style="color:#BB86FC;margin-bottom:1rem;">
                <strong>Plateforme:</strong> ${game.platforms?.map(p => p.platform.name).join(', ') || 'Inconnue'}<br>
                <strong>Sortie:</strong> ${game.released || 'Inconnue'}<br>
                <strong>Genre:</strong> ${game.genres?.map(g => g.name).join(', ') || 'Inconnu'}
            </div>
            ${game.developers?.length ? `<div style="color:#BB86FC;margin-bottom:1rem;"><strong>Développeur:</strong> ${game.developers.map(d => d.name).join(', ')}</div>` : ''}
            ${game.publishers?.length ? `<div style="color:#BB86FC;margin-bottom:1rem;"><strong>Éditeur:</strong> ${game.publishers.map(p => p.name).join(', ')}</div>` : ''}
            <div style="color:#E0F7FA;margin-bottom:1.5rem;max-height:100px;overflow:auto;">
                ${game.description_raw || 'Aucune description disponible.'}
            </div>
            <div style="text-align:center;">
                <button onclick="window.gameLibrary.addToWishlist(${JSON.stringify(game).replace(/"/g, '&quot;')})" 
                        class="home-btn" style="margin:0.5rem;">Ajouter à la wishlist</button>
                <button onclick="window.gameLibrary.addToMyGames(${JSON.stringify(game).replace(/"/g, '&quot;')})" 
                        class="home-btn" style="margin:0.5rem;background:#00E5FF;color:#1E1E2F;">Ajouter à mes jeux</button>
            </div>
        `;
    }

    createInlineCoverPlaceholder(title) {
        return `
            <div style="width:200px;height:200px;background:linear-gradient(45deg,#1F1B2E,#2A1B3D);border-radius:10px;display:flex;flex-direction:column;justify-content:center;align-items:center;margin:0 auto 1rem;border:2px solid #7F39FB;">
                <div style="color:#9B5DE5;font-weight:bold;text-align:center;padding:1rem;font-size:1.1rem;">${title}</div>
                <div style="color:#BB86FC;opacity:0.8;font-size:0.9rem;">Aucune jaquette</div>
            </div>
        `;
    }

    addToWishlist(game) {
        if (!this.checkAuth()) return;
        
        const gameData = {
            game_id: game.id,
            searchGame: game.name,
            platform: game.platforms?.[0]?.platform?.name || 'Inconnue',
            releaseYear: this.extractYear(game.released),
            genre: game.genres?.map(g => g.name).join(', ') || '',
            cover: game.background_image || '',
            developer: game.developers?.map(d => d.name).join(', ') || '',
            publisher: game.publishers?.map(p => p.name).join(', ') || ''
        };

        this.submitToEndpoint('/checkpoint/public/wishlist/add', gameData);
        
        // Fermer le modal après l'ajout
        document.getElementById('gameModal')?.classList.remove('active');
    }

    addToMyGames(game) {
        if (!this.checkAuth()) return;
        document.getElementById('gameModal')?.classList.remove('active');
        window.openAddGameModalFromRawg?.(game);
    }

    checkAuth() {
        // Vérification simple : si on a les éléments du menu connecté, on est connecté
        const profileLink = document.querySelector('a[href*="profile"]');
        const logoutLink = document.querySelector('a[href*="logout"]');
        const isLoggedIn = profileLink && logoutLink;
        
        if (!isLoggedIn) {
            setTimeout(() => window.location.href = CONFIG.BASE_URL + 'login', 500);
            return false;
        }
        return true;
    }

    async submitToEndpoint(url, data) {
        const response = await fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify(data)
        });
        
        const result = await response.json();
        if (!result.success && result.error?.includes('non connecté')) {
            setTimeout(() => window.location.href = CONFIG.BASE_URL + 'login', 500);
        }
    }

    extractYear(dateString) {
        return dateString ? new Date(dateString).getFullYear() : '';
    }

    initGameActions() {
        // Délégation d'événements pour les actions de jeux
        document.addEventListener('click', async (e) => {
            if (e.target.matches('.btn-action.delete')) {
                await this.handleDelete(e.target);
            } else if (e.target.matches('.btn-action.edit')) {
                this.handleEdit(e.target);
            }
        });
    }

    async handleDelete(button) {
        if (!confirm('Êtes-vous sûr de vouloir supprimer ce jeu ?')) return;
        
        const gameId = button.dataset.id;
        if (!gameId) return;

        const currentPath = window.location.pathname;
        let endpoint = '';
        
        if (currentPath.includes('/mes-jeux')) {
            endpoint = `/checkpoint/public/mes-jeux/delete/${gameId}`;
        } else if (currentPath.includes('/wishlist')) {
            endpoint = `/checkpoint/public/wishlist/delete/${gameId}`;
        }
        
        if (endpoint) {
            const response = await fetch(endpoint, {
                method: 'POST',
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });
            
            const data = await response.json();
            if (data.success) {
                button.closest('.game-card-universal')?.remove();
                this.checkEmptyPage();
            }
        }
    }

    handleEdit(button) {
        if (!this.checkAuth()) return;
        
        const gameData = JSON.parse(button.dataset.game || '{}');
        this.openEditModal(gameData);
    }

    openEditModal(gameData) {
        const modal = document.getElementById('editGameModal');
        if (!modal) return;
        
        // Remplir les champs du modal d'édition
        Object.entries(gameData).forEach(([key, value]) => {
            const field = document.getElementById(`edit_${key}`);
            if (field) field.value = value || '';
        });
        
        modal.classList.add('active');
    }

    checkEmptyPage() {
        const gameCards = document.querySelectorAll('.game-card-universal');
        if (gameCards.length === 0) {
            setTimeout(() => location.reload(), 300);
        }
    }
}

// Fonctions utilitaires globales
window.createSmallCoverPlaceholder = (title) => {
    const placeholder = document.createElement('div');
    placeholder.className = 'game-cover-placeholder';
    placeholder.style.cssText = 'width:60px;height:60px;display:flex;flex-direction:column;justify-content:center;align-items:center;background:linear-gradient(45deg,#1F1B2E,#2A1B3D);border-radius:8px;border:2px solid #7F39FB;';
    placeholder.innerHTML = `
        <div class="placeholder-title" style="color:#9B5DE5;font-weight:bold;font-size:0.8rem;text-align:center;padding:0.2rem;">${title}</div>
    `;
    return placeholder;
};

window.createInlineCoverPlaceholder = (title) => {
    return `
        <div style="width:220px;height:220px;background:linear-gradient(45deg,#1F1B2E,#2A1B3D);border-radius:12px;display:flex;flex-direction:column;justify-content:center;align-items:center;margin:0 auto 1.2rem;border:2px solid #7F39FB;">
            <div style="color:#9B5DE5;font-weight:bold;text-align:center;padding:1rem;font-size:1.2rem;">${title}</div>
            <div style="color:#BB86FC;opacity:0.8;font-size:1rem;">Aucune jaquette</div>
        </div>
    `;
};

window.extractYear = (dateString) => {
    return dateString ? new Date(dateString).getFullYear() : '';
};

// Initialisation
window.gameLibrary = new GameLibraryApp();

// Fonction globale simplifiée pour ouvrir le modal d'ajout de jeu
window.openAddGameModalFromRawg = function(game) {
    // Vérifier si on est connecté
    const profileLink = document.querySelector('a[href*="profile"]');
    const logoutLink = document.querySelector('a[href*="logout"]');
    const isLoggedIn = profileLink && logoutLink;
    
    if (!isLoggedIn) {
        setTimeout(() => window.location.href = CONFIG.BASE_URL + 'login', 500);
        return;
    }

    // Utiliser le modal global
    const globalModal = document.getElementById('globalAddGameModal');
    if (!globalModal) return;

    // Remplir les champs cachés
    const fields = {
        'global_addGame_game_id': game.id || '',
        'global_addGame_searchGame': game.name || '',
        'global_addGame_platform': game.platforms?.[0]?.platform?.name || 'Inconnue',
        'global_addGame_releaseYear': game.released ? new Date(game.released).getFullYear() : '',
        'global_addGame_genre': game.genres?.map(g => g.name).join(', ') || '',
        'global_addGame_cover': game.background_image || '',
        'global_addGame_developer': game.developers?.map(d => d.name).join(', ') || '',
        'global_addGame_publisher': game.publishers?.map(p => p.name).join(', ') || ''
    };

    Object.entries(fields).forEach(([id, value]) => {
        const element = document.getElementById(id);
        if (element) element.value = value;
    });

    // Afficher le nom du jeu
    const selectedGameName = document.getElementById('global_selectedGameName');
    if (selectedGameName) {
        selectedGameName.textContent = game.name || 'Jeu sélectionné';
    }

    // Réinitialiser les champs utilisateur
    ['global_addGame_status', 'global_addGame_playtime', 'global_addGame_notes'].forEach(id => {
        const element = document.getElementById(id);
        if (element) element.value = '';
    });

    // Ouvrir le modal
    globalModal.classList.add('active');
};

// Gestion du modal global d'ajout
document.addEventListener('DOMContentLoaded', function() {
    const globalModal = document.getElementById('globalAddGameModal');
    const closeBtn = document.getElementById('closeGlobalAddGameModal');
    const form = document.getElementById('globalAddGameForm');
    
    if (!globalModal) return;
    
    // Fermer le modal
    closeBtn?.addEventListener('click', function() {
        globalModal.classList.remove('active');
    });
    
    globalModal.addEventListener('click', function(e) {
        if (e.target === this) this.classList.remove('active');
    });
    
    // Gestion du formulaire
    form?.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
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
                globalModal.classList.remove('active');
                setTimeout(() => location.reload(), 300);
            }
        });
    });
});