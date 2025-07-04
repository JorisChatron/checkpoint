/**
 * ===== SCRIPT JAVASCRIPT POUR LES PAGES D'ERREUR DE DÉBOGAGE =====
 * 
 * Ce fichier JavaScript gère les interactions utilisateur dans les pages d'erreur
 * de débogage de CodeIgniter. Il implémente un système d'onglets pour organiser
 * l'information et des fonctions utilitaires pour améliorer l'expérience utilisateur.
 * 
 * FONCTIONNALITÉS :
 * - Système d'onglets interactif
 * - Navigation au clavier
 * - Fonctions de basculement (toggle)
 * - Gestion des événements DOM
 */

// ===== VARIABLES GLOBALES =====
// Tableaux pour stocker les références aux onglets et contenus
var tabLinks    = new Array();    // Stocke les liens des onglets
var contentDivs = new Array();    // Stocke les divs de contenu

/**
 * ===== FONCTION D'INITIALISATION PRINCIPALE =====
 * 
 * Cette fonction initialise le système d'onglets en :
 * 1. Récupérant tous les onglets et contenus depuis le DOM
 * 2. Assignant les événements onclick aux onglets
 * 3. Masquant tous les contenus sauf le premier
 * 4. Activant le premier onglet par défaut
 */
function init()
{
    // ===== RÉCUPÉRATION DES ONGLETS ET CONTENUS =====
    // Récupération de la liste des onglets depuis le DOM
    var tabListItems = document.getElementById('tabs').childNodes;
    console.log(tabListItems); // Debug : affichage des éléments trouvés
    
    // Parcours de tous les éléments de la liste d'onglets
    for (var i = 0; i < tabListItems.length; i ++)
    {
        // Vérification que l'élément est bien un LI (élément de liste)
        if (tabListItems[i].nodeName == "LI")
        {
            // Récupération du lien (A) dans l'élément de liste
            var tabLink     = getFirstChildWithTagName(tabListItems[i], 'A');
            // Extraction de l'ID depuis l'attribut href (ex: #tab1 -> tab1)
            var id          = getHash(tabLink.getAttribute('href'));
            // Stockage des références dans les tableaux globaux
            tabLinks[id]    = tabLink;
            contentDivs[id] = document.getElementById(id);
        }
    }

    // ===== ASSIGNATION DES ÉVÉNEMENTS ET ACTIVATION DU PREMIER ONGLET =====
    var i = 0; // Compteur pour identifier le premier onglet

    // Parcours de tous les onglets trouvés
    for (var id in tabLinks)
    {
        // Assignation de l'événement onclick pour changer d'onglet
        tabLinks[id].onclick = showTab;
        // Assignation de l'événement onfocus pour éviter le focus visuel
        tabLinks[id].onfocus = function () {
            this.blur() // Suppression du focus pour éviter l'outline
        };
        
        // Activation du premier onglet (i == 0)
        if (i == 0)
        {
            tabLinks[id].className = 'active'; // Ajout de la classe CSS 'active'
        }
        i ++; // Incrémentation du compteur
    }

    // ===== MASQUAGE DES CONTENUS SAUF LE PREMIER =====
    var i = 0; // Réinitialisation du compteur

    // Parcours de tous les contenus
    for (var id in contentDivs)
    {
        if (i != 0) // Si ce n'est pas le premier contenu
        {
            console.log(contentDivs[id]); // Debug : affichage du contenu masqué
            contentDivs[id].className = 'content hide'; // Ajout de la classe 'hide' pour masquer
        }
        i ++; // Incrémentation du compteur
    }
}

/**
 * ===== FONCTION D'AFFICHAGE D'ONGLET =====
 * 
 * Cette fonction gère le changement d'onglet actif :
 * 1. Désactive tous les onglets
 * 2. Active l'onglet sélectionné
 * 3. Masque tous les contenus
 * 4. Affiche le contenu de l'onglet sélectionné
 * 
 * @returns {boolean} false pour empêcher la navigation du navigateur
 */
function showTab()
{
    // Extraction de l'ID de l'onglet depuis l'attribut href du lien cliqué
    var selectedId = getHash(this.getAttribute('href'));

    // ===== MISE À JOUR DE L'ÉTAT DES ONGLETS ET CONTENUS =====
    // Parcours de tous les onglets et contenus
    for (var id in contentDivs)
    {
        if (id == selectedId) // Si c'est l'onglet sélectionné
        {
            // Activation de l'onglet et affichage du contenu
            tabLinks[id].className    = 'active';     // Onglet actif
            contentDivs[id].className = 'content';    // Contenu visible
        }
        else // Si ce n'est pas l'onglet sélectionné
        {
            // Désactivation de l'onglet et masquage du contenu
            tabLinks[id].className    = '';           // Onglet inactif
            contentDivs[id].className = 'content hide'; // Contenu masqué
        }
    }

    // Empêche le navigateur de suivre le lien (évite la navigation)
    return false;
}

/**
 * ===== FONCTION UTILITAIRE : RÉCUPÉRATION DU PREMIER ENFANT =====
 * 
 * Cette fonction recherche le premier enfant d'un élément avec un nom de tag spécifique.
 * Utile pour naviguer dans la structure DOM de manière fiable.
 * 
 * @param {Element} element - L'élément parent
 * @param {string} tagName - Le nom du tag à rechercher (ex: 'A', 'DIV')
 * @returns {Element|null} Le premier enfant trouvé ou null
 */
function getFirstChildWithTagName(element, tagName)
{
    // Parcours de tous les enfants de l'élément
    for (var i = 0; i < element.childNodes.length; i ++)
    {
        // Vérification si le nom du node correspond au tag recherché
        if (element.childNodes[i].nodeName == tagName)
        {
            return element.childNodes[i]; // Retour du premier enfant trouvé
        }
    }
    // Retour null si aucun enfant avec ce tag n'est trouvé
}

/**
 * ===== FONCTION UTILITAIRE : EXTRACTION DU HASH =====
 * 
 * Cette fonction extrait la partie après le # d'une URL.
 * Exemple : "page.html#tab1" -> "tab1"
 * 
 * @param {string} url - L'URL contenant le hash
 * @returns {string} La partie après le # ou la chaîne complète si pas de #
 */
function getHash(url)
{
    // Recherche de la position du caractère # dans l'URL
    var hashPos = url.lastIndexOf('#');
    // Retour de la partie après le # (ou toute l'URL si pas de #)
    return url.substring(hashPos + 1);
}

/**
 * ===== FONCTION DE BASCULEMENT (TOGGLE) =====
 * 
 * Cette fonction bascule l'affichage d'un élément entre visible et masqué.
 * Elle gère la compatibilité cross-browser pour la propriété display.
 * 
 * @param {string} elem - L'ID de l'élément à basculer
 * @returns {boolean} false pour empêcher la propagation de l'événement
 */
function toggle(elem)
{
    // Récupération de l'élément par son ID
    elem = document.getElementById(elem);

    // ===== DÉTECTION DE LA PROPRIÉTÉ DISPLAY ACTUELLE =====
    var disp;
    
    if (elem.style && elem.style['display'])
    {
        // Méthode standard : propriété style inline
        disp = elem.style['display'];
    }
    else if (elem.currentStyle)
    {
        // Méthode pour Internet Explorer (ancien)
        disp = elem.currentStyle['display'];
    }
    else if (window.getComputedStyle)
    {
        // Méthode moderne pour la plupart des navigateurs
        disp = document.defaultView.getComputedStyle(elem, null).getPropertyValue('display');
    }

    // ===== BASCULEMENT DE L'ÉTAT D'AFFICHAGE =====
    // Si l'élément est visible (block), on le masque (none), sinon on l'affiche
    elem.style.display = disp == 'block' ? 'none' : 'block';

    // Empêche la propagation de l'événement
    return false;
}

/**
 * ===== FONCTIONNEMENT GÉNÉRAL =====
 * 
 * Ce script implémente un système d'onglets complet avec :
 * 
 * 1. INITIALISATION AUTOMATIQUE :
 *    - Détection automatique des onglets dans le DOM
 *    - Configuration des événements
 *    - État initial (premier onglet actif)
 * 
 * 2. NAVIGATION INTERACTIVE :
 *    - Clic sur les onglets pour changer de contenu
 *    - Gestion du focus pour l'accessibilité
 *    - Prévention de la navigation du navigateur
 * 
 * 3. COMPATIBILITÉ CROSS-BROWSER :
 *    - Support des anciens navigateurs (IE)
 *    - Méthodes modernes (getComputedStyle)
 *    - Fallbacks pour la détection des propriétés CSS
 * 
 * 4. FONCTIONS UTILITAIRES :
 *    - Navigation DOM sécurisée
 *    - Extraction de paramètres d'URL
 *    - Basculement d'affichage d'éléments
 * 
 * UTILISATION :
 * - Appel de init() au chargement de la page
 * - Utilisation de toggle() pour masquer/afficher des sections
 * - Navigation automatique entre les onglets
 */
