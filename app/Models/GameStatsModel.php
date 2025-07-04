<?php

namespace App\Models;
use CodeIgniter\Model;

/**
 * Modèle de gestion des statistiques de jeux des utilisateurs
 * 
 * Ce modèle gère toutes les opérations de base de données liées aux statistiques
 * personnelles des jeux pour chaque utilisateur. Il hérite de CodeIgniter\Model
 * et fournit une interface pour :
 * - Suivre la progression des utilisateurs dans leurs jeux
 * - Enregistrer le temps de jeu pour chaque utilisateur/jeu
 * - Gérer les statuts de progression (en cours, terminé, abandonné, etc.)
 * - Stocker les notes personnelles des utilisateurs sur leurs jeux
 * 
 * Le modèle utilise la table 'game_stats' qui fait le lien entre les utilisateurs
 * et leurs jeux, avec des informations de progression personnalisées.
 * 
 * Utilisé par :
 * - Les contrôleurs pour gérer la collection personnelle des utilisateurs
 * - L'affichage des statistiques sur la page d'accueil
 * - Le calcul des statistiques globales des utilisateurs
 * - Les transferts depuis la wishlist vers la collection
 */
class GameStatsModel extends Model
{
    /**
     * Nom de la table en base de données
     * 
     * Cette propriété définit le nom de la table SQL utilisée par ce modèle.
     * La table 'game_stats' contient les statistiques personnelles de chaque
     * utilisateur pour chaque jeu de sa collection.
     */
    protected $table = 'game_stats';
    
    /**
     * Champs autorisés pour les opérations d'insertion et de mise à jour
     * 
     * Cette propriété définit la liste des colonnes de la table 'game_stats'
     * qui peuvent être modifiées via les méthodes insert() et update().
     * 
     * Champs inclus :
     * - user_id : ID de l'utilisateur propriétaire des statistiques
     * - game_id : ID du jeu concerné par ces statistiques
     * - play_time : Temps de jeu en heures pour ce jeu
     * - status : Statut de progression (en cours, terminé, abandonné, etc.)
     * - notes : Notes personnelles de l'utilisateur sur ce jeu
     * - created_at : Date de création de l'entrée (géré automatiquement)
     * - updated_at : Date de dernière modification (géré automatiquement)
     * 
     * Sécurité : Seuls ces champs peuvent être modifiés, protégeant
     * contre les attaques par injection de champs non autorisés.
     * 
     * Relations :
     * - user_id → table 'users' (utilisateur propriétaire)
     * - game_id → table 'games' (jeu concerné)
     */
    protected $allowedFields = [
        'user_id',        // ID de l'utilisateur (clé étrangère vers users)
        'game_id',        // ID du jeu (clé étrangère vers games)
        'play_time',      // Temps de jeu en heures
        'status',         // Statut de progression (en cours, terminé, abandonné, etc.)
        'notes',          // Notes personnelles de l'utilisateur
        'created_at',     // Date de création (géré automatiquement par CodeIgniter)
        'updated_at'      // Date de modification (géré automatiquement par CodeIgniter)
    ];
}