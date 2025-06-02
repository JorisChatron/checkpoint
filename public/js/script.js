console.log('Script.js chargé');

document.addEventListener('DOMContentLoaded', () => {
    // Gestion du menu burger
    initBurgerMenu();
    
    // Gestion du modal
    initModal();
    
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
    const burger = document.querySelector('.burger');
    const dropdown = document.getElementById('burger-dropdown');
    if (!burger || !dropdown) return;

    burger.addEventListener('click', () => dropdown.classList.toggle('active'));
    document.addEventListener('click', (e) => {
        if (!burger.contains(e.target) && !dropdown.contains(e.target)) {
            dropdown.classList.remove('active');
        }
    });
}

function initModal() {
    console.log('Initialisation du modal');
    const modal = document.getElementById('addGameModal');
    const openBtn = document.getElementById('openModal');
    const closeBtn = document.getElementById('closeModal');
    
    console.log('Modal:', modal);
    console.log('Bouton ouvert:', openBtn);
    console.log('Bouton fermé:', closeBtn);
    
    if (!modal) {
        console.log('Modal non trouvé');
        return;
    }

    const toggleModal = (show) => {
        console.log('Toggle modal:', show);
        modal.classList[show ? 'add' : 'remove']('active');
    };
    
    openBtn?.addEventListener('click', () => {
        console.log('Clic sur le bouton ouvert');
        toggleModal(true);
    });
    closeBtn?.addEventListener('click', () => {
        console.log('Clic sur le bouton fermé');
        toggleModal(false);
    });
    modal.addEventListener('click', (e) => {
        if (e.target === modal) {
            console.log('Clic en dehors du modal');
            toggleModal(false);
        }
    });
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

            Object.entries(fields).forEach(([id, value]) => {
                const element = document.getElementById(id);
                if (element) element.value = value;
            });

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

    // Gestion du retournement des cartes
    document.querySelectorAll('.carousel-card').forEach(card => {
        card.addEventListener('click', (e) => {
            if (e.target.closest('button')) return;
            document.querySelectorAll('.carousel-card.flipped').forEach(c => {
                if (c !== card) c.classList.remove('flipped');
            });
            card.classList.toggle('flipped');
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
    // Desktop
    const input = document.getElementById('navbarGameSearchInput');
    const suggestions = document.getElementById('navbarGameSuggestions');
    // Mobile
    const inputMobile = document.getElementById('navbarGameSearchInputMobile');
    const suggestionsMobile = document.getElementById('navbarGameSuggestionsMobile');

    function setupSearch(inputEl, suggestionsEl) {
        if (!inputEl || !suggestionsEl) return;
        let timeout;
        inputEl.addEventListener('input', function() {
            clearTimeout(timeout);
            const q = this.value.trim();
            if (q.length < 2) {
                suggestionsEl.style.display = 'none';
                suggestionsEl.innerHTML = '';
                return;
            }
            timeout = setTimeout(async () => {
                try {
                    // Recherche RAWG uniquement
                    const API_KEY = 'ff6f7941c211456c8806541638fdfaff';
                    const rawgRes = await fetch(`https://api.rawg.io/api/games?key=${API_KEY}&search=${encodeURIComponent(q)}`);
                    const rawgData = await rawgRes.json();
                    suggestionsEl.innerHTML = '';
                    if (rawgData.results && rawgData.results.length) {
                        rawgData.results.slice(0, 8).forEach(game => {
                            const li = document.createElement('li');
                            li.textContent = game.name;
                            li.style.cursor = 'pointer';
                            li.style.fontStyle = 'italic';
                            li.style.color = '#00E5FF';
                            li.addEventListener('click', () => {
                                showRawgGameModal(game);
                                suggestionsEl.style.display = 'none';
                                suggestionsEl.innerHTML = '';
                                inputEl.value = '';
                            });
                            suggestionsEl.appendChild(li);
                        });
                    } else {
                        suggestionsEl.innerHTML = '<li style="color:#BB86FC;padding:0.5rem;">Aucun résultat</li>';
                    }
                    suggestionsEl.style.display = 'block';
                } catch (e) {
                    suggestionsEl.innerHTML = '<li style="color:#BB86FC;padding:0.5rem;">Erreur</li>';
                    suggestionsEl.style.display = 'block';
                }
            }, 250);
        });
        document.addEventListener('click', (e) => {
            if (!inputEl.contains(e.target) && !suggestionsEl.contains(e.target)) {
                suggestionsEl.style.display = 'none';
            }
        });
    }
    setupSearch(input, suggestions);
    setupSearch(inputMobile, suggestionsMobile);
}

function showNavbarGameModal(game) {
    const modal = document.getElementById('navbarGameModal');
    const body = document.getElementById('navbarGameModalBody');
    const actions = document.getElementById('navbarGameModalActions');
    if (!modal || !body || !actions) return;
    // Affichage des infos du jeu
    let html = '';
    html += game.cover ? `<img src="${game.cover}" alt="${game.name}" style="width:220px;height:220px;object-fit:cover;border-radius:12px;box-shadow:0 2px 12px #7F39FB44;margin-bottom:1.2rem;">` : '';
    html += `<h2 style=\"color:#9B5DE5;margin-bottom:0.7rem;\">${game.name}</h2>`;
    html += `<div style=\"color:#BB86FC;font-size:1.05rem;margin-bottom:0.7rem;\">Plateforme : ${game.platform || 'Inconnue'}<br>Année : ${(game.release_date||'').split('-')[0] || 'Inconnue'}<br>Genre : ${game.category || 'Inconnu'}</div>`;
    // Si developer présent, affiche-le, sinon tente RAWG
    if (game.developer && game.developer !== 'Inconnu') {
        html += `<div style=\"color:#BB86FC;font-size:1.05rem;margin-bottom:0.7rem;\">Développeur : ${game.developer}</div>`;
        body.innerHTML = html;
        actions.style.display = 'block';
    } else if (game.rawg_id) {
        // Si rawg_id présent, fetch RAWG
        fetch(`https://api.rawg.io/api/games/${game.rawg_id}?key=ff6f7941c211456c8806541638fdfaff`)
            .then(res => res.json())
            .then(rawg => {
                const devs = rawg.developers && rawg.developers.length ? rawg.developers.map(d=>d.name).join(', ') : 'Inconnu';
                html += `<div style=\"color:#BB86FC;font-size:1.05rem;margin-bottom:0.7rem;\">Développeur : ${devs}</div>`;
                body.innerHTML = html;
                actions.style.display = 'block';
            })
            .catch(() => {
                html += `<div style=\"color:#BB86FC;font-size:1.05rem;margin-bottom:0.7rem;\">Développeur : Inconnu</div>`;
                body.innerHTML = html;
                actions.style.display = 'block';
            });
    } else {
        html += `<div style=\"color:#BB86FC;font-size:1.05rem;margin-bottom:0.7rem;\">Développeur : Inconnu</div>`;
        body.innerHTML = html;
        actions.style.display = 'block';
    }
    // Boutons d'action
    actions.innerHTML = `
        <button class="home-btn" id="addToMyGamesBtn" style="margin:0 0.5rem 0.5rem 0;">Ajouter à mes jeux</button>
        <button class="home-btn" id="addToWishlistBtn" style="background:linear-gradient(90deg,#00E5FF 80%,#9B5DE5 100%);color:#1E1E2F;border-color:#00E5FF;">Ajouter à la wishlist</button>
    `;
    modal.classList.add('active');
    // Fermeture
    document.getElementById('closeNavbarGameModal').onclick = () => modal.classList.remove('active');
    window.onclick = (e) => { if (e.target === modal) modal.classList.remove('active'); };
    // Ajout à mes jeux
    document.getElementById('addToMyGamesBtn').onclick = async () => {
        await addGameToCollection(game, false);
    };
    // Ajout à la wishlist
    document.getElementById('addToWishlistBtn').onclick = async () => {
        await addGameToCollection(game, true);
    };
}

function showRawgGameModal(game) {
    const modal = document.getElementById('navbarGameModal');
    const body = document.getElementById('navbarGameModalBody');
    const actions = document.getElementById('navbarGameModalActions');
    if (!modal || !body || !actions) return;
    // On recharge toutes les infos RAWG par ID
    body.innerHTML = '<span style="color:#BB86FC;">Chargement...</span>';
    actions.innerHTML = '';
    actions.style.display = 'none';
    fetch(`https://api.rawg.io/api/games/${game.id}?key=ff6f7941c211456c8806541638fdfaff`)
        .then(res => res.json())
        .then(rawg => {
            let html = '';
            html += rawg.background_image ? `<img src="${rawg.background_image}" alt="${rawg.name}" style="width:220px;height:220px;object-fit:cover;border-radius:12px;box-shadow:0 2px 12px #7F39FB44;margin-bottom:1.2rem;">` : '';
            html += `<h2 style=\"color:#9B5DE5;margin-bottom:0.7rem;\">${rawg.name}</h2>`;
            html += `<div style=\"color:#BB86FC;font-size:1.05rem;margin-bottom:0.7rem;\">Plateforme : ${rawg.platforms && rawg.platforms.length ? rawg.platforms.map(p=>p.platform.name).join(', ') : 'Inconnue'}<br>Année : ${(rawg.released||'').split('-')[0] || 'Inconnue'}<br>Genre : ${rawg.genres && rawg.genres.length ? rawg.genres.map(g=>g.name).join(', ') : 'Inconnu'}</div>`;
            html += `<div style=\"color:#BB86FC;font-size:1.05rem;margin-bottom:0.7rem;\">Développeur : ${rawg.developers && rawg.developers.length ? rawg.developers.map(d=>d.name).join(', ') : 'Inconnu'}</div>`;
            html += `<div style=\"color:#BB86FC;font-size:1.05rem;margin-bottom:0.7rem;\">Éditeur : ${rawg.publishers && rawg.publishers.length ? rawg.publishers.map(d=>d.name).join(', ') : 'Inconnu'}</div>`;
            html += `<div style=\"color:#E0F7FA;font-size:1rem;margin-bottom:1.2rem;max-height:120px;overflow:auto;\">${rawg.description_raw || '<i>Aucune description disponible.</i>'}</div>`;
            body.innerHTML = html;
            actions.innerHTML = `
                <button class="home-btn" id="addToMyGamesBtn" style="margin:0 0.5rem 0.5rem 0;">Ajouter à mes jeux</button>
                <button class="home-btn" id="addToWishlistBtn" style="background:linear-gradient(90deg,#00E5FF 80%,#9B5DE5 100%);color:#1E1E2F;border-color:#00E5FF;">Ajouter à la wishlist</button>
            `;
            actions.style.display = 'block';
            modal.classList.add('active');
            document.getElementById('closeNavbarGameModal').onclick = () => modal.classList.remove('active');
            window.onclick = (e) => { if (e.target === modal) modal.classList.remove('active'); };
            document.getElementById('addToMyGamesBtn').onclick = async () => {
                await addGameToCollectionRawg(rawg, false);
            };
            document.getElementById('addToWishlistBtn').onclick = async () => {
                await addGameToCollectionRawg(rawg, true);
            };
        })
        .catch(() => {
            body.innerHTML = '<span style="color:#FF6F61;">Erreur lors du chargement des infos du jeu.</span>';
        });
}

async function addGameToCollection(game, wishlist) {
    const url = wishlist ? '/wishlist/add' : '/mes-jeux/add';
    const payload = {
        game_id: game.id,
        searchGame: game.name,
        platform: game.platform,
        releaseYear: (game.release_date||'').split('-')[0] || '',
        genre: game.category,
        cover: game.cover,
        status: wishlist ? 'souhaité' : 'en cours'
    };
    try {
        const response = await fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify(payload)
        });
        const data = await response.json();
        if (data.success) {
            showToast('success', wishlist ? 'Ajouté à la wishlist !' : 'Ajouté à votre collection !');
            setTimeout(() => location.reload(), 1200);
        } else {
            showToast('error', data.error || data.message || 'Erreur lors de l\'ajout');
        }
    } catch (e) {
        showToast('error', 'Erreur lors de l\'ajout');
    }
}

async function addGameToCollectionRawg(game, wishlist) {
    const url = wishlist ? '/wishlist/add' : '/mes-jeux/add';
    const payload = {
        game_id: game.id,
        searchGame: game.name,
        platform: game.platforms && game.platforms.length ? game.platforms[0].platform.name : '',
        releaseYear: (game.released||'').split('-')[0] || '',
        genre: game.genres && game.genres.length ? game.genres.map(g=>g.name).join(', ') : '',
        cover: game.background_image,
        status: wishlist ? 'souhaité' : 'en cours',
        rawg_id: game.id,
        developer: game.developers && game.developers.length ? game.developers.map(d=>d.name).join(', ') : '',
        publisher: game.publishers && game.publishers.length ? game.publishers.map(p=>p.name).join(', ') : ''
    };
    try {
        const response = await fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify(payload)
        });
        const data = await response.json();
        if (data.success) {
            showToast('success', wishlist ? 'Ajouté à la wishlist !' : 'Ajouté à votre collection !');
            setTimeout(() => location.reload(), 1200);
        } else {
            showToast('error', data.error || data.message || 'Erreur lors de l\'ajout');
        }
    } catch (e) {
        showToast('error', 'Erreur lors de l\'ajout');
    }
}

// Initialisation de la recherche navbar
initNavbarGameSearch();