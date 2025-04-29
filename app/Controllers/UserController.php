<?php 

namespace App\Controllers;
use App\Models\UserModel;

class UserController extends BaseController
{
    public function profile()
    {
        $model = new UserModel();
        $user = $model->find(1); // Exemple : user 1

        return view('profile', ['user' => $user]);
    }
}
