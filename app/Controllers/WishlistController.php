<?php 

namespace App\Controllers;
use App\Models\WishlistModel;  // Modèle pour la gestion de la wishlist
use App\Models\GameModel;      // Modèle pour la gestion des jeux

/**
 * Contrôleur de gestion de la wishlist (liste de souhaits)
 * 
 * Ce contrôleur gère toute la logique de la liste de souhaits des utilisateurs :
 * - Affichage de la wishlist avec filtres par plateforme et genre
 * - Ajout de jeux à la wishlist (depuis l'API RAWG ou formulaire manuel)
 * - Suppression de jeux de la wishlist
 * - Transfert de jeux de la wishlist vers la collection "Mes Jeux"
 * 
 * Fonctionnalités principales :
 * - Interface de gestion complète de la wishlist
 * - Intégration avec l'API RAWG pour l'ajout automatique
 * - Système de filtrage multi-critères
 * - Transfert sécurisé vers la collection personnelle
 * - Gestion des transactions pour assurer la cohérence des données
 */
class WishlistController extends BaseController
{
    protected $wishlistModel;  // Instance du modèle Wishlist
    protected $gameModel;      // Instance du modèle Game

    /**
     * Constructeur : initialise les modèles nécessaires
     * 
     * Crée les instances des modèles WishlistModel et GameModel
     * pour les utiliser dans toutes les méthodes du contrôleur.
     */
    public function __construct()
    {
        $this->wishlistModel = new WishlistModel();
        $this->gameModel = new GameModel();
    }

    /**
     * Affiche la liste des jeux dans la wishlist avec filtres
     * 
     * Cette méthode est le point d'entrée principal de la wishlist.
     * Elle récupère tous les jeux de la wishlist de l'utilisateur connecté
     * et applique des filtres optionnels selon les paramètres de l'URL.
     * 
     * Filtres disponibles :
     * - Par plateforme (PC, PS5, Xbox, etc.)
     * - Par genre (Action, RPG, Aventure, etc.)
     * 
     * @return mixed Vue de la wishlist ou redirection si non connecté
     */
    public function index()
    {
        // ===== VÉRIFICATION DE LA CONNEXION UTILISATEUR =====
        
        // Vérification que l'utilisateur est connecté
        $userId = session()->get('user_id');
        if (!$userId) return redirect()->to('/login');

        // ===== RÉCUPÉRATION DES FILTRES =====
        
        // Récupération des filtres depuis l'URL (méthode GET)
        $filters = [
            'platform' => $this->request->getGet('platform'),  // Filtre par plateforme (ex: PC, PS5)
            'genre' => $this->request->getGet('genre')         // Filtre par genre (ex: Action, RPG)
        ];

        // ===== CONSTRUCTION DE LA REQUÊTE PRINCIPALE =====
        
        // Construction de la requête avec jointure entre wishlist et games
        $builder = $this->wishlistModel
            ->select('wishlist.*, games.name, games.platform, games.release_date, games.category, games.cover, games.developer, games.publisher')
            ->join('games', 'games.id = wishlist.game_id')  // Jointure pour récupérer les infos des jeux
            ->where('wishlist.user_id', $userId);           // Filtrage par utilisateur connecté

        // ===== APPLICATION DES FILTRES DYNAMIQUES =====
        
        // Application des filtres si présents dans l'URL
        foreach ($filters as $key => $value) {
            if ($value) {
                // Gestion spéciale pour le filtre genre qui utilise games.category
                $field = $key === 'genre' ? 'games.category' : 'games.platform';
                $builder->where($field, $value);
            }
        }

        // ===== RENDU DE LA VUE =====
        
        // Rendu de la vue avec toutes les données nécessaires
        return view('wishlist/index', [
            'title' => 'Ma Wishlist',
            'wishlist' => $builder->findAll(),                                    // Liste des jeux filtrée
            'platforms' => $this->wishlistModel->getDistinctValues('platform', $userId),  // Plateformes pour le menu de filtre
            'genres' => $this->wishlistModel->getDistinctValues('category', $userId),     // Genres pour le menu de filtre
            'selectedPlatform' => $filters['platform'],                          // Plateforme actuellement sélectionnée
            'selectedGenre' => $filters['genre']                                 // Genre actuellement sélectionné
        ]);
    }

    /**
     * Ajoute un jeu à la wishlist
     * 
     * Cette méthode gère l'ajout de jeux à la wishlist via deux sources différentes :
     * 1. Depuis l'API RAWG (avec game_id)
     * 2. Depuis un formulaire manuel (avec les détails du jeu)
     * 
     * La méthode détermine automatiquement la source et redirige vers
     * la méthode appropriée pour le traitement.
     * 
     * Gère à la fois les jeux RAWG et les jeux ajoutés manuellement
     * 
     * @return \CodeIgniter\HTTP\Response Réponse JSON avec le statut de l'opération
     */
    public function add()
    {
        try {
            // ===== VÉRIFICATION DE LA CONNEXION UTILISATEUR =====
            
            // Vérification que l'utilisateur est connecté
            $userId = session()->get('user_id');
            if (!$userId) return $this->response->setJSON(['success' => false, 'error' => 'Utilisateur non connecté']);

            // ===== RÉCUPÉRATION DES DONNÉES =====
            
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
     * puis l'ajoute à la wishlist de l'utilisateur.
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
        
        // Ajout du jeu à la wishlist de l'utilisateur
        return $this->addToWishlist($userId, $gameId);
    }

    /**
     * Gère l'ajout d'un jeu depuis le formulaire manuel
     * 
     * Cette méthode traite l'ajout d'un jeu saisi manuellement par l'utilisateur.
     * Elle vérifie si le jeu existe déjà en base, le crée si nécessaire,
     * puis l'ajoute à la wishlist de l'utilisateur.
     * 
     * @param int $userId ID de l'utilisateur qui ajoute le jeu
     * @param array $data Données du formulaire (nom, plateforme, etc.)
     * @return \CodeIgniter\HTTP\Response Réponse JSON
     */
    protected function handleFormGame($userId, $data)
    {
        // ===== PRÉPARATION DES DONNÉES DU JEU =====
        
        // Préparation des données du jeu avec valeurs par défaut
        $gameData = [
            'name' => $data['searchGame'] ?? 'Jeu sans nom',           // Nom du jeu (obligatoire)
            'platform' => $data['platform'] ?? 'Inconnue',             // Plateforme (obligatoire)
            'release_date' => isset($data['releaseYear']) ? $data['releaseYear'] . '-01-01' : null,  // Format YYYY-MM-DD
            'category' => $data['genre'] ?? 'Inconnu',                 // Genre du jeu
            'cover' => $data['cover'] ?? null,                         // URL de la couverture
            'developer' => $data['developer'] ?? null,                 // Développeur
            'publisher' => $data['publisher'] ?? null,                 // Éditeur
            'rawg_id' => $data['rawg_id'] ?? null                      // ID RAWG optionnel
        ];

        // ===== VÉRIFICATION D'EXISTENCE ET CRÉATION DU JEU =====
        
        // Vérifie si le jeu existe déjà en base (par nom et plateforme)
        $game = $this->gameModel->where([
            'name' => $gameData['name'], 
            'platform' => $gameData['platform']
        ])->first();
        
        // Récupération de l'ID du jeu (existant ou nouvellement créé)
        $gameId = $game ? $game['id'] : $this->gameModel->insert($gameData, true);

        // Ajout du jeu à la wishlist de l'utilisateur
        return $this->addToWishlist($userId, $gameId);
    }

    /**
     * Ajoute un jeu à la wishlist de l'utilisateur
     * 
     * Cette méthode finale ajoute un jeu (identifié par son ID) à la wishlist
     * de l'utilisateur en créant une entrée dans la table wishlist.
     * Elle vérifie d'abord que le jeu n'est pas déjà dans la wishlist.
     * 
     * @param int $userId ID de l'utilisateur
     * @param int $gameId ID du jeu à ajouter
     * @return \CodeIgniter\HTTP\Response Réponse JSON
     */
    protected function addToWishlist($userId, $gameId)
    {
        // ===== VÉRIFICATION DE DOUBLON =====
        
        // Vérifie si le jeu est déjà dans la wishlist de l'utilisateur
        if ($this->wishlistModel->where(['user_id' => $userId, 'game_id' => $gameId])->first()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Ce jeu est déjà dans votre wishlist']);
        }

        try {
            // ===== AJOUT À LA WISHLIST =====
            
            // Ajout du jeu à la wishlist de l'utilisateur
            $this->wishlistModel->insert([
                'user_id' => $userId,
                'game_id' => $gameId
            ]);
            
            return $this->response->setJSON(['success' => true]);
        } catch (\Exception $e) {
            // En cas d'erreur lors de l'insertion
            return $this->response->setJSON(['success' => false, 'error' => 'Erreur lors de l\'ajout à la wishlist']);
        }
    }

    /**
     * Supprime un jeu de la wishlist
     * 
     * Cette méthode supprime un jeu de la wishlist de l'utilisateur.
     * Elle vérifie que l'utilisateur est connecté et que le jeu lui appartient
     * avant de procéder à la suppression.
     * 
     * @param int $wishlistId ID de l'entrée dans la wishlist à supprimer
     * @return \CodeIgniter\HTTP\Response Réponse JSON
     */
    public function delete($wishlistId)
    {
        try {
            // ===== VÉRIFICATION DE LA CONNEXION UTILISATEUR =====
            
            // Vérification que l'utilisateur est connecté
            $userId = session()->get('user_id');
            if (!$userId) return $this->response->setJSON(['success' => false, 'error' => 'Utilisateur non connecté']);

            // ===== SUPPRESSION SÉCURISÉE =====
            
            // Suppression avec vérification de propriété (sécurité)
            $deleted = $this->wishlistModel->where([
                'id' => $wishlistId, 
                'user_id' => $userId  // SÉCURITÉ : vérifie que l'entrée appartient à l'utilisateur
            ])->delete();
            
            return $this->response->setJSON(['success' => (bool)$deleted]);
        } catch (\Exception $e) {
            // En cas d'erreur lors de la suppression
            return $this->response->setJSON(['success' => false, 'error' => 'Une erreur est survenue']);
        }
    }

    /**
     * Transfère un jeu de la wishlist vers la collection "Mes Jeux"
     * 
     * Cette méthode permet de transférer un jeu de la wishlist vers la collection
     * personnelle de l'utilisateur. Elle effectue deux opérations en une transaction :
     * 1. Ajout du jeu à la collection avec les statistiques fournies
     * 2. Suppression du jeu de la wishlist
     * 
     * La transaction garantit que les deux opérations réussissent ou échouent ensemble.
     * 
     * @return \CodeIgniter\HTTP\Response Réponse JSON
     */
    public function transfer()
    {
        try {
            // ===== VÉRIFICATION DE LA CONNEXION UTILISATEUR =====
            
            // Vérification que l'utilisateur est connecté
            $userId = session()->get('user_id');
            if (!$userId) {
                return $this->response->setJSON(['success' => false, 'error' => 'Utilisateur non connecté']);
            }

            // ===== RÉCUPÉRATION ET VALIDATION DES DONNÉES =====
            
            // Récupération des données depuis le formulaire
            $data = $this->request->getJSON(true) ?: $this->request->getPost();
            
            // Validation des données requises pour le transfert
            if (!isset($data['wishlist_id']) || !isset($data['status'])) {
                return $this->response->setJSON(['success' => false, 'error' => 'Données manquantes']);
            }

            $wishlistId = $data['wishlist_id'];
            
            // ===== VÉRIFICATION DE LA PROPRIÉTÉ DE L'ENTRÉE WISHLIST =====
            
            // Vérification que l'entrée wishlist appartient à l'utilisateur
            $wishlistEntry = $this->wishlistModel->where([
                'id' => $wishlistId, 
                'user_id' => $userId
            ])->first();
            
            if (!$wishlistEntry) {
                return $this->response->setJSON(['success' => false, 'error' => 'Jeu non trouvé dans votre wishlist']);
            }

            $gameId = $wishlistEntry['game_id'];

            // ===== VÉRIFICATION DE DOUBLON DANS LA COLLECTION =====
            
            // Vérification que le jeu n'est pas déjà dans la collection
            $gameStatsModel = new \App\Models\GameStatsModel();
            if ($gameStatsModel->where(['user_id' => $userId, 'game_id' => $gameId])->first()) {
                return $this->response->setJSON(['success' => false, 'error' => 'Ce jeu est déjà dans votre collection']);
            }

            // ===== DÉBUT DE LA TRANSACTION =====
            
            // Début d'une transaction pour assurer la cohérence des données
            $db = \Config\Database::connect();
            $db->transBegin();

            try {
                // ===== 1. AJOUT À LA COLLECTION "MES JEUX" =====
                
                // Ajout du jeu à la collection avec les statistiques fournies
                $gameStatsModel->insert([
                    'user_id' => $userId,
                    'game_id' => $gameId,
                    'play_time' => $data['playtime'] ?? 0,    // Temps de jeu en heures
                    'status' => $data['status'],              // Statut de progression
                    'notes' => $data['notes'] ?? null,        // Notes personnelles
                ]);

                // ===== 2. SUPPRESSION DE LA WISHLIST =====
                
                // Suppression du jeu de la wishlist
                $this->wishlistModel->where([
                    'id' => $wishlistId, 
                    'user_id' => $userId
                ])->delete();

                // ===== VALIDATION DE LA TRANSACTION =====
                
                // Validation de la transaction (toutes les opérations ont réussi)
                $db->transCommit();

                return $this->response->setJSON(['success' => true, 'message' => 'Jeu transféré avec succès']);

            } catch (\Exception $e) {
                // ===== ANNULATION DE LA TRANSACTION =====
                
                // Annulation de la transaction en cas d'erreur
                // Toutes les modifications sont annulées
                $db->transRollback();
                throw $e;
            }

        } catch (\Exception $e) {
            // ===== GESTION DES ERREURS =====
            
            // Log de l'erreur pour le débogage
            log_message('error', 'Erreur dans transfer(): ' . $e->getMessage());
            
            return $this->response->setJSON([
                'success' => false, 
                'error' => 'Une erreur est survenue lors du transfert'
            ]);
        }
    }
}
