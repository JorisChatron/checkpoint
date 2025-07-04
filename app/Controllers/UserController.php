<?php 

namespace App\Controllers;

use App\Models\UserModel;

/**
 * Contrôleur de gestion des utilisateurs
 * 
 * Ce contrôleur gère toutes les fonctionnalités liées au profil utilisateur :
 * - Affichage du profil personnel avec statistiques
 * - Gestion de la photo de profil (upload et mise à jour)
 * - Gestion du Top 5 des jeux préférés (création, modification, réorganisation)
 * - Gestion des préférences utilisateur (contenu adulte)
 * 
 * Fonctionnalités principales :
 * - Interface de profil personnalisée
 * - Système d'upload de photos de profil
 * - Gestion dynamique du Top 5 des jeux
 * - Préférences de contenu utilisateur
 */
class UserController extends BaseController
{
    /**
     * Affiche le profil de l'utilisateur connecté
     * 
     * Cette méthode est le point d'entrée principal du profil utilisateur.
     * Elle récupère et affiche toutes les informations personnelles :
     * - Données du profil (nom, email, photo)
     * - Top 5 des jeux préférés
     * - Statistiques de jeu complètes
     * - Liste complète de tous les jeux possédés
     * 
     * Inclut ses statistiques, son top 5 et sa liste de jeux
     * 
     * @return mixed Vue du profil ou redirection si non connecté
     */
    public function profile()
    {
        // ===== VÉRIFICATION DE LA CONNEXION UTILISATEUR =====
        
        // Récupération de l'ID utilisateur depuis la session
        $userId = session()->get('user_id');

        // Redirection si l'utilisateur n'est pas connecté
        if (!$userId) {
            return redirect()->to('profile')->with('error', 'Utilisateur non connecté.');
        }

        // ===== RÉCUPÉRATION DES DONNÉES DU PROFIL =====
        
        $userModel = new UserModel();
        
        // Récupération des informations de base de l'utilisateur
        $user = $userModel->find($userId);
        
        // Récupération du Top 5 des jeux préférés via le modèle
        $top5 = $userModel->getUserTopGames($userId);
        
        // Récupération des statistiques personnelles via le modèle
        $stats = $userModel->getUserStats($userId);

        // ===== RÉCUPÉRATION DE LA LISTE COMPLÈTE DES JEUX =====
        
        // Récupère tous les jeux possédés par l'utilisateur pour l'affichage
        $db = \Config\Database::connect();
        $allGames = $db->table('game_stats')
            ->select('games.id, games.name, games.platform, games.release_date, games.category, games.cover')
            ->join('games', 'games.id = game_stats.game_id')  // Jointure pour récupérer les infos des jeux
            ->where('game_stats.user_id', $userId)            // Filtrage par utilisateur
            ->groupBy('games.id')                             // Évite les doublons si plusieurs entrées
            ->orderBy('games.name', 'ASC')                    // Tri alphabétique par nom
            ->get()->getResultArray();

        // ===== MISE À JOUR DE LA SESSION =====
        
        // Met à jour la session avec la photo de profil actuelle
        // Utilise une image par défaut si aucune photo n'est définie
        session()->set('profile_picture', $user['profile_picture'] ?? 'images/burger-icon.png');

        // ===== RENDU DE LA VUE =====
        
        return view('profile', [
            'user' => $user,        // Informations du profil utilisateur
            'top5' => $top5,        // Top 5 des jeux préférés
            'stats' => $stats,      // Statistiques personnelles
            'allGames' => $allGames // Liste complète des jeux possédés
        ]);
    }

    /**
     * Gère le téléchargement et la mise à jour de la photo de profil
     * 
     * Cette méthode traite l'upload d'une nouvelle photo de profil.
     * Elle valide le fichier, le sauvegarde sur le serveur et met à jour
     * les informations en base de données et en session.
     * 
     * Processus :
     * 1. Validation de la connexion utilisateur
     * 2. Validation du fichier uploadé
     * 3. Génération d'un nom unique pour le fichier
     * 4. Sauvegarde dans le dossier uploads/profile_pictures
     * 5. Mise à jour en base de données
     * 6. Mise à jour de la session
     * 
     * @return \CodeIgniter\HTTP\Response Réponse JSON avec le statut de l'opération
     */
    public function upload()
    {
        $userModel = new UserModel();
        
        // ===== VÉRIFICATION DE LA CONNEXION UTILISATEUR =====
        
        // Récupération de l'ID utilisateur depuis la session
        $userId = session()->get('user_id');

        // Vérification que l'utilisateur est connecté
        if (!$userId) {
            return $this->response->setJSON([
                'success' => false,
                'error' => 'Utilisateur non connecté.'
            ]);
        }

        // ===== TRAITEMENT DU FICHIER UPLOADÉ =====
        
        // Récupération du fichier uploadé depuis le formulaire
        $file = $this->request->getFile('profile_picture');

        // Vérification que le fichier est valide et n'a pas déjà été déplacé
        if ($file->isValid() && !$file->hasMoved()) {
            
            // ===== SAUVEGARDE DU FICHIER =====
            
            // Génération d'un nom unique pour éviter les conflits
            $newName = $file->getRandomName();
            
            // Déplacement du fichier vers le dossier de destination
            $file->move('uploads/profile_pictures', $newName);

            // ===== MISE À JOUR EN BASE DE DONNÉES =====
            
            // Met à jour le chemin de la photo de profil dans la base de données
            $userModel->update($userId, ['profile_picture' => 'uploads/profile_pictures/' . $newName]);

            // ===== MISE À JOUR DE LA SESSION =====
            
            // Met à jour la session avec la nouvelle photo pour l'affichage immédiat
            session()->set('profile_picture', 'uploads/profile_pictures/' . $newName);

            // ===== RÉPONSE DE SUCCÈS =====
            
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Photo de profil mise à jour avec succès.'
            ]);
        }

        // ===== RÉPONSE D'ERREUR =====
        
        // En cas d'erreur lors du traitement du fichier
        return $this->response->setJSON([
            'success' => false,
            'error' => 'Erreur lors du téléchargement de la photo.'
        ]);
    }

    /**
     * Met à jour l'ordre des jeux dans le top 5 de l'utilisateur
     * 
     * Cette méthode permet de réorganiser l'ordre des jeux dans le Top 5
     * existant. Elle reçoit un tableau d'IDs dans le nouvel ordre et met
     * à jour les positions en base de données.
     * 
     * Utilisée pour le drag & drop ou la réorganisation manuelle du Top 5.
     * 
     * @return \CodeIgniter\HTTP\Response Réponse JSON
     */
    public function updateTop5()
    {
        // ===== VÉRIFICATION DE LA CONNEXION UTILISATEUR =====
        
        $userId = session()->get('user_id');
        if (!$userId) {
            return $this->response->setStatusCode(401)->setJSON(['error' => 'Non autorisé']);
        }
        
        // ===== RÉCUPÉRATION ET VALIDATION DES DONNÉES =====
        
        // Récupération du nouvel ordre depuis le formulaire
        $order = $this->request->getPost('order'); // tableau d'IDs user_top_games
        
        // Validation que l'ordre est bien un tableau
        if (!is_array($order)) {
            return $this->response->setStatusCode(400)->setJSON(['error' => 'Format invalide']);
        }
        
        // ===== MISE À JOUR DE L'ORDRE EN BASE =====
        
        $db = \Config\Database::connect();
        
        // Parcours du tableau d'ordre et mise à jour de chaque position
        foreach ($order as $position => $id) {
            $db->table('user_top_games')
                ->where('id', $id)
                ->where('user_id', $userId)  // SÉCURITÉ : vérifie que l'entrée appartient à l'utilisateur
                ->update(['rank_position' => $position + 1]);  // Position + 1 car les positions commencent à 1
        }
        
        return $this->response->setJSON(['success' => true]);
    }

    /**
     * Définit un nouveau top 5 complet pour l'utilisateur
     * 
     * Cette méthode remplace complètement l'ancien Top 5 par une nouvelle
     * sélection de 5 jeux. Elle supprime d'abord l'ancien Top 5 puis
     * insère le nouveau avec les positions appropriées.
     * 
     * Remplace l'ancien top 5 par la nouvelle sélection
     * 
     * @return \CodeIgniter\HTTP\Response Réponse JSON
     */
    public function setTop5()
    {
        // ===== VÉRIFICATION DE LA CONNEXION UTILISATEUR =====
        
        $userId = session()->get('user_id');
        if (!$userId) {
            return $this->response->setJSON(['success' => false, 'error' => 'Utilisateur non connecté']);
        }
        
        // ===== RÉCUPÉRATION ET VALIDATION DES DONNÉES =====
        
        // Récupération des données JSON du nouveau Top 5
        $data = $this->request->getJSON(true);
        
        // ===== LOGS POUR DÉBOGAGE =====
        log_message('info', 'setTop5 - Données reçues: ' . json_encode($data));
        
        $top5 = $data['top5'] ?? [];
        
        // ===== VALIDATION DÉTAILLÉE =====
        log_message('info', 'setTop5 - Top5 reçu: ' . json_encode($top5));
        log_message('info', 'setTop5 - Type: ' . gettype($top5));
        log_message('info', 'setTop5 - Count: ' . count($top5));
        
        // Validation que le Top 5 est un tableau
        if (!is_array($top5)) {
            log_message('error', 'setTop5 - Top5 n\'est pas un tableau: ' . gettype($top5));
            return $this->response->setJSON(['success' => false, 'error' => 'Format de données invalide.']);
        }
        
        // Validation que le Top 5 contient exactement 5 jeux
        if (count($top5) !== 5) {
            log_message('error', 'setTop5 - Nombre de jeux incorrect: ' . count($top5) . ' au lieu de 5');
            return $this->response->setJSON(['success' => false, 'error' => 'Vous devez sélectionner exactement 5 jeux (actuellement: ' . count($top5) . ').']);
        }
        
        // ===== VALIDATION DES IDs DE JEUX =====
        foreach ($top5 as $index => $gameId) {
            if (!is_numeric($gameId)) {
                log_message('error', 'setTop5 - ID de jeu invalide à l\'index ' . $index . ': ' . $gameId);
                return $this->response->setJSON(['success' => false, 'error' => 'ID de jeu invalide à la position ' . ($index + 1) . '.']);
            }
        }
        
        // ===== REMPLACEMENT DU TOP 5 =====
        
        $db = \Config\Database::connect();
        
        try {
            // Début de transaction pour garantir l'intégrité
            $db->transStart();
            
            // Supprime l'ancien top 5 de l'utilisateur avec requête SQL directe
            $sql = "DELETE FROM user_top_games WHERE user_id = ?";
            $db->query($sql, [$userId]);
            log_message('info', 'setTop5 - Ancien top 5 supprimé pour l\'utilisateur ' . $userId);
            
            // Vérification que la suppression a bien fonctionné
            $sql = "SELECT COUNT(*) as count FROM user_top_games WHERE user_id = ?";
            $result = $db->query($sql, [$userId])->getRow();
            $remaining = $result->count;
            
            if ($remaining > 0) {
                log_message('error', 'setTop5 - Suppression incomplète, ' . $remaining . ' entrées restantes');
                $db->transRollback();
                return $this->response->setJSON(['success' => false, 'error' => 'Erreur lors de la suppression de l\'ancien top 5.']);
            }
            
            // Insertion avec requêtes SQL directes
            $inserted = 0;
            foreach ($top5 as $position => $gameId) {
                try {
                    $sql = "INSERT INTO user_top_games (user_id, game_id, rank_position) VALUES (?, ?, ?)";
                    $result = $db->query($sql, [$userId, $gameId, $position + 1]);
                    
                    if ($result) {
                        $inserted++;
                        log_message('info', 'setTop5 - Jeu ' . $gameId . ' inséré à la position ' . ($position + 1));
                    } else {
                        log_message('error', 'setTop5 - Échec insertion jeu ' . $gameId . ' à la position ' . ($position + 1));
                    }
                } catch (\Exception $e) {
                    log_message('error', 'setTop5 - Exception lors de l\'insertion du jeu ' . $gameId . ': ' . $e->getMessage());
                }
            }
            
            if ($inserted === 5) {
                log_message('info', 'setTop5 - Top 5 mis à jour avec succès: ' . $inserted . ' jeux insérés');
                $db->transComplete();
                return $this->response->setJSON(['success' => true]);
            } else {
                log_message('error', 'setTop5 - Nombre incorrect d\'insertions: ' . $inserted . ' au lieu de 5');
                $db->transRollback();
                return $this->response->setJSON(['success' => false, 'error' => 'Erreur lors de la sauvegarde du top 5.']);
            }
            
        } catch (\Exception $e) {
            log_message('error', 'setTop5 - Exception: ' . $e->getMessage());
            $db->transRollback();
            return $this->response->setJSON(['success' => false, 'error' => 'Erreur lors de la sauvegarde: ' . $e->getMessage()]);
        }
    }

    /**
     * Active ou désactive l'affichage du contenu adulte
     * 
     * Cette méthode gère la préférence utilisateur concernant l'affichage
     * du contenu adulte dans l'application (notamment dans le calendrier).
     * La préférence est stockée en session pour une persistance temporaire.
     * 
     * Met à jour la préférence dans la session
     * 
     * @return \CodeIgniter\HTTP\Response Réponse JSON
     */
    public function toggleAdult()
    {
        // ===== RÉCUPÉRATION ET TRAITEMENT DE LA PRÉFÉRENCE =====
        
        // Récupération de la valeur depuis le formulaire et conversion en booléen
        $show = $this->request->getPost('show_adult') == '1';
        
        // Stockage de la préférence en session
        session()->set('show_adult', $show);
        
        return $this->response->setJSON(['success' => true]);
    }
}
