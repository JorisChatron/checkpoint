<?php

namespace App\Models;
use CodeIgniter\Model;

class WishlistModel extends Model
{
    protected $table = 'wishlist';
    protected $primaryKey = 'id';
    protected $allowedFields = ['user_id', 'game_id'];
    protected $useTimestamps = false;
    protected $createdField = '';
    protected $updatedField = '';
    protected $deletedField = '';
    protected $useSoftDeletes = false;

    protected $validationRules = [
        'user_id' => 'required|numeric',
        'game_id' => 'required|numeric'
    ];

    public function getWishlistByUser($userId)
    {
        return $this->select('wishlist.*, games.name, games.platform')
                    ->join('games', 'games.id = wishlist.game_id')
                    ->where('wishlist.user_id', $userId)
                    ->findAll();
    }

    public function getDistinctValues($field, $userId)
    {
        return $this->select("games.{$field}")
                   ->join('games', 'games.id = wishlist.game_id')
                   ->where('wishlist.user_id', $userId)
                   ->distinct()
                   ->findAll();
    }
}
