<?php

namespace App\Controllers;
use App\Models\ReviewModel;

class ReviewController extends BaseController
{
    public function index()
    {
        $model = new ReviewModel();
        $data['reviews'] = $model->getAllReviews();
        return view('reviews/index', $data);
    }
}
