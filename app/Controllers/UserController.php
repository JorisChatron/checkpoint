<?php 

namespace App\Controllers;

use App\Models\UserModel;

class UserController extends BaseController
{
    public function profile()
    {
        $userId = session()->get('user_id');

        if (!$userId) {
            return redirect()->to('profile')->with('error', 'Utilisateur non connecté.');
        }

        $userModel = new UserModel();
        $user = $userModel->find($userId);
        $top5 = $userModel->getUserTopGames($userId);
        $stats = $userModel->getUserStats($userId);

        // Met à jour la session avec la photo de profil actuelle
        session()->set('profile_picture', $user['profile_picture'] ?? 'images/burger-icon.png');

        return view('profile', ['user' => $user, 'top5' => $top5, 'stats' => $stats]);
    }

    public function upload()
    {
        $userModel = new UserModel();
        $userId = session()->get('user_id');

        if (!$userId) {
            return $this->response->setJSON([
                'success' => false,
                'error' => 'Utilisateur non connecté.'
            ]);
        }

        $file = $this->request->getFile('profile_picture');

        if ($file->isValid() && !$file->hasMoved()) {
            $newName = $file->getRandomName();
            $file->move('uploads/profile_pictures', $newName);

            // Met à jour le chemin de la photo de profil dans la base de données
            $userModel->update($userId, ['profile_picture' => 'uploads/profile_pictures/' . $newName]);

            // Met à jour la session avec la nouvelle photo
            session()->set('profile_picture', 'uploads/profile_pictures/' . $newName);

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Photo de profil mise à jour avec succès.'
            ]);
        }

        return $this->response->setJSON([
            'success' => false,
            'error' => 'Erreur lors du téléchargement de la photo.'
        ]);
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
            'profile_picture' => 'images/burger-icon.png', // Change l'image par défaut
        ]);

        return redirect()->to('/'); // Redirige vers la page d'accueil
    }

    public function updateTop5()
    {
        $userId = session()->get('user_id');
        if (!$userId) {
            return $this->response->setStatusCode(401)->setJSON(['error' => 'Non autorisé']);
        }
        $order = $this->request->getPost('order'); // tableau d'IDs user_top_games
        if (!is_array($order)) {
            return $this->response->setStatusCode(400)->setJSON(['error' => 'Format invalide']);
        }
        $db = \Config\Database::connect();
        foreach ($order as $position => $id) {
            $db->table('user_top_games')
                ->where('id', $id)
                ->where('user_id', $userId)
                ->update(['position' => $position + 1]);
        }
        return $this->response->setJSON(['success' => true]);
    }
}
