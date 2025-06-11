<?php

namespace App\Services;

use App\Models\GameModel;

class RawgService
{
    protected $gameModel;
    protected $apiKey;

    public function __construct()
    {
        $this->gameModel = new GameModel();
        $this->apiKey = getenv('RAWG_API_KEY');
    }

    /**
     * Crée ou récupère un jeu depuis l'API RAWG
     * 
     * @param int $rawgId ID du jeu dans l'API RAWG
     * @return int ID du jeu dans notre base de données
     */
    public function getOrCreateGame($rawgId)
    {
        // Vérifie si le jeu existe déjà
        $existingGame = $this->gameModel->where('rawg_id', $rawgId)->first();
        if ($existingGame) {
            return $existingGame['id'];
        }

        return $this->createGameFromRawg($rawgId);
    }

    /**
     * Crée un nouveau jeu à partir des données RAWG
     * 
     * @param int $rawgId ID du jeu dans l'API RAWG
     * @return int ID du jeu créé
     */
    protected function createGameFromRawg($rawgId)
    {
        try {
            $response = @file_get_contents("https://api.rawg.io/api/games/{$rawgId}?key={$this->apiKey}");
            
            if (!$response) {
                return $this->createMinimalGame($rawgId);
            }

            $game = json_decode($response, true);
            
            // Traitement des données RAWG
            $developers = $this->extractDevelopers($game);
            $publishers = $this->extractPublishers($game);
            $platform = $this->extractPlatform($game);
            
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
            log_message('error', 'Erreur RAWG: ' . $e->getMessage());
            return $this->createMinimalGame($rawgId);
        }
    }

    /**
     * Crée une entrée minimale pour un jeu
     */
    protected function createMinimalGame($rawgId)
    {
        return $this->gameModel->insert([
            'name' => 'Jeu ID: ' . $rawgId,
            'platform' => 'Inconnue',
            'rawg_id' => $rawgId
        ], true);
    }

    protected function extractDevelopers($game)
    {
        if (!isset($game['developers']) || !is_array($game['developers'])) {
            return '';
        }
        return implode(', ', array_map(fn($dev) => $dev['name'], $game['developers']));
    }

    protected function extractPublishers($game)
    {
        if (!isset($game['publishers']) || !is_array($game['publishers'])) {
            return '';
        }
        return implode(', ', array_map(fn($pub) => $pub['name'], $game['publishers']));
    }

    protected function extractPlatform($game)
    {
        if (!isset($game['platforms']) || !is_array($game['platforms'])) {
            return 'Inconnue';
        }
        return $game['platforms'][0]['platform']['name'] ?? 'Inconnue';
    }
}
