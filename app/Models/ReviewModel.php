<?php

namespace App\Models;
use CodeIgniter\Model;

class ReviewModel extends Model
{
    protected $table = 'reviews';
    protected $allowedFields = ['user_id', 'game_id', 'rating', 'comment'];

    public function getAllReviews()
    {
        return $this->select('reviews.*, users.username, games.name')
                    ->join('users', 'users.id = reviews.user_id')
                    ->join('games', 'games.id = reviews.game_id')
                    ->findAll();
    }
}
