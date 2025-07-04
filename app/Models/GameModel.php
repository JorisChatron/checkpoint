<?php

namespace App\Models;
use CodeIgniter\Model;

/**
 * Modèle de gestion des jeux vidéo
 * 
 * Ce modèle gère toutes les opérations de base de données liées aux jeux vidéo.
 * Il hérite de CodeIgniter\Model et fournit une interface simple pour :
 * - Créer de nouveaux jeux en base de données
 * - Récupérer des informations sur les jeux existants
 * - Mettre à jour les données des jeux
 * - Supprimer des jeux (si nécessaire)
 * 
 * Le modèle utilise la table 'games' qui contient toutes les informations
 * de base des jeux : nom, plateforme, date de sortie, genre, couverture, etc.
 * 
 * Utilisé par :
 * - Les contrôleurs pour gérer les jeux de la collection utilisateur
 * - Le service RAWG pour créer/récupérer des jeux depuis l'API
 * - Les autres modèles pour les jointures et relations
 */
class GameModel extends Model
{
    /**
     * Nom de la table en base de données
     * 
     * Cette propriété définit le nom de la table SQL utilisée par ce modèle.
     * Toutes les requêtes seront exécutées sur la table 'games'.
     */
    protected $table = 'games';
    
    /**
     * Champs autorisés pour les opérations d'insertion et de mise à jour
     * 
     * Cette propriété définit la liste des colonnes de la table 'games'
     * qui peuvent être modifiées via les méthodes insert() et update().
     * 
     * Champs inclus :
     * - name : Nom du jeu vidéo
     * - platform : Plateforme de jeu (PC, PS5, Xbox, etc.)
     * - release_date : Date de sortie du jeu
     * - category : Genre/catégorie du jeu (Action, RPG, etc.)
     * - cover : URL de l'image de couverture du jeu
     * - developer : Nom du développeur du jeu
     * - publisher : Nom de l'éditeur du jeu
     * - rawg_id : ID du jeu dans l'API RAWG (pour la synchronisation)
     * 
     * Sécurité : Seuls ces champs peuvent être modifiés, protégeant
     * contre les attaques par injection de champs non autorisés.
     */
    protected $allowedFields = [
        'name',           // Nom du jeu (obligatoire)
        'platform',       // Plateforme de jeu (obligatoire)
        'release_date',   // Date de sortie (format YYYY-MM-DD)
        'category',       // Genre/catégorie du jeu
        'cover',          // URL de l'image de couverture
        'developer',      // Nom du développeur
        'publisher',      // Nom de l'éditeur
        'rawg_id'         // ID RAWG pour synchronisation API
    ];
}
