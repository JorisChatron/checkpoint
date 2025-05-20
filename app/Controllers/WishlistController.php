<?php 

namespace App\Controllers;
use App\Models\WishlistModel;
use App\Models\GameModel;

class WishlistController extends BaseController
{
    public function index()
    {
        $userId = session()->get('user_id');
        if (!$userId) {
            return redirect()->to('/login');
        }

        $model = new WishlistModel();

        // Récupération des filtres
        $platform = $this->request->getGet('platform');
        $status = $this->request->getGet('status');
        $genre = $this->request->getGet('genre');

        $builder = $model
            ->select('wishlist.*, games.name, games.platform, games.release_date, games.category, games.cover')
            ->join('games', 'games.id = wishlist.game_id')
            ->where('wishlist.user_id', $userId);

        if ($platform) {
            $builder->where('games.platform', $platform);
        }
        if ($status) {
            $builder->where('wishlist.status', $status);
        }
        if ($genre) {
            $builder->where('games.category', $genre);
        }

        $wishlist = $builder->findAll();

        // Récupérer les valeurs distinctes pour les filtres
        $platforms = $model->select('games.platform')->join('games', 'games.id = wishlist.game_id')->where('wishlist.user_id', $userId)->distinct()->findAll();
        $genres = $model->select('games.category')->join('games', 'games.id = wishlist.game_id')->where('wishlist.user_id', $userId)->distinct()->findAll();
        $statuses = [
            ['status' => 'souhaité'],
            ['status' => 'acheté'],
            ['status' => 'joué']
        ];

        return view('wishlist/index', [
            'title' => 'Ma Wishlist',
            'wishlist' => $wishlist,
            'platforms' => $platforms,
            'genres' => $genres,
            'statuses' => $statuses,
            'selectedPlatform' => $platform,
            'selectedStatus' => $status,
            'selectedGenre' => $genre
        ]);
    }

    public function add()
    {
        $userId = session()->get('user_id');
        if (!$userId) {
            return $this->response->setJSON(['success' => false, 'error' => 'Utilisateur non connecté']);
        }

        $gameModel = new GameModel();
        $wishlistModel = new WishlistModel();

        $data = $this->request->getPost();
        log_message('debug', 'Données reçues : ' . json_encode($data));

        $gameName = $data['searchGame'] ?? null;
        $platform = $data['platform'] ?? null;
        $releaseYear = $data['releaseYear'] ?? null;
        $genre = $data['genre'] ?? null;
        $cover = $data['cover'] ?? null;

        if (!$gameName || !$platform) {
            log_message('error', 'Champs manquants : name=' . $gameName . ', platform=' . $platform);
            return $this->response->setJSON(['success' => false, 'error' => 'Champs manquants']);
        }

        // Vérifie si le jeu existe déjà
        $game = $gameModel->where([
            'name' => $gameName,
            'platform' => $platform
        ])->first();

        if (!$game) {
            log_message('debug', 'Création d\'un nouveau jeu');
            $gameId = $gameModel->insert([
                'name' => $gameName,
                'platform' => $platform,
                'release_date' => $releaseYear ? $releaseYear . '-01-01' : null,
                'category' => $genre,
                'cover' => $cover,
            ], true);
        } else {
            log_message('debug', 'Jeu existant trouvé : ' . $game['id']);
            $gameId = $game['id'];
        }

        // Vérifie si le jeu n'est pas déjà dans la wishlist
        $existingWishlistItem = $wishlistModel->where([
            'user_id' => $userId,
            'game_id' => $gameId
        ])->first();

        if ($existingWishlistItem) {
            log_message('error', 'Jeu déjà dans la wishlist');
            return $this->response->setJSON(['success' => false, 'error' => 'Ce jeu est déjà dans votre wishlist']);
        }

        try {
            $wishlistModel->insert([
                'user_id' => $userId,
                'game_id' => $gameId,
                'status' => $data['status'] ?? 'souhaité'
            ]);
            log_message('debug', 'Jeu ajouté à la wishlist avec succès');
            return $this->response->setJSON(['success' => true]);
        } catch (\Exception $e) {
            log_message('error', 'Erreur lors de l\'ajout à la wishlist : ' . $e->getMessage());
            return $this->response->setJSON(['success' => false, 'error' => 'Erreur lors de l\'ajout à la wishlist : ' . $e->getMessage()]);
        }
    }

    public function delete($wishlistId)
    {
        $userId = session()->get('user_id');
        if (!$userId) {
            return $this->response->setJSON(['success' => false, 'error' => 'Utilisateur non connecté']);
        }

        $wishlistModel = new WishlistModel();
        $deleted = $wishlistModel
            ->where('id', $wishlistId)
            ->where('user_id', $userId)
            ->delete();

        return $this->response->setJSON(['success' => (bool)$deleted]);
    }
}
