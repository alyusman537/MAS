<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Anggota::index');

$routes->post('/api/login/auth', 'Login::auth');
$routes->get('/api/login/refresh/(:any)', 'Login::refreshToken/$1');

$routes->get('/api/admin/wilayah', 'Wilayah::index');
$routes->get('/api/admin/wilayah/(:num)', 'Wilayah::wilayahById/$1');
$routes->post('/api/admin/wilayah', 'Wilayah::add');
$routes->put('/api/admin/wilayah/(:num)', 'Wilayah::update/$1');
$routes->delete('/api/admin/wilayah/(:num)', 'Wilayah::delete/$1');

$routes->get('/api/admin/anggota-semua', 'Anggota::allAnggota');
$routes->get('/api/admin/anggota-id/(:num)', 'Anggota::anggotaById/$1');
$routes->post('/api/admin/anggota-update/(:any)', 'Anggota::updateAnggota/$1');
$routes->put('/api/admin/reset-password/(:any)', 'Anggota::resetPassword/$1');

$routes->get('/api/anggota-register', 'Anggota::newAnggota');
$routes->post('/api/anggota-register', 'Anggota::addAnggota');
$routes->put('/api/update-profile', 'Anggota::updateProfile');