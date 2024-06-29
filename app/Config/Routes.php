<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/api', 'Home::index');

$routes->post('/api/login', 'Auth::login');
$routes->post('/api/signup', 'Auth::signup');

$routes->get('/api/saldo', 'Payment::getSaldo');
$routes->post('/api/saldo', 'Payment::addSaldo');

$routes->get('/api/bus', 'Bus::getAllBus');
$routes->get('/api/tiket-by-bus/(:segment)', 'Tiket::GetTiketByIdBus/$1');
$routes->get('/api/tiket-by-filter', 'Tiket::getTiketByFilter');

$routes->post('/api/pesan-tiket', 'Payment::buyTiket');


// Web App
$routes->get('/', 'Home::homePage');
$routes->get('/home', 'Home::homePage');

$routes->get('/bus', 'Bus::indexWeb');
$routes->get('/bus/create', 'Bus::createWeb');
$routes->post('/bus/store', 'Bus::storeWeb');
$routes->get('/bus/edit/(:num)', 'Bus::editWeb/$1');
$routes->post('/bus/update/(:num)', 'Bus::updateWeb/$1');
$routes->delete('/bus/delete/(:num)', 'Bus::deleteWeb/$1');

$routes->get('/tiket', 'Tiket::indexWeb');
$routes->get('/tiket/create', 'Tiket::createWeb');
$routes->post('/tiket/store', 'Tiket::storeWeb');
$routes->get('/tiket/edit/(:num)', 'Tiket::editWeb/$1');
$routes->post('/tiket/update/(:num)', 'Tiket::updateWeb/$1');
$routes->get('/tiket/delete/(:num)', 'Tiket::deleteWeb/$1');

$routes->get('/pesanan', 'Payment::getPesananWeb');

$routes->get('/user', 'Auth::getAllUsersWeb');
