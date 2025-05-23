<?php 

namespace App\Controllers;
use App\Models\WishlistModel;
use App\Models\GameModel;

class WishlistController extends BaseController
{
    protected $wishlistModel;
    protected $gameModel;

    public function __construct()
    {
        $this->wishlistModel = new WishlistModel();
        $this->gameModel = new GameModel();
    }

    public function index()
    {
        $userId = session()->get('user_id');
        if (!$userId) return redirect()->to('/login');

        $filters = [
            'platform' => $this->request->getGet('platform'),
            'status' => $this->request->getGet('status'),
            'genre' => $this->request->getGet('genre')
        ];

        $builder = $this->wishlistModel
            ->select('wishlist.*, games.name, games.platform, games.release_date, games.category, games.cover')
            ->join('games', 'games.id = wishlist.game_id')
            ->where('wishlist.user_id', $userId);

        foreach ($filters as $key => $value) {
            if ($value) $builder->where($key === 'genre' ? 'games.category' : ($key === 'platform' ? 'games.platform' : 'wishlist.status'), $value);
        }

        return view('wishlist/index', [
            'title' => 'Ma Wishlist',
            'wishlist' => $builder->findAll(),
            'platforms' => $this->wishlistModel->getDistinctValues('platform', $userId),
            'genres' => $this->wishlistModel->getDistinctValues('category', $userId),
            'statuses' => [['status' => 'souhaité'], ['status' => 'acheté'], ['status' => 'joué']],
            'selectedPlatform' => $filters['platform'],
            'selectedStatus' => $filters['status'],
            'selectedGenre' => $filters['genre']
        ]);
    }

    public function add()
    {
        try {
            $userId = session()->get('user_id');
            if (!$userId) return $this->response->setJSON(['success' => false, 'error' => 'Utilisateur non connecté']);

            $data = $this->request->getJSON(true) ?: $this->request->getPost();
            
            // Gestion RAWG
            if (isset($data['game_id']) && !isset($data['searchGame'])) {
                return $this->handleRawgGame($userId, $data);
            }

            // Gestion formulaire classique
            return $this->handleFormGame($userId, $data);

        } catch (\Exception $e) {
            log_message('error', 'Exception dans add(): ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false, 
                'error' => 'Une erreur est survenue. Veuillez réessayer.'
            ]);
        }
    }

    protected function handleRawgGame($userId, $data)
    {
        $rawgId = $data['game_id'];
        $game = $this->gameModel->where('rawg_id', $rawgId)->first();

        if (!$game) {
            $gameId = $this->createGameFromRawg($rawgId);
        } else {
            $gameId = $game['id'];
        }

        return $this->addToWishlist($userId, $gameId, $data['status'] ?? 'souhaité');
    }

    protected function handleFormGame($userId, $data)
    {
        $gameData = [
            'name' => $data['searchGame'] ?? 'Jeu sans nom',
            'platform' => $data['platform'] ?? 'Inconnue',
            'release_date' => isset($data['releaseYear']) ? $data['releaseYear'] . '-01-01' : null,
            'category' => $data['genre'] ?? 'Inconnu',
            'cover' => $data['cover'] ?? null
        ];

        $game = $this->gameModel->where(['name' => $gameData['name'], 'platform' => $gameData['platform']])->first();
        $gameId = $game ? $game['id'] : $this->gameModel->insert($gameData, true);

        return $this->addToWishlist($userId, $gameId, $data['status'] ?? 'souhaité');
    }

    protected function createGameFromRawg($rawgId)
    {
        try {
            $apiKey = 'ff6f7941c211456c8806541638fdfaff';
            $response = @file_get_contents("https://api.rawg.io/api/games/{$rawgId}?key={$apiKey}");
            
            if (!$response) {
                return $this->gameModel->insert([
                    'name' => 'Jeu ID: ' . $rawgId,
                    'platform' => 'Inconnue',
                    'rawg_id' => $rawgId
                ], true);
            }

            $game = json_decode($response, true);
            return $this->gameModel->insert([
                'name' => $game['name'] ?? ('Jeu ID: ' . $rawgId),
                'platform' => $game['platforms'][0]['platform']['name'] ?? 'Inconnue',
                'release_date' => $game['released'] ?? null,
                'category' => $game['genres'][0]['name'] ?? 'Inconnu',
                'cover' => $game['background_image'] ?? null,
                'rawg_id' => $rawgId
            ], true);
        } catch (\Exception $e) {
            log_message('error', 'Erreur RAWG: ' . $e->getMessage());
            return $this->gameModel->insert([
                'name' => 'Jeu ID: ' . $rawgId,
                'platform' => 'Inconnue',
                'rawg_id' => $rawgId
            ], true);
        }
    }

    protected function addToWishlist($userId, $gameId, $status)
    {
        if ($this->wishlistModel->where(['user_id' => $userId, 'game_id' => $gameId])->first()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Ce jeu est déjà dans votre wishlist']);
        }

        try {
            $this->wishlistModel->insert([
                'user_id' => $userId,
                'game_id' => $gameId,
                'status' => $status
            ]);
            return $this->response->setJSON(['success' => true]);
        } catch (\Exception $e) {
            return $this->response->setJSON(['success' => false, 'error' => 'Erreur lors de l\'ajout à la wishlist']);
        }
    }

    public function delete($wishlistId)
    {
        try {
            $userId = session()->get('user_id');
            if (!$userId) return $this->response->setJSON(['success' => false, 'error' => 'Utilisateur non connecté']);

            $deleted = $this->wishlistModel->where(['id' => $wishlistId, 'user_id' => $userId])->delete();
            return $this->response->setJSON(['success' => (bool)$deleted]);
        } catch (\Exception $e) {
            return $this->response->setJSON(['success' => false, 'error' => 'Une erreur est survenue']);
        }
    }
}
