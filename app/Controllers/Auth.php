<?php

namespace App\Controllers;

use App\Models\UserModel;
use CodeIgniter\Controller;

/**
 * Contrôleur d'authentification
 * Gère l'inscription, la connexion et la déconnexion des utilisateurs
 */
class Auth extends Controller
{
    public function __construct()
    {
        helper('form'); // Charger le helper form pour set_value()
    }

    /**
     * Définit le cookie "remember me"
     */
    private function setRememberCookie($token)
    {
        $response = service('response');
        $response->setCookie([
            'name' => 'remember_token',
            'value' => $token,
            'expire' => 30 * 24 * 60 * 60, // 30 jours
            'httponly' => true, // Empêche l'accès via JavaScript
            'secure' => false, // Mettre à true en production HTTPS
            'samesite' => 'Lax'
        ]);
    }

    /**
     * Supprime le cookie "remember me"
     */
    private function clearRememberCookie()
    {
        $response = service('response');
        $response->setCookie([
            'name' => 'remember_token',
            'value' => '',
            'expire' => -1
        ]);
    }

    /**
     * Affiche le formulaire d'inscription
     * @return string Vue du formulaire d'inscription
     */
    public function showRegisterForm()
    {
        return view('auth/register');
    }

    /**
     * Traite l'inscription d'un nouvel utilisateur
     * Valide les données, crée le compte et redirige
     * @return mixed Vue avec erreurs ou redirection
     */
    public function register()
    {
        // Récupération du service de validation
        $validation = \Config\Services::validation();

        // Définition des règles de validation
        $validation->setRules([
            'username' => 'required|min_length[3]',     // Nom d'utilisateur requis, min 3 caractères
            'email'    => 'required|valid_email',       // Email requis et valide
            'password' => 'required|min_length[6]'      // Mot de passe requis, min 6 caractères
        ]);

        // Exécution de la validation
        if (! $validation->withRequest($this->request)->run()) {
            // Si la validation échoue, retour au formulaire avec les erreurs
            return view('auth/register', [
                'validation' => $validation
            ]);
        }

        // Création d'une instance du modèle utilisateur
        $userModel = new UserModel();

        // Sauvegarde du nouvel utilisateur
        $userModel->save([
            'username' => $this->request->getPost('username'),
            'email'    => $this->request->getPost('email'),
            'password' => password_hash($this->request->getPost('password'), PASSWORD_DEFAULT), // Hashage du mot de passe
            'profile_picture' => 'images/burger-icon.png', // Image de profil par défaut
        ]);

        return redirect()->to('/'); // Redirection vers la page d'accueil
    }

    /**
     * Affiche le formulaire de connexion
     * @return string Vue du formulaire de connexion
     */
    public function showLoginForm()
    {
        return view('auth/login');
    }

    /**
     * Traite la tentative de connexion
     * Vérifie les identifiants et crée la session
     * @return mixed Vue avec erreurs ou redirection
     */
    public function login()
    {
        // Récupération du service de validation
        $validation = \Config\Services::validation();

        // Définition des règles de validation
        $validation->setRules([
            'username' => 'required', // Nom d'utilisateur requis
            'password' => 'required'  // Mot de passe requis
        ]);

        // Exécution de la validation
        if (! $validation->withRequest($this->request)->run()) {
            // Si la validation échoue, retour au formulaire avec les erreurs
            return view('auth/login', [
                'validation' => $validation
            ]);
        }

        // Récupération de l'utilisateur par son nom d'utilisateur
        $userModel = new UserModel();
        $user = $userModel->where('username', $this->request->getPost('username'))->first();

        // Vérification de l'existence de l'utilisateur et du mot de passe
        if ($user && password_verify($this->request->getPost('password'), $user['password'])) {
            // Création de la session utilisateur
            session()->set([
                'user_id' => $user['id'],
                'username' => $user['username'],
                'profile_picture' => $user['profile_picture'] ?? 'images/burger-icon.png', // Image par défaut si non définie
            ]);

            // Gestion du "Se souvenir de moi"
            if ($this->request->getPost('remember_me')) {
                $token = $userModel->setRememberToken($user['id']);
                $this->setRememberCookie($token);
            }

            return redirect()->to('/'); // Redirection vers la page d'accueil
        } else {
            // Affichage des erreurs de connexion
            return view('auth/login', [
                'validation' => $validation,
                'error' => 'Nom d\'utilisateur ou mot de passe incorrect'
            ]);
        }
    }

    /**
     * Gère la déconnexion de l'utilisateur
     * Détruit la session et redirige
     * @return mixed Redirection vers la page de connexion
     */
    public function logout()
    {
        $userId = session()->get('user_id');
        
        // Supprimer le token remember me si l'utilisateur était connecté
        if ($userId) {
            $userModel = new UserModel();
            $userModel->clearRememberToken($userId);
        }
        
        // Supprimer le cookie
        $this->clearRememberCookie();
        
        session()->destroy(); // Destruction de la session
        return redirect()->to('/login'); // Redirection vers la page de connexion
    }
}





