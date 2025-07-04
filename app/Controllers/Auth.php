<?php

namespace App\Controllers;

use App\Models\UserModel;
use CodeIgniter\Controller;

/**
 * Contrôleur d'authentification
 * Gère l'inscription, la connexion et la déconnexion des utilisateurs
 * 
 * Ce contrôleur contient toute la logique d'authentification de l'application :
 * - Inscription de nouveaux utilisateurs avec validation des données
 * - Connexion avec vérification des identifiants 
 * - Déconnexion avec destruction de session
 * - Gestion des erreurs et des redirections
 */
class Auth extends Controller
{
    /**
     * Constructeur du contrôleur
     * Charge automatiquement les helpers nécessaires
     */
    public function __construct()
    {
        // Charger le helper form pour utiliser des fonctions comme set_value()
        // qui permettent de conserver les valeurs des champs en cas d'erreur
        helper('form'); 
    }



    /**
     * Affiche le formulaire d'inscription
     * 
     * Cette méthode se contente d'afficher la vue du formulaire d'inscription.
     * Aucune logique métier ici, juste l'affichage de la page.
     * 
     * @return string Vue du formulaire d'inscription (auth/register.php)
     */
    public function showRegisterForm()
    {
        return view('auth/register');
    }

    /**
     * Traite l'inscription d'un nouvel utilisateur
     * 
     * Cette méthode gère tout le processus d'inscription :
     * 1. Validation des données du formulaire
     * 2. Vérification que les règles sont respectées
     * 3. Hashage sécurisé du mot de passe
     * 4. Sauvegarde en base de données
     * 5. Redirection vers l'accueil
     * 
     * @return mixed Vue avec erreurs si échec ou redirection si succès
     */
    public function register()
    {
        // Récupération du service de validation de CodeIgniter
        $validation = \Config\Services::validation();

        // Définition des règles de validation pour chaque champ
        $validation->setRules([
            'username' => 'required|min_length[3]',    // Nom d'utilisateur obligatoire, min 3 caractères
            'email'    => 'required|valid_email',      // Email obligatoire et valide
            'password' => 'required|min_length[6]'     // Mot de passe obligatoire, min 6 caractères
        ]);

        // Exécution de la validation sur les données reçues via POST
        if (! $validation->withRequest($this->request)->run()) {
            // Si la validation échoue, retour au formulaire avec les erreurs
            // L'objet $validation contient les messages d'erreur à afficher
            return view('auth/register', [
                'validation' => $validation
            ]);
        }

        // Si la validation réussit, création du compte utilisateur
        
        // Création d'une instance du modèle utilisateur pour interagir avec la BDD
        $userModel = new UserModel();

        // Sauvegarde du nouvel utilisateur en base de données
        $userModel->save([
            'username' => $this->request->getPost('username'),     // Récupération du nom d'utilisateur
            'email'    => $this->request->getPost('email'),        // Récupération de l'email
            // SÉCURITÉ : Le mot de passe est hashé avec PASSWORD_DEFAULT (bcrypt actuellement)
            // Jamais stocker un mot de passe en clair !
            'password' => password_hash($this->request->getPost('password'), PASSWORD_DEFAULT),
            // Attribution d'une image de profil par défaut
            'profile_picture' => 'images/burger-icon.png',
        ]);

        // Redirection vers la page d'accueil après inscription réussie
        return redirect()->to('/');
    }

    /**
     * Affiche le formulaire de connexion
     * 
     * Méthode simple qui affiche la vue de connexion.
     * 
     * @return string Vue du formulaire de connexion (auth/login.php)
     */
    public function showLoginForm()
    {
        return view('auth/login');
    }

    /**
     * Traite la tentative de connexion
     * 
     * Cette méthode gère tout le processus de connexion :
     * 1. Validation des données saisies
     * 2. Recherche de l'utilisateur en base
     * 3. Vérification du mot de passe
     * 4. Création de la session utilisateur
     * 5. Redirection appropriée
     * 
     * @return mixed Vue avec erreurs si échec ou redirection si succès
     */
    public function login()
    {
        // Récupération du service de validation de CodeIgniter
        $validation = \Config\Services::validation();

        // Règles de validation pour la connexion (plus simples que l'inscription)
        $validation->setRules([
            'username' => 'required',  // Nom d'utilisateur obligatoire
            'password' => 'required'   // Mot de passe obligatoire
        ]);

        // Validation des données du formulaire
        if (! $validation->withRequest($this->request)->run()) {
            // Si la validation échoue, retour au formulaire avec les erreurs
            return view('auth/login', [
                'validation' => $validation
            ]);
        }

        // Si la validation réussit, vérification des identifiants
        
        // Récupération de l'utilisateur par son nom d'utilisateur
        $userModel = new UserModel();
        $user = $userModel->where('username', $this->request->getPost('username'))->first();

        // Double vérification de sécurité :
        // 1. L'utilisateur existe-t-il en base ?
        // 2. Le mot de passe saisi correspond-il au hash stocké ?
        if ($user && password_verify($this->request->getPost('password'), $user['password'])) {
            
            // CONNEXION RÉUSSIE : Création de la session utilisateur
            // Stockage des informations essentielles dans la session
            session()->set([
                'user_id' => $user['id'],                                           // ID unique de l'utilisateur
                'username' => $user['username'],                                    // Nom d'utilisateur
                'profile_picture' => $user['profile_picture'] ?? 'images/burger-icon.png', // Image de profil avec fallback
            ]);

            // Redirection vers la page d'accueil une fois connecté
            return redirect()->to('/');
        } else {
            
            // CONNEXION ÉCHOUÉE : Affichage d'un message d'erreur générique
            // On ne précise pas si c'est l'utilisateur ou le mot de passe qui est incorrect
            // pour des raisons de sécurité (éviter l'énumération d'utilisateurs)
            return view('auth/login', [
                'validation' => $validation,
                'error' => 'Nom d\'utilisateur ou mot de passe incorrect'
            ]);
        }
    }

    /**
     * Gère la déconnexion de l'utilisateur
     * 
     * Cette méthode nettoie complètement la session utilisateur
     * et redirige vers la page de connexion.
     * 
     * @return mixed Redirection vers la page de connexion
     */
    public function logout()
    {
        // Destruction complète de la session (supprime toutes les données de session)
        // Ceci déconnecte effectivement l'utilisateur
        session()->destroy();
        
        // Redirection vers la page de connexion
        return redirect()->to('/login');
    }
}





