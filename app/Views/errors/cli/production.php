<?php

/**
 * ===== PAGE D'ERREUR CLI EN PRODUCTION =====
 * 
 * Ce fichier gère l'affichage des erreurs dans l'interface CLI en environnement de production.
 * Contrairement aux pages d'erreur web qui masquent les détails en production, l'interface CLI
 * conserve l'affichage complet des erreurs pour faciliter le diagnostic et la maintenance.
 * 
 * FONCTIONNEMENT :
 * - En CLI, les erreurs restent visibles même en production
 * - Réutilisation du template d'exception pour un affichage complet
 * - Pas de masquage des informations sensibles (contrairement au web)
 * 
 * RAISON :
 * L'interface CLI est principalement utilisée par les développeurs et administrateurs
 * qui ont besoin d'informations détaillées pour diagnostiquer et résoudre les problèmes,
 * même en environnement de production.
 */

// ===== INCLUSION DU TEMPLATE D'EXCEPTION =====
// En CLI, nous voulons toujours voir les erreurs en production
// donc nous utilisons simplement le template d'exception complet
include __DIR__ . '/error_exception.php';

/**
 * ===== DIFFÉRENCES AVEC L'ENVIRONNEMENT WEB =====
 * 
 * WEB PRODUCTION :
 * - Masquage des détails d'erreur
 * - Messages génériques pour l'utilisateur
 * - Pas d'affichage de backtrace
 * - Sécurité des informations sensibles
 * 
 * CLI PRODUCTION :
 * - Affichage complet des erreurs
 * - Backtrace détaillée (si activée)
 * - Informations techniques complètes
 * - Diagnostic facilité pour les administrateurs
 * 
 * ===== UTILISATION TYPIQUE =====
 * 
 * Cette page est utilisée quand :
 * - Une commande CLI échoue en production
 * - Un script de maintenance rencontre une erreur
 * - Un cron job ou tâche planifiée génère une exception
 * - Diagnostic d'erreurs système en production
 * 
 * ===== SÉCURITÉ =====
 * 
 * L'affichage complet des erreurs en CLI production est sécurisé car :
 * - L'interface CLI n'est accessible qu'aux administrateurs
 * - Pas d'exposition aux utilisateurs finaux
 * - Nécessaire pour la maintenance et le diagnostic
 * - Contrôlé par les permissions système
 */
