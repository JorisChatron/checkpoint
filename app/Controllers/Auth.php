<?php

namespace App\Controllers;

use App\Models\UserModel;
use CodeIgniter\Controller;

class Auth extends Controller
{
    // Affiche le formulaire d'inscription
    public function showRegisterForm()
    {
        return view('auth/register');
    }

    // Gère l'inscription de l'utilisateur
    public function register()
    {
        $validation = \Config\Services::validation();

        // Définir les règles de validation
        $validation->setRules([
            'username' => 'required|min_length[3]',
            'email'    => 'required|valid_email',
            'password' => 'required|min_length[6]'
        ]);

        // Vérifie la validation
        if (! $validation->withRequest($this->request)->run()) {
            // Si validation échoue, on renvoie la vue avec les erreurs
            return view('auth/register', [
                'validation' => $validation // Passe l'objet validation à la vue
            ]);
        }

        // Si validation passe, on enregistre l'utilisateur
        $userModel = new UserModel();

        $userModel->save([
            'username' => $this->request->getPost('username'),
            'email'    => $this->request->getPost('email'),
            'password' => password_hash($this->request->getPost('password'), PASSWORD_DEFAULT),
        ]);

        // Redirige vers la page d'accueil après inscription réussie
        return redirect()->to('/');
    }
}




