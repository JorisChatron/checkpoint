<?php

namespace App\Controllers;
use App\Models\GameModel;
use App\Models\GameStatsModel;
use CodeIgniter\Database\BaseConnection;

class Home extends BaseController
{
    public function index()
    {
        $userId = session()->get('user_id');
        if (!$userId) {
            return redirect()->to('/login');
        }

        $db = \Config\Database::connect();

        // Derniers jeux joués (5 plus récents)
        $lastPlayedGames = $db->table('game_stats')
            ->select('games.name, games.cover')
            ->join('games', 'games.id = game_stats.game_id')
            ->where('game_stats.user_id', $userId)
            ->orderBy('game_stats.updated_at DESC, game_stats.created_at DESC')
            ->limit(5)
            ->get()->getResultArray();

        // Top 5 (ordre défini par l'utilisateur)
        $top5 = $db->table('user_top_games')
            ->select('games.name, games.cover')
            ->join('games', 'games.id = user_top_games.game_id')
            ->where('user_top_games.user_id', $userId)
            ->orderBy('user_top_games.position ASC')
            ->limit(5)
            ->get()->getResultArray();

        // Statistiques
        $owned = $db->table('game_stats')->where('user_id', $userId)->countAllResults();
        $finished = $db->table('game_stats')->where('user_id', $userId)->where('status', 'termine')->countAllResults();
        $expected = $db->table('wishlist')->where('user_id', $userId)->where('status', 'souhaité')->countAllResults();
        $completed = $db->table('game_stats')->where('user_id', $userId)->where('status', 'complete')->countAllResults();
        $playtime = $db->table('game_stats')->selectSum('play_time')->where('user_id', $userId)->get()->getRow('play_time') ?? 0;
        // Conversion du temps de jeu en format lisible
        $playtimeStr = $this->formatPlaytime($playtime);

        $stats = [
            'owned' => $owned,
            'finished' => $finished,
            'playtime' => $playtimeStr,
            'expected' => $expected,
            'completed' => $completed
        ];
        return view('home', [
            'lastPlayedGames' => $lastPlayedGames,
            'top5' => $top5,
            'stats' => $stats
        ]);
    }

    // Formate le temps de jeu total en heures
    private function formatPlaytime($hours)
    {
        $hours = (int) $hours;
        return $hours . 'h';
    }
}

