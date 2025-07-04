<?php

namespace App\Models;
use CodeIgniter\Model;

/**
 * Modèle de gestion de la wishlist (liste de souhaits)
 * 
 * Ce modèle gère toutes les opérations de base de données liées à la wishlist
 * des utilisateurs. Il hérite de CodeIgniter\Model et fournit une interface pour :
 * - Ajouter des jeux à la wishlist d'un utilisateur
 * - Supprimer des jeux de la wishlist
 * - Récupérer les jeux de la wishlist avec filtres
 * - Obtenir des valeurs distinctes pour les filtres (plateformes, genres)
 * 
 * Le modèle utilise la table 'wishlist' qui fait le lien entre les utilisateurs
 * et les jeux qu'ils souhaitent acquérir. C'est une table de liaison simple
 * qui permet de gérer les listes de souhaits personnalisées.
 * 
 * Utilisé par :
 * - Le contrôleur WishlistController pour la gestion de la wishlist
 * - L'affichage des jeux souhaités avec filtres
 * - Les transferts vers la collection "Mes Jeux"
 * - Le calcul des statistiques utilisateur
 */
class WishlistModel extends Model
{
    /**
     * Nom de la table en base de données
     * 
     * Cette propriété définit le nom de la table SQL utilisée par ce modèle.
     * La table 'wishlist' est une table de liaison qui associe les utilisateurs
     * aux jeux qu'ils souhaitent acquérir.
     */
    protected $table = 'wishlist';
    
    /**
     * Clé primaire de la table
     * 
     * Cette propriété définit la colonne utilisée comme clé primaire.
     * Chaque entrée de la wishlist a un ID unique pour permettre
     * les opérations de modification et suppression.
     */
    protected $primaryKey = 'id';
    
    /**
     * Champs autorisés pour les opérations d'insertion et de mise à jour
     * 
     * Cette propriété définit la liste des colonnes de la table 'wishlist'
     * qui peuvent être modifiées via les méthodes insert() et update().
     * 
     * Champs inclus :
     * - user_id : ID de l'utilisateur propriétaire de la wishlist
     * - game_id : ID du jeu ajouté à la wishlist
     * 
     * Sécurité : Seuls ces champs peuvent être modifiés, protégeant
     * contre les attaques par injection de champs non autorisés.
     * 
     * Relations :
     * - user_id → table 'users' (utilisateur propriétaire)
     * - game_id → table 'games' (jeu souhaité)
     */
    protected $allowedFields = [
        'user_id',    // ID de l'utilisateur (clé étrangère vers users)
        'game_id'     // ID du jeu (clé étrangère vers games)
    ];
    
    /**
     * Désactivation de la gestion automatique des timestamps
     * 
     * Cette propriété désactive la gestion automatique des champs created_at
     * et updated_at par CodeIgniter. Pour la wishlist, on n'a pas besoin
     * de tracer les dates de création/modification.
     */
    protected $useTimestamps = false;
    
    /**
     * Champs de timestamp personnalisés (non utilisés)
     * 
     * Ces propriétés définissent les noms des champs de timestamp,
     * mais comme useTimestamps est à false, elles ne sont pas utilisées.
     * Elles sont définies pour être explicites sur la configuration.
     */
    protected $createdField = '';   // Champ de date de création (non utilisé)
    protected $updatedField = '';   // Champ de date de modification (non utilisé)
    protected $deletedField = '';   // Champ de date de suppression (non utilisé)
    
    /**
     * Désactivation de la suppression douce (soft delete)
     * 
     * Cette propriété désactive la suppression douce. Quand un jeu est
     * supprimé de la wishlist, il est définitivement retiré de la base
     * de données (pas de marquage comme "supprimé").
     */
    protected $useSoftDeletes = false;
    
    /**
     * Règles de validation pour les données
     * 
     * Cette propriété définit les règles de validation automatique
     * appliquées par CodeIgniter lors des opérations d'insertion
     * et de mise à jour.
     * 
     * Règles définies :
     * - user_id : obligatoire et doit être numérique
     * - game_id : obligatoire et doit être numérique
     * 
     * Ces règles garantissent l'intégrité des données et la cohérence
     * des relations entre utilisateurs et jeux.
     */
    protected $validationRules = [
        'user_id' => 'required|numeric',  // ID utilisateur obligatoire et numérique
        'game_id' => 'required|numeric'   // ID jeu obligatoire et numérique
    ];

    /**
     * Récupère les valeurs distinctes d'un champ pour un utilisateur
     * 
     * Cette méthode effectue une requête avec jointure pour récupérer
     * toutes les valeurs distinctes d'un champ spécifique (comme 'platform'
     * ou 'category') pour les jeux présents dans la wishlist d'un utilisateur.
     * 
     * Utilisée principalement pour :
     * - Générer les options de filtrage par plateforme
     * - Générer les options de filtrage par genre
     * - Éviter les doublons dans les menus déroulants
     * 
     * @param string $field Nom du champ à récupérer (ex: 'platform', 'category')
     * @param int $userId ID de l'utilisateur dont on veut les valeurs
     * @return array Tableau des valeurs distinctes pour le champ spécifié
     */
    public function getDistinctValues($field, $userId)
    {
        // Requête avec jointure pour récupérer les valeurs distinctes
        return $this->select("games.{$field}")                    // Sélection du champ spécifié depuis la table games
                   ->join('games', 'games.id = wishlist.game_id')  // Jointure avec la table des jeux
                   ->where('wishlist.user_id', $userId)            // Filtrage par utilisateur
                   ->distinct()                                    // Élimination des doublons
                   ->findAll();                                    // Exécution et récupération des résultats
    }
}
