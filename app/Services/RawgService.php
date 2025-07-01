<?php

namespace App\Services;

use App\Models\GameModel;

/**
 * Service pour interagir avec l'API RAWG (Raw Accel Web Gaming)
 * RAWG est une base de données de jeux vidéo accessible via API
 * Ce service gère la récupération et la sauvegarde des informations des jeux
 */
class RawgService
{
    /**
     * Instance du modèle de jeu pour interagir avec la base de données
     * @var GameModel
     */
    protected $gameModel;

    /**
     * Clé API pour accéder aux services RAWG
     * @var string
     */
    protected $apiKey;

    /**
     * Initialise le service avec le modèle de jeu et la clé API
     * La clé API est récupérée depuis les variables d'environnement
     */
    public function __construct()
    {
        $this->gameModel = new GameModel();
        $this->apiKey = getenv('RAWG_API_KEY');
    }

    /**
     * Récupère un jeu existant ou en crée un nouveau à partir de l'API RAWG
     * Cette méthode évite les doublons en vérifiant d'abord si le jeu existe déjà
     * 
     * @param int $rawgId ID du jeu dans l'API RAWG
     * @return int ID du jeu dans notre base de données locale
     */
    public function getOrCreateGame($rawgId)
    {
        // Check si le jeu existe déjà dans la bdd
        $existingGame = $this->gameModel->where('rawg_id', $rawgId)->first();
        if ($existingGame) {
            return $existingGame['id'];
        }

        return $this->createGameFromRawg($rawgId);
    }

    /**
     * Crée un nouveau jeu en récupérant ses informations depuis l'API RAWG
     * Gère les erreurs potentielles de l'API en créant une entrée minimale si nécessaire
     * 
     * @param int $rawgId ID du jeu dans l'API RAWG
     * @return int ID du nouveau jeu créé dans notre base
     */
    protected function createGameFromRawg($rawgId)
    {
        try {
            // essaie de récupérer les données depuis l'API
            $response = @file_get_contents("https://api.rawg.io/api/games/{$rawgId}?key={$this->apiKey}");
            
            if (!$response) {
                return $this->createMinimalGame($rawgId);
            }

            $game = json_decode($response, true);
            
            // extrait les infos
            $developers = $this->extractDevelopers($game);
            $publishers = $this->extractPublishers($game);
            $platform = $this->extractPlatform($game);
            
            // On crée une nouvelle entrée dans la base de données
            return $this->gameModel->insert([
                'name' => $game['name'],
                'platform' => $platform,
                'release_date' => isset($game['released']) ? $game['released'] : null,
                'category' => isset($game['genres'][0]['name']) ? $game['genres'][0]['name'] : 'Inconnu',
                'cover' => $game['background_image'] ?? null,
                'developer' => $developers ?: null,
                'publisher' => $publishers ?: null,
                'rawg_id' => $rawgId
            ], true);
        } catch (\Exception $e) {
            // Log l'erreur, crée une entrée minimale si échec
            log_message('error', 'Erreur RAWG: ' . $e->getMessage());
            return $this->createMinimalGame($rawgId);
        }
    }

    /**
     * Crée une entrée minimale pour un jeu quand les données RAWG sont inaccessibles
     * Utilisé comme fallback en cas d'erreur avec l'API
     * 
     * @param int $rawgId ID du jeu dans l'API RAWG
     * @return int ID du jeu créé
     */
    protected function createMinimalGame($rawgId)
    {
        return $this->gameModel->insert([
            'name' => 'Jeu ID: ' . $rawgId,
            'platform' => 'Inconnue',
            'rawg_id' => $rawgId
        ], true);
    }

    /**
     * Extrait la liste des développeurs du jeu depuis les données RAWG
     * Les combine en une seule chaîne séparée par des virgules
     * 
     * @param array $game Données du jeu de l'API RAWG
     * @return string Liste des développeurs ou chaîne vide si aucun
     */
    protected function extractDevelopers($game)
    {
        if (!isset($game['developers']) || !is_array($game['developers'])) {
            return '';
        }
        return implode(', ', array_map(fn($dev) => $dev['name'], $game['developers']));
    }

    /**
     * Extrait la liste des éditeurs du jeu depuis les données RAWG
     * Les combine en une seule chaîne séparée par des virgules
     * 
     * @param array $game Données du jeu de l'API RAWG
     * @return string Liste des éditeurs ou chaîne vide si aucun
     */
    protected function extractPublishers($game)
    {
        if (!isset($game['publishers']) || !is_array($game['publishers'])) {
            return '';
        }
        return implode(', ', array_map(fn($pub) => $pub['name'], $game['publishers']));
    }

    /**
     * Extrait la première plateforme disponible pour le jeu
     * Utilisé pour définir la plateforme principale du jeu
     * 
     * @param array $game Données du jeu de l'API RAWG
     * @return string Nom de la plateforme ou 'Inconnue' si aucune n'est trouvée
     */
    protected function extractPlatform($game)
    {
        if (!isset($game['platforms']) || !is_array($game['platforms'])) {
            return 'Inconnue';
        }
        return $game['platforms'][0]['platform']['name'] ?? 'Inconnue';
    }
}
