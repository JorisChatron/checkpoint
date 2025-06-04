<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\UserModel;

class RememberMeFilter implements FilterInterface
{
    /**
     * Vérifie si l'utilisateur a un cookie "remember me" valide
     * et le connecte automatiquement si c'est le cas
     */
    public function before(RequestInterface $request, $arguments = null)
    {
        // Ne pas vérifier si l'utilisateur est déjà connecté
        if (session()->get('user_id')) {
            return;
        }

        $rememberToken = $request->getCookie('remember_token');
        
        if ($rememberToken) {
            $userModel = new UserModel();
            $user = $userModel->getUserByRememberToken($rememberToken);
            
            if ($user) {
                // Connecter automatiquement l'utilisateur
                session()->set([
                    'user_id' => $user['id'],
                    'username' => $user['username'],
                    'profile_picture' => $user['profile_picture'] ?? 'images/burger-icon.png',
                ]);
                
                // Régénérer un nouveau token pour la sécurité
                $newToken = $userModel->setRememberToken($user['id']);
                
                // Définir le nouveau cookie
                service('response')->setCookie([
                    'name' => 'remember_token',
                    'value' => $newToken,
                    'expire' => 30 * 24 * 60 * 60, // 30 jours
                    'httponly' => true,
                    'secure' => false, // Mettre à true en production HTTPS
                    'samesite' => 'Lax'
                ]);
            } else {
                // Token invalide, supprimer le cookie
                service('response')->setCookie([
                    'name' => 'remember_token',
                    'value' => '',
                    'expire' => -1
                ]);
            }
        }
    }

    /**
     * Allows After filters to inspect and modify the response
     * object as needed. This method does not allow any way
     * to stop execution of other after filters, short of
     * throwing an Exception or Error.
     *
     * @param RequestInterface  $request
     * @param ResponseInterface $response
     * @param array|null        $arguments
     *
     * @return ResponseInterface|void
     */
    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Rien à faire après la requête
    }
}
