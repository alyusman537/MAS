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
$routes->post('/api/admin/wilayah-add', 'Wilayah::add');
$routes->put('/api/admin/wilayah-update/(:num)', 'Wilayah::update/$1');
$routes->delete('/api/admin/wilayah-delete/(:num)', 'Wilayah::delete/$1');

$routes->get('/api/admin/anggota-semua', 'Anggota::all');
$routes->get('/api/admin/anggota-id/(:num)', 'Anggota::ById/$1');
$routes->get('/api/admin/anggota-edit/(:any)', 'Anggota::edit/$1');
$routes->put('/api/admin/anggota-update/(:any)', 'Anggota::update/$1');
$routes->delete('/api/admin/anggota-delete/(:any)', 'Anggota::delete/$1');
$routes->get('/api/admin/reset-password/(:any)', 'Anggota::resetPassword/$1');

$routes->put('/api/user/update-password/(:any)', 'Anggota::updatePassword/$1');
$routes->put('/api/user/update-profile/(:any)', 'Anggota::updateProfile/$1');

$routes->get('/api/admin/infaq-semua', 'Infaq::index');
$routes->get('/api/admin/infaq-id/(:num)', 'Infaq::byId/$1');
$routes->get('/api/admin/infaq-new', 'Infaq::new');
$routes->post('/api/admin/infaq-add', 'Infaq::add');
$routes->get('/api/admin/infaq-edit/(:num)', 'Infaq::edit/$1');
$routes->put('/api/admin/infaq-update/(:num)', 'Infaq::update/$1');
$routes->put('/api/admin/infaq-hapus/(:num)', 'Infaq::delete/$1');