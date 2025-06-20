<?php 

namespace App\Controllers;

use App\Models\UserModel;

/**
 * Contrôleur de gestion des utilisateurs
 * Gère le profil, l'inscription et les préférences des utilisateurs
 */
class UserController extends BaseController
{
    /**
     * Affiche le profil de l'utilisateur connecté
     * Inclut ses statistiques, son top 5 et sa liste de jeux
     * 
     * @return mixed Vue du profil ou redirection
     */
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

        // Récupère tous les jeux possédés par l'utilisateur
        $db = \Config\Database::connect();
        $allGames = $db->table('game_stats')
            ->select('games.id, games.name, games.platform, games.release_date, games.category, games.cover')
            ->join('games', 'games.id = game_stats.game_id')
            ->where('game_stats.user_id', $userId)
            ->groupBy('games.id')
            ->orderBy('games.name', 'ASC')
            ->get()->getResultArray();

        // Met à jour la session avec la photo de profil actuelle
        session()->set('profile_picture', $user['profile_picture'] ?? 'images/burger-icon.png');

        return view('profile', [
            'user' => $user,
            'top5' => $top5,
            'stats' => $stats,
            'allGames' => $allGames
        ]);
    }

    /**
     * Gère le téléchargement et la mise à jour de la photo de profil
     * 
     * @return \CodeIgniter\HTTP\Response Réponse JSON
     */
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

    /**
     * Met à jour l'ordre des jeux dans le top 5 de l'utilisateur
     * 
     * @return \CodeIgniter\HTTP\Response Réponse JSON
     */
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

    /**
     * Définit un nouveau top 5 complet pour l'utilisateur
     * Remplace l'ancien top 5 par la nouvelle sélection
     * 
     * @return \CodeIgniter\HTTP\Response Réponse JSON
     */
    public function setTop5()
    {
        $userId = session()->get('user_id');
        if (!$userId) {
            return $this->response->setJSON(['success' => false, 'error' => 'Utilisateur non connecté']);
        }
        $data = $this->request->getJSON(true);
        $top5 = $data['top5'] ?? [];
        if (!is_array($top5) || count($top5) !== 5) {
            return $this->response->setJSON(['success' => false, 'error' => 'Vous devez sélectionner exactement 5 jeux.']);
        }
        $db = \Config\Database::connect();
        // Supprime l'ancien top 5
        $db->table('user_top_games')->where('user_id', $userId)->delete();
        // Insère le nouveau top 5
        foreach ($top5 as $position => $gameId) {
            $db->table('user_top_games')->insert([
                'user_id' => $userId,
                'game_id' => $gameId,
                'position' => $position + 1
            ]);
        }
        return $this->response->setJSON(['success' => true]);
    }

    /**
     * Active ou désactive l'affichage du contenu adulte
     * Met à jour la préférence dans la session
     * 
     * @return \CodeIgniter\HTTP\Response Réponse JSON
     */
    public function toggleAdult()
    {
        $show = $this->request->getPost('show_adult') == '1';
        session()->set('show_adult', $show);
        return $this->response->setJSON(['success' => true]);
    }
}
