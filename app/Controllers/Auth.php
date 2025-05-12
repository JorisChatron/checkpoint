<?php

namespace App\Controllers;

use App\Models\UserModel;
use CodeIgniter\Controller;

class Auth extends Controller
{
    public function showRegisterForm()
    {
        return view('auth/register');
    }

    public function register()
    {
        $validation = \Config\Services::validation();

        $validation->setRules([
            'username' => 'required|min_length[3]',
            'email'    => 'required|valid_email',
            'password' => 'required|min_length[6]'
        ]);

        if (! $validation->withRequest($this->request)->run()) {
            return view('auth/register', [
                'validation' => $validation
            ]);
        }

        $userModel = new UserModel();

        $userModel->save([
            'username' => $this->request->getPost('username'),
            'email'    => $this->request->getPost('email'),
            'password' => password_hash($this->request->getPost('password'), PASSWORD_DEFAULT),
            'profile_picture' => 'images/burger-icon.png', // Ajoute l'image par défaut
        ]);

        return redirect()->to('/'); // Redirige vers la page d'accueil
    }

    // Afficher le formulaire de connexion
    public function showLoginForm()
    {
        return view('auth/login');
    }

    // Gérer la connexion
    public function login()
    {
        $validation = \Config\Services::validation();

        $validation->setRules([
            'username' => 'required',
            'password' => 'required'
        ]);

        if (! $validation->withRequest($this->request)->run()) {
            // Si la validation échoue, retourner la vue avec les erreurs de validation
            return view('auth/login', [
                'validation' => $validation
            ]);
        }

        $userModel = new UserModel();
        $user = $userModel->where('username', $this->request->getPost('username'))->first();

        // Vérifier si l'utilisateur existe et si le mot de passe est correct
        if ($user && password_verify($this->request->getPost('password'), $user['password'])) {
            // Si tout est ok, l'utilisateur peut être connecté
            session()->set([
                'user_id' => $user['id'],
                'username' => $user['username'],
                'profile_picture' => $user['profile_picture'] ?? 'images/burger-icon.png', // Utilise l'image par défaut si aucune photo n'est définie
            ]);

            return redirect()->to('/'); // Rediriger vers la page d'accueil après connexion
        } else {
            // Sinon afficher une erreur
            return view('auth/login', [
                'validation' => $validation,  // Assure-toi de passer aussi validation ici
                'error' => 'Nom d\'utilisateur ou mot de passe incorrect'
            ]);
        }
    }

    // Déconnexion
    public function logout()
    {
        session()->destroy(); // Détruire la session pour déconnecter l'utilisateur
        return redirect()->to('/login'); // Redirige vers la page de connexion après déconnexion
    }
}





