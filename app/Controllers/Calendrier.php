<?php
namespace App\Controllers;

use CodeIgniter\Controller;

/**
 * Contrôleur du calendrier des sorties de jeux
 * 
 * Ce contrôleur gère l'affichage des sorties de jeux vidéo par semaine.
 * Il utilise l'API RAWG pour récupérer les données et implémente un système
 * de filtrage pour masquer le contenu adulte selon les préférences utilisateur.
 * 
 * Fonctionnalités principales :
 * - Affichage des jeux par semaine avec pagination
 * - Filtrage automatique du contenu adulte
 * - Navigation entre les semaines
 * - Gestion des erreurs API
 */
class Calendrier extends Controller
{
    /**
     * Affiche les sorties de jeux pour une semaine donnée
     * 
     * Cette méthode est le point d'entrée principal du calendrier.
     * Elle récupère les jeux sortant dans une semaine donnée via l'API RAWG,
     * applique des filtres de contenu adulte si nécessaire, et affiche
     * les résultats avec pagination.
     * 
     * @param int|null $year Année (format YYYY) - si null, utilise l'année courante
     * @param int|null $week Numéro de semaine (1-53) - si null, utilise la semaine courante
     * @param string|null $segment Segment URL pour la pagination ('page')
     * @param int|null $page Numéro de page pour la pagination
     * @return string Vue avec les jeux de la semaine
     */
    public function index($year = null, $week = null, $segment = null, $page = null)
    {
        // ===== DÉTERMINATION DE LA SEMAINE À AFFICHER =====
        
        // Si année/semaine non spécifiées, utilise la semaine courante
        $now = new \DateTime();
        if (!$year || !$week) {
            $year = (int)$now->format('o');  // Année ISO-8601 (peut différer de Y pour les semaines à cheval)
            $week = (int)$now->format('W');  // Numéro de semaine ISO-8601 (1-53)
        }
        
        // ===== CONFIGURATION DE LA PAGINATION =====
        
        // Détermine le numéro de page à afficher
        // Si l'URL contient 'page' suivi d'un numéro, on utilise ce numéro
        // Sinon on affiche la première page
        $page = ($segment === 'page' && $page) ? (int)$page : 1;
        
        // ===== CALCUL DES DATES DE LA SEMAINE =====
        
        // Calcul des dates de début et fin de la semaine
        $monday = new \DateTime();
        $monday->setISODate($year, $week);        // Définit le lundi de la semaine ISO
        $start = $monday->format('Y-m-d');        // Date de début (lundi) au format Y-m-d
        
        $sunday = clone $monday;                  // Clone pour ne pas modifier l'objet original
        $sunday->modify('+6 days');               // Avance de 6 jours pour atteindre dimanche
        $end = $sunday->format('Y-m-d');          // Date de fin (dimanche) au format Y-m-d

        // ===== CONFIGURATION DE L'APPEL API =====
        
        // Configuration de la pagination pour l'API RAWG
        $pageSize = 50;  // Nombre de jeux par page (limite de l'API)
        $apiPage = $page; // Numéro de page à demander à l'API

        // ===== APPEL À L'API RAWG =====
        
        // Construction de l'URL de l'API avec tous les paramètres nécessaires
        $apiKey = getenv('RAWG_API_KEY');  // Récupération de la clé API depuis les variables d'environnement
        $url = "https://api.rawg.io/api/games?key=$apiKey&dates=$start,$end&ordering=released&page_size=$pageSize&page=$apiPage";
        
        // Initialisation des variables pour stocker les résultats
        $games = [];      // Liste des jeux récupérés
        $error = null;    // Message d'erreur éventuel
        $count = 0;       // Nombre total de jeux disponibles

        // Tentative de récupération des données depuis l'API
        try {
            $response = file_get_contents($url);  // Appel HTTP à l'API
            $data = json_decode($response, true); // Conversion de la réponse JSON en tableau PHP
            
            // Vérification que la réponse contient des résultats
            if (isset($data['results'])) {
                $games = $data['results'];        // Liste des jeux
                $count = $data['count'] ?? 0;     // Nombre total de jeux (pour la pagination)
            }
        } catch (\Exception $e) {
            // En cas d'erreur (API indisponible, clé invalide, etc.)
            $error = "Erreur lors de la récupération des sorties : " . $e->getMessage();
        }

        // ===== FILTRAGE DU CONTENU ADULTE =====
        
        // Récupération de la préférence utilisateur pour le contenu adulte
        $showAdult = session()->get('show_adult');
        
        // Si l'utilisateur n'a pas activé l'affichage du contenu adulte, on filtre
        if (!$showAdult) {
            
            // Liste exhaustive des mots-clés pour identifier le contenu adulte
            // Cette liste couvre différents types de contenu adulte en français et anglais
            $adultWords = [
                // Contenu sexuel explicite
                'sex', 'sexual', 'sexuel', 'sexuelle', 'nude', 'nudity', 'nudité', 'naked', 'nu', 'nue',
                'erotic', 'érotique', 'hentai', 'ecchi', 'yaoi', 'yuri', 'nsfw', '18+', 'r18',
                'porn', 'pornographic', 'pornographique', 'xxx', 'adult', 'adulte',
                
                // Termes liés au contenu sexuel
                'intimate', 'intime', 'seduction', 'séduction', 'seductive', 'séductrice',
                'explicit', 'explicite', 'mature', 'mature content', 'contenu mature',
                
                // Termes spécifiques aux jeux
                'dating sim', 'visual novel', 'otome', 'dating game', 'jeu de drague',
                'strip', 'striptease', 'strip poker', 'strip game'
            ];

            // Filtrage des jeux selon plusieurs critères de sécurité
            $games = array_filter($games, function($game) use ($adultWords) {
                
                // 1. Vérification des genres du jeu
                if (!empty($game['genres'])) {
                    foreach ($game['genres'] as $genre) {
                        foreach ($adultWords as $word) {
                            // Recherche insensible à la casse dans le nom du genre
                            if (stripos($genre['name'], $word) !== false) return false;
                        }
                    }
                }

                // 2. Vérification du titre du jeu
                foreach ($adultWords as $word) {
                    if (isset($game['name']) && stripos($game['name'], $word) !== false) return false;
                }

                // 3. Vérification de la description du jeu
                if (isset($game['description_raw'])) {
                    foreach ($adultWords as $word) {
                        if (stripos($game['description_raw'], $word) !== false) return false;
                    }
                }

                // 4. Vérification des tags du jeu
                if (!empty($game['tags'])) {
                    foreach ($game['tags'] as $tag) {
                        foreach ($adultWords as $word) {
                            if (stripos($tag['name'], $word) !== false) return false;
                        }
                    }
                }

                // 5. Vérification de l'URL du site officiel du jeu
                if (isset($game['website'])) {
                    foreach ($adultWords as $word) {
                        if (stripos($game['website'], $word) !== false) return false;
                    }
                }

                // 6. Vérification des noms des développeurs
                if (!empty($game['developers'])) {
                    foreach ($game['developers'] as $developer) {
                        foreach ($adultWords as $word) {
                            if (stripos($developer['name'], $word) !== false) return false;
                        }
                    }
                }

                // Si le jeu passe tous les filtres, il est conservé
                return true;
            });
        }

        // ===== RENDU DE LA VUE =====
        
        // Passage de toutes les données nécessaires à la vue
        return view('calendrier/index', [
            'games' => $games,           // Liste des jeux (filtrée si nécessaire)
            'year' => $year,            // Année sélectionnée pour la navigation
            'week' => $week,            // Semaine sélectionnée pour la navigation
            'page' => $page,            // Page courante pour la pagination
            'count' => $count,          // Nombre total de jeux (pour afficher le total)
            'start' => $start,          // Date de début de semaine (pour l'affichage)
            'end' => $end,              // Date de fin de semaine (pour l'affichage)
            'error' => $error           // Message d'erreur éventuel à afficher
        ]);
    }
} 