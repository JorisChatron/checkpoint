<?php

namespace App\Controllers;

use App\Controllers\BaseController;

class MesJeux extends BaseController
{
    public function index()
    {
        // Charge la vue "mes-jeux/index" avec un titre
        return view('mes-jeux/index', ['title' => 'Mes Jeux']);
    }
}