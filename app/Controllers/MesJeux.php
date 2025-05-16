<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\GameModel;
use App\Models\GameStatsModel;

class MesJeux extends BaseController
{
    public function index()
    {
        $userId = session()->get('user_id');
        $gameStatsModel = new GameStatsModel();
        $games = $gameStatsModel
            ->select('game_stats.*, games.name, games.platform, games.release_date, games.category')
            ->join('games', 'games.id = game_stats.game_id')
            ->where('game_stats.user_id', $userId)
            ->findAll();

        return view('mes-jeux/index', [
            'title' => 'Mes Jeux',
            'games' => $games
        ]);
    }

    public function add()
    {
        $userId = session()->get('user_id');
        if (!$userId) {
            return $this->response->setJSON(['success' => false, 'error' => 'Utilisateur non connecté']);
        }

        $gameModel = new GameModel();
        $gameStatsModel = new GameStatsModel();

        $data = $this->request->getPost();

        // Utilise les bons noms de champs du formulaire
        $gameName = $data['searchGame'] ?? null;
        $platform = $data['platform'] ?? null;
        $releaseYear = $data['releaseYear'] ?? null;
        $genre = $data['genre'] ?? null;

        if (!$gameName || !$platform) {
            return $this->response->setJSON(['success' => false, 'error' => 'Champs manquants']);
        }

        // Vérifie si le jeu existe déjà
        $game = $gameModel->where([
            'name' => $gameName,
            'platform' => $platform
        ])->first();

        if (!$game) {
            // Ajoute le jeu dans la table games
            $gameId = $gameModel->insert([
                'name' => $gameName,
                'platform' => $platform,
                'release_date' => $releaseYear ? $releaseYear . '-01-01' : null,
                'category' => $genre,
            ], true);
        } else {
            $gameId = $game['id'];
        }

        // Ajoute dans game_stats
        $gameStatsModel->insert([
            'user_id' => $userId,
            'game_id' => $gameId,
            'play_time' => $data['playtime'] ?? 0,
            'progress' => 0,
        ]);

        return $this->response->setJSON(['success' => true]);
    }
}