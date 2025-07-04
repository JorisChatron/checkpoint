<?php

namespace App\Models;
use CodeIgniter\Model;

/**
 * Modèle de gestion des utilisateurs
 * 
 * Ce modèle gère toutes les opérations de base de données liées aux utilisateurs.
 * Il hérite de CodeIgniter\Model et fournit une interface pour :
 * - Gérer les comptes utilisateurs (création, modification, suppression)
 * - Récupérer les informations de profil des utilisateurs
 * - Calculer les statistiques personnelles des utilisateurs
 * - Récupérer le Top 5 des jeux préférés de chaque utilisateur
 * 
 * Le modèle utilise la table 'users' qui contient les informations de base
 * des utilisateurs et fournit des méthodes spécialisées pour les statistiques
 * et les préférences de jeux.
 * 
 * Utilisé par :
 * - Le contrôleur d'authentification pour la connexion/inscription
 * - Le contrôleur utilisateur pour la gestion du profil
 * - L'affichage des statistiques sur la page d'accueil
 * - La gestion des préférences utilisateur
 */
class UserModel extends Model
{
    /**
     * Nom de la table en base de données
     * 
     * Cette propriété définit le nom de la table SQL utilisée par ce modèle.
     * La table 'users' contient les informations de base des utilisateurs :
     * identifiants, informations de profil, et données d'authentification.
     */
    protected $table = 'users';
    
    /**
     * Champs autorisés pour les opérations d'insertion et de mise à jour
     * 
     * Cette propriété définit la liste des colonnes de la table 'users'
     * qui peuvent être modifiées via les méthodes insert() et update().
     * 
     * Champs inclus :
     * - username : Nom d'utilisateur unique pour la connexion
     * - email : Adresse email de l'utilisateur
     * - password : Mot de passe hashé de l'utilisateur
     * - profile_picture : Chemin vers la photo de profil
     * 
     * Sécurité : Seuls ces champs peuvent être modifiés, protégeant
     * contre les attaques par injection de champs non autorisés.
     */
    protected $allowedFields = [
        'username',        // Nom d'utilisateur unique
        'email',           // Adresse email
        'password',        // Mot de passe hashé
        'profile_picture'  // Chemin vers la photo de profil
    ];
    
    /**
     * Activation de la gestion automatique des timestamps
     * 
     * Cette propriété active la gestion automatique des champs created_at
     * et updated_at par CodeIgniter. Quand elle est à true, CodeIgniter
     * remplit automatiquement ces champs lors des opérations d'insertion
     * et de mise à jour.
     */
    protected $useTimestamps = true; // CodeIgniter remplit automatiquement created_at et updated_at

    /**
     * Récupère le Top 5 des jeux préférés d'un utilisateur
     * 
     * Cette méthode effectue une requête complexe pour récupérer le Top 5
     * des jeux préférés d'un utilisateur spécifique. Elle joint les tables
     * user_top_games et games pour obtenir toutes les informations nécessaires
     * sur les jeux du Top 5.
     * 
     * @param int $userId ID de l'utilisateur dont on veut récupérer le Top 5
     * @return array Tableau des jeux du Top 5 avec leurs informations complètes
     */
    public function getUserTopGames($userId)
    {
        // Connexion à la base de données
        $db = \Config\Database::connect();
        
        // Requête pour récupérer le Top 5 avec jointure
        return $db->table('user_top_games')
            ->select('user_top_games.*, games.name, games.platform, games.release_date, games.category, games.cover')
            ->join('games', 'games.id = user_top_games.game_id')  // Jointure avec la table des jeux
            ->where('user_top_games.user_id', $userId)            // Filtrage par utilisateur
            ->orderBy('user_top_games.rank_position', 'ASC')      // Tri par position (1 à 5)
            ->get()->getResultArray();                            // Exécution et récupération des résultats
    }

    /**
     * Calcule et retourne les statistiques personnelles d'un utilisateur
     * 
     * Cette méthode effectue plusieurs requêtes pour calculer les statistiques
     * complètes d'un utilisateur : nombre de jeux possédés, temps de jeu total,
     * nombre de jeux terminés, etc. Elle retourne un tableau avec toutes ces
     * informations formatées pour l'affichage.
     * 
     * @param int $userId ID de l'utilisateur dont on veut calculer les statistiques
     * @return array Tableau contenant toutes les statistiques de l'utilisateur
     */
    public function getUserStats($userId)
    {
        // Connexion à la base de données
        $db = \Config\Database::connect();
        
        // ===== CALCUL DES STATISTIQUES =====
        
        // Nombre total de jeux possédés (dans la collection)
        $nbGames = $db->table('game_stats')->where('user_id', $userId)->countAllResults();
        
        // Nombre de jeux dans la wishlist (liste de souhaits)
        $nbWishlist = $db->table('wishlist')->where('user_id', $userId)->countAllResults();
        
        // Temps de jeu total (somme de tous les temps de jeu)
        $playtime = $db->table('game_stats')
            ->selectSum('play_time')                    // Somme de tous les temps de jeu
            ->where('user_id', $userId)
            ->get()->getRow('play_time') ?? 0;          // Récupération avec valeur par défaut 0
        
        // Formatage du temps de jeu en chaîne lisible (ex: "150h")
        $playtimeStr = ((int)$playtime) . 'h';
        
        // Nombre de jeux terminés (status = 'termine')
        $nbFinished = $db->table('game_stats')
            ->where('user_id', $userId)
            ->where('status', 'termine')
            ->countAllResults();
        
        // Nombre de jeux complétés à 100% (status = 'complete')
        $nbCompleted = $db->table('game_stats')
            ->where('user_id', $userId)
            ->where('status', 'complete')
            ->countAllResults();
        
        // ===== RETOUR DES STATISTIQUES =====
        
        // Retour d'un tableau avec toutes les statistiques calculées
        return [
            'nbGames' => $nbGames,           // Nombre total de jeux possédés
            'nbWishlist' => $nbWishlist,     // Nombre de jeux en wishlist
            'totalPlayTime' => $playtimeStr, // Temps de jeu total formaté
            'nbFinished' => $nbFinished,     // Nombre de jeux terminés
            'nbCompleted' => $nbCompleted    // Nombre de jeux complétés à 100%
        ];
    }
}

