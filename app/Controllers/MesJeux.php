<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\GameModel;        // Modèle pour la gestion des jeux
use App\Models\GameStatsModel;   // Modèle pour les statistiques des jeux

/**
 * Contrôleur de gestion de la bibliothèque de jeux de l'utilisateur
 * Permet d'afficher, ajouter et supprimer des jeux de sa collection
 */
class MesJeux extends BaseController
{
    /**
     * Affiche la liste des jeux de l'utilisateur avec filtres
     * 
     * @return string Vue avec la liste des jeux filtrée
     */
    public function index()
    {
        // Récupération de l'ID utilisateur depuis la session
        $userId = session()->get('user_id');
        $gameStatsModel = new GameStatsModel();

        // Récupération des paramètres de filtrage depuis l'URL
        $platform = $this->request->getGet('platform');  // Filtre par plateforme
        $status = $this->request->getGet('status');      // Filtre par statut
        $genre = $this->request->getGet('genre');        // Filtre par genre

        // Construction de la requête de base avec jointure
        $builder = $gameStatsModel
            ->select('game_stats.*, games.name, games.platform, games.release_date, games.category, games.cover, games.developer, games.publisher')
            ->join('games', 'games.id = game_stats.game_id')
            ->where('game_stats.user_id', $userId);

        // Application des filtres si présents
        if ($platform) {
            $builder->where('games.platform', $platform);
        }
        if ($status) {
            $builder->where('game_stats.status', $status);
        }
        if ($genre) {
            $builder->like('games.category', $genre);
        }

        // Exécution de la requête
        $games = $builder->findAll();

        // Récupération des valeurs uniques pour les filtres
        // Liste des plateformes disponibles
        $platforms = $gameStatsModel
            ->select('games.platform')
            ->join('games', 'games.id = game_stats.game_id')
            ->where('game_stats.user_id', $userId)
            ->groupBy('games.platform')
            ->findAll();

        // Liste des statuts utilisés
        $statuses = $gameStatsModel
            ->select('game_stats.status')
            ->where('game_stats.user_id', $userId)
            ->groupBy('game_stats.status')
            ->findAll();

        // Liste des genres disponibles
        $genres = $gameStatsModel
            ->select('games.category')
            ->join('games', 'games.id = game_stats.game_id')
            ->where('game_stats.user_id', $userId)
            ->groupBy('games.category')
            ->findAll();

        // Rendu de la vue avec toutes les données
        return view('mes-jeux/index', [
            'title' => 'Mes Jeux',
            'games' => $games,                    // Liste des jeux filtrée
            'platforms' => $platforms,            // Liste des plateformes pour le filtre
            'statuses' => $statuses,             // Liste des statuts pour le filtre
            'genres' => $genres,                 // Liste des genres pour le filtre
            'selectedPlatform' => $platform,     // Plateforme actuellement sélectionnée
            'selectedStatus' => $status,         // Statut actuellement sélectionné
            'selectedGenre' => $genre,           // Genre actuellement sélectionné
        ]);
    }

    /**
     * Ajoute un nouveau jeu à la collection de l'utilisateur
     * 
     * @return \CodeIgniter\HTTP\Response Réponse JSON
     */
    public function add()
    {
        try {
        // vérif connexion utilisateur
        $userId = session()->get('user_id');
        if (!$userId) {
            return $this->response->setJSON(['success' => false, 'error' => 'Utilisateur non connecté']);
        }

            // récup données formulaire
            $data = $this->request->getJSON(true) ?: $this->request->getPost();

            // Prio à l'ID RAWG même si on a searchGame comme dans le calendrier
            if (isset($data['game_id']) && is_numeric($data['game_id'])) {
                return $this->handleRawgGame($userId, $data); 
            }

            return $this->handleFormGame($userId, $data);      // Jeu depuis formulaire

        } catch (\Exception $e) {
            log_message('error', 'Exception dans add(): ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false, 
                'error' => 'Une erreur est survenue. Veuillez réessayer.'
            ]);
        }
    }

    /**
     * Gère l'ajout d'un jeu depuis l'API RAWG
     * 
     * @param int $userId ID de l'utilisateur
     * @param array $data Données du jeu
     * @return \CodeIgniter\HTTP\Response Réponse JSON
     */
    protected function handleRawgGame($userId, $data)
    {
        $rawgService = new \App\Services\RawgService();
        $gameId = $rawgService->getOrCreateGame($data['game_id']);
        return $this->addToGameStats($userId, $gameId, $data);
    }

    /**
     * Gère l'ajout d'un jeu depuis le formulaire manuel
     * 
     * @param int $userId ID de l'utilisateur
     * @param array $data Données du formulaire
     * @return \CodeIgniter\HTTP\Response Réponse JSON
     */
    protected function handleFormGame($userId, $data)
    {
        $gameModel = new GameModel();

        // Extraction des données nécessaires
        $gameName = $data['searchGame'] ?? null;
        $platform = $data['platform'] ?? null;
        $releaseYear = $data['releaseYear'] ?? null;
        $genre = $data['genre'] ?? null;
        $cover = $data['cover'] ?? null;
        $developer = $data['developer'] ?? null;
        $publisher = $data['publisher'] ?? null;
        $rawgId = $data['rawg_id'] ?? null;

        // Validation des données requises
        if (!$gameName || !$platform) {
            return $this->response->setJSON(['success' => false, 'error' => 'Champs manquants']);
        }

        // Vérification si le jeu existe déjà dans la base
        $game = $gameModel->where([
            'name' => $gameName,
            'platform' => $platform
        ])->first();

        // Création ou récupération de l'ID du jeu
        if (!$game) {
            // Création d'un nouveau jeu
            $gameId = $gameModel->insert([
                'name' => $gameName,
                'platform' => $platform,
                'release_date' => $releaseYear ? $releaseYear . '-01-01' : null,
                'category' => $genre,
                'cover' => $cover,
                'developer' => $developer,
                'publisher' => $publisher,
                'rawg_id' => $rawgId
            ], true);
        } else {
            $gameId = $game['id'];
        }

        return $this->addToGameStats($userId, $gameId, $data);
    }



    /**
     * Ajoute un jeu aux statistiques de l'utilisateur
     * 
     * @param int $userId ID de l'utilisateur
     * @param int $gameId ID du jeu
     * @param array $data Données du formulaire
     * @return \CodeIgniter\HTTP\Response Réponse JSON
     */
    protected function addToGameStats($userId, $gameId, $data)
    {
        $gameStatsModel = new GameStatsModel();
        
        // Vérification si le jeu est déjà dans la collection
        if ($gameStatsModel->where(['user_id' => $userId, 'game_id' => $gameId])->first()) {
            return $this->response->setJSON(['success' => false, 'error' => 'Ce jeu est déjà dans votre collection']);
        }

        try {
        // Ajout des statistiques du jeu pour l'utilisateur
        $gameStatsModel->insert([
            'user_id' => $userId,
            'game_id' => $gameId,
                'play_time' => $data['playtime'] ?? 0,
                'status' => $data['status'] ?? null,
                'notes' => $data['notes'] ?? null,
        ]);

        return $this->response->setJSON(['success' => true]);
        } catch (\Exception $e) {
            return $this->response->setJSON(['success' => false, 'error' => 'Erreur lors de l\'ajout à la collection']);
        }
    }

    /**
     * Supprime un jeu de la collection de l'utilisateur
     * 
     * @param int $gameStatsId ID des statistiques du jeu à supprimer
     * @return \CodeIgniter\HTTP\Response Réponse JSON
     */
    public function delete($gameStatsId)
    {
        // Vérification de la connexion de l'utilisateur
        $userId = session()->get('user_id');
        if (!$userId) {
            return $this->response->setJSON(['success' => false, 'error' => 'Utilisateur non connecté']);
        }

        // Suppression des statistiques du jeu
        $gameStatsModel = new GameStatsModel();
        $deleted = $gameStatsModel
            ->where('id', $gameStatsId)
            ->where('user_id', $userId)  // Sécurité : vérifie que le jeu appartient à l'utilisateur
            ->delete();

        return $this->response->setJSON(['success' => (bool)$deleted]);
    }

    /**
     * Modifie les données d'un jeu dans la collection de l'utilisateur
     * 
     * @param int $gameStatsId ID des statistiques du jeu à modifier
     * @return \CodeIgniter\HTTP\Response Réponse JSON
     */
    public function edit($gameStatsId)
    {
        // Vérification de la connexion de l'utilisateur
        $userId = session()->get('user_id');
        if (!$userId) {
            return $this->response->setJSON(['success' => false, 'error' => 'Utilisateur non connecté']);
        }

        // Récupération des données du formulaire
        $data = $this->request->getJSON(true) ?: $this->request->getPost();

        // Validation des données
        if (!isset($data['status'])) {
            return $this->response->setJSON(['success' => false, 'error' => 'Statut requis']);
        }

        // Préparation des données à mettre à jour
        $updateData = [
            'status' => $data['status'],
            'play_time' => $data['playtime'] ?? 0,
            'notes' => $data['notes'] ?? '',
        ];

        // Mise à jour des statistiques du jeu
        $gameStatsModel = new GameStatsModel();
        $updated = $gameStatsModel
            ->where('id', $gameStatsId)
            ->where('user_id', $userId)  // Sécurité : vérifie que le jeu appartient à l'utilisateur
            ->set($updateData)
            ->update();

        return $this->response->setJSON(['success' => (bool)$updated]);
    }
}