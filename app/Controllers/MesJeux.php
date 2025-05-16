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
            ->select('game_stats.*, games.name, games.platform, games.release_date, games.category, games.cover')
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

        $gameName = $data['searchGame'] ?? null;
        $platform = $data['platform'] ?? null;
        $releaseYear = $data['releaseYear'] ?? null;
        $genre = $data['genre'] ?? null;
        $cover = $data['cover'] ?? null;

        if (!$gameName || !$platform) {
            return $this->response->setJSON(['success' => false, 'error' => 'Champs manquants']);
        }

        // Vérifie si le jeu existe déjà
        $game = $gameModel->where([
            'name' => $gameName,
            'platform' => $platform
        ])->first();

        if (!$game) {
            $gameId = $gameModel->insert([
                'name' => $gameName,
                'platform' => $platform,
                'release_date' => $releaseYear ? $releaseYear . '-01-01' : null,
                'category' => $genre,
                'cover' => $cover,
            ], true);
        } else {
            $gameId = $game['id'];
        }

        $gameStatsModel->insert([
            'user_id' => $userId,
            'game_id' => $gameId,
            'play_time' => $data['playtime'] ?? 0,
            'status' => $data['status'] ?? null,
            'notes' => $data['notes'] ?? null,
            'progress' => 0,
        ]);

        return $this->response->setJSON(['success' => true]);
    }

    public function delete($gameStatsId)
    {
        $userId = session()->get('user_id');
        if (!$userId) {
            return $this->response->setJSON(['success' => false, 'error' => 'Utilisateur non connecté']);
        }

        $gameStatsModel = new GameStatsModel();
        $deleted = $gameStatsModel
            ->where('id', $gameStatsId)
            ->where('user_id', $userId)
            ->delete();

        return $this->response->setJSON(['success' => (bool)$deleted]);
    }
}