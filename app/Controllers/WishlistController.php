<?php 

namespace App\Controllers;
use App\Models\WishlistModel;  // Modèle pour la gestion de la wishlist
use App\Models\GameModel;      // Modèle pour la gestion des jeux

/**
 * Contrôleur de gestion de la wishlist (liste de souhaits)
 * Permet de gérer les jeux que l'utilisateur souhaite acquérir
 */
class WishlistController extends BaseController
{
    protected $wishlistModel;  // Instance du modèle Wishlist
    protected $gameModel;      // Instance du modèle Game

    /**
     * Constructeur : initialise les modèles nécessaires
     */
    public function __construct()
    {
        $this->wishlistModel = new WishlistModel();
        $this->gameModel = new GameModel();
    }

    /**
     * Affiche la liste des jeux dans la wishlist avec filtres
     * 
     * @return mixed Vue de la wishlist ou redirection
     */
    public function index()
    {
        // Vérification de la connexion utilisateur
        $userId = session()->get('user_id');
        if (!$userId) return redirect()->to('/login');

        // Récupération des filtres depuis l'URL
        $filters = [
            'platform' => $this->request->getGet('platform'),  // Filtre par plateforme
            'genre' => $this->request->getGet('genre')         // Filtre par genre
        ];

        // Construction de la requête avec jointure
        $builder = $this->wishlistModel
            ->select('wishlist.*, games.name, games.platform, games.release_date, games.category, games.cover, games.developer, games.publisher')
            ->join('games', 'games.id = wishlist.game_id')
            ->where('wishlist.user_id', $userId);

        // Application des filtres
        foreach ($filters as $key => $value) {
            if ($value) $builder->where($key === 'genre' ? 'games.category' : 'games.platform', $value);
        }

        // Rendu de la vue avec les données
        return view('wishlist/index', [
            'title' => 'Ma Wishlist',
            'wishlist' => $builder->findAll(),
            'platforms' => $this->wishlistModel->getDistinctValues('platform', $userId),
            'genres' => $this->wishlistModel->getDistinctValues('category', $userId),
            'selectedPlatform' => $filters['platform'],
            'selectedGenre' => $filters['genre']
        ]);
    }

    /**
     * Ajoute un jeu à la wishlist
     * Gère à la fois les jeux RAWG et les jeux ajoutés manuellement
     * 
     * @return \CodeIgniter\HTTP\Response Réponse JSON
     */
    public function add()
    {
        try {
            // Vérification de la connexion
            $userId = session()->get('user_id');
            if (!$userId) return $this->response->setJSON(['success' => false, 'error' => 'Utilisateur non connecté']);

            // Récupération des données (JSON ou POST)
            $data = $this->request->getJSON(true) ?: $this->request->getPost();
            
            // Priorité à l'ID RAWG même si on a searchGame (cas du calendrier)
            if (isset($data['game_id']) && is_numeric($data['game_id'])) {
                return $this->handleRawgGame($userId, $data);  // Jeu depuis RAWG
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
        return $this->addToWishlist($userId, $gameId);
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
        // Préparation des données du jeu
        $gameData = [
            'name' => $data['searchGame'] ?? 'Jeu sans nom',
            'platform' => $data['platform'] ?? 'Inconnue',
            'release_date' => isset($data['releaseYear']) ? $data['releaseYear'] . '-01-01' : null,
            'category' => $data['genre'] ?? 'Inconnu',
            'cover' => $data['cover'] ?? null,
            'developer' => $data['developer'] ?? null,
            'publisher' => $data['publisher'] ?? null,
            'rawg_id' => $data['rawg_id'] ?? null
        ];

        // Vérifie si le jeu existe déjà
        $game = $this->gameModel->where(['name' => $gameData['name'], 'platform' => $gameData['platform']])->first();
        $gameId = $game ? $game['id'] : $this->gameModel->insert($gameData, true);

        return $this->addToWishlist($userId, $gameId);
    }



    /**
     * Ajoute un jeu à la wishlist de l'utilisateur
     * 
     * @param int $userId ID de l'utilisateur
     * @param int $gameId ID du jeu
     * @return \CodeIgniter\HTTP\Response Réponse JSON
     */
    protected function addToWishlist($userId, $gameId)
    {
        // Vérifie si le jeu est déjà dans la wishlist
        if ($this->wishlistModel->where(['user_id' => $userId, 'game_id' => $gameId])->first()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Ce jeu est déjà dans votre wishlist']);
        }

        try {
            // Ajout à la wishlist
            $this->wishlistModel->insert([
                'user_id' => $userId,
                'game_id' => $gameId
            ]);
            return $this->response->setJSON(['success' => true]);
        } catch (\Exception $e) {
            return $this->response->setJSON(['success' => false, 'error' => 'Erreur lors de l\'ajout à la wishlist']);
        }
    }

    /**
     * Supprime un jeu de la wishlist
     * 
     * @param int $wishlistId ID de l'entrée dans la wishlist
     * @return \CodeIgniter\HTTP\Response Réponse JSON
     */
    public function delete($wishlistId)
    {
        try {
            // Vérification de la connexion
            $userId = session()->get('user_id');
            if (!$userId) return $this->response->setJSON(['success' => false, 'error' => 'Utilisateur non connecté']);

            // Suppression avec vérification de propriété
            $deleted = $this->wishlistModel->where(['id' => $wishlistId, 'user_id' => $userId])->delete();
            return $this->response->setJSON(['success' => (bool)$deleted]);
        } catch (\Exception $e) {
            return $this->response->setJSON(['success' => false, 'error' => 'Une erreur est survenue']);
        }
    }

    /**
     * Transfère un jeu de la wishlist vers la collection "Mes Jeux"
     * 
     * @return \CodeIgniter\HTTP\Response Réponse JSON
     */
    public function transfer()
    {
        try {
            // Vérification de la connexion
            $userId = session()->get('user_id');
            if (!$userId) {
                return $this->response->setJSON(['success' => false, 'error' => 'Utilisateur non connecté']);
            }

            // Récupération des données
            $data = $this->request->getJSON(true) ?: $this->request->getPost();
            
            // Validation des données requises
            if (!isset($data['wishlist_id']) || !isset($data['status'])) {
                return $this->response->setJSON(['success' => false, 'error' => 'Données manquantes']);
            }

            $wishlistId = $data['wishlist_id'];
            
            // Vérification que l'entrée wishlist appartient à l'utilisateur
            $wishlistEntry = $this->wishlistModel->where(['id' => $wishlistId, 'user_id' => $userId])->first();
            if (!$wishlistEntry) {
                return $this->response->setJSON(['success' => false, 'error' => 'Jeu non trouvé dans votre wishlist']);
            }

            $gameId = $wishlistEntry['game_id'];

            // Vérification que le jeu n'est pas déjà dans la collection
            $gameStatsModel = new \App\Models\GameStatsModel();
            if ($gameStatsModel->where(['user_id' => $userId, 'game_id' => $gameId])->first()) {
                return $this->response->setJSON(['success' => false, 'error' => 'Ce jeu est déjà dans votre collection']);
            }

            // Début d'une transaction pour assurer la cohérence
            $db = \Config\Database::connect();
            $db->transBegin();

            try {
                // 1. Ajout à la collection "Mes Jeux"
                $gameStatsModel->insert([
                    'user_id' => $userId,
                    'game_id' => $gameId,
                    'play_time' => $data['playtime'] ?? 0,
                    'status' => $data['status'],
                    'notes' => $data['notes'] ?? null,
                ]);

                // 2. Suppression de la wishlist
                $this->wishlistModel->where(['id' => $wishlistId, 'user_id' => $userId])->delete();

                // Validation de la transaction
                $db->transCommit();

                return $this->response->setJSON(['success' => true, 'message' => 'Jeu transféré avec succès']);

            } catch (\Exception $e) {
                // Annulation de la transaction en cas d'erreur
                $db->transRollback();
                throw $e;
            }

        } catch (\Exception $e) {
            log_message('error', 'Erreur dans transfer(): ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false, 
                'error' => 'Une erreur est survenue lors du transfert'
            ]);
        }
    }
}
