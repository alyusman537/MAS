<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Anggota::index');

$routes->post('/api/user-login', 'Login::auth');
$routes->get('/api/user/refresh-token', 'Login::refreshToken');

$routes->get('/api/user/profile', 'Profile::index');
$routes->get('/api/user/profile/edit/(:any)', 'Profile::edit/$1');
$routes->put('/api/user/profile/update/(:any)', 'Profile::update/$1');
$routes->get('/api/user/profile/edit-password', 'Profile::editPassword');
$routes->put('/api/user/profile/update-password/(:any)', 'profile::updatePassword/$1');

$routes->get('/api/user/home/infaq-belum', 'HomeUser::infaqBelum');
$routes->get('/api/user/home/infaq-lunas', 'HomeUser::infaqlunas');
$routes->get('/api/user/home/infaq-umum', 'HomeUser::infaqUmum');

$routes->get('/api/user/infaq-umum/new', 'Umum::new');
$routes->post('/api/user/infaq-umum/add', 'Umum::add');
$routes->get('/api/user/infaq-umum/edit/(:any)', 'Umum::edit/$1');
$routes->put('/api/user/infaq-umum/update/(:any)', 'Umum::update/$1');

//////////// ADMIN /////////////////////

$routes->post('/api/admin-login', 'Login::adminAuth');
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


$routes->get('/api/admin/infaq-semua', 'Infaq::index');
$routes->get('/api/admin/infaq-id/(:num)', 'Infaq::byId/$1');
$routes->get('/api/admin/infaq-new', 'Infaq::new');
$routes->post('/api/admin/infaq-add', 'Infaq::add');
$routes->get('/api/admin/infaq-edit/(:num)', 'Infaq::edit/$1');
$routes->put('/api/admin/infaq-update/(:num)', 'Infaq::update/$1');
$routes->put('/api/admin/infaq-hapus/(:num)', 'Infaq::delete/$1');