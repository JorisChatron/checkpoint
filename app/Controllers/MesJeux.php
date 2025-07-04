<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\GameModel;        // Modèle pour la gestion des jeux
use App\Models\GameStatsModel;   // Modèle pour les statistiques des jeux

/**
 * Contrôleur de gestion de la bibliothèque de jeux de l'utilisateur
 * 
 * Ce contrôleur gère toute la logique de la bibliothèque personnelle de jeux :
 * - Affichage de la collection avec filtres avancés
 * - Ajout de nouveaux jeux (depuis l'API RAWG ou formulaire manuel)
 * - Modification des statistiques de jeux existants
 * - Suppression de jeux de la collection
 * - Gestion des filtres par plateforme, statut et genre
 * 
 * Fonctionnalités principales :
 * - Interface de gestion complète de la collection
 * - Intégration avec l'API RAWG pour l'ajout automatique
 * - Système de filtrage multi-critères
 * - Validation et sécurité des données
 */
class MesJeux extends BaseController
{
    /**
     * Affiche la liste des jeux de l'utilisateur avec filtres
     * 
     * Cette méthode est le point d'entrée principal de la bibliothèque.
     * Elle récupère tous les jeux de l'utilisateur connecté et applique
     * des filtres optionnels selon les paramètres de l'URL.
     * 
     * Filtres disponibles :
     * - Par plateforme (PC, PS5, Xbox, etc.)
     * - Par statut (en cours, terminé, abandonné, etc.)
     * - Par genre (Action, RPG, Aventure, etc.)
     * 
     * @return string Vue avec la liste des jeux filtrée et les options de filtrage
     */
    public function index()
    {
        // ===== RÉCUPÉRATION DES DONNÉES DE BASE =====
        
        // Récupération de l'ID utilisateur depuis la session
        $userId = session()->get('user_id');
        $gameStatsModel = new GameStatsModel();

        // ===== RÉCUPÉRATION DES PARAMÈTRES DE FILTRAGE =====
        
        // Récupération des paramètres de filtrage depuis l'URL (méthode GET)
        $platform = $this->request->getGet('platform');  // Filtre par plateforme (ex: PC, PS5)
        $status = $this->request->getGet('status');      // Filtre par statut (ex: termine, en cours)
        $genre = $this->request->getGet('genre');        // Filtre par genre (ex: Action, RPG)

        // ===== CONSTRUCTION DE LA REQUÊTE PRINCIPALE =====
        
        // Construction de la requête de base avec jointure entre game_stats et games
        $builder = $gameStatsModel
            ->select('game_stats.*, games.name, games.platform, games.release_date, games.category, games.cover, games.developer, games.publisher')
            ->join('games', 'games.id = game_stats.game_id')  // Jointure pour récupérer les infos du jeu
            ->where('game_stats.user_id', $userId);           // Filtrage par utilisateur connecté

        // ===== APPLICATION DES FILTRES DYNAMIQUES =====
        
        // Application des filtres si présents dans l'URL
        if ($platform) {
            $builder->where('games.platform', $platform);  // Filtre par plateforme spécifique
        }
        if ($status) {
            $builder->where('game_stats.status', $status);  // Filtre par statut de progression
        }
        if ($genre) {
            $builder->like('games.category', $genre);       // Recherche partielle dans le genre
        }

        // Exécution de la requête pour récupérer les jeux filtrés
        $games = $builder->findAll();

        // ===== RÉCUPÉRATION DES OPTIONS DE FILTRAGE =====
        
        // Récupération des valeurs uniques pour peupler les menus déroulants de filtrage
        
        // Liste des plateformes disponibles dans la collection de l'utilisateur
        $platforms = $gameStatsModel
            ->select('games.platform')
            ->join('games', 'games.id = game_stats.game_id')
            ->where('game_stats.user_id', $userId)
            ->groupBy('games.platform')  // Évite les doublons
            ->findAll();

        // Liste des statuts utilisés par l'utilisateur
        $statuses = $gameStatsModel
            ->select('game_stats.status')
            ->where('game_stats.user_id', $userId)
            ->groupBy('game_stats.status')  // Évite les doublons
            ->findAll();

        // Liste des genres disponibles dans la collection
        $genres = $gameStatsModel
            ->select('games.category')
            ->join('games', 'games.id = game_stats.game_id')
            ->where('game_stats.user_id', $userId)
            ->groupBy('games.category')  // Évite les doublons
            ->findAll();

        // ===== RENDU DE LA VUE =====
        
        // Rendu de la vue avec toutes les données nécessaires
        return view('mes-jeux/index', [
            'title' => 'Mes Jeux',
            'games' => $games,                    // Liste des jeux filtrée selon les critères
            'platforms' => $platforms,            // Liste des plateformes pour le menu de filtre
            'statuses' => $statuses,             // Liste des statuts pour le menu de filtre
            'genres' => $genres,                 // Liste des genres pour le menu de filtre
            'selectedPlatform' => $platform,     // Plateforme actuellement sélectionnée (pour garder le filtre actif)
            'selectedStatus' => $status,         // Statut actuellement sélectionné (pour garder le filtre actif)
            'selectedGenre' => $genre,           // Genre actuellement sélectionné (pour garder le filtre actif)
        ]);
    }

    /**
     * Ajoute un nouveau jeu à la collection de l'utilisateur
     * 
     * Cette méthode gère l'ajout de jeux via deux sources différentes :
     * 1. Depuis l'API RAWG (avec game_id)
     * 2. Depuis un formulaire manuel (avec les détails du jeu)
     * 
     * La méthode détermine automatiquement la source et redirige vers
     * la méthode appropriée pour le traitement.
     * 
     * @return \CodeIgniter\HTTP\Response Réponse JSON avec le statut de l'opération
     */
    public function add()
    {
        try {
            // ===== VÉRIFICATION DE LA CONNEXION UTILISATEUR =====
            
            // Vérification que l'utilisateur est connecté
            $userId = session()->get('user_id');
            if (!$userId) {
                return $this->response->setJSON(['success' => false, 'error' => 'Utilisateur non connecté']);
            }

            // ===== RÉCUPÉRATION DES DONNÉES DU FORMULAIRE =====
            
            // Récupération des données depuis le formulaire
            // Supporte à la fois JSON (AJAX) et POST (formulaire classique)
            $data = $this->request->getJSON(true) ?: $this->request->getPost();

            // ===== DÉTERMINATION DE LA SOURCE DU JEU =====
            
            // Priorité à l'ID RAWG si présent (ajout depuis l'API)
            if (isset($data['game_id']) && is_numeric($data['game_id'])) {
                return $this->handleRawgGame($userId, $data);  // Traitement jeu RAWG
            }

            // Sinon, traitement d'un jeu saisi manuellement
            return $this->handleFormGame($userId, $data);      // Traitement jeu formulaire

        } catch (\Exception $e) {
            // ===== GESTION DES ERREURS =====
            
            // Log de l'erreur pour le débogage
            log_message('error', 'Exception dans add(): ' . $e->getMessage());
            
            // Retour d'une réponse d'erreur générique
            return $this->response->setJSON([
                'success' => false, 
                'error' => 'Une erreur est survenue. Veuillez réessayer.'
            ]);
        }
    }

    /**
     * Gère l'ajout d'un jeu depuis l'API RAWG
     * 
     * Cette méthode traite l'ajout d'un jeu identifié par son ID RAWG.
     * Elle utilise le service RAWG pour récupérer ou créer le jeu en base,
     * puis l'ajoute à la collection de l'utilisateur.
     * 
     * @param int $userId ID de l'utilisateur qui ajoute le jeu
     * @param array $data Données du jeu (incluant game_id RAWG)
     * @return \CodeIgniter\HTTP\Response Réponse JSON
     */
    protected function handleRawgGame($userId, $data)
    {
        // Utilisation du service RAWG pour récupérer/créer le jeu
        $rawgService = new \App\Services\RawgService();
        $gameId = $rawgService->getOrCreateGame($data['game_id']);
        
        // Ajout du jeu aux statistiques de l'utilisateur
        return $this->addToGameStats($userId, $gameId, $data);
    }

    /**
     * Gère l'ajout d'un jeu depuis le formulaire manuel
     * 
     * Cette méthode traite l'ajout d'un jeu saisi manuellement par l'utilisateur.
     * Elle vérifie si le jeu existe déjà en base, le crée si nécessaire,
     * puis l'ajoute à la collection de l'utilisateur.
     * 
     * @param int $userId ID de l'utilisateur qui ajoute le jeu
     * @param array $data Données du formulaire (nom, plateforme, etc.)
     * @return \CodeIgniter\HTTP\Response Réponse JSON
     */
    protected function handleFormGame($userId, $data)
    {
        $gameModel = new GameModel();

        // ===== EXTRACTION ET VALIDATION DES DONNÉES =====
        
        // Extraction des données nécessaires depuis le formulaire
        $gameName = $data['searchGame'] ?? null;      // Nom du jeu (obligatoire)
        $platform = $data['platform'] ?? null;        // Plateforme (obligatoire)
        $releaseYear = $data['releaseYear'] ?? null;  // Année de sortie
        $genre = $data['genre'] ?? null;              // Genre du jeu
        $cover = $data['cover'] ?? null;              // URL de la couverture
        $developer = $data['developer'] ?? null;      // Développeur
        $publisher = $data['publisher'] ?? null;      // Éditeur
        $rawgId = $data['rawg_id'] ?? null;           // ID RAWG optionnel

        // Validation des données requises
        if (!$gameName || !$platform) {
            return $this->response->setJSON(['success' => false, 'error' => 'Champs manquants']);
        }

        // ===== VÉRIFICATION D'EXISTENCE DU JEU =====
        
        // Vérification si le jeu existe déjà dans la base de données
        $game = $gameModel->where([
            'name' => $gameName,
            'platform' => $platform
        ])->first();

        // ===== CRÉATION OU RÉCUPÉRATION DE L'ID DU JEU =====
        
        if (!$game) {
            // Le jeu n'existe pas, création d'un nouveau jeu en base
            $gameId = $gameModel->insert([
                'name' => $gameName,
                'platform' => $platform,
                'release_date' => $releaseYear ? $releaseYear . '-01-01' : null,  // Format YYYY-MM-DD
                'category' => $genre,
                'cover' => $cover,
                'developer' => $developer,
                'publisher' => $publisher,
                'rawg_id' => $rawgId
            ], true);  // true pour récupérer l'ID généré
        } else {
            // Le jeu existe déjà, récupération de son ID
            $gameId = $game['id'];
        }

        // Ajout du jeu aux statistiques de l'utilisateur
        return $this->addToGameStats($userId, $gameId, $data);
    }

    /**
     * Ajoute un jeu aux statistiques de l'utilisateur
     * 
     * Cette méthode finale ajoute un jeu (identifié par son ID) à la collection
     * de l'utilisateur en créant une entrée dans la table game_stats.
     * Elle vérifie d'abord que le jeu n'est pas déjà dans la collection.
     * 
     * @param int $userId ID de l'utilisateur
     * @param int $gameId ID du jeu à ajouter
     * @param array $data Données du formulaire (temps de jeu, statut, notes)
     * @return \CodeIgniter\HTTP\Response Réponse JSON
     */
    protected function addToGameStats($userId, $gameId, $data)
    {
        $gameStatsModel = new GameStatsModel();
        
        // ===== VÉRIFICATION DE DOUBLON =====
        
        // Vérification si le jeu est déjà dans la collection de l'utilisateur
        if ($gameStatsModel->where(['user_id' => $userId, 'game_id' => $gameId])->first()) {
            return $this->response->setJSON(['success' => false, 'error' => 'Ce jeu est déjà dans votre collection']);
        }

        try {
            // ===== AJOUT DU JEU À LA COLLECTION =====
            
            // Ajout des statistiques du jeu pour l'utilisateur
            $gameStatsModel->insert([
                'user_id' => $userId,
                'game_id' => $gameId,
                'play_time' => $data['playtime'] ?? 0,    // Temps de jeu en heures
                'status' => $data['status'] ?? null,      // Statut (en cours, terminé, etc.)
                'notes' => $data['notes'] ?? null,        // Notes personnelles
            ]);

            return $this->response->setJSON(['success' => true]);
        } catch (\Exception $e) {
            // En cas d'erreur lors de l'insertion
            return $this->response->setJSON(['success' => false, 'error' => 'Erreur lors de l\'ajout à la collection']);
        }
    }

    /**
     * Supprime un jeu de la collection de l'utilisateur
     * 
     * Cette méthode supprime un jeu de la collection personnelle de l'utilisateur.
     * Elle vérifie que l'utilisateur est connecté et que le jeu lui appartient
     * avant de procéder à la suppression.
     * 
     * @param int $gameStatsId ID des statistiques du jeu à supprimer
     * @return \CodeIgniter\HTTP\Response Réponse JSON
     */
    public function delete($gameStatsId)
    {
        // ===== VÉRIFICATION DE LA CONNEXION UTILISATEUR =====
        
        // Vérification que l'utilisateur est connecté
        $userId = session()->get('user_id');
        if (!$userId) {
            return $this->response->setJSON(['success' => false, 'error' => 'Utilisateur non connecté']);
        }

        // ===== SUPPRESSION SÉCURISÉE =====
        
        // Suppression des statistiques du jeu avec vérification de propriété
        $gameStatsModel = new GameStatsModel();
        $deleted = $gameStatsModel
            ->where('id', $gameStatsId)
            ->where('user_id', $userId)  // SÉCURITÉ : vérifie que le jeu appartient à l'utilisateur
            ->delete();

        return $this->response->setJSON(['success' => (bool)$deleted]);
    }

    /**
     * Modifie les données d'un jeu dans la collection de l'utilisateur
     * 
     * Cette méthode permet de mettre à jour les statistiques d'un jeu existant
     * dans la collection de l'utilisateur (statut, temps de jeu, notes).
     * Elle vérifie que l'utilisateur est connecté et propriétaire du jeu.
     * 
     * @param int $gameStatsId ID des statistiques du jeu à modifier
     * @return \CodeIgniter\HTTP\Response Réponse JSON
     */
    public function edit($gameStatsId)
    {
        // ===== VÉRIFICATION DE LA CONNEXION UTILISATEUR =====
        
        // Vérification que l'utilisateur est connecté
        $userId = session()->get('user_id');
        if (!$userId) {
            return $this->response->setJSON(['success' => false, 'error' => 'Utilisateur non connecté']);
        }

        // ===== RÉCUPÉRATION ET VALIDATION DES DONNÉES =====
        
        // Récupération des données du formulaire
        $data = $this->request->getJSON(true) ?: $this->request->getPost();

        // Validation des données requises
        if (!isset($data['status'])) {
            return $this->response->setJSON(['success' => false, 'error' => 'Statut requis']);
        }

        // ===== PRÉPARATION DES DONNÉES À METTRE À JOUR =====
        
        // Préparation des données à mettre à jour
        $updateData = [
            'status' => $data['status'],           // Nouveau statut de progression
            'play_time' => $data['playtime'] ?? 0, // Nouveau temps de jeu
            'notes' => $data['notes'] ?? '',       // Nouvelles notes personnelles
        ];

        // ===== MISE À JOUR SÉCURISÉE =====
        
        // Mise à jour des statistiques du jeu avec vérification de propriété
        $gameStatsModel = new GameStatsModel();
        $updated = $gameStatsModel
            ->where('id', $gameStatsId)
            ->where('user_id', $userId)  // SÉCURITÉ : vérifie que le jeu appartient à l'utilisateur
            ->set($updateData)
            ->update();

        return $this->response->setJSON(['success' => (bool)$updated]);
    }
}