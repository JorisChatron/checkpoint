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
        try {
            $userId = session()->get('user_id');
            if (!$userId) {
                return $this->response->setJSON(['success' => false, 'error' => 'Utilisateur non connecté']);
            }

            $gameModel = new GameModel();
            $wishlistModel = new WishlistModel();

            // Accepte JSON ou POST classique
            $data = $this->request->getJSON(true) ?: $this->request->getPost();
            log_message('debug', 'Données reçues : ' . json_encode($data));

            // Cas ajout rapide depuis le calendrier (RAWG)
            if (isset($data['game_id']) && !isset($data['searchGame'])) {
                $rawgId = $data['game_id'];
                // Vérifie si le jeu existe déjà dans la base
                $game = $gameModel->where('rawg_id', $rawgId)->first();
                if (!$game) {
                    try {
                        // Récupère les infos du jeu via l'API RAWG
                        $apiKey = 'ff6f7941c211456c8806541638fdfaff';
                        $url = "https://api.rawg.io/api/games/{$rawgId}?key={$apiKey}";
                        $response = @file_get_contents($url);
                        
                        if (!$response) {
                            // Créer le jeu avec des informations minimales
                            $gameId = $gameModel->insert([
                                'name' => 'Jeu ID: ' . $rawgId,
                                'platform' => 'Plateforme inconnue',
                                'rawg_id' => $rawgId
                            ], true);
                            log_message('notice', 'Jeu créé avec informations minimales, RAWG ID: ' . $rawgId);
                        } else {
                            $rawgGame = json_decode($response, true);
                            // Crée le jeu dans la base même avec des informations partielles
                            $gameId = $gameModel->insert([
                                'name' => $rawgGame['name'] ?? ('Jeu ID: ' . $rawgId),
                                'platform' => isset($rawgGame['platforms'][0]['platform']['name']) ? $rawgGame['platforms'][0]['platform']['name'] : 'Plateforme inconnue',
                                'release_date' => $rawgGame['released'] ?? null,
                                'category' => isset($rawgGame['genres'][0]['name']) ? $rawgGame['genres'][0]['name'] : null,
                                'cover' => $rawgGame['background_image'] ?? null,
                                'rawg_id' => $rawgId
                            ], true);
                        }
                    } catch (\Exception $e) {
                        log_message('error', 'Exception lors de la création du jeu : ' . $e->getMessage());
                        // Créer le jeu avec des informations minimales en cas d'erreur
                        $gameId = $gameModel->insert([
                            'name' => 'Jeu ID: ' . $rawgId,
                            'platform' => 'Plateforme inconnue',
                            'rawg_id' => $rawgId
                        ], true);
                    }
                } else {
                    $gameId = $game['id'];
                }
                // Vérifie si déjà dans la wishlist
                $existingWishlistItem = $wishlistModel->where([
                    'user_id' => $userId,
                    'game_id' => $gameId
                ])->first();
                if ($existingWishlistItem) {
                    return $this->response->setJSON(['success' => false, 'message' => 'Ce jeu est déjà dans votre wishlist']);
                }
                // Ajoute à la wishlist
                try {
                    $wishlistModel->insert([
                        'user_id' => $userId,
                        'game_id' => $gameId,
                        'status' => $data['status'] ?? 'souhaité'
                    ]);
                    return $this->response->setJSON(['success' => true]);
                } catch (\Exception $e) {
                    return $this->response->setJSON(['success' => false, 'error' => 'Erreur lors de l\'ajout à la wishlist : ' . $e->getMessage()]);
                }
            }

            // --- Cas classique (formulaire) ---
            $gameName = $data['searchGame'] ?? 'Jeu sans nom';
            $platform = $data['platform'] ?? 'Plateforme inconnue';
            $releaseYear = $data['releaseYear'] ?? null;
            $genre = $data['genre'] ?? null;
            $cover = $data['cover'] ?? null;
            
            // Log mais continuer même si des valeurs sont manquantes
            if (empty($gameName) || empty($platform)) {
                log_message('notice', 'Informations partielles : name=' . $gameName . ', platform=' . $platform);
                // On continue avec les valeurs par défaut
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
        } catch (\Exception $e) {
            log_message('error', 'Exception globale dans add() : ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false, 
                'error' => 'Une erreur inattendue est survenue. Veuillez réessayer.'
            ]);
        }
    }

    public function delete($wishlistId)
    {
        try {
            $userId = session()->get('user_id');
            if (!$userId) {
                return $this->response->setJSON(['success' => false, 'error' => 'Utilisateur non connecté']);
            }

            $wishlistModel = new WishlistModel();
            
            // Vérifier si l'élément existe et appartient à l'utilisateur
            $item = $wishlistModel->where('id', $wishlistId)
                                ->where('user_id', $userId)
                                ->first();
                                
            if (!$item) {
                return $this->response->setJSON(['success' => false, 'error' => 'Élément non trouvé']);
            }

            // Utiliser une requête directe pour la suppression
            $builder = $wishlistModel->builder();
            $deleted = $builder->where('id', $wishlistId)
                             ->where('user_id', $userId)
                             ->delete();

            if ($deleted) {
                return $this->response->setJSON(['success' => true]);
            } else {
                log_message('error', 'Échec de la suppression pour wishlist_id: ' . $wishlistId);
                return $this->response->setJSON(['success' => false, 'error' => 'Erreur lors de la suppression']);
            }
        } catch (\Exception $e) {
            log_message('error', 'Erreur lors de la suppression: ' . $e->getMessage());
            return $this->response->setJSON(['success' => false, 'error' => 'Une erreur est survenue: ' . $e->getMessage()]);
        }
    }
}
