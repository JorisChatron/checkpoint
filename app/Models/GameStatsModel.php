<?php

namespace App\Models;
use CodeIgniter\Model;

class GameStatsModel extends Model
{
    protected $table = 'game_stats';
    protected $allowedFields = [
        'user_id', 'game_id', 'play_time', 'progress', 'status', 'notes', 'created_at', 'updated_at'
    ];
}