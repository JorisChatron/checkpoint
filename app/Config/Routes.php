<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */


$routes->get('/', 'Home::index');
$routes->get('profile', 'UserController::profile');
$routes->get('wishlist', 'WishlistController::index');
$routes->get('avis', 'ReviewController::index');
$routes->get('login', 'AuthController::login');
$routes->get('register', 'AuthController::register');
