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
        // Vérification de la connexion de l'utilisateur
        $userId = session()->get('user_id');
        if (!$userId) {
            return $this->response->setJSON(['success' => false, 'error' => 'Utilisateur non connecté']);
        }

        // Initialisation des modèles
        $gameModel = new GameModel();
        $gameStatsModel = new GameStatsModel();

        // Récupération des données du formulaire
        $data = $this->request->getJSON(true) ?: $this->request->getPost();

        // Extraction des données nécessaires
        $gameName = $data['searchGame'] ?? null;     // Nom du jeu
        $platform = $data['platform'] ?? null;       // Plateforme
        $releaseYear = $data['releaseYear'] ?? null; // Année de sortie
        $genre = $data['genre'] ?? null;            // Genre
        $cover = $data['cover'] ?? null;            // Image de couverture
        $developer = $data['developer'] ?? null;    // Développeur
        $publisher = $data['publisher'] ?? null;    // Éditeur
        $rawgId = $data['rawg_id'] ?? null;         // ID RAWG si disponible

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

        // Ajout des statistiques du jeu pour l'utilisateur
        $gameStatsModel->insert([
            'user_id' => $userId,
            'game_id' => $gameId,
            'play_time' => $data['playtime'] ?? 0,    // Temps de jeu
            'status' => $data['status'] ?? null,      // Statut du jeu
            'notes' => $data['notes'] ?? null,        // Notes personnelles
            'progress' => 0,                          // Progression initiale
        ]);

        return $this->response->setJSON(['success' => true]);
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