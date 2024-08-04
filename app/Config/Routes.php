<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');

$routes->get('/api/anggota-semua', 'Anggota::allAnggota');
$routes->get('/api/anggota-id/(:num)', 'Anggota::anggotaById/$1');
$routes->post('/api/anggota-tambah', 'Anggota::addAnggota');
