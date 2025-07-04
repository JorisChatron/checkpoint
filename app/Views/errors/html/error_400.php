<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <!-- ===== TITRE DE LA PAGE ===== -->
    <!-- Utilisation de la fonction lang() pour l'internationalisation -->
    <title><?= lang('Errors.badRequest') ?></title>

    <!-- ===== STYLES CSS INTÉGRÉS ===== -->
    <!-- Styles définis directement dans la page pour garantir le chargement -->
    <style>
        /* ===== LOGO DE FOND ===== */
        /* Logo CodeIgniter en filigrane pour l'identité visuelle */
        div.logo {
            height: 200px;                    /* Hauteur fixe du logo */
            width: 155px;                     /* Largeur fixe du logo */
            display: inline-block;            /* Affichage en ligne */
            opacity: 0.08;                    /* Très transparent (filigrane) */
            position: absolute;               /* Positionnement absolu */
            top: 2rem;                        /* Distance du haut */
            left: 50%;                        /* Centrage horizontal */
            margin-left: -73px;               /* Ajustement pour centrage parfait */
        }
        
        /* ===== STYLES DE BASE ===== */
        /* Configuration générale du corps de la page */
        body {
            height: 100%;                     /* Hauteur complète de la fenêtre */
            background: #fafafa;              /* Fond gris très clair */
            font-family: "Helvetica Neue", Helvetica, Arial, sans-serif; /* Police moderne */
            color: #777;                      /* Couleur de texte grise */
            font-weight: 300;                 /* Police légère */
        }
        
        /* ===== TITRE PRINCIPAL ===== */
        /* Titre "400" avec style distinctif */
        h1 {
            font-weight: lighter;             /* Police très légère */
            letter-spacing: normal;           /* Espacement normal des lettres */
            font-size: 3rem;                  /* Taille importante */
            margin-top: 0;                    /* Pas de marge supérieure */
            margin-bottom: 0;                 /* Pas de marge inférieure */
            color: #222;                      /* Couleur sombre */
        }
        
        /* ===== CONTENEUR PRINCIPAL ===== */
        /* Boîte centrale contenant le message d'erreur */
        .wrap {
            max-width: 1024px;                /* Largeur maximale pour les grands écrans */
            margin: 5rem auto;                /* Marge verticale et centrage horizontal */
            padding: 2rem;                    /* Padding interne */
            background: #fff;                 /* Fond blanc */
            text-align: center;               /* Centrage du contenu */
            border: 1px solid #efefef;        /* Bordure subtile */
            border-radius: 0.5rem;            /* Coins arrondis */
            position: relative;               /* Position relative pour le logo */
        }
        
        /* ===== BLOCS DE CODE ===== */
        /* Style pour les blocs de code (si présents) */
        pre {
            white-space: normal;              /* Espaces normaux (pas de préservation) */
            margin-top: 1.5rem;               /* Marge supérieure */
        }
        
        /* Style pour le code inline */
        code {
            background: #fafafa;              /* Fond gris clair */
            border: 1px solid #efefef;        /* Bordure subtile */
            padding: 0.5rem 1rem;             /* Padding interne */
            border-radius: 5px;               /* Coins arrondis */
            display: block;                   /* Affichage en bloc */
        }
        
        /* ===== PARAGRAPHES ===== */
        /* Espacement des paragraphes */
        p {
            margin-top: 1.5rem;               /* Marge supérieure uniforme */
        }
        
        /* ===== PIED DE PAGE ===== */
        /* Section de pied de page avec informations supplémentaires */
        .footer {
            margin-top: 2rem;                 /* Marge supérieure importante */
            border-top: 1px solid #efefef;    /* Ligne de séparation */
            padding: 1em 2em 0 2em;           /* Padding interne */
            font-size: 85%;                   /* Taille de police plus petite */
            color: #999;                      /* Couleur grise claire */
        }
        
        /* ===== LIENS ===== */
        /* Style uniforme pour tous les états des liens */
        a:active,
        a:link,
        a:visited {
            color: #dd4814;                   /* Couleur orange CodeIgniter */
        }
    </style>
</head>
<body>
<!-- ===== CONTENEUR PRINCIPAL ===== -->
<div class="wrap">
    <!-- ===== CODE D'ERREUR HTTP ===== -->
    <!-- Affichage du code d'erreur 400 (Bad Request) -->
    <h1>400</h1>

    <!-- ===== MESSAGE D'ERREUR ===== -->
    <!-- Message adaptatif selon l'environnement -->
    <p>
        <?php if (ENVIRONMENT !== 'production') : ?>
            <!-- ===== ENVIRONNEMENT DE DÉVELOPPEMENT ===== -->
            <!-- Affichage du message d'erreur détaillé pour le débogage -->
            <?= nl2br(esc($message)) ?>
            <!-- 
            nl2br() : Convertit les retours à la ligne en <br>
            esc() : Échappe le HTML pour la sécurité
            $message : Variable contenant le message d'erreur détaillé
            -->
        <?php else : ?>
            <!-- ===== ENVIRONNEMENT DE PRODUCTION ===== -->
            <!-- Message générique pour les utilisateurs finaux -->
            <?= lang('Errors.sorryBadRequest') ?>
            <!-- 
            lang() : Fonction d'internationalisation
            'Errors.sorryBadRequest' : Clé de traduction pour le message générique
            -->
        <?php endif; ?>
    </p>
</div>
</body>
</html>

<?php
/**
 * ===== PAGE D'ERREUR HTTP 400 (BAD REQUEST) =====
 * 
 * Cette page affiche une erreur HTTP 400 (Bad Request) avec :
 * 
 * FONCTIONNALITÉS :
 * - Design responsive et moderne
 * - Gestion des environnements (dev vs production)
 * - Internationalisation des messages
 * - Sécurité (échappement HTML)
 * - Identité visuelle CodeIgniter
 * 
 * ERREUR 400 (BAD REQUEST) :
 * - Le serveur ne peut pas traiter la requête
 * - Syntaxe de la requête incorrecte
 * - Paramètres manquants ou invalides
 * - URL malformée
 * 
 * GESTION DES ENVIRONNEMENTS :
 * 
 * DÉVELOPPEMENT :
 * - Affichage du message d'erreur détaillé
 * - Informations techniques pour le débogage
 * - Utilisation de nl2br() pour préserver le formatage
 * - Échappement HTML avec esc() pour la sécurité
 * 
 * PRODUCTION :
 * - Message générique et convivial
 * - Pas d'informations techniques sensibles
 * - Utilisation de la fonction lang() pour l'internationalisation
 * - Expérience utilisateur optimisée
 * 
 * SÉCURITÉ :
 * - Échappement HTML avec esc()
 * - Pas d'affichage d'informations sensibles en production
 * - Validation des variables avant affichage
 * 
 * DESIGN :
 * - Style minimaliste et professionnel
 * - Couleurs neutres et lisibles
 * - Responsive design
 * - Logo CodeIgniter en filigrane
 * 
 * UTILISATION :
 * - Erreurs de syntaxe dans les requêtes
 * - Paramètres manquants dans les formulaires
 * - URLs malformées
 * - Requêtes invalides vers l'API
 */
?>
