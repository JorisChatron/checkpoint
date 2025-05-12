<?php

namespace App\Models;
use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table = 'users';
    protected $allowedFields = ['username', 'email', 'password', 'profile_picture'];
    protected $useTimestamps = true; // si tu veux que CI remplisse automatiquement created_at
}

