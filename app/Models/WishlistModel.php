<?php

namespace App\Models;
use CodeIgniter\Model;

class WishlistModel extends Model
{
    protected $table = 'wishlist';
    protected $allowedFields = ['user_id', 'game_id', 'status'];

    public function getWishlistByUser($userId)
    {
        return $this->select('wishlist.*, games.name, games.platform')
                    ->join('games', 'games.id = wishlist.game_id')
                    ->where('wishlist.user_id', $userId)
                    ->findAll();
    }
}
