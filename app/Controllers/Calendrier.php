<?php
namespace App\Controllers;

use CodeIgniter\Controller;

/**
 * Contrôleur du calendrier des sorties de jeux
 * Gère l'affichage des jeux par semaine avec filtrage du contenu
 */
class Calendrier extends Controller
{
    /**
     * Affiche les sorties de jeux pour une semaine donnée
     * 
     * @param int|null $year Année (format YYYY)
     * @param int|null $week Numéro de semaine (1-53)
     * @param string|null $segment Segment URL pour la pagination ('page')
     * @param int|null $page Numéro de page
     * @return string Vue avec les jeux de la semaine
     */
    public function index($year = null, $week = null, $segment = null, $page = null)
    {
        // Si année/semaine non spécifiées, utilise la semaine courante
        $now = new \DateTime();
        if (!$year || !$week) {
            $year = (int)$now->format('o');  // Année ISO-8601
            $week = (int)$now->format('W');  // Numéro de semaine ISO-8601
        }
        
        // Configuration de la pagination
        $page = ($segment === 'page' && $page) ? (int)$page : 1;
        
        // Calcul des dates de début et fin de la semaine
        $monday = new \DateTime();
        $monday->setISODate($year, $week);        // Définit le lundi de la semaine
        $start = $monday->format('Y-m-d');        // Date de début (lundi)
        $sunday = clone $monday;
        $sunday->modify('+6 days');               // Avance de 6 jours pour atteindre dimanche
        $end = $sunday->format('Y-m-d');          // Date de fin (dimanche)

        // Configuration de la pagination pour l'API
        $pageSize = 50;  // Nombre de jeux par page
        $apiPage = $page;

        // Appel à l'API RAWG pour récupérer les jeux
        $apiKey = getenv('RAWG_API_KEY');
        $url = "https://api.rawg.io/api/games?key=$apiKey&dates=$start,$end&ordering=released&page_size=$pageSize&page=$apiPage";
        $games = [];
        $error = null;
        $count = 0;

        // Tentative de récupération des données
        try {
            $response = file_get_contents($url);
            $data = json_decode($response, true);
            if (isset($data['results'])) {
                $games = $data['results'];
                $count = $data['count'] ?? 0;
            }
        } catch (\Exception $e) {
            $error = "Erreur lors de la récupération des sorties : " . $e->getMessage();
        }

        // Filtrage du contenu adulte si l'option n'est pas activée
        $showAdult = session()->get('show_adult');
        if (!$showAdult) {
            // Liste des mots-clés pour identifier le contenu adulte
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

            // Filtrage des jeux selon plusieurs critères
            $games = array_filter($games, function($game) use ($adultWords) {
                // Vérifie les genres du jeu
                if (!empty($game['genres'])) {
                    foreach ($game['genres'] as $genre) {
                        foreach ($adultWords as $word) {
                            if (stripos($genre['name'], $word) !== false) return false;
                        }
                    }
                }

                // Vérifie le titre du jeu
                foreach ($adultWords as $word) {
                    if (isset($game['name']) && stripos($game['name'], $word) !== false) return false;
                }

                // Vérifie la description du jeu
                if (isset($game['description_raw'])) {
                    foreach ($adultWords as $word) {
                        if (stripos($game['description_raw'], $word) !== false) return false;
                    }
                }

                // Vérifie les tags du jeu
                if (!empty($game['tags'])) {
                    foreach ($game['tags'] as $tag) {
                        foreach ($adultWords as $word) {
                            if (stripos($tag['name'], $word) !== false) return false;
                        }
                    }
                }

                // Vérifie l'URL du site du jeu
                if (isset($game['website'])) {
                    foreach ($adultWords as $word) {
                        if (stripos($game['website'], $word) !== false) return false;
                    }
                }

                // Vérifie les noms des développeurs
                if (!empty($game['developers'])) {
                    foreach ($game['developers'] as $developer) {
                        foreach ($adultWords as $word) {
                            if (stripos($developer['name'], $word) !== false) return false;
                        }
                    }
                }

                return true; // Le jeu passe tous les filtres
            });
        }

        // Rendu de la vue avec les données
        return view('calendrier/index', [
            'games' => $games,           // Liste des jeux filtrée
            'year' => $year,            // Année sélectionnée
            'week' => $week,            // Semaine sélectionnée
            'page' => $page,            // Page courante
            'count' => $count,          // Nombre total de jeux
            'start' => $start,          // Date de début de semaine
            'end' => $end,              // Date de fin de semaine
            'error' => $error           // Message d'erreur éventuel
        ]);
    }
} 