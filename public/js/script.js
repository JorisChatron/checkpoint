/* =================== Gaming Library - JavaScript Optimisé =================== */

/**
 * Configuration globale de l'application
 * @constant {Object} CONFIG
 */
const CONFIG = {
    // Clé API pour accéder à RAWG.io (API de jeux vidéo)
    API_KEY: 'ff6f7941c211456c8806541638fdfaff',
    // URL de base de l'application, peut être configurée globalement
    BASE_URL: window.CP_BASE_URL || '',
    // Délai en millisecondes avant de déclencher la recherche
    SEARCH_DELAY: 300,
    // Nombre minimum de caractères pour déclencher la recherche
    MIN_SEARCH_LENGTH: 2
};

/**
 * Classe principale qui gère toute la logique de l'application
 * Implémente le pattern Singleton pour une instance unique
 */
class GameLibraryApp {
    /**
     * Initialise l'application
     * Crée une instance unique et configure les écouteurs d'événements
     */
    constructor() {
        this.initEventListeners();
    }

    /**
     * Configure tous les écouteurs d'événements au chargement du DOM
     * Initialise les différentes fonctionnalités de l'application
     */
    initEventListeners() {
        document.addEventListener('DOMContentLoaded', () => {
            this.initDropdownMenu();     // Menu déroulant
            this.initNavbarSearch();     // Barre de recherche
            this.initModalSystem();      // Système de modales
            this.initGameModals();       // Modales spécifiques aux jeux
            this.initGameActions();      // Actions sur les jeux (suppression uniquement)
            this.initGlobalAddModal();   // Modal d'ajout global
        });
    }

    /**
     * Initialise le menu dropdown du burger menu
     * Gère l'ouverture/fermeture et les clics en dehors
     */
    initDropdownMenu() {
        const burger = document.querySelector('.burger');
        const dropdown = document.querySelector('.dropdown');
        
        // Sort si les éléments n'existent pas
        if (!burger || !dropdown) return;

        // Ouvre/ferme le dropdown au clic sur le burger
        burger.addEventListener('click', (e) => {
            e.stopPropagation();  // Empêche la propagation du clic
            dropdown.classList.toggle('active');
        });

        // Ferme le dropdown si on clique en dehors
        document.addEventListener('click', (e) => {
            if (!dropdown.contains(e.target) && !burger.contains(e.target)) {
                dropdown.classList.remove('active');
            }
        });
    }

    /**
     * Initialise la barre de recherche de la navbar
     * Configure l'autocomplétion et la recherche de jeux
     */
    initNavbarSearch() {
        const searchInput = document.getElementById('navbarGameSearchInput');
        const suggestionsContainer = document.getElementById('navbarGameSuggestions');
        
        // Sort si les éléments n'existent pas
        if (!searchInput || !suggestionsContainer) return;

        // Timer pour le délai de recherche
        let searchTimeout;

        // Gère la saisie dans la barre de recherche
        searchInput.addEventListener('input', (e) => {
            clearTimeout(searchTimeout);  // Réinitialise le timer à chaque frappe
            const query = e.target.value.trim();

            // Vérifie si la recherche est assez longue
            if (query.length < CONFIG.MIN_SEARCH_LENGTH) {
                this.hideSuggestions(suggestionsContainer);
                return;
            }

            // Délai avant de lancer la recherche
            searchTimeout = setTimeout(() => {
                this.searchGames(query, suggestionsContainer);
            }, CONFIG.SEARCH_DELAY);
        });

        // Ferme les suggestions si on clique en dehors
        document.addEventListener('click', (e) => {
            if (!searchInput.contains(e.target) && !suggestionsContainer.contains(e.target)) {
                this.hideSuggestions(suggestionsContainer);
            }
        });
    }

    /**
     * Recherche des jeux via l'API RAWG
     * @param {string} query - La requête de recherche
     * @param {HTMLElement} container - Le conteneur où afficher les suggestions
     */
    async searchGames(query, container) {
        try {
            const response = await fetch(`https://api.rawg.io/api/games?key=${CONFIG.API_KEY}&search=${encodeURIComponent(query)}&page_size=8`);
            const data = await response.json();
            this.displaySuggestions(data.results || [], container);
        } catch (error) {
            container.innerHTML = '<li style="padding:8px 12px;color:#f44336;">Erreur de recherche</li>';
            container.style.display = 'block';
        }
    }

    /**
     * Affiche les suggestions de jeux dans le conteneur spécifié
     * @param {Array} games - La liste des jeux à afficher
     * @param {HTMLElement} container - Le conteneur où afficher les jeux
     */
    displaySuggestions(games, container) {
        // Aucun résultat trouvé
        if (games.length === 0) {
            container.innerHTML = '<li style="padding:8px 12px;color:#BB86FC;">Aucun résultat trouvé</li>';
            container.style.display = 'block';
            return;
        }

        // Affiche les jeux trouvés
        container.innerHTML = games.map(game => {
            const released = game.released ? new Date(game.released).getFullYear() : 'Année inconnue';
            const platform = game.platforms?.[0]?.platform?.name || 'Plateforme inconnue';
            
            return `
                <li onclick="window.gameLibrary.openGameModal(${game.id})" style="padding:8px 12px;cursor:pointer;border-bottom:1px solid #2D2742;">
                    <div style="font-weight:bold;color:#9B5DE5;">${game.name}</div>
                    <small style="color:#BB86FC;">${platform} • ${released}</small>
                </li>
            `;
        }).join('');
        
        container.style.display = 'block';
    }

    /**
     * Cache les suggestions de jeux
     * @param {HTMLElement} container - Le conteneur des suggestions
     */
    hideSuggestions(container) {
        container.style.display = 'none';
        container.innerHTML = '';
    }

    /**
     * Initialise le système de modales
     * Gère l'ouverture et la fermeture des modales
     */
    initModalSystem() {
        document.addEventListener('click', (e) => {
            // Ferme la modal si on clique en dehors
            if (e.target.classList.contains('modal')) {
                e.target.classList.remove('active');
            }
            
            // Gère le bouton de fermeture des modales
            if (e.target.classList.contains('modal-close')) {
                const modal = e.target.closest('.modal');
                if (modal) modal.classList.remove('active');
            }
        });
    }

    /**
     * Initialise les modales spécifiques aux jeux
     * Configure l'ouverture des modales avec les détails du jeu
     */
    initGameModals() {
        // Fonction globale pour ouvrir une modal de jeu
        window.openGameModal = (gameId) => this.openGameModal(gameId);
    }

    /**
     * Ouvre une modal avec les détails d'un jeu
     * @param {number} gameId - L'ID du jeu à afficher
     */
    openGameModal(gameId) {
        const modal = document.getElementById('gameModal');
        const modalBody = document.getElementById('gameModalBody');
        
        // Sort si les éléments n'existent pas
        if (!modal || !modalBody) return;
        
        // Indicateur de chargement
        modalBody.innerHTML = '<div style="text-align:center;color:#BB86FC;padding:2rem;">Chargement...</div>';
        modal.classList.add('active');
        
        // Charge les détails du jeu
        this.loadGameDetails(gameId, modalBody);
    }

    /**
     * Charge les détails d'un jeu spécifique
     * @param {number} gameId - L'ID du jeu à charger
     * @param {HTMLElement} container - Le conteneur où afficher les détails du jeu
     */
    async loadGameDetails(gameId, container) {
        try {
            const response = await fetch(`https://api.rawg.io/api/games/${gameId}?key=${CONFIG.API_KEY}`);
            const game = await response.json();
            this.renderGameModal(game, container);
        } catch (error) {
            container.innerHTML = '<div style="text-align:center;color:#f44336;padding:2rem;">Erreur lors du chargement</div>';
        }
    }

    /**
     * Rendu des détails du jeu dans la modal
     * @param {Object} game - Les données du jeu
     * @param {HTMLElement} container - Le conteneur où afficher les détails du jeu
     */
    renderGameModal(game, container) {
        // Affiche la jaquette du jeu ou un placeholder
        const coverHtml = game.background_image 
            ? `<img src="${game.background_image}" alt="${game.name}" style="width:200px;height:200px;object-fit:cover;border-radius:10px;margin-bottom:1rem;">`
            : this.createInlineCoverPlaceholder(game.name);

        // Remplit le contenu de la modal avec les détails du jeu
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
                <button class="home-btn game-action-btn" data-action="wishlist" style="margin:0.5rem;">Ajouter à la wishlist</button>
                <button class="home-btn game-action-btn" data-action="mygames" style="margin:0.5rem;background:#00E5FF;color:#1E1E2F;">Ajouter à mes jeux</button>
            </div>
        `;
        
        // Stocker les données du jeu de manière sûre dans le conteneur
        container.gameData = game;
        
        // Ajouter des écouteurs d'événements aux boutons
        const actionButtons = container.querySelectorAll('.game-action-btn');
        actionButtons.forEach(button => {
            button.addEventListener('click', (e) => {
                const action = e.target.dataset.action;
                const gameData = container.gameData;
                
                if (action === 'wishlist') {
                    this.addToWishlist(gameData);
                } else if (action === 'mygames') {
                    this.addToMyGames(gameData);
                }
            });
        });
    }

    /**
     * Crée un placeholder pour la jaquette du jeu (style inline)
     * @param {string} title - Le titre du jeu, utilisé dans le placeholder
     * @returns {string} - Le HTML du placeholder
     */
    createInlineCoverPlaceholder(title) {
        return `
            <div style="width:200px;height:200px;background:linear-gradient(45deg,#1F1B2E,#2A1B3D);border-radius:10px;display:flex;flex-direction:column;justify-content:center;align-items:center;margin:0 auto 1rem;border:2px solid #7F39FB;">
                <div style="color:#9B5DE5;font-weight:bold;text-align:center;padding:1rem;font-size:1.1rem;">${title}</div>
                <div style="color:#BB86FC;opacity:0.8;font-size:0.9rem;">Aucune jaquette</div>
            </div>
        `;
    }

    /**
     * Ajoute un jeu à la wishlist de l'utilisateur
     * @param {Object} game - Les données du jeu à ajouter
     */
    addToWishlist(game) {
        // Vérifie si l'utilisateur est connecté
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

        // Envoie les données au point de terminaison pour ajout à la wishlist
        this.submitToEndpoint(`${CONFIG.BASE_URL}wishlist/add`, gameData);
        
        // Fermer le modal après l'ajout
        document.getElementById('gameModal')?.classList.remove('active');
    }

    /**
     * Ajoute un jeu à la collection personnelle de l'utilisateur
     * @param {Object} game - Les données du jeu à ajouter
     */
    addToMyGames(game) {
        // Vérifie si l'utilisateur est connecté
        if (!this.checkAuth()) return;
        document.getElementById('gameModal')?.classList.remove('active');
        this.openAddGameModalFromRawg(game);
    }

    /**
     * Vérifie si l'utilisateur est authentifié
     * @returns {boolean} - Retourne true si l'utilisateur est connecté, sinon false
     */
    checkAuth() {
        // Vérification simple : si on a les éléments du menu connecté, on est connecté
        const profileLink = document.querySelector('a[href*="profile"]');
        const logoutLink = document.querySelector('a[href*="logout"]');
        const isLoggedIn = profileLink && logoutLink;
        
        if (!isLoggedIn) {
            // Redirige vers la page de connexion si non connecté
            setTimeout(() => window.location.href = CONFIG.BASE_URL + 'login', 500);
            return false;
        }
        return true;
    }

    /**
     * Soumet des données à un point de terminaison spécifique
     * @param {string} url - L'URL du point de terminaison
     * @param {Object} data - Les données à soumettre
     */
    async submitToEndpoint(url, data) {
        try {
            const response = await fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify(data)
            });
            
            const result = await response.json();
            
            if (result.success) {
                // Afficher un message de succès temporaire
                this.showMessage('Jeu ajouté avec succès !', 'success');
            } else {
                // Gérer les erreurs
                if (result.error?.includes('non connecté')) {
                    setTimeout(() => window.location.href = CONFIG.BASE_URL + 'login', 500);
                } else if (result.message) {
                    this.showMessage(result.message, 'warning');
                } else {
                    this.showMessage(result.error || 'Erreur lors de l\'ajout', 'error');
                }
            }
        } catch (error) {
            this.showMessage('Erreur de connexion', 'error');
        }
    }

    /**
     * Affiche un message temporaire à l'utilisateur
     * @param {string} message - Le message à afficher
     * @param {string} type - Le type de message ('success', 'error', 'warning')
     */
    showMessage(message, type = 'info') {
        // Supprimer les anciens messages
        const existingMessage = document.querySelector('.temp-message');
        if (existingMessage) existingMessage.remove();
        
        // Créer le nouveau message
        const messageEl = document.createElement('div');
        messageEl.className = 'temp-message';
        messageEl.textContent = message;
        messageEl.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 12px 20px;
            border-radius: 8px;
            color: white;
            font-weight: bold;
            z-index: 9999;
            max-width: 300px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.3);
            background: ${type === 'success' ? '#4CAF50' : type === 'error' ? '#f44336' : type === 'warning' ? '#ff9800' : '#2196F3'};
        `;
        
        document.body.appendChild(messageEl);
        
        // Supprimer le message après 3 secondes
        setTimeout(() => {
            if (messageEl.parentNode) {
                messageEl.remove();
            }
        }, 3000);
    }

    /**
     * Extrait l'année d'une chaîne de date
     * @param {string} dateString - La chaîne de date
     * @returns {string} - L'année extraite
     */
    extractYear(dateString) {
        return dateString ? new Date(dateString).getFullYear() : '';
    }

    /**
     * Initialise les actions sur les jeux (suppression uniquement)
     * L'édition est gérée localement dans chaque page
     */
    initGameActions() {
        // Délégation d'événements pour la suppression de jeux
        document.addEventListener('click', async (e) => {
            if (e.target.matches('.btn-action.delete')) {
                await this.handleDelete(e.target);
            }
        });
    }

    /**
     * Gère la suppression d'un jeu
     * @param {HTMLElement} button - Le bouton de suppression cliqué
     */
    async handleDelete(button) {
        // Confirmation de l'utilisateur
        if (!confirm('Êtes-vous sûr de vouloir supprimer ce jeu ?')) return;
        
        const gameId = button.dataset.id;
        if (!gameId) return;

        const currentPath = window.location.pathname;
        let endpoint = '';
        
        // Détermine le point de terminaison en fonction de la page actuelle
        if (currentPath.includes('/mes-jeux')) {
            endpoint = `${CONFIG.BASE_URL}mes-jeux/delete/${gameId}`;
        } else if (currentPath.includes('/wishlist')) {
            endpoint = `${CONFIG.BASE_URL}wishlist/delete/${gameId}`;
        }
        
        if (endpoint) {
            try {
                // Envoie la requête de suppression
                const response = await fetch(endpoint, {
                    method: 'POST',
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                });
                
                const data = await response.json();
                if (data.success) {
                    // Supprime la carte du jeu de l'interface
                    button.closest('.game-card-universal')?.remove();
                    this.checkEmptyPage();
                    this.showMessage('Jeu supprimé avec succès !', 'success');
                }
            } catch (error) {
                this.showMessage('Erreur lors de la suppression', 'error');
            }
        }
    }

    /**
     * Vérifie si la page est vide (sans jeux affichés)
     * Recharge la page si aucun jeu n'est présent
     */
    checkEmptyPage() {
        const gameCards = document.querySelectorAll('.game-card-universal');
        if (gameCards.length === 0) {
            setTimeout(() => location.reload(), 300);
        }
    }

    /**
     * Initialise le modal d'ajout global
     */
    initGlobalAddModal() {
        const globalModal = document.getElementById('globalAddGameModal');
        const closeBtn = document.getElementById('closeGlobalAddGameModal');
        const form = document.getElementById('globalAddGameForm');
        
        // Sortie anticipée si le modal n'existe pas dans le DOM
        if (!globalModal) return;
        
        // Configuration des gestionnaires de fermeture du modal
        closeBtn?.addEventListener('click', () => {
            globalModal.classList.remove('active');
        });
        
        // Fermeture du modal en cliquant en dehors de son contenu
        globalModal.addEventListener('click', (e) => {
            if (e.target === globalModal) globalModal.classList.remove('active');
        });
        
        // Gestion de la soumission du formulaire d'ajout de jeu
        form?.addEventListener('submit', async (e) => {
            e.preventDefault();
            
            // Conversion des données du formulaire en objet JSON
            const formData = new FormData(form);
            const jsonData = {};
            formData.forEach((value, key) => {
                jsonData[key] = value;
            });
            
            try {
                // Envoi des données au serveur via une requête AJAX
                const response = await fetch(`${CONFIG.BASE_URL}mes-jeux/add`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify(jsonData)
                });
                
                const data = await response.json();
                if (data.success) {
                    // Fermeture du modal et rechargement de la page après ajout réussi
                    globalModal.classList.remove('active');
                    this.showMessage('Jeu ajouté avec succès !', 'success');
                    setTimeout(() => location.reload(), 300);
                }
            } catch (error) {
                this.showMessage('Erreur lors de l\'ajout', 'error');
            }
        });
    }

    /**
     * Ouvre le modal d'ajout de jeu avec les données d'un jeu de l'API RAWG
     * @param {Object} game - Les données du jeu depuis l'API RAWG
     */
    openAddGameModalFromRawg(game) {
        // Vérifier si on est connecté
        if (!this.checkAuth()) return;

        // Utiliser le modal global
        const globalModal = document.getElementById('globalAddGameModal');
        if (!globalModal) return;

        // Définition d'un objet contenant tous les champs à remplir automatiquement
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

        // Parcourt chaque paire clé/valeur de l'objet fields
        Object.entries(fields).forEach(([id, value]) => {
            const element = document.getElementById(id);
            if (element) element.value = value;
        });

        // Récupère l'élément qui affiche le nom du jeu sélectionné
        const selectedGameName = document.getElementById('global_selectedGameName');
        if (selectedGameName) {
            selectedGameName.textContent = game.name || 'Jeu sélectionné';
        }

        // Liste des champs utilisateur à réinitialiser
        ['global_addGame_status', 'global_addGame_playtime', 'global_addGame_notes'].forEach(id => {
            const element = document.getElementById(id);
            if (element) element.value = '';
        });

        // Active le modal en ajoutant la classe CSS 'active'
        globalModal.classList.add('active');
    }
}

// Fonctions utilitaires globales

/**
 * Crée un placeholder miniature pour représenter la jaquette d'un jeu quand aucune image n'est disponible
 * @param {string} title - Le titre du jeu à afficher dans le placeholder
 * @returns {HTMLElement} Un élément DIV stylisé contenant le titre du jeu
 */
window.createSmallCoverPlaceholder = (title) => {
    const placeholder = document.createElement('div');
    placeholder.className = 'game-cover-placeholder';
    placeholder.style.cssText = 'width:60px;height:60px;display:flex;flex-direction:column;justify-content:center;align-items:center;background:linear-gradient(45deg,#1F1B2E,#2A1B3D);border-radius:8px;border:2px solid #7F39FB;';
    placeholder.innerHTML = `
        <div class="placeholder-title" style="color:#9B5DE5;font-weight:bold;font-size:0.8rem;text-align:center;padding:0.2rem;">${title}</div>
    `;
    return placeholder;
};

/**
 * Génère un placeholder HTML pour une jaquette de jeu de taille normale
 * Utilisé dans les modales et les listes détaillées quand aucune image n'est disponible
 * @param {string} title - Le titre du jeu à afficher dans le placeholder
 * @returns {string} Une chaîne HTML contenant le code du placeholder stylisé
 */
window.createInlineCoverPlaceholder = (title) => {
    return `
        <div style="width:220px;height:220px;background:linear-gradient(45deg,#1F1B2E,#2A1B3D);border-radius:12px;display:flex;flex-direction:column;justify-content:center;align-items:center;margin:0 auto 1.2rem;border:2px solid #7F39FB;">
            <div style="color:#9B5DE5;font-weight:bold;text-align:center;padding:1rem;font-size:1.2rem;">${title}</div>
            <div style="color:#BB86FC;opacity:0.8;font-size:1rem;">Aucune jaquette</div>
        </div>
    `;
};

/**
 * Extrait l'année d'une chaîne de date
 * Utilisée pour formater les dates de sortie des jeux
 * @param {string} dateString - La date au format ISO (YYYY-MM-DD)
 * @returns {string} L'année extraite ou une chaîne vide si la date est invalide
 */
window.extractYear = (dateString) => {
    return dateString ? new Date(dateString).getFullYear() : '';
};

// Fonctions utilitaires globales communes à toutes les vues

/**
 * Utilitaires pour les API calls - évite la duplication de code
 */
window.GameUtils = {
    /**
     * Effectue un appel API standardisé
     * @param {string} url - L'URL de l'endpoint
     * @param {string} method - La méthode HTTP (GET, POST, etc.)
     * @param {Object|FormData} data - Les données à envoyer
     * @returns {Promise} - Promesse avec la réponse JSON
     */
    async apiCall(url, method = 'POST', data = null) {
        const options = { 
            method, 
            headers: { 'X-Requested-With': 'XMLHttpRequest' } 
        };
        
        if (data) {
            if (data instanceof FormData) {
                options.body = data;
            } else {
                options.headers['Content-Type'] = 'application/json';
                options.body = JSON.stringify(data);
            }
        }
        
        const response = await fetch(url, options);
        return await response.json();
    },

    /**
     * Affiche un message d'erreur temporaire
     * @param {string} message - Le message d'erreur
     */
    showError(message) {
        console.error('Erreur: ' + message);
        // Afficher un message temporaire si nécessaire
        if (window.gameLibrary && window.gameLibrary.showMessage) {
            window.gameLibrary.showMessage(message, 'error');
        }
    },

    /**
     * Affiche un message de succès temporaire
     * @param {string} message - Le message de succès
     */
    showSuccess(message) {
        if (window.gameLibrary && window.gameLibrary.showMessage) {
            window.gameLibrary.showMessage(message, 'success');
        }
    }
};

/**
 * Utilitaires pour la gestion des modals
 */
window.ModalUtils = {
    /**
     * Ouvre une modal
     * @param {string} modalId - L'ID de la modal à ouvrir
     */
    open(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) modal.classList.add('active');
    },

    /**
     * Ferme une modal
     * @param {string} modalId - L'ID de la modal à fermer
     */
    close(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) modal.classList.remove('active');
    },

    /**
     * Configure la fermeture automatique d'une modal
     * @param {string} modalId - L'ID de la modal
     * @param {string} closeButtonId - L'ID du bouton de fermeture
     */
    setupAutoClose(modalId, closeButtonId) {
        const modal = document.getElementById(modalId);
        const closeBtn = document.getElementById(closeButtonId);
        
        if (!modal) return;
        
        // Fermeture par bouton
        if (closeBtn) {
            closeBtn.addEventListener('click', () => this.close(modalId));
        }
        
        // Fermeture par clic en dehors
        modal.addEventListener('click', (e) => {
            if (e.target === modal) this.close(modalId);
        });
    }
};

/**
 * Utilitaires pour la gestion des formulaires
 */
window.FormUtils = {
    /**
     * Convertit un FormData en objet JSON
     * @param {FormData} formData - Les données du formulaire
     * @returns {Object} - Objet JSON
     */
    formDataToJson(formData) {
        const jsonData = {};
        formData.forEach((value, key) => {
            jsonData[key] = value;
        });
        return jsonData;
    },

    /**
     * Gère la soumission d'un formulaire avec AJAX
     * @param {HTMLFormElement} form - Le formulaire
     * @param {string} endpoint - L'URL de l'endpoint
     * @param {Function} onSuccess - Callback en cas de succès
     * @param {Function} onError - Callback en cas d'erreur
     */
    async handleSubmit(form, endpoint, onSuccess = null, onError = null) {
        const formData = new FormData(form);
        const jsonData = this.formDataToJson(formData);
        
        try {
            const result = await GameUtils.apiCall(endpoint, 'POST', jsonData);
            
            if (result.success) {
                if (onSuccess) onSuccess(result);
                else GameUtils.showSuccess('Opération réussie !');
            } else {
                if (onError) onError(result);
                else GameUtils.showError(result.error || 'Erreur lors de l\'opération');
            }
        } catch (error) {
            if (onError) onError(error);
            else GameUtils.showError('Erreur de connexion');
        }
    }
};

/**
 * Utilitaires pour la gestion des fichiers (upload, preview)
 */
window.FileUtils = {
    /**
     * Configure la preview d'un fichier image
     * @param {string} inputId - L'ID de l'input file
     * @param {string} previewId - L'ID de l'élément de preview
     * @param {string} labelId - L'ID du label (optionnel)
     */
    setupImagePreview(inputId, previewId, labelId = null) {
        const fileInput = document.getElementById(inputId);
        const preview = document.getElementById(previewId);
        const label = labelId ? document.querySelector(`label[for="${inputId}"], #${labelId}`) : null;
        
        if (!fileInput || !preview) return;
        
        fileInput.addEventListener('change', (event) => {
            const file = event.target.files[0];
            
            if (file) {
                // Vérification de la taille (5MB max)
                if (file.size > 5 * 1024 * 1024) {
                    GameUtils.showError('Fichier trop volumineux (max 5MB)');
                    fileInput.value = '';
                    if (label) label.textContent = 'Choisir un fichier';
                    return;
                }
                
                // Mise à jour du label
                if (label) {
                    const displayName = file.name.length > 20 ? 
                        file.name.substring(0, 17) + '...' : file.name;
                    label.textContent = displayName;
                }
                
                // Preview de l'image
                const reader = new FileReader();
                reader.onload = (e) => preview.src = e.target.result;
                reader.readAsDataURL(file);
            } else {
                if (label) label.textContent = 'Choisir un fichier';
            }
        });
    }
};

/**
 * Fonction pour vérifier l'authentification
 * @returns {boolean} - True si l'utilisateur est connecté
 */
window.checkAuth = () => {
    if (window.gameLibrary && window.gameLibrary.checkAuth) {
        return window.gameLibrary.checkAuth();
    }
    return true; // Fallback
};

/**
 * Fonction pour vérifier si la page est vide après suppression
 */
window.checkEmptyPage = () => {
    const gameCards = document.querySelectorAll('.game-card-universal');
    if (gameCards.length === 0) {
        setTimeout(() => location.reload(), 300);
    }
};

// Initialisation de l'application
window.gameLibrary = new GameLibraryApp();

// Fonction globale pour compatibilité avec le code existant
window.openAddGameModalFromRawg = (game) => window.gameLibrary.openAddGameModalFromRawg(game);