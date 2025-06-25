<?php

namespace App\Models;
use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table = 'users';
    protected $allowedFields = ['username', 'email', 'password', 'profile_picture'];
    protected $useTimestamps = true; // si tu veux que CI remplisse automatiquement created_at



    public function getUserTopGames($userId)
    {
        $db = \Config\Database::connect();
        return $db->table('user_top_games')
            ->select('user_top_games.*, games.name, games.platform, games.release_date, games.category, games.cover')
            ->join('games', 'games.id = user_top_games.game_id')
            ->where('user_top_games.user_id', $userId)
            ->orderBy('user_top_games.position', 'ASC')
            ->get()->getResultArray();
    }

    public function getUserStats($userId)
    {
        $db = \Config\Database::connect();
        // Nombre de jeux possédés
        $nbGames = $db->table('game_stats')->where('user_id', $userId)->countAllResults();
        // Nombre de jeux en wishlist
        $nbWishlist = $db->table('wishlist')->where('user_id', $userId)->countAllResults();
        // Temps de jeu total (formaté en heures)
        $playtime = $db->table('game_stats')->selectSum('play_time')->where('user_id', $userId)->get()->getRow('play_time') ?? 0;
        $playtimeStr = ((int)$playtime) . 'h';
        // Nombre de jeux terminés (status = 'termine')
        $nbFinished = $db->table('game_stats')->where('user_id', $userId)->where('status', 'termine')->countAllResults();
        // Nombre de jeux complétés (status = 'complete')
        $nbCompleted = $db->table('game_stats')->where('user_id', $userId)->where('status', 'complete')->countAllResults();
        return [
            'nbGames' => $nbGames,
            'nbWishlist' => $nbWishlist,
            'totalPlayTime' => $playtimeStr,
            'nbFinished' => $nbFinished,
            'nbCompleted' => $nbCompleted
        ];
    }
}

