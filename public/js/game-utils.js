// ============================================================
// FONCTIONS UTILITAIRES POUR LES JEUX - PARTAGÉES
// ============================================================

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