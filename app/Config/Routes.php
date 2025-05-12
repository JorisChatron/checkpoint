<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */


$routes->get('/', 'Home::index');
$routes->get('profile', 'UserController::profile');
$routes->get('wishlist', 'WishlistController::index');
$routes->get('avis', 'ReviewController::index');
$routes->get('register', 'Auth::showRegisterForm');
$routes->post('register', 'Auth::register');
$routes->get('login', 'Auth::showLoginForm');
$routes->post('login', 'Auth::login');
$routes->get('logout', 'Auth::logout');
$routes->get('mes-jeux', 'MesJeux::index');

