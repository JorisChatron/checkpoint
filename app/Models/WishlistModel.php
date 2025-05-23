<?php

namespace App\Models;
use CodeIgniter\Model;

class WishlistModel extends Model
{
    protected $table = 'wishlist';
    protected $primaryKey = 'id';
    protected $allowedFields = ['user_id', 'game_id', 'status'];
    protected $useTimestamps = false;
    protected $createdField = '';
    protected $updatedField = '';
    protected $deletedField = '';
    protected $useSoftDeletes = false;

    protected $validationRules = [
        'user_id' => 'required|numeric',
        'game_id' => 'required|numeric',
        'status' => 'required|in_list[souhaité,acheté,joué]'
    ];

    public function getWishlistByUser($userId)
    {
        return $this->select('wishlist.*, games.name, games.platform')
                    ->join('games', 'games.id = wishlist.game_id')
                    ->where('wishlist.user_id', $userId)
                    ->findAll();
    }
}
