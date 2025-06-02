<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */


$routes->get('/', 'Home::index');
$routes->get('profile', 'UserController::profile');
$routes->post('profile/upload', 'UserController::upload');
$routes->get('wishlist', 'WishlistController::index');
$routes->get('register', 'Auth::showRegisterForm');
$routes->post('register', 'Auth::register');
$routes->get('login', 'Auth::showLoginForm');
$routes->post('login', 'Auth::login');
$routes->get('logout', 'Auth::logout');
$routes->get('mes-jeux', 'MesJeux::index');
$routes->post('mes-jeux/add', 'MesJeux::add');
$routes->post('mes-jeux/edit/(:num)', 'MesJeux::edit/$1');
$routes->post('mes-jeux/delete/(:num)', 'MesJeux::delete/$1');
$routes->post('wishlist/add', 'WishlistController::add');
$routes->post('wishlist/delete/(:num)', 'WishlistController::delete/$1');
$routes->post('wishlist/transfer', 'WishlistController::transfer');
$routes->post('profile/setTop5', 'UserController::setTop5');
$routes->get('calendrier', 'Calendrier::index');
$routes->get('calendrier/(:num)/(:num)', 'Calendrier::index/$1/$2');
$routes->get('calendrier/(:num)/(:num)/page/(:num)', 'Calendrier::index/$1/$2/page/$3');
$routes->post('profile/toggleAdult', 'UserController::toggleAdult');

