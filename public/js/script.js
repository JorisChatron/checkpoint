document.addEventListener('DOMContentLoaded', () => {
    const burgerButton = document.getElementById('burger-button');
    const burgerDropdown = document.getElementById('burger-dropdown');

    if (!burgerButton || !burgerDropdown) {
        console.error('Burger button or dropdown not found in the DOM.');
        return;
    }

    // Ouvrir/fermer le menu burger au clic
    burgerButton.addEventListener('click', () => {
        burgerDropdown.classList.toggle('active');
    });

    // Fermer le menu si on clique en dehors
    document.addEventListener('click', (event) => {
        if (!burgerButton.contains(event.target) && !burgerDropdown.contains(event.target)) {
            burgerDropdown.classList.remove('active');
        }
    });

    const openModalButton = document.getElementById('openModal');
    const closeModalButton = document.getElementById('closeModal');
    const modal = document.getElementById('addGameModal');

    // Ouvrir le modal
    openModalButton.addEventListener('click', () => {
        modal.style.display = 'flex';
    });

    // Fermer le modal
    closeModalButton.addEventListener('click', () => {
        modal.style.display = 'none';
    });

    // Fermer le modal en cliquant à l'extérieur
    window.addEventListener('click', (event) => {
        if (event.target === modal) {
            modal.style.display = 'none';
        }
    });

    const searchGameInput = document.getElementById('searchGame');
    const suggestionsList = document.getElementById('suggestions');
    const releaseYearInput = document.getElementById('releaseYear');
    const genreInput = document.getElementById('genre');
    const coverInput = document.getElementById('cover');
    const coverPreview = document.getElementById('coverPreview');

    const API_KEY = 'ff6f7941c211456c8806541638fdfaff'; // Remplacez par votre clé API RAWG

    // Masquer l'aperçu de la jaquette au chargement
    coverPreview.classList.add('hidden');

    searchGameInput.addEventListener('input', async () => {
        const query = searchGameInput.value;

        if (query.length > 1) { // Effectue une recherche après 2 caractères
            try {
                const response = await fetch(`https://api.rawg.io/api/games?key=${API_KEY}&search=${query}`);
                const data = await response.json();

                // Vide les suggestions précédentes
                suggestionsList.innerHTML = '';

                if (data.results && data.results.length > 0) {
                    data.results.forEach(game => {
                        const li = document.createElement('li');
                        li.textContent = game.name;
                        li.addEventListener('click', () => {
                            // Remplit les champs avec les données du jeu sélectionné
                            searchGameInput.value = game.name;
                            releaseYearInput.value = game.released ? game.released.split('-')[0] : '';
                            genreInput.value = game.genres.map(genre => genre.name).join(', ');
                            coverInput.value = game.background_image;

                            // Affiche l'aperçu de la jaquette si une image est disponible
                            if (game.background_image) {
                                coverPreview.src = game.background_image;
                                coverPreview.classList.remove('hidden');
                            } else {
                                coverPreview.classList.add('hidden');
                            }

                            // Vide les suggestions
                            suggestionsList.innerHTML = '';
                        });
                        suggestionsList.appendChild(li);
                    });
                } else {
                    const li = document.createElement('li');
                    li.textContent = 'Aucun résultat trouvé';
                    suggestionsList.appendChild(li);
                }
            } catch (error) {
                console.error('Erreur lors de la récupération des suggestions :', error);
            }
        } else {
            // Vide les suggestions si moins de 2 caractères
            suggestionsList.innerHTML = '';
        }
    });

    // Ferme les suggestions si on clique en dehors
    document.addEventListener('click', (event) => {
        if (!searchGameInput.contains(event.target) && !suggestionsList.contains(event.target)) {
            suggestionsList.innerHTML = '';
        }
    });

    const form = document.getElementById('addGameForm');
    if (form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(form);
            
            // Détermine l'URL en fonction de la page actuelle
            const isWishlistPage = window.location.pathname.includes('wishlist');
            const endpoint = isWishlistPage ? '/checkpoint/public/wishlist/add' : '/checkpoint/public/mes-jeux/add';
            
            console.log('Envoi du formulaire à :', endpoint);
            console.log('Données du formulaire :', Object.fromEntries(formData));
            
            fetch(endpoint, {
                method: 'POST',
                body: formData
            })
            .then(res => {
                console.log('Réponse reçue :', res);
                return res.json();
            })
            .then(data => {
                console.log('Données reçues :', data);
                if (data.success) {
                    location.reload();
                } else {
                    alert(data.error || 'Une erreur est survenue');
                }
            })
            .catch(error => {
                console.error('Erreur lors de l\'envoi du formulaire :', error);
                alert('Une erreur est survenue lors de l\'envoi du formulaire');
            });
        });
    }

    document.querySelectorAll('.delete-game-form').forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            if (!confirm('Supprimer ce jeu ?')) return;
            
            const gameId = this.getAttribute('data-id');
            // Détermine l'URL en fonction de la page actuelle
            const isWishlistPage = window.location.pathname.includes('wishlist');
            const endpoint = isWishlistPage ? `/checkpoint/public/wishlist/delete/${gameId}` : `/checkpoint/public/mes-jeux/delete/${gameId}`;
            
            fetch(endpoint, {
                method: 'POST'
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) location.reload();
            });
        });
    });

    document.querySelectorAll('.carousel-card').forEach(card => {
        card.addEventListener('click', function(e) {
            // Ne retourne pas la carte si on clique sur le bouton supprimer
            if (e.target.closest('button')) return;
            // Ferme les autres cartes ouvertes
            document.querySelectorAll('.carousel-card.flipped').forEach(c => {
                if (c !== card) c.classList.remove('flipped');
            });
            card.classList.toggle('flipped');
        });
    });

    if (coverInput.value) {
        coverPreview.src = coverInput.value;
        coverPreview.classList.remove('hidden');
    } else {
        coverPreview.src = '/public/images/default-cover.png';
        coverPreview.classList.remove('hidden');
    }
});