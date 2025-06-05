// Fonction utilitaire pour extraire l'année d'une date
function extractYear(dateString) {
    return dateString ? dateString.split('-')[0] : '';
}

document.addEventListener('DOMContentLoaded', () => {
    // Gestion du menu burger
    initBurgerMenu();
    
    // Gestion des formulaires
    initForms();
    
    // Gestion des cartes
    initCards();

    // === Recherche de jeux dans la navbar (desktop & mobile) ===
    initNavbarGameSearch();
});

function initBurgerMenu() {
    const burger = document.querySelector('.burger');
    const dropdown = document.getElementById('burger-dropdown');
    
    if (!burger || !dropdown) {
        return;
    }

    // S'assurer que le dropdown est fermé au départ et forcer l'état
    dropdown.classList.remove('active');
    dropdown.style.opacity = '0';
    dropdown.style.visibility = 'hidden';
    dropdown.style.pointerEvents = 'none';
    dropdown.style.transform = 'translateY(-10px)';
    
    // Fonction pour fermer le dropdown
    const closeDropdown = () => {
        dropdown.classList.remove('active');
        // Force les styles pour assurer la fermeture
        dropdown.style.opacity = '0';
        dropdown.style.visibility = 'hidden';
        dropdown.style.pointerEvents = 'none';
        dropdown.style.transform = 'translateY(-10px)';
    };
    
    // Fonction pour ouvrir le dropdown
    const openDropdown = () => {
        dropdown.classList.add('active');
        // Force les styles pour assurer l'ouverture
        dropdown.style.opacity = '1';
        dropdown.style.visibility = 'visible';
        dropdown.style.pointerEvents = 'auto';
        dropdown.style.transform = 'translateY(0)';
    };
    
    // Fonction pour basculer le dropdown
    const toggleDropdown = (e) => {
        e.preventDefault();
        e.stopPropagation();
        
        const isActive = dropdown.classList.contains('active');
        
        if (isActive) {
            closeDropdown();
        } else {
            openDropdown();
        }
    };
    
    // Event listener sur le burger
    burger.addEventListener('click', toggleDropdown);
    
    // Fermer le menu si on clique en dehors
    document.addEventListener('click', (e) => {
        // Vérifier si le clic est à l'extérieur du burger et du dropdown
        if (!burger.contains(e.target) && !dropdown.contains(e.target)) {
            if (dropdown.classList.contains('active')) {
                closeDropdown();
            }
        }
    });
    
    // Fermer le menu quand on clique sur un lien
    const dropdownLinks = dropdown.querySelectorAll('a');
    dropdownLinks.forEach((link) => {
        link.addEventListener('click', () => {
            closeDropdown();
        });
    });
    
    // Fermer avec la touche Escape
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && dropdown.classList.contains('active')) {
            closeDropdown();
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
                setTimeout(() => location.reload(), 300);
            } else {
                console.error(data.error || data.message || 'Une erreur est survenue');
            }
        } catch (error) {
            console.error('Erreur:', error);
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
                console.error('ID du jeu non trouvé');
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
                    const card = button.closest('.game-card-universal');
                    if (card) {
                        card.remove();
                        checkEmptyPage();
                    }
                } else {
                    console.error(data.error || 'Une erreur est survenue lors de la suppression');
                }
            } catch (error) {
                console.error('Une erreur est survenue lors de la suppression');
            }
        });
    });
}

// Fonction unifiée pour vérifier si la page est vide
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
                    Année : ${extractYear(game.released) || 'Inconnue'}<br>
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
        releaseYear: extractYear(rawg.released),
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
            document.getElementById('gameModal').classList.remove('active');
            // Recharger la page si on est sur la page mes-jeux pour voir le nouveau jeu
            if (window.location.pathname.includes('mes-jeux')) {
                setTimeout(() => location.reload(), 300);
            }
        } else {
            console.error(data.error || data.message || 'Erreur lors de l\'ajout');
        }
    } catch (e) {
        console.error('Erreur lors de l\'ajout');
    }
}

// Fonction pour ajouter à la wishlist depuis un jeu RAWG
async function addToWishlistFromRawg(rawg) {
    const gameData = {
        game_id: rawg.id,
        searchGame: rawg.name || 'Jeu sans nom',
        platform: (rawg.platforms && rawg.platforms.length && rawg.platforms[0].platform && rawg.platforms[0].platform.name) ? rawg.platforms[0].platform.name : 'Inconnue',
        releaseYear: extractYear(rawg.released),
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
            document.getElementById('gameModal').classList.remove('active');
            // Recharger la page si on est sur la page wishlist pour voir le nouveau jeu
            if (window.location.pathname.includes('wishlist')) {
                setTimeout(() => location.reload(), 300);
            }
        } else {
            console.error(data.error || data.message || 'Une erreur est survenue');
        }
    } catch (error) {
        console.error(error);
    }
}

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