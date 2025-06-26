<?php
// Définition de l'espace de noms pour ce contrôleur
namespace App\Controllers;

// Import des classes nécessaires
use CodeIgniter\Database\BaseConnection;  // Classe pour la connexion à la base de données

/**
 * Contrôleur de la page d'accueil
 * Gère l'affichage des données sur la page d'accueil
 */
class Home extends BaseController
{
    /**
     * Méthode index - Point d'entrée principal de la page d'accueil
     * Affiche soit une vue publique pour les visiteurs non connectés,
     * soit une vue personnalisée pour les utilisateurs connectés
     */
    public function index()
    {
        // Récupération de l'ID de l'utilisateur depuis la session
        $userId = session()->get('user_id');

        // Si l'utilisateur n'est pas connecté
        if (!$userId) {
            // Retourne la vue publique avec des tableaux vides
            return view('home', [
                'lastPlayedGames' => [],
                'top5' => [],
                'stats' => [],
                'username' => session()->get('username')
            ]);
        }

        // Connexion à la base de données
        $db = \Config\Database::connect();

        // Récupération des 5 derniers jeux joués par l'utilisateur
        $lastPlayedGames = $db->table('game_stats')
            ->select('games.name, games.cover, games.platform, games.release_date, games.category, game_stats.status, game_stats.play_time, game_stats.notes')
            ->join('games', 'games.id = game_stats.game_id')
            ->where('game_stats.user_id', $userId)
            ->orderBy('game_stats.updated_at DESC, game_stats.created_at DESC')
            ->limit(5)
            ->get()->getResultArray();

        // Récupération du Top 5 des jeux de l'utilisateur
        $top5 = $db->table('user_top_games')
            ->select('games.name, games.cover, games.platform, games.release_date, games.category, game_stats.status, game_stats.play_time, game_stats.notes')
            ->join('games', 'games.id = user_top_games.game_id')
            ->join('game_stats', 'game_stats.game_id = games.id AND game_stats.user_id = user_top_games.user_id')
            ->where('user_top_games.user_id', $userId)
            ->orderBy('user_top_games.rank_position ASC')
            ->limit(5)
            ->get()->getResultArray();

        // Calcul des différentes statistiques
        // Nombre total de jeux possédés
        $owned = $db->table('game_stats')->where('user_id', $userId)->countAllResults();
        // Nombre de jeux terminés
        $finished = $db->table('game_stats')->where('user_id', $userId)->where('status', 'termine')->countAllResults();
        // Nombre de jeux dans la wishlist
        $wishlistCount = $db->table('wishlist')->where('user_id', $userId)->countAllResults();
        // Nombre de jeux complétés à 100%
        $completed = $db->table('game_stats')->where('user_id', $userId)->where('status', 'complete')->countAllResults();
        // Temps de jeu total
        $playtime = $db->table('game_stats')->selectSum('play_time')->where('user_id', $userId)->get()->getRow('play_time') ?? 0;
        // Conversion du temps de jeu en format lisible
        $playtimeStr = $this->formatPlaytime($playtime);

        // Regroupement des statistiques dans un tableau
        $stats = [
            'owned' => $owned,          // Jeux possédés
            'finished' => $finished,    // Jeux terminés
            'playtime' => $playtimeStr, // Temps de jeu total
            'wishlist' => $wishlistCount,    // Jeux dans la wishlist
            'completed' => $completed   // Jeux complétés à 100%
        ];

        // Retourne la vue avec toutes les données
        return view('home', [
            'lastPlayedGames' => $lastPlayedGames,  // 5 derniers jeux joués
            'top5' => $top5,                        // Top 5 des jeux
            'stats' => $stats,                      // Statistiques
            'username' => session()->get('username') // Nom d'utilisateur
        ]);
    }

    /**
     * Méthode privée pour formater le temps de jeu
     * Convertit le nombre d'heures en chaîne formatée
     * 
     * @param int $hours Nombre d'heures à formater
     * @return string Temps formaté avec 'h' comme suffixe
     */
    private function formatPlaytime($hours)
    {
        $hours = (int) $hours;  // Conversion en entier
        return $hours . 'h';    // Ajout du suffixe 'h'
    }
}

