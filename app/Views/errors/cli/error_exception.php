<?php

/**
 * ===== PAGE D'ERREUR EXCEPTION POUR L'INTERFACE CLI =====
 * 
 * Ce fichier gère l'affichage détaillé des exceptions dans l'interface en ligne de commande (CLI)
 * de CodeIgniter. Il affiche la chaîne complète d'exceptions et la backtrace pour le débogage.
 * 
 * Variables disponibles :
 * - $exception : L'objet Exception principal
 * - $message : Le message d'erreur principal
 */

// Import de la classe CLI de CodeIgniter pour l'affichage formaté
use CodeIgniter\CLI\CLI;

// ===== AFFICHAGE DE L'EXCEPTION PRINCIPALE =====
// Affichage du nom de la classe d'exception avec formatage (fond rouge, texte gris clair)
CLI::write('[' . $exception::class . ']', 'light_gray', 'red');

// Affichage du message d'erreur principal
CLI::write($message);

// Affichage du fichier et de la ligne où l'exception s'est produite (en vert)
CLI::write('at ' . CLI::color(clean_path($exception->getFile()) . ':' . $exception->getLine(), 'green'));

// Ligne vide pour séparer les sections
CLI::newLine();

// ===== GESTION DE LA CHAÎNE D'EXCEPTIONS =====
// Parcours de toutes les exceptions précédentes (causes en cascade)
$last = $exception;

while ($prevException = $last->getPrevious()) {
    $last = $prevException;

    // Indicateur de cause
    CLI::write('  Caused by:');
    
    // Nom de la classe de l'exception précédente (en rouge)
    CLI::write('  [' . $prevException::class . ']', 'red');
    
    // Message de l'exception précédente
    CLI::write('  ' . $prevException->getMessage());
    
    // Fichier et ligne de l'exception précédente (en vert)
    CLI::write('  at ' . CLI::color(clean_path($prevException->getFile()) . ':' . $prevException->getLine(), 'green'));
    
    // Ligne vide entre chaque exception
    CLI::newLine();
}

// ===== AFFICHAGE DE LA BACKTRACE =====
// Affichage de la pile d'appels si le mode debug est activé
if (defined('SHOW_DEBUG_BACKTRACE') && SHOW_DEBUG_BACKTRACE) {
    // Récupération de la backtrace de la dernière exception
    $backtraces = $last->getTrace();

    if ($backtraces) {
        // Titre de la section backtrace (en vert)
        CLI::write('Backtrace:', 'green');
    }

    // ===== PARCOURS DE CHAQUE ÉTAPE DE LA BACKTRACE =====
    foreach ($backtraces as $i => $error) {
        // Définition des espacements pour l'alignement
        $padFile  = '    '; // 4 espaces pour l'alignement du fichier
        $padClass = '       '; // 7 espaces pour l'alignement de la classe
        $c        = str_pad($i + 1, 3, ' ', STR_PAD_LEFT); // Numéro d'étape aligné à droite

        // ===== AFFICHAGE DU FICHIER ET DE LA LIGNE =====
        if (isset($error['file'])) {
            // Chemin du fichier et numéro de ligne (en jaune)
            $filepath = clean_path($error['file']) . ':' . $error['line'];
            CLI::write($c . $padFile . CLI::color($filepath, 'yellow'));
        } else {
            // Fonction interne (en jaune)
            CLI::write($c . $padFile . CLI::color('[internal function]', 'yellow'));
        }

        // ===== CONSTRUCTION DE LA SIGNATURE DE LA FONCTION =====
        $function = '';

        // Gestion des méthodes de classe
        if (isset($error['class'])) {
            // Détermination du type d'appel (-> pour méthode d'instance, :: pour méthode statique)
            $type = ($error['type'] === '->') ? '()' . $error['type'] : $error['type'];
            $function .= $padClass . $error['class'] . $type . $error['function'];
        } 
        // Gestion des fonctions globales
        elseif (! isset($error['class']) && isset($error['function'])) {
            $function .= $padClass . $error['function'];
        }

        // ===== FORMATAGE DES ARGUMENTS =====
        // Conversion des arguments en chaînes lisibles
        $args = implode(', ', array_map(static fn ($value): string => match (true) {
            // Objets : affichage du nom de la classe
            is_object($value) => 'Object(' . $value::class . ')',
            // Tableaux : affichage simplifié
            is_array($value)  => $value !== [] ? '[...]' : '[]',
            // Valeur null : affichage en minuscules
            $value === null   => 'null',
            // Autres types : affichage de la valeur
            default           => var_export($value, true),
        }, array_values($error['args'] ?? [])));

        // Ajout des arguments à la signature de la fonction
        $function .= '(' . $args . ')';

        // Affichage de la signature complète de la fonction
        CLI::write($function);
        
        // Ligne vide entre chaque étape de la backtrace
        CLI::newLine();
    }
}

/**
 * ===== FONCTIONNEMENT DÉTAILLÉ =====
 * 
 * Cette page affiche une information complète sur les exceptions :
 * 
 * 1. EXCEPTION PRINCIPALE :
 *    - Nom de la classe d'exception
 *    - Message d'erreur
 *    - Fichier et ligne d'origine
 * 
 * 2. CHAÎNE D'EXCEPTIONS :
 *    - Toutes les exceptions précédentes (causes en cascade)
 *    - Chaque exception avec son contexte
 * 
 * 3. BACKTRACE (si activée) :
 *    - Pile d'appels complète
 *    - Fichiers et lignes de chaque étape
 *    - Signatures des fonctions avec arguments
 *    - Formatage coloré pour la lisibilité
 * 
 * UTILISATION :
 * - Débogage en développement
 * - Diagnostic des erreurs en production (si activé)
 * - Compréhension de la chaîne d'erreurs
 */
