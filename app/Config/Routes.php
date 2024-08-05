<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Anggota::index');

$routes->post('/api/login/auth', 'Login::auth');
$routes->get('/api/login/refresh/(:any)', 'Login::refreshToken/$1');

$routes->get('/api/anggota-semua', 'Anggota::allAnggota');
$routes->get('/api/anggota-id/(:num)', 'Anggota::anggotaById/$1');
$routes->get('/api/anggota-tambah', 'Anggota::newAnggota');
$routes->post('/api/anggota-tambah', 'Anggota::addAnggota');
