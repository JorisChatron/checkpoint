<?php

namespace App\Models;
use CodeIgniter\Model;

class GameModel extends Model
{
    protected $table = 'games';
    protected $allowedFields = ['name', 'platform', 'release_date', 'category', 'cover'];
}
