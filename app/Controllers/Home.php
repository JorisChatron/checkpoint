<?php
// Définition de l'espace de noms pour ce contrôleur
namespace App\Controllers;

// Import des classes nécessaires
use CodeIgniter\Database\BaseConnection;  // Classe pour la connexion à la base de données

/**
 * Contrôleur de la page d'accueil
 * 
 * Ce contrôleur gère l'affichage de la page d'accueil de l'application.
 * Il affiche des données personnalisées pour les utilisateurs connectés :
 * - Les 5 derniers jeux joués
 * - Le Top 5 des jeux préférés
 * - Des statistiques personnelles (jeux possédés, terminés, temps de jeu, etc.)
 * 
 * Pour les visiteurs non connectés, il affiche une version publique
 * avec des tableaux vides.
 */
class Home extends BaseController
{
    /**
     * Méthode index - Point d'entrée principal de la page d'accueil
     * 
     * Cette méthode détermine si l'utilisateur est connecté et affiche
     * le contenu approprié. Pour les utilisateurs connectés, elle récupère
     * et affiche leurs données personnelles de jeux.
     * 
     * Logique de fonctionnement :
     * 1. Vérification de l'état de connexion
     * 2. Si non connecté → affichage public
     * 3. Si connecté → récupération des données personnelles
     * 4. Calcul des statistiques
     * 5. Affichage de la vue avec les données
     * 
     * @return string Vue de la page d'accueil avec les données appropriées
     */
    public function index()
    {
        // ===== VÉRIFICATION DE L'ÉTAT DE CONNEXION =====
        
        // Récupération de l'ID de l'utilisateur depuis la session
        // Si null, l'utilisateur n'est pas connecté
        $userId = session()->get('user_id');

        // Si l'utilisateur n'est pas connecté, affichage de la version publique
        if (!$userId) {
            // Retourne la vue publique avec des tableaux vides
            // Les utilisateurs non connectés ne voient pas de données personnelles
            return view('home', [
                'lastPlayedGames' => [],    // Aucun jeu récent à afficher
                'top5' => [],              // Aucun top 5 à afficher
                'stats' => [],             // Aucune statistique à afficher
                'username' => session()->get('username')  // Peut être null
            ]);
        }

        // ===== RÉCUPÉRATION DES DONNÉES POUR UTILISATEUR CONNECTÉ =====
        
        // Connexion à la base de données pour récupérer les données personnelles
        $db = \Config\Database::connect();

        // ===== 1. RÉCUPÉRATION DES 5 DERNIERS JEUX JOUÉS =====
        
        // Requête pour obtenir les 5 jeux les plus récemment joués par l'utilisateur
        $lastPlayedGames = $db->table('game_stats')
            ->select('games.name, games.cover, games.platform, games.release_date, games.category, game_stats.status, game_stats.play_time, game_stats.notes')
            ->join('games', 'games.id = game_stats.game_id')  // Jointure avec la table des jeux
            ->where('game_stats.user_id', $userId)            // Filtrage par utilisateur
            ->orderBy('game_stats.updated_at DESC, game_stats.created_at DESC')  // Tri par date de modification puis création
            ->limit(5)                                        // Limitation à 5 résultats
            ->get()->getResultArray();                        // Exécution et récupération des résultats

        // ===== 2. RÉCUPÉRATION DU TOP 5 DES JEUX PRÉFÉRÉS =====
        
        // Requête pour obtenir le Top 5 des jeux préférés de l'utilisateur
        $top5 = $db->table('user_top_games')
            ->select('games.name, games.cover, games.platform, games.release_date, games.category, game_stats.status, game_stats.play_time, game_stats.notes')
            ->join('games', 'games.id = user_top_games.game_id')  // Jointure avec la table des jeux
            ->join('game_stats', 'game_stats.game_id = games.id AND game_stats.user_id = user_top_games.user_id')  // Jointure avec les stats
            ->where('user_top_games.user_id', $userId)            // Filtrage par utilisateur
            ->orderBy('user_top_games.rank_position ASC')         // Tri par position dans le classement
            ->limit(5)                                            // Limitation à 5 résultats
            ->get()->getResultArray();                            // Exécution et récupération des résultats

        // ===== 3. CALCUL DES STATISTIQUES PERSONNELLES =====
        
        // Calcul des différentes statistiques pour l'utilisateur connecté
        
        // Nombre total de jeux possédés (tous les jeux dans game_stats)
        $owned = $db->table('game_stats')->where('user_id', $userId)->countAllResults();
        
        // Nombre de jeux terminés (status = 'termine')
        $finished = $db->table('game_stats')->where('user_id', $userId)->where('status', 'termine')->countAllResults();
        
        // Nombre de jeux dans la wishlist (liste de souhaits)
        $wishlistCount = $db->table('wishlist')->where('user_id', $userId)->countAllResults();
        
        // Nombre de jeux complétés à 100% (status = 'complete')
        $completed = $db->table('game_stats')->where('user_id', $userId)->where('status', 'complete')->countAllResults();
        
        // Temps de jeu total (somme de tous les temps de jeu)
        $playtime = $db->table('game_stats')->selectSum('play_time')->where('user_id', $userId)->get()->getRow('play_time') ?? 0;
        
        // Conversion du temps de jeu en format lisible (ex: "150h")
        $playtimeStr = $this->formatPlaytime($playtime);

        // ===== 4. REGROUPEMENT DES STATISTIQUES =====
        
        // Regroupement de toutes les statistiques dans un tableau associatif
        // pour faciliter leur utilisation dans la vue
        $stats = [
            'owned' => $owned,          // Jeux possédés (total)
            'finished' => $finished,    // Jeux terminés
            'playtime' => $playtimeStr, // Temps de jeu total (formaté)
            'wishlist' => $wishlistCount,    // Jeux dans la wishlist
            'completed' => $completed   // Jeux complétés à 100%
        ];

        // ===== 5. RENDU DE LA VUE AVEC TOUTES LES DONNÉES =====
        
        // Retourne la vue avec toutes les données personnalisées
        return view('home', [
            'lastPlayedGames' => $lastPlayedGames,  // 5 derniers jeux joués
            'top5' => $top5,                        // Top 5 des jeux préférés
            'stats' => $stats,                      // Statistiques personnelles
            'username' => session()->get('username') // Nom d'utilisateur pour l'affichage
        ]);
    }

    /**
     * Méthode privée pour formater le temps de jeu
     * 
     * Cette méthode convertit le nombre d'heures (stocké en base) en une
     * chaîne de caractères formatée pour l'affichage utilisateur.
     * 
     * Exemple : 150 → "150h"
     * 
     * @param int $hours Nombre d'heures à formater (peut être une chaîne)
     * @return string Temps formaté avec 'h' comme suffixe
     */
    private function formatPlaytime($hours)
    {
        $hours = (int) $hours;  // Conversion en entier pour s'assurer du bon type
        return $hours . 'h';    // Ajout du suffixe 'h' pour indiquer les heures
    }
}

