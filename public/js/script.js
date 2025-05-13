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
});