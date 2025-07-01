# 🎮 DOSSIER DE PROJET
## Titre Professionnel : Développeur Web et Web Mobile

**Nom du candidat :** [Votre nom]  
**Promotion :** [Votre promotion]  
**Date de soutenance :** [Date]  
**Centre AFPA :** [Votre centre]

---

## 📄 SOMMAIRE

1. [Présentation du Projet](#1-présentation-du-projet)
2. [Contexte et Cahier des Charges](#2-contexte-et-cahier-des-charges)
3. [Analyse et Conception](#3-analyse-et-conception)
4. [Réalisation Technique](#4-réalisation-technique)
5. [Activité Type 1 : Développement Front-end](#5-activité-type-1--développement-front-end)
6. [Activité Type 2 : Développement Back-end](#6-activité-type-2--développement-back-end)
7. [Sécurité et Bonnes Pratiques](#7-sécurité-et-bonnes-pratiques)
8. [Tests et Validation](#8-tests-et-validation)
9. [Déploiement et Documentation](#9-déploiement-et-documentation)
10. [Difficultés Rencontrées et Solutions](#10-difficultés-rencontrées-et-solutions)
11. [Compétences Développées](#11-compétences-développées)
12. [Conclusion et Perspectives](#12-conclusion-et-perspectives)
13. [Annexes](#13-annexes)

---

## 1. PRÉSENTATION DU PROJET

### 1.1 Nom du projet
**Checkpoint** - Gestionnaire de Bibliothèque de Jeux Vidéo

### 1.2 Description générale
Checkpoint est une application web responsive permettant aux utilisateurs de gérer leur collection personnelle de jeux vidéo. L'application offre un tableau de bord personnalisé avec des statistiques de jeu, une gestion de wishlist, et l'intégration de l'API RAWG pour l'ajout automatique de jeux.

### 1.3 Objectifs du projet
- Créer une interface moderne et responsive
- Permettre la gestion complète d'une bibliothèque de jeux
- Offrir des statistiques personnalisées
- Intégrer une API externe (RAWG)
- Assurer la sécurité des données utilisateur
- Respecter les standards d'accessibilité (RGAA)

### 1.4 Utilisateurs cibles
- Joueurs souhaitant organiser leur collection
- Passionnés de jeux vidéo désirant suivre leurs statistiques
- Utilisateurs recherchant une interface simple et intuitive

---

## 2. CONTEXTE ET CAHIER DES CHARGES

### 2.1 Contexte
Dans le cadre de ma formation Développeur Web et Web Mobile à l'AFPA, j'ai choisi de développer une application de gestion de bibliothèque de jeux vidéo personnelle. Ce projet me permet de démontrer mes compétences en développement front-end et back-end.

### 2.2 Besoins fonctionnels
- **Authentification sécurisée** avec système de sessions
- **Gestion de profil utilisateur** avec upload d'image de profil
- **Ajout de jeux** manuel ou via API RAWG
- **Gestion des statuts** de jeux (En cours, Terminé, Complété, etc.)
- **Système de wishlist** pour les jeux désirés
- **Top 5 personnel** avec système de classement
- **Statistiques personnalisées** (temps de jeu, jeux terminés, etc.)
- **Calendrier des sorties** de jeux
- **Recherche et filtrage** des jeux

### 2.3 Besoins techniques
- **Responsive design** compatible mobile/desktop
- **Sécurité** : protection CSRF, validation des données, sessions sécurisées
- **Performance** : optimisation des requêtes, cache
- **Accessibilité** : conformité RGAA
- **Compatibilité** : navigateurs modernes
- **API REST** pour l'intégration externe

### 2.4 Contraintes
- Utilisation de CodeIgniter 4 (framework imposé)
- Hébergement sur serveur Apache/MySQL
- Respect des délais de développement
- Conformité aux standards W3C

---

## 3. ANALYSE ET CONCEPTION

### 3.1 Architecture générale
L'application suit le pattern **MVC (Modèle-Vue-Contrôleur)** de CodeIgniter 4 :
- **Modèles** : Gestion des données (UserModel, GameModel, etc.)
- **Vues** : Templates HTML/CSS/JS
- **Contrôleurs** : Logique métier (Auth, Home, MesJeux, etc.)

### 3.2 Diagramme de cas d'utilisation
```
[Utilisateur] 
    ↓
[S'inscrire/Se connecter] → [Gérer son profil]
    ↓
[Ajouter des jeux] → [Consulter sa bibliothèque]
    ↓
[Gérer sa wishlist] → [Voir ses statistiques]
    ↓
[Créer son Top 5] → [Consulter le calendrier]
```

### 3.3 Modèle de données
**Tables principales :**
- `users` : Données utilisateurs
- `games` : Informations sur les jeux
- `game_stats` : Statistiques de jeu par utilisateur
- `wishlist` : Liste de souhaits
- `user_top_games` : Top 5 personnel

### 3.4 Maquettage et ergonomie
- **Design responsive** avec approche mobile-first
- **Interface moderne** avec thème gaming (couleurs violet/cyan)
- **Navigation intuitive** avec menu burger sur mobile
- **Cartes de jeux** avec overlay d'informations
- **Tableau de bord** avec statistiques visuelles

---

## 4. RÉALISATION TECHNIQUE

### 4.1 Technologies utilisées

#### Front-end
- **HTML5** : Structure sémantique
- **CSS3** : Variables CSS, Grid, Flexbox, animations
- **JavaScript Vanilla** : Interactions utilisateur, AJAX
- **Police Orbitron** : Thème gaming/futuriste

#### Back-end
- **PHP 8.1+** : Langage de programmation
- **CodeIgniter 4** : Framework MVC
- **MySQL** : Base de données
- **API RAWG** : Données externes de jeux

#### Outils et environnement
- **Composer** : Gestionnaire de dépendances
- **Git** : Versionning
- **Apache** : Serveur web
- **VS Code** : Environnement de développement

### 4.2 Arborescence du projet
```
checkpoint/
├── app/
│   ├── Controllers/     # Contrôleurs MVC
│   ├── Models/         # Modèles de données
│   ├── Views/          # Templates et vues
│   ├── Services/       # Services (RawgService)
│   └── Config/         # Configuration
├── public/
│   ├── css/           # Feuilles de style
│   ├── js/            # Scripts JavaScript
│   └── images/        # Ressources graphiques
└── writable/          # Logs et fichiers temporaires
```

---

## 5. ACTIVITÉ TYPE 1 : DÉVELOPPEMENT FRONT-END

### 5.1 Compétence : Installer et configurer son environnement de travail

**Mise en œuvre :**
- Installation de XAMPP (Apache, MySQL, PHP)
- Configuration de VS Code avec extensions PHP
- Installation de Composer pour CodeIgniter 4
- Configuration Git pour le versionning
- Setup de l'environnement de développement

**Fichiers concernés :**
- `.env` : Configuration environnement
- `composer.json` : Dépendances PHP

### 5.2 Compétence : Maquetter des interfaces utilisateur

**Mise en œuvre :**
- Analyse des besoins utilisateur
- Création de wireframes pour les principales pages
- Définition de la charte graphique (thème gaming)
- Respect des principes d'accessibilité RGAA
- Adaptation responsive mobile/desktop

**Éléments de maquettage :**
- Page d'accueil avec dashboard
- Interface de gestion de jeux
- Formulaires d'authentification
- Navigation responsive

### 5.3 Compétence : Réaliser des interfaces utilisateur statiques

**Technologies utilisées :**
```css
/* Variables CSS pour la cohérence */
:root {
    --primary-color: #7F39FB;
    --secondary-color: #BB86FC;
    --accent-color: #00E5FF;
    --text-color: #E0F7FA;
    --background-dark: rgba(31, 27, 46, 0.9);
}
```

**Réalisations :**
- Structure HTML5 sémantique
- CSS Grid et Flexbox pour le layout
- Système de cartes responsive
- Navigation avec menu burger
- Formulaires stylisés

**Fichiers principaux :**
- `public/css/style.css` : 1327 lignes de CSS optimisé
- `app/Views/layouts/default.php` : Template principal
- `app/Views/layouts/navbar.php` : Navigation responsive

### 5.4 Compétence : Développer la partie dynamique des interfaces

**JavaScript implémenté :**
```javascript
// Recherche en temps réel avec AJAX
function searchGames(query) {
    if (query.length >= 2) {
        fetch(`/mes-jeux/search?q=${encodeURIComponent(query)}`)
            .then(response => response.json())
            .then(data => displaySuggestions(data));
    }
}
```

**Fonctionnalités dynamiques :**
- Recherche AJAX avec suggestions
- Modales interactives (ajout jeux, Top 5)
- Filtrage dynamique des jeux
- Menu burger responsive
- Validation côté client des formulaires
- Gestion des événements utilisateur

**Fichiers concernés :**
- `public/js/script.js` : Scripts principaux
- Intégration AJAX dans les contrôleurs

---

## 6. ACTIVITÉ TYPE 2 : DÉVELOPPEMENT BACK-END

### 6.1 Compétence : Mettre en place une base de données relationnelle

**Schéma de base de données :**
```sql
-- Table utilisateurs
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    profile_picture VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Table jeux
CREATE TABLE games (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    platform VARCHAR(100),
    release_date DATE,
    category VARCHAR(100),
    cover VARCHAR(500),
    developer VARCHAR(255),
    publisher VARCHAR(255),
    rawg_id INT UNIQUE
);
```

**Fichier de base :**
- `app/Database/checkpoint.sql` : Structure complète

### 6.2 Compétence : Développer des composants d'accès aux données

**Modèles implémentés :**
```php
class GameModel extends Model
{
    protected $table = 'games';
    protected $allowedFields = ['name', 'platform', 'release_date', 
                               'category', 'cover', 'developer', 
                               'publisher', 'rawg_id'];
}
```

**Sécurisation des accès :**
- Utilisation du Query Builder CodeIgniter
- Requêtes préparées automatiques
- Validation des données d'entrée
- Gestion des transactions

**Fichiers concernés :**
- `app/Models/GameModel.php`
- `app/Models/UserModel.php`
- `app/Models/WishlistModel.php`
- `app/Models/GameStatsModel.php`

### 6.3 Compétence : Développer des composants métier côté serveur

**Contrôleurs développés :**

```php
// Exemple : Contrôleur MesJeux
class MesJeux extends BaseController
{
    public function index()
    {
        // Logique métier pour afficher les jeux
        $userId = session()->get('user_id');
        $games = $this->getGamesWithStats($userId);
        return view('mes-jeux/index', ['games' => $games]);
    }
}
```

**Services développés :**
```php
// Service d'intégration API RAWG
class RawgService
{
    public function searchGames($query)
    {
        $apiKey = env('RAWG_API_KEY');
        $url = "https://api.rawg.io/api/games?key={$apiKey}&search={$query}";
        return $this->makeApiRequest($url);
    }
}
```

**Fonctionnalités métier :**
- Authentification avec sessions sécurisées
- Gestion des statistiques de jeu
- Calcul du temps de jeu total
- Système de Top 5 avec classement
- Intégration API externe (RAWG)

**Fichiers principaux :**
- `app/Controllers/Auth.php` : Authentification
- `app/Controllers/MesJeux.php` : Gestion des jeux
- `app/Controllers/WishlistController.php` : Wishlist
- `app/Services/RawgService.php` : API externe

### 6.4 Compétence : Documenter le déploiement

**Documentation créée :**
- `README.md` : Guide d'installation et utilisation (410 lignes)
- Instructions de déploiement Apache/Nginx
- Configuration des variables d'environnement
- Guide de sauvegarde/restauration base de données

**Éléments de déploiement :**
- Configuration serveur web
- Variables d'environnement de production
- Scripts de déploiement
- Documentation technique complète

---

## 7. SÉCURITÉ ET BONNES PRATIQUES

### 7.1 Sécurité implémentée

**Protection CSRF :**
```php
// Configuration dans Config/Security.php
public $csrfProtection = true;
public $tokenRandomize = true;
```

**Authentification sécurisée :**
```php
// Hachage des mots de passe
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

// Validation des sessions
if (!session()->get('user_id')) {
    return redirect()->to('/auth/login');
}
```

**Validation des données :**
```php
// Règles de validation
$validation = service('validation');
$validation->setRules([
    'email' => 'required|valid_email|is_unique[users.email]',
    'password' => 'required|min_length[8]'
]);
```

### 7.2 Bonnes pratiques appliquées
- Échappement automatique des sorties (XSS)
- Requêtes préparées (injection SQL)
- Validation côté serveur et client
- Sessions sécurisées avec HttpOnly
- Upload sécurisé d'images de profil
- Protection des dossiers sensibles

---

## 8. TESTS ET VALIDATION

### 8.1 Tests réalisés
- **Tests fonctionnels** : Toutes les fonctionnalités principales
- **Tests de sécurité** : Tentatives d'injection, XSS
- **Tests de compatibilité** : Chrome, Firefox, Safari, Edge
- **Tests responsive** : Mobile, tablette, desktop
- **Tests de performance** : Temps de chargement

### 8.2 Validation W3C
- HTML5 valide selon les standards W3C
- CSS3 conforme aux spécifications
- Accessibilité testée avec outils RGAA

### 8.3 Outils utilisés
- DevTools des navigateurs
- Lighthouse pour les performances
- Validator W3C pour HTML/CSS

---

## 9. DÉPLOIEMENT ET DOCUMENTATION

### 9.1 Documentation utilisateur
- README complet avec installation
- Guide d'utilisation des fonctionnalités
- FAQ pour les problèmes courants
- Documentation API RAWG

### 9.2 Documentation technique
- Commentaires dans le code
- Documentation des API
- Schéma de base de données
- Architecture du projet

---

## 10. DIFFICULTÉS RENCONTRÉES ET SOLUTIONS

### 10.1 Intégration API RAWG
**Problème :** Limitation du taux de requêtes de l'API
**Solution :** Mise en place d'un système de cache et limitation des appels

### 10.2 Responsive design complexe
**Problème :** Affichage des cartes de jeux sur différentes tailles d'écran
**Solution :** Utilisation de CSS Grid avec `repeat(auto-fill, minmax())`

### 10.3 Gestion des sessions
**Problème :** Persistance des données utilisateur
**Solution :** Configuration optimisée des sessions CodeIgniter

### 10.4 Upload d'images sécurisé
**Problème :** Validation et redimensionnement des images de profil
**Solution :** Validation MIME type et traitement côté serveur

---

## 11. COMPÉTENCES DÉVELOPPÉES

### 11.1 Compétences techniques front-end
✅ **Maquettage** : Création d'interfaces responsive  
✅ **HTML5/CSS3** : Structure sémantique, animations CSS  
✅ **JavaScript** : AJAX, manipulation DOM, événements  
✅ **Responsive design** : Mobile-first, Grid, Flexbox  
✅ **Accessibilité** : Respect des standards RGAA  

### 11.2 Compétences techniques back-end
✅ **PHP/CodeIgniter 4** : Architecture MVC, routage  
✅ **Base de données** : Conception, optimisation MySQL  
✅ **Sécurité** : CSRF, XSS, validation, authentification  
✅ **API REST** : Intégration services externes  
✅ **Tests** : Tests unitaires et de sécurité  

### 11.3 Compétences transversales
✅ **Gestion de projet** : Planification, respect des délais  
✅ **Documentation** : Technique et utilisateur  
✅ **Résolution de problèmes** : Débogage, optimisation  
✅ **Veille technologique** : Suivi des standards web  

---

## 12. CONCLUSION ET PERSPECTIVES

### 12.1 Bilan du projet
Le projet Checkpoint m'a permis de mettre en pratique l'ensemble des compétences du titre DWWM. J'ai pu développer une application complète respectant les standards de sécurité et d'accessibilité.

### 12.2 Perspectives d'évolution
- **Application mobile** : Version React Native/Flutter
- **API publique** : Partage de collections entre utilisateurs
- **Intégrations** : Steam, PlayStation, Xbox
- **Intelligence artificielle** : Recommandations personnalisées
- **Mode hors ligne** : Progressive Web App (PWA)

### 12.3 Apports personnels
Ce projet a renforcé ma compréhension de :
- L'architecture MVC et les bonnes pratiques
- La sécurité web et la protection des données
- L'importance de l'expérience utilisateur
- L'intégration d'APIs externes
- La documentation technique

---

## 13. ANNEXES

### Annexe A : Captures d'écran
[Inclure des captures d'écran de l'application]

### Annexe B : Code source significatif
[Extraits de code important commentés]

### Annexe C : Schéma de base de données
[Diagramme relationnel détaillé]

### Annexe D : Maquettes et wireframes
[Maquettes initiales et finales]

### Annexe E : Tests et validation
[Résultats des tests W3C, Lighthouse, etc.]

---

**Date de rédaction :** [Date]  
**Nombre de pages :** [Nombre]  
**Mots-clés :** Développement web, PHP, CodeIgniter, MySQL, JavaScript, CSS3, Responsive design, API REST, Sécurité web 