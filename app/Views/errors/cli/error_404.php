<?php

/**
 * ===== PAGE D'ERREUR 404 POUR L'INTERFACE CLI =====
 * 
 * Ce fichier gère l'affichage des erreurs 404 dans l'interface en ligne de commande (CLI)
 * de CodeIgniter. Il est utilisé quand une route ou une ressource n'est pas trouvée
 * lors de l'exécution de commandes CLI.
 * 
 * Variables disponibles :
 * - $code : Le code d'erreur HTTP (404)
 * - $message : Le message d'erreur descriptif
 */

// Import de la classe CLI de CodeIgniter pour l'affichage formaté
use CodeIgniter\CLI\CLI;

// ===== AFFICHAGE DE L'ERREUR =====
// Affichage du code d'erreur avec formatage d'erreur (généralement en rouge)
CLI::error('ERROR: ' . $code);

// Affichage du message d'erreur descriptif
CLI::write($message);

// Ajout d'une ligne vide pour améliorer la lisibilité
CLI::newLine();

/**
 * ===== FONCTIONNEMENT =====
 * 
 * Cette page est automatiquement appelée par CodeIgniter quand :
 * 1. Une commande CLI n'existe pas
 * 2. Une route CLI n'est pas trouvée
 * 3. Une ressource demandée via CLI n'existe pas
 * 
 * L'affichage est optimisé pour les terminaux avec :
 * - Couleurs pour distinguer les types de messages
 * - Formatage approprié pour la lecture en console
 * - Messages concis et informatifs
 */
