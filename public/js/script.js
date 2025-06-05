console.log('Script.js chargé');

document.addEventListener('DOMContentLoaded', () => {
    console.log('DOM chargé, initialisation...');
    
    // Gestion du menu burger
    initBurgerMenu();
    
    // Gestion de la recherche de jeux
    initGameSearch();
    
    // Gestion des formulaires
    initForms();
    
    // Gestion des cartes
    initCards();

    // === Recherche de jeux dans la navbar (desktop & mobile) ===
    initNavbarGameSearch();
});

function initBurgerMenu() {
    console.log('Initialisation du burger menu...');
    const burger = document.querySelector('.burger');
    const dropdown = document.getElementById('burger-dropdown');
    
    console.log('Burger:', burger);
    console.log('Dropdown:', dropdown);
    
    if (!burger || !dropdown) {
        console.log('Burger ou dropdown non trouvé');
        return;
    }

    // S'assurer que le dropdown est fermé au départ et forcer l'état
    dropdown.classList.remove('active');
    dropdown.style.opacity = '0';
    dropdown.style.visibility = 'hidden';
    dropdown.style.pointerEvents = 'none';
    dropdown.style.transform = 'translateY(-10px)';
    console.log('Dropdown forcé fermé au démarrage');
    
    // Fonction pour fermer le dropdown
    const closeDropdown = () => {
        dropdown.classList.remove('active');
        // Force les styles pour assurer la fermeture
        dropdown.style.opacity = '0';
        dropdown.style.visibility = 'hidden';
        dropdown.style.pointerEvents = 'none';
        dropdown.style.transform = 'translateY(-10px)';
        console.log('Dropdown fermé avec styles forcés');
    };
    
    // Fonction pour ouvrir le dropdown
    const openDropdown = () => {
        dropdown.classList.add('active');
        // Force les styles pour assurer l'ouverture
        dropdown.style.opacity = '1';
        dropdown.style.visibility = 'visible';
        dropdown.style.pointerEvents = 'auto';
        dropdown.style.transform = 'translateY(0)';
        console.log('Dropdown ouvert avec styles forcés');
    };
    
    // Fonction pour basculer le dropdown
    const toggleDropdown = (e) => {
        e.preventDefault();
        e.stopPropagation();
        
        const isActive = dropdown.classList.contains('active');
        console.log('Toggle dropdown, actuellement ouvert:', isActive);
        
        if (isActive) {
            closeDropdown();
        } else {
            openDropdown();
        }
    };
    
    // Event listener sur le burger
    burger.addEventListener('click', toggleDropdown);
    console.log('Event listener ajouté sur burger');
    
    // Fermer le menu si on clique en dehors
    document.addEventListener('click', (e) => {
        // Vérifier si le clic est à l'extérieur du burger et du dropdown
        if (!burger.contains(e.target) && !dropdown.contains(e.target)) {
            if (dropdown.classList.contains('active')) {
                console.log('Clic en dehors détecté, fermeture dropdown');
                closeDropdown();
            }
        }
    });
    
    // Fermer le menu quand on clique sur un lien
    const dropdownLinks = dropdown.querySelectorAll('a');
    dropdownLinks.forEach((link, index) => {
        link.addEventListener('click', (e) => {
            console.log(`Clic sur lien ${index + 1}, fermeture dropdown`);
            closeDropdown();
        });
    });
    
    // Fermer avec la touche Escape
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && dropdown.classList.contains('active')) {
            console.log('Touche Escape pressée, fermeture dropdown');
            closeDropdown();
        }
    });
    
    console.log('Burger menu initialisé avec succès');
}

function initGameSearch() {
    const searchInput = document.getElementById('searchGame');
    const suggestionsList = document.getElementById('suggestions');
    if (!searchInput || !suggestionsList) return;

    const API_KEY = 'ff6f7941c211456c8806541638fdfaff';
    let searchTimeout;

    const updateGameFields = async (game) => {
        // Faire un appel API pour récupérer les détails complets du jeu
        try {
            const detailResponse = await fetch(`https://api.rawg.io/api/games/${game.id}?key=${API_KEY}`);
            const gameDetails = await detailResponse.json();
            
            const fields = {
                'searchGame': gameDetails.name || game.name,
                'platform': gameDetails.platforms?.[0]?.platform?.name || game.platforms?.[0]?.platform?.name || '',
                'releaseYear': (gameDetails.released || game.released)?.split('-')[0] || '',
                'genre': gameDetails.genres?.map(g => g.name).join(', ') || game.genres?.map(g => g.name).join(', ') || '',
                'cover': gameDetails.background_image || game.background_image || '',
                'game_id': gameDetails.id || game.id || '',
                'developer': gameDetails.developers?.map(d => d.name).join(', ') || '',
                'publisher': gameDetails.publishers?.map(p => p.name).join(', ') || ''
            };

            // Remplir les champs cachés
            Object.entries(fields).forEach(([id, value]) => {
                const element = document.getElementById(id);
                if (element) element.value = value;
            });

            // Gérer l'aperçu du jeu sélectionné
            const gamePreview = document.getElementById('gamePreview');
            const selectedGameCover = document.getElementById('selectedGameCover');
            const selectedGameName = document.getElementById('selectedGameName');
            const selectedGameDetails = document.getElementById('selectedGameDetails');

            if (gamePreview && selectedGameCover && selectedGameName && selectedGameDetails) {
                // Afficher l'aperçu
                gamePreview.style.display = 'block';
                
                // Jaquette - utiliser le placeholder si pas d'image
                if (fields.cover) {
                    selectedGameCover.src = fields.cover;
                    selectedGameCover.style.display = 'block';
                    // Cacher le placeholder s'il existe
                    const placeholder = gamePreview.querySelector('.game-cover-placeholder');
                    if (placeholder) placeholder.style.display = 'none';
                } else {
                    selectedGameCover.style.display = 'none';
                    // Afficher le placeholder
                    let placeholder = gamePreview.querySelector('.game-cover-placeholder');
                    if (!placeholder) {
                        placeholder = document.createElement('div');
                        placeholder.className = 'game-cover-placeholder size-small';
                        placeholder.style.cssText = 'width: 60px; height: 60px; border-radius: 8px; border: 2px solid var(--secondary-color); margin-right: 1rem;';
                        placeholder.innerHTML = `
                            <div class="placeholder-title">${fields.searchGame}</div>
                        `;
                        selectedGameCover.parentNode.insertBefore(placeholder, selectedGameCover);
                    } else {
                        placeholder.style.display = 'flex';
                        placeholder.querySelector('.placeholder-title').textContent = fields.searchGame;
                    }
                }
                
                // Nom du jeu
                selectedGameName.textContent = fields.searchGame;
                
                // Détails (plateforme, année, genre)
                const details = [];
                if (fields.platform) details.push(fields.platform);
                if (fields.releaseYear) details.push(fields.releaseYear);
                if (fields.genre) details.push(fields.genre);
                selectedGameDetails.textContent = details.join(' • ');
            }

            // Gestion de l'ancien système d'aperçu (pour compatibilité)
            const preview = document.getElementById('coverPreview');
            const container = document.getElementById('coverPreviewContainer');
            if (preview && container) {
                if (gameDetails.background_image || game.background_image) {
                    preview.src = gameDetails.background_image || game.background_image;
                    preview.classList.remove('hidden');
                    container.classList.remove('hidden');
                } else {
                    preview.classList.add('hidden');
                    container.classList.add('hidden');
                }
            }
        } catch (error) {
            console.error('Erreur lors de la récupération des détails:', error);
            // Fallback avec les données de base
            const fields = {
                'searchGame': game.name,
                'platform': game.platforms?.[0]?.platform?.name || '',
                'releaseYear': game.released?.split('-')[0] || '',
                'genre': game.genres?.map(g => g.name).join(', ') || '',
                'cover': game.background_image || '',
                'game_id': game.id || ''
            };

            Object.entries(fields).forEach(([id, value]) => {
                const element = document.getElementById(id);
                if (element) element.value = value;
            });

            // Fallback pour l'aperçu aussi
            const gamePreview = document.getElementById('gamePreview');
            const selectedGameCover = document.getElementById('selectedGameCover');
            const selectedGameName = document.getElementById('selectedGameName');
            const selectedGameDetails = document.getElementById('selectedGameDetails');

            if (gamePreview && selectedGameCover && selectedGameName && selectedGameDetails) {
                gamePreview.style.display = 'block';
                
                // Jaquette - utiliser le placeholder si pas d'image
                if (fields.cover) {
                    selectedGameCover.src = fields.cover;
                    selectedGameCover.style.display = 'block';
                    // Cacher le placeholder s'il existe
                    const placeholder = gamePreview.querySelector('.game-cover-placeholder');
                    if (placeholder) placeholder.style.display = 'none';
                } else {
                    selectedGameCover.style.display = 'none';
                    // Afficher le placeholder
                    let placeholder = gamePreview.querySelector('.game-cover-placeholder');
                    if (!placeholder) {
                        placeholder = document.createElement('div');
                        placeholder.className = 'game-cover-placeholder size-small';
                        placeholder.style.cssText = 'width: 60px; height: 60px; border-radius: 8px; border: 2px solid var(--secondary-color); margin-right: 1rem;';
                        placeholder.innerHTML = `
                            <div class="placeholder-title">${fields.searchGame}</div>
                        `;
                        selectedGameCover.parentNode.insertBefore(placeholder, selectedGameCover);
                    } else {
                        placeholder.style.display = 'flex';
                        placeholder.querySelector('.placeholder-title').textContent = fields.searchGame;
                    }
                }
                
                selectedGameName.textContent = fields.searchGame;
                
                const details = [];
                if (fields.platform) details.push(fields.platform);
                if (fields.releaseYear) details.push(fields.releaseYear);
                if (fields.genre) details.push(fields.genre);
                selectedGameDetails.textContent = details.join(' • ');
            }
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
}

function initForms() {
    const form = document.getElementById('addGameForm');
    if (!form) return;

    form.addEventListener('submit', async (e) => {
        e.preventDefault();
        const formData = new FormData(form);
        const isWishlist = window.location.pathname.includes('wishlist');
        const endpoint = isWishlist ? '/checkpoint/public/wishlist/add' : '/checkpoint/public/mes-jeux/add';

        try {
            // Conversion des données FormData en JSON pour les deux cas
            const jsonData = {
                game_id: formData.get('game_id'),
                searchGame: formData.get('searchGame'),
                platform: formData.get('platform'),
                releaseYear: formData.get('releaseYear'),
                genre: formData.get('genre'),
                cover: formData.get('cover'),
                developer: formData.get('developer'),
                publisher: formData.get('publisher'),
                status: formData.get('status'),
                playtime: formData.get('playtime'),
                notes: formData.get('notes')
            };

            const response = await fetch(endpoint, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify(jsonData)
            });
            
            const data = await response.json();
            
            if (data.success) {
                showToast('success', 'Jeu ajouté avec succès !');
                setTimeout(() => location.reload(), 1200);
            } else {
                showToast('error', data.error || data.message || 'Une erreur est survenue');
            }
        } catch (error) {
            console.error('Erreur:', error);
            showToast('error', 'Erreur lors de l\'envoi du formulaire');
        }
    });
}

function initCards() {
    // Gestion des boutons de suppression
    document.querySelectorAll('.btn-action.delete').forEach(button => {
        button.addEventListener('click', async (e) => {
            e.preventDefault();
            if (!confirm('Êtes-vous sûr de vouloir supprimer ce jeu ?')) return;

            const gameId = button.getAttribute('data-id');
            if (!gameId) {
                showToast('error', 'ID du jeu non trouvé');
                return;
            }

            const isWishlist = window.location.pathname.includes('wishlist');
            const endpoint = isWishlist ? 
                `/checkpoint/public/wishlist/delete/${gameId}` : 
                `/checkpoint/public/mes-jeux/delete/${gameId}`;

            try {
                const response = await fetch(endpoint, {
                    method: 'POST',
                    headers: {'X-Requested-With': 'XMLHttpRequest'}
                });
                const data = await response.json();

                if (data.success) {
                    const card = button.closest('.wishlist-card, .carousel-card');
                    if (card) {
                        card.remove();
                        checkEmptyContainer(isWishlist);
                    }
                    showToast('success', 'Jeu supprimé avec succès !');
                } else {
                    showToast('error', data.error || 'Une erreur est survenue lors de la suppression');
                }
            } catch (error) {
                showToast('error', 'Une erreur est survenue lors de la suppression');
            }
        });
    });
}

function checkEmptyContainer(isWishlist) {
    const container = document.querySelector(isWishlist ? '.wishlist-carousel' : '.games-carousel');
    const cards = document.querySelectorAll(isWishlist ? '.wishlist-card' : '.carousel-card');
    
    if (container && cards.length === 0) {
        container.innerHTML = `<p class="${isWishlist ? 'wishlist' : 'games'}-empty-message">
            ${isWishlist ? 'Votre wishlist est vide.' : 'Vous n\'avez aucun jeu.'}
        </p>`;
    }
}

window.showToast = function(type, message) {
    const container = document.getElementById('toast-container');
    if (!container) return;

    const toast = document.createElement('div');
    toast.className = `toast toast-${type}`;
    toast.setAttribute('role', 'alert');
    toast.setAttribute('aria-live', 'assertive');
    toast.setAttribute('tabindex', '0');
    toast.innerHTML = message;
    container.appendChild(toast);

    setTimeout(() => {
        toast.style.opacity = '0';
        toast.style.transform = 'translateY(-20px) scale(0.98)';
        setTimeout(() => container.removeChild(toast), 400);
    }, 2600);
    
    toast.focus();
};

// === Recherche de jeux dans la navbar (desktop & mobile) ===
function initNavbarGameSearch() {
    // Une seule barre de recherche maintenant
    const input = document.getElementById('navbarGameSearchInput');
    const suggestions = document.getElementById('navbarGameSuggestions');

    if (!input || !suggestions) return;

        let timeout;
    input.addEventListener('input', function() {
            clearTimeout(timeout);
            const q = this.value.trim();
            if (q.length < 2) {
            suggestions.style.display = 'none';
            suggestions.innerHTML = '';
            return;
        }
            timeout = setTimeout(async () => {
                try {
                    // Recherche RAWG uniquement
                    const API_KEY = 'ff6f7941c211456c8806541638fdfaff';
                    const rawgRes = await fetch(`https://api.rawg.io/api/games?key=${API_KEY}&search=${encodeURIComponent(q)}`);
                    const rawgData = await rawgRes.json();
                suggestions.innerHTML = '';
                    if (rawgData.results && rawgData.results.length) {
                        rawgData.results.slice(0, 8).forEach(game => {
                            const li = document.createElement('li');
                            li.textContent = game.name;
                            li.style.cursor = 'pointer';
                            li.style.fontStyle = 'italic';
                            li.style.color = '#00E5FF';
                            li.addEventListener('click', () => {
                                openGameModal(game.id);
                            suggestions.style.display = 'none';
                            suggestions.innerHTML = '';
                            input.value = '';
                        });
                        suggestions.appendChild(li);
                        });
                    } else {
                    suggestions.innerHTML = '<li style="color:#BB86FC;padding:0.5rem;">Aucun résultat</li>';
                }
        suggestions.style.display = 'block';
            } catch (e) {
                suggestions.innerHTML = '<li style="color:#BB86FC;padding:0.5rem;">Erreur</li>';
                suggestions.style.display = 'block';
                }
            }, 250);
        });
        
        document.addEventListener('click', (e) => {
        if (!input.contains(e.target) && !suggestions.contains(e.target)) {
            suggestions.style.display = 'none';
            }
        });
}

// Fonction pour ouvrir le modal de détails depuis un jeu de la DB
function openGameModalFromDb(game) {
    const modal = document.getElementById('gameModal');
    const gameModalBody = document.getElementById('gameModalBody');
    
    if (!modal || !gameModalBody) return;
    
    // Fermer les suggestions
    document.getElementById('navbarGameSuggestions').style.display = 'none';
    
    // Affichage des infos du jeu
    let html = '';
    html += game.cover ? `<img src="${game.cover}" alt="${game.name}" style="width:220px;height:220px;object-fit:cover;border-radius:12px;box-shadow:0 2px 12px #7F39FB44;margin-bottom:1.2rem;">` : `
        <div style="width:220px;height:220px;margin:0 auto 1.2rem auto;background:linear-gradient(45deg, #1F1B2E, #2A1B3D);border-radius:10px;display:flex;flex-direction:column;justify-content:center;align-items:center;padding:0.5rem;box-sizing:border-box;text-align:center;border:2px solid #7F39FB;box-shadow:0 2px 8px #7F39FB44;">
            <div style="color:#9B5DE5;font-size:1.2rem;font-weight:bold;margin-bottom:0.5rem;text-shadow:0 2px 8px rgba(0,0,0,0.5);letter-spacing:1px;line-height:1.2;">${game.name}</div>
            <div style="color:#BB86FC;font-size:0.9rem;opacity:0.8;max-width:85%;line-height:1.3;text-align:center;">Aucune jaquette</div>
        </div>
    `;
    html += `<h2 style="color:#9B5DE5;margin-bottom:0.7rem;">${game.name}</h2>`;
    html += `<div style="color:#BB86FC;font-size:1.05rem;margin-bottom:0.7rem;">Plateforme : ${game.platform || 'Inconnue'}<br>Année : ${(game.release_date||'').split('-')[0] || 'Inconnue'}<br>Genre : ${game.category || 'Inconnu'}</div>`;
    
    // Si rawg_id présent, enrichir avec RAWG
    if (game.rawg_id) {
        fetch(`https://api.rawg.io/api/games/${game.rawg_id}?key=ff6f7941c211456c8806541638fdfaff`)
            .then(res => res.json())
            .then(rawg => {
                const devs = rawg.developers && rawg.developers.length ? rawg.developers.map(d=>d.name).join(', ') : 'Inconnu';
                html += `<div style="color:#BB86FC;font-size:1.05rem;margin-bottom:0.7rem;">Développeur : ${devs}</div>`;
                html += `<div style="color:#BB86FC;font-size:1.05rem;margin-bottom:1.2rem;">Éditeur : ${rawg.publishers && rawg.publishers.length ? rawg.publishers.map(p=>p.name).join(', ') : 'Inconnu'}</div>`;
                if (rawg.description_raw) {
                    html += `<div style="color:#E0F7FA;font-size:1rem;margin-bottom:1.2rem;max-height:120px;overflow:auto;">${rawg.description_raw}</div>`;
                }
                html += `
                    <div style="margin-top:1.5rem;text-align:center;">
                        <button onclick="addToMyGamesFromRawg(${JSON.stringify(rawg).replace(/"/g, '&quot;')})" class="home-btn" style="margin:0 0.5rem 0.5rem 0;">Ajouter à mes jeux</button>
                        <button onclick="addToWishlistFromGame(${JSON.stringify(game).replace(/"/g, '&quot;')})" class="home-btn" style="background:linear-gradient(90deg,#00E5FF 80%,#9B5DE5 100%);color:#1E1E2F;border-color:#00E5FF;">Ajouter à la wishlist</button>
                    </div>
                `;
                gameModalBody.innerHTML = html;
            })
            .catch(() => {
                html += `<div style="color:#BB86FC;font-size:1.05rem;margin-bottom:1.2rem;">Développeur : Inconnu</div>`;
                html += `
                    <div style="margin-top:1.5rem;text-align:center;">
                        <button onclick="addToMyGamesFromGame(${JSON.stringify(game).replace(/"/g, '&quot;')})" class="home-btn" style="margin:0 0.5rem 0.5rem 0;">Ajouter à mes jeux</button>
                        <button onclick="addToWishlistFromGame(${JSON.stringify(game).replace(/"/g, '&quot;')})" class="home-btn" style="background:linear-gradient(90deg,#00E5FF 80%,#9B5DE5 100%);color:#1E1E2F;border-color:#00E5FF;">Ajouter à la wishlist</button>
                    </div>
                `;
                gameModalBody.innerHTML = html;
            });
    } else {
        html += `<div style="color:#BB86FC;font-size:1.05rem;margin-bottom:1.2rem;">Développeur : Inconnu</div>`;
        html += `
            <div style="margin-top:1.5rem;text-align:center;">
                <button onclick="addToMyGamesFromGame(${JSON.stringify(game).replace(/"/g, '&quot;')})" class="home-btn" style="margin:0 0.5rem 0.5rem 0;">Ajouter à mes jeux</button>
                <button onclick="addToWishlistFromGame(${JSON.stringify(game).replace(/"/g, '&quot;')})" class="home-btn" style="background:linear-gradient(90deg,#00E5FF 80%,#9B5DE5 100%);color:#1E1E2F;border-color:#00E5FF;">Ajouter à la wishlist</button>
            </div>
        `;
        gameModalBody.innerHTML = html;
    }
    
    modal.classList.add('active');
}

// Fonction unifiée pour ouvrir le modal de détails (identique au calendrier)
function openGameModal(gameId) {
    const gameModal = document.getElementById('gameModal');
    const gameModalBody = document.getElementById('gameModalBody');
    
    if (!gameModal || !gameModalBody) return;
    
    // Fermer les suggestions
    document.getElementById('navbarGameSuggestions').style.display = 'none';
    
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
                    Plateforme : ${game.platforms && game.platforms.length ? game.platforms.map(p=>p.platform.name).join(', ') : 'Inconnue'}<br>
                    Année : ${(game.released||'').split('-')[0] || 'Inconnue'}<br>
                    Genre : ${game.genres && game.genres.length ? game.genres.map(g=>g.name).join(', ') : 'Inconnu'}
                </div>
                <div style="color:#BB86FC;font-size:1.05rem;margin-bottom:0.7rem;">
                    Développeur : ${game.developers && game.developers.length ? game.developers.map(d=>d.name).join(', ') : 'Inconnu'}
                </div>
                <div style="color:#BB86FC;font-size:1.05rem;margin-bottom:1.2rem;">
                    Éditeur : ${game.publishers && game.publishers.length ? game.publishers.map(p=>p.name).join(', ') : 'Inconnu'}
                </div>
                ${game.description_raw ? `<div style="color:#E0F7FA;font-size:1rem;margin-bottom:1.2rem;max-height:120px;overflow:auto;">${game.description_raw}</div>` : ''}
                <div style="margin-top:1.5rem;text-align:center;">
                    <button onclick="addToMyGamesFromRawg(${JSON.stringify(game).replace(/"/g, '&quot;')})" class="home-btn" style="margin:0 0.5rem 0.5rem 0;">Ajouter à mes jeux</button>
                    <button onclick="addToWishlistFromRawg(${JSON.stringify(game).replace(/"/g, '&quot;')})" class="home-btn" style="background:linear-gradient(90deg,#00E5FF 80%,#9B5DE5 100%);color:#1E1E2F;border-color:#00E5FF;">Ajouter à la wishlist</button>
                </div>
            `;
        })
        .catch(() => {
            gameModalBody.innerHTML = '<span style="color:#FF6F61;">Erreur lors du chargement des infos du jeu.</span>';
        });
}

// Fonction pour ajouter directement à mes jeux depuis un jeu RAWG
async function addToMyGamesFromRawg(rawg) {
    const gameData = {
        game_id: rawg.id,
        searchGame: rawg.name || 'Jeu sans nom',
        platform: (rawg.platforms && rawg.platforms.length && rawg.platforms[0].platform && rawg.platforms[0].platform.name) ? rawg.platforms[0].platform.name : 'Inconnue',
        releaseYear: rawg.released ? rawg.released.split('-')[0] : '',
        genre: (rawg.genres && rawg.genres.length) ? rawg.genres.map(g => g.name).join(', ') : '',
        cover: rawg.background_image || '',
        developer: (rawg.developers && rawg.developers.length) ? rawg.developers.map(d => d.name).join(', ') : '',
        publisher: (rawg.publishers && rawg.publishers.length) ? rawg.publishers.map(p => p.name).join(', ') : '',
        status: 'en cours'
    };

    try {
        const response = await fetch(window.CP_BASE_URL + 'mes-jeux/add', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify(gameData)
        });
        const data = await response.json();
        if (data.success) {
            showToast('success', 'Jeu ajouté à votre collection !');
            document.getElementById('gameModal').classList.remove('active');
        } else {
            showToast('error', data.error || data.message || 'Erreur lors de l\'ajout');
        }
    } catch (e) {
        showToast('error', 'Erreur lors de l\'ajout');
    }
}

// Fonction pour ajouter à la wishlist depuis un jeu RAWG
async function addToWishlistFromRawg(rawg) {
    const gameData = {
        game_id: rawg.id,
        searchGame: rawg.name || 'Jeu sans nom',
        platform: (rawg.platforms && rawg.platforms.length && rawg.platforms[0].platform && rawg.platforms[0].platform.name) ? rawg.platforms[0].platform.name : 'Inconnue',
        releaseYear: rawg.released ? rawg.released.split('-')[0] : '',
        genre: (rawg.genres && rawg.genres.length) ? rawg.genres.map(g => g.name).join(', ') : '',
        cover: rawg.background_image || '',
        developer: (rawg.developers && rawg.developers.length) ? rawg.developers.map(d => d.name).join(', ') : '',
        publisher: (rawg.publishers && rawg.publishers.length) ? rawg.publishers.map(p => p.name).join(', ') : ''
    };

    try {
        const response = await fetch(window.CP_BASE_URL + 'wishlist/add', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify(gameData)
        });
        const data = await response.json();
        if (data.success) {
            showToast('success', 'Jeu ajouté à votre wishlist avec succès !');
            document.getElementById('gameModal').classList.remove('active');
        } else {
            showToast('error', data.error || data.message || 'Une erreur est survenue');
        }
    } catch (error) {
        console.error(error);
        showToast('error', 'Erreur lors de l\'ajout à la wishlist');
    }
}

// Fonction pour ajouter à la wishlist depuis un jeu de la DB
async function addToWishlistFromGame(game) {
    const gameData = {
        game_id: game.id,
        searchGame: game.name,
        platform: game.platform,
        releaseYear: (game.release_date||'').split('-')[0] || '',
        genre: game.category,
        cover: game.cover
    };
    
    try {
        const response = await fetch(window.CP_BASE_URL + 'wishlist/add', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify(gameData)
        });
        const data = await response.json();
        if (data.success) {
            showToast('success', 'Jeu ajouté à votre wishlist avec succès !');
            document.getElementById('gameModal').classList.remove('active');
        } else {
            showToast('error', data.error || data.message || 'Erreur lors de l\'ajout');
        }
    } catch (e) {
        showToast('error', 'Erreur lors de l\'ajout à la wishlist');
    }
}

// Fonction pour ajouter directement à mes jeux depuis un jeu de la DB
async function addToMyGamesFromGame(game) {
    const gameData = {
        game_id: game.id,
        searchGame: game.name,
        platform: game.platform,
        releaseYear: (game.release_date||'').split('-')[0] || '',
        genre: game.category,
        cover: game.cover,
        status: 'en cours'
    };
    
    try {
        const response = await fetch(window.CP_BASE_URL + 'mes-jeux/add', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify(gameData)
        });
        const data = await response.json();
        if (data.success) {
            showToast('success', 'Jeu ajouté à votre collection !');
            document.getElementById('gameModal').classList.remove('active');
        } else {
            showToast('error', data.error || data.message || 'Erreur lors de l\'ajout');
        }
    } catch (e) {
        showToast('error', 'Erreur lors de l\'ajout');
    }
}

// Initialisation de la recherche navbar
initNavbarGameSearch();

// Initialisation des modals unifiés pour la navbar
document.addEventListener('DOMContentLoaded', function() {
    // Event listeners pour le modal de détails
    const gameModal = document.getElementById('gameModal');
    const closeGameModal = document.getElementById('closeGameModal');
    
    if (gameModal && closeGameModal) {
        closeGameModal.addEventListener('click', () => gameModal.classList.remove('active'));
        gameModal.addEventListener('click', (e) => {
            if (e.target === gameModal) gameModal.classList.remove('active');
        });
    }
});