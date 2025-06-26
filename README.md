# 🎮 Checkpoint - Gestionnaire de Bibliothèque de Jeux

[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](https://opensource.org/licenses/MIT)
[![PHP Version](https://img.shields.io/badge/PHP-8.1%2B-blue.svg)](https://php.net)
[![CodeIgniter](https://img.shields.io/badge/CodeIgniter-4.x-red.svg)](https://codeigniter.com)
[![MySQL](https://img.shields.io/badge/MySQL-8.0%2B-orange.svg)](https://mysql.com)

Une application web moderne pour gérer votre collection de jeux vidéo, développée avec CodeIgniter 4.

## 📸 Aperçu

> **Note :** Ajoutez ici des captures d'écran de votre application une fois déployée
> 
> - 🏠 Page d'accueil avec statistiques
> - 🎮 Interface de gestion des jeux
> - 📊 Tableau de bord utilisateur
> - 💝 Système de wishlist

## 📋 Description

Checkpoint est une application de gestion de bibliothèque de jeux personnelle qui vous permet de :
- Organiser votre collection de jeux
- Suivre votre progression et temps de jeu
- Gérer votre liste de souhaits (wishlist)
- Consulter vos statistiques de jeu
- Créer votre top 5 personnel

## ✨ Fonctionnalités

### 🔐 Authentification
- Inscription et connexion sécurisées
- Système "Se souvenir de moi"
- Gestion de profil utilisateur avec photo

### 📚 Gestion de bibliothèque
- Ajout manuel ou via l'API RAWG
- Filtrage par plateforme, statut et genre
- Suivi du temps de jeu et progression
- Notes personnalisées sur les jeux
- Statuts : En cours, Terminé, Complété à 100%, etc.

### 📊 Statistiques personnelles
- Nombre de jeux possédés
- Temps de jeu total
- Jeux terminés/complétés
- Tableau de bord personnalisé

### 🎯 Fonctionnalités avancées
- Liste de souhaits (wishlist)
- Top 5 personnel
- Calendrier des sorties
- Interface responsive et moderne

## 🛠️ Technologies utilisées

- **Framework** : CodeIgniter 4
- **Langage** : PHP 8.1+
- **Base de données** : MySQL/MariaDB
- **Frontend** : HTML5, CSS3 personnalisé, JavaScript Vanilla
- **Design** : CSS Grid, Flexbox, Variables CSS
- **API externe** : RAWG Video Games Database API
- **Typographie** : Google Fonts (Orbitron)

## 📦 Installation

### Prérequis
- PHP 8.1 ou supérieur
- MySQL/MariaDB
- Composer
- Extensions PHP : intl, mbstring, json, mysqlnd, libcurl

### Étapes d'installation

1. **Cloner le repository**
```bash
git clone https://github.com/votre-username/checkpoint.git
cd checkpoint
```

2. **Installer les dépendances**
```bash
composer install
```

3. **Configuration de l'environnement**
```bash
cp env .env
```

4. **Configurer la base de données**
Éditez le fichier `.env` :
```env
database.default.hostname = localhost
database.default.database = checkpoint
database.default.username = votre_utilisateur
database.default.password = votre_mot_de_passe
```

5. **Importer la base de données**
```bash
mysql -u votre_utilisateur -p checkpoint < app/Database/checkpoint.sql
```

6. **Configurer les permissions**
```bash
chmod -R 755 writable/
```

7. **Démarrer le serveur de développement**
```bash
php spark serve
```

L'application sera accessible sur `http://localhost:8080`

## ⚙️ Configuration

### Variables d'environnement complètes

Créez un fichier `.env` à partir du modèle :
```bash
cp env .env
```

Configuration recommandée pour `.env` :

```env
#--------------------------------------------------------------------
# ENVIRONMENT
#--------------------------------------------------------------------
CI_ENVIRONMENT = development

#--------------------------------------------------------------------
# APP
#--------------------------------------------------------------------
app.baseURL = 'http://localhost:8080/'
app.appTimezone = 'Europe/Paris'

#--------------------------------------------------------------------
# DATABASE
#--------------------------------------------------------------------
database.default.hostname = localhost
database.default.database = checkpoint
database.default.username = votre_utilisateur
database.default.password = votre_mot_de_passe
database.default.DBDriver = MySQLi
database.default.port = 3306

#--------------------------------------------------------------------
# RAWG API (Optionnel)
#--------------------------------------------------------------------
RAWG_API_KEY = votre_cle_api_rawg

#--------------------------------------------------------------------
# SECURITY
#--------------------------------------------------------------------
security.csrfProtection = true
security.tokenRandomize = true
security.tokenName = 'csrf_token_name'
security.headerName = 'X-CSRF-TOKEN'
security.cookieName = 'csrf_cookie_name'

#--------------------------------------------------------------------
# SESSION
#--------------------------------------------------------------------
session.driver = 'CodeIgniter\Session\Handlers\FileHandler'
session.cookieName = 'ci_session'
session.expiration = 7200
session.savePath = WRITEPATH . 'session'
session.regenerateDestroy = false

#--------------------------------------------------------------------
# UPLOADS
#--------------------------------------------------------------------
upload.maxSize = 2048
upload.allowedTypes = 'jpg,jpeg,png,gif'
```

### API RAWG (Optionnel)
Pour utiliser l'API RAWG pour l'ajout automatique de jeux :
1. Inscrivez-vous sur [RAWG.io](https://rawg.io/apidocs)
2. Obtenez votre clé API
3. Ajoutez-la dans votre fichier `.env`

### Upload d'images
Assurez-vous que le dossier `public/uploads/` est accessible en écriture :
```bash
mkdir -p public/uploads/profile_pictures
chmod -R 755 public/uploads/
```

## 🚀 Utilisation

### Première connexion
1. Créez un compte via le formulaire d'inscription
2. Connectez-vous avec vos identifiants
3. Commencez à ajouter vos jeux via "Mes Jeux"

### Ajout de jeux
- **Manuel** : Remplissez le formulaire avec les informations du jeu
- **Via RAWG** : Recherchez et sélectionnez directement depuis la base de données RAWG

### Gestion de la progression
- Mettez à jour le statut de vos jeux (En cours, Terminé, etc.)
- Ajoutez votre temps de jeu
- Rédigez des notes personnelles

## 📁 Structure du projet

```
checkpoint/
├── app/
│   ├── Controllers/     # Contrôleurs MVC
│   ├── Models/         # Modèles de données
│   ├── Views/          # Vues et templates
│   ├── Database/       # Migrations et seeders
│   └── Config/         # Configuration de l'application
├── public/             # Fichiers publics (CSS, JS, images)
├── writable/           # Logs, cache, sessions
└── vendor/             # Dépendances Composer
```

### Contrôleurs principaux
- `Home.php` - Page d'accueil et statistiques
- `Auth.php` - Authentification
- `MesJeux.php` - Gestion de la bibliothèque
- `WishlistController.php` - Liste de souhaits
- `UserController.php` - Gestion du profil

## 🔒 Sécurité

L'application implémente plusieurs couches de sécurité pour protéger vos données :

### 🛡️ Protection contre les injections SQL
- **Query Builder sécurisé** : Toutes les requêtes utilisent le Query Builder de CodeIgniter qui échappe automatiquement les paramètres
- **Requêtes préparées** : L'ORM génère des requêtes préparées pour éliminer les risques d'injection
- **Aucune concaténation SQL** : Aucune requête SQL brute dans le code

### 🔐 Authentification et sessions
- **Mots de passe hachés** : Utilisation de `password_hash()` avec algorithme bcrypt
- **Sessions sécurisées** : Configuration sécurisée des sessions PHP

- **Contrôle d'accès** : Vérification des droits utilisateur sur chaque action

### 🛡️ Protection des données
- **Validation des entrées** : Validation stricte de toutes les données utilisateur
- **Protection CSRF** : Tokens anti-CSRF activés sur tous les formulaires
- **Protection XSS** : Échappement automatique des données en sortie
- **Filtrage des uploads** : Validation des types de fichiers uploadés

### 🔒 Sécurité réseau
- **Cookies sécurisés** : Configuration HttpOnly et SameSite
- **Headers de sécurité** : Headers de protection intégrés
- **Gestion des erreurs** : Pas d'exposition d'informations sensibles

## 🧪 Tests

Exécuter les tests :
```bash
composer test
# ou
vendor/bin/phpunit
```

## 🤝 Contribution

1. Fork le projet
2. Créez une branche pour votre fonctionnalité (`git checkout -b feature/AmazingFeature`)
3. Committez vos changements (`git commit -m 'Add some AmazingFeature'`)
4. Push vers la branche (`git push origin feature/AmazingFeature`)
5. Ouvrez une Pull Request

## 📝 Licence

Ce projet est sous licence MIT. Voir le fichier `LICENSE` pour plus de détails.

## 📞 Support

Si vous rencontrez des problèmes :
1. Vérifiez les [issues existantes](https://github.com/votre-username/checkpoint/issues)
2. Créez une nouvelle issue si nécessaire
3. Fournissez un maximum de détails sur le problème

## 🎯 Roadmap

- [ ] Application mobile (React Native/Flutter)
- [ ] Import/Export de données
- [ ] Partage de listes publiques
- [ ] Intégration Steam/PlayStation/Xbox
- [ ] Recommandations basées sur l'IA
- [ ] Mode multijoueur/communautaire

---

**Développé avec ❤️ et beaucoup de ☕**

> "Un checkpoint bien placé peut sauver des heures de progression !"

## 🚀 Déploiement en production

### Configuration serveur web

#### Apache (.htaccess)
```apache
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteRule ^(.*)$ index.php/$1 [L]
</IfModule>
```

#### Nginx
```nginx
server {
    listen 80;
    server_name votre-domaine.com;
    root /var/www/checkpoint/public;
    index index.php;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }
}
```

### Variables d'environnement de production

```env
CI_ENVIRONMENT = production
app.baseURL = 'https://votre-domaine.com'
security.csrfProtection = true
session.cookieSecure = true
session.cookieHTTPOnly = true
```

## 📊 Schéma de base de données

```sql
-- Tables principales
users (id, username, email, password, profile_picture)
games (id, name, platform, release_date, category, developer, publisher, cover, rawg_id)
game_stats (id, user_id, game_id, play_time, progress, status, notes)
wishlist (id, user_id, game_id)
user_top_games (id, user_id, game_id, rank_position)
```

## 🛠️ Troubleshooting

### Problèmes courants

#### 1. Erreur de permissions
```bash
sudo chown -R www-data:www-data writable/
sudo chmod -R 755 writable/
```

#### 2. Erreur de base de données
- Vérifiez les identifiants dans `.env`
- Assurez-vous que MySQL est démarré
- Vérifiez que la base `checkpoint` existe

#### 3. Erreur 404 sur les routes
- Activez `mod_rewrite` sur Apache
- Vérifiez la configuration du virtual host

#### 4. Problèmes d'upload d'images
```bash
mkdir -p public/uploads/profile_pictures
chmod 755 public/uploads/
```

#### 5. API RAWG ne fonctionne pas
- Vérifiez votre clé API dans `.env`
- Testez l'accès réseau : `curl https://api.rawg.io/api/games`

### Logs de débogage

```bash
# Consulter les logs
tail -f writable/logs/log-*.log

# Activer le debug en développement
# Dans .env : CI_ENVIRONMENT = development
```

## 📈 Performance

- **Cache** : Utilisation du cache de CodeIgniter pour les requêtes fréquentes
- **Optimisation DB** : Index sur les colonnes les plus utilisées
- **Compression** : Activation gzip recommandée en production
- **CDN** : Utilisation recommandée pour les images de jeux

## 🔄 Changelog

### Version 1.0.0 (2025-01-XX)
- ✨ Version initiale
- 🔐 Système d'authentification complet
- 📚 Gestion de bibliothèque de jeux
- 💝 Système de wishlist
- 📊 Statistiques utilisateur
- 🔌 Intégration API RAWG
