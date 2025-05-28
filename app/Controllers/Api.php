<?php
namespace App\Controllers;
use CodeIgniter\Controller;

/**
 * Contrôleur API
 * Gère les appels aux services externes comme la traduction
 */
class Api extends Controller
{
    /**
     * Traduit un texte de l'anglais vers le français
     * Utilise le service de traduction Argos Translate
     * 
     * @return \CodeIgniter\HTTP\Response Réponse JSON contenant le texte traduit
     */
    public function translate()
    {
        $json = $this->request->getJSON(true);
        $text = $json['text'] ?? '';
        if (!$text) {
            return $this->response->setJSON(['error' => 'Texte manquant'])->setStatusCode(400);
        }
        $ch = curl_init('https://translate.argosopentech.com/translate');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
            'q' => $text,
            'source' => 'en',
            'target' => 'fr',
            'format' => 'text'
        ]));
        $result = curl_exec($ch);
        $err = curl_error($ch);
        curl_close($ch);
        if ($err) {
            return $this->response->setJSON(['error' => $err])->setStatusCode(500);
        }
        $data = json_decode($result, true);
        return $this->response->setJSON(['translatedText' => $data['translatedText'] ?? '']);
    }

    /**
     * Recherche des jeux par nom (partiel)
     * URL: /api/searchGames?q=nom
     * Retourne un tableau JSON de jeux
     */
    public function searchGames()
    {
        $q = $this->request->getGet('q');
        if (!$q || strlen($q) < 2) {
            return $this->response->setJSON([]);
        }
        $db = \Config\Database::connect();
        $games = $db->table('games')
            ->select('id, name, platform, release_date, category, cover, developer, rawg_id')
            ->like('name', $q)
            ->limit(10)
            ->get()->getResultArray();
        return $this->response->setJSON($games);
    }
} 