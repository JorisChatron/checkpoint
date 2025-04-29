<?php 

namespace App\Controllers;
use App\Models\WishlistModel;

class WishlistController extends BaseController
{
    public function index()
    {
        $model = new WishlistModel();
        $data['wishlist'] = $model->getWishlistByUser(1); // à remplacer par session user id
        return view('wishlist/index', $data);
    }
}
