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

    const updateGameFields = (game) => {
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

        const preview = document.getElementById('coverPreview');
        const container = document.getElementById('coverPreviewContainer');
        if (preview && container) {
            if (game.background_image) {
                preview.src = game.background_image;
                preview.classList.remove('hidden');
                container.classList.remove('hidden');
            } else {
                preview.classList.add('hidden');
                container.classList.add('hidden');
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
                        li.addEventListener('click', () => {
                            updateGameFields(game);
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
            let response;
            if (isWishlist) {
                response = await fetch(endpoint, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({
                        game_id: formData.get('game_id'),
                        searchGame: formData.get('searchGame'),
                        platform: formData.get('platform'),
                        releaseYear: formData.get('releaseYear'),
                        genre: formData.get('genre'),
                        cover: formData.get('cover'),
                        status: formData.get('status')
                    })
                });
            } else {
                response = await fetch(endpoint, {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: formData
                });
            }
            
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