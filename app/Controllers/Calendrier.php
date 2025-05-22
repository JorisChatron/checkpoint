<?php
namespace App\Controllers;

use CodeIgniter\Controller;

class Calendrier extends Controller
{
    public function index($year = null, $week = null, $page = 1)
    {
        // Détermine la semaine à afficher
        $now = new \DateTime();
        if (!$year || !$week) {
            $year = (int)$now->format('o');
            $week = (int)$now->format('W');
        }
        $page = (int)($page ?? 1);
        // Calcule le lundi et dimanche de la semaine
        $monday = new \DateTime();
        $monday->setISODate($year, $week);
        $start = $monday->format('Y-m-d');
        $sunday = clone $monday;
        $sunday->modify('+6 days');
        $end = $sunday->format('Y-m-d');

        // Pagination
        $pageSize = 50;
        $apiPage = $page;

        // Appel API RAWG
        $apiKey = 'ff6f7941c211456c8806541638fdfaff';
        $url = "https://api.rawg.io/api/games?key=$apiKey&dates=$start,$end&ordering=released&page_size=$pageSize&page=$apiPage";
        $games = [];
        $error = null;
        $count = 0;
        try {
            $response = file_get_contents($url);
            $data = json_decode($response, true);
            if (isset($data['results'])) {
                $games = $data['results'];
                $count = $data['count'] ?? 0;
            }
        } catch (\Exception $e) {
            $error = "Erreur lors de la récupération des sorties : " . $e->getMessage();
        }

        return view('calendrier/index', [
            'games' => $games,
            'year' => $year,
            'week' => $week,
            'page' => $page,
            'count' => $count,
            'start' => $start,
            'end' => $end,
            'error' => $error
        ]);
    }
} 