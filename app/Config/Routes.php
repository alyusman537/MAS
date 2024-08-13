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
$routes->get('/api/user/infaq-umum/id/(:any)', 'Umum::byId/$1');
$routes->get('/api/user/infaq-umum/edit/(:any)', 'Umum::edit/$1');
$routes->put('/api/user/infaq-umum/update/(:any)', 'Umum::update/$1');

$routes->put('/api/user/pembayaran/(:any)', 'Pembayaran::bayar/$1');
$routes->put('/api/user/pembayaran-bukti/(:any)', 'Pembayaran::bayar/$1');

//////////// ADMIN /////////////////////

$routes->post('/api/admin-login', 'Login::adminAuth');
$routes->get('/api/admin-refresh', 'Login::adminRefresh');

$routes->get('/api/admin/wilayah', 'Wilayah::index');
$routes->get('/api/admin/wilayah/(:num)', 'Wilayah::wilayahById/$1');
$routes->post('/api/admin/wilayah', 'Wilayah::add');
$routes->put('/api/admin/wilayah/(:num)', 'Wilayah::update/$1');
$routes->delete('/api/admin/wilayah/(:num)', 'Wilayah::delete/$1');

$routes->get('/api/admin/anggota', 'Anggota::all');
$routes->get('/api/admin/anggota/(:num)', 'Anggota::byId/$1');
$routes->get('/api/admin/anggota-wilayah/(:any)', 'Anggota::byWilayah/$1');
$routes->get('/api/admin/anggota/new', 'Anggota::new');
$routes->post('/api/admin/anggota', 'Anggota::add');
$routes->get('/api/admin/anggota/edit/(:num)', 'Anggota::edit/$1');
$routes->put('/api/admin/anggota/(:num)', 'Anggota::update/$1');
$routes->delete('/api/admin/anggota/(:num)', 'Anggota::delete/$1');
$routes->get('/api/admin/anggota-reset/(:any)', 'Anggota::resetPassword/$1');


$routes->get('/api/admin/infaq', 'Infaq::index');
$routes->get('/api/admin/infaq/(:num)', 'Infaq::byId/$1');
$routes->get('/api/admin/infaq/new', 'Infaq::new');
$routes->post('/api/admin/infaq', 'Infaq::add');
$routes->get('/api/admin/infaq/edit/(:num)', 'Infaq::edit/$1');
$routes->put('/api/admin/infaq/(:num)', 'Infaq::update/$1');
$routes->delete('/api/admin/infaq/(:num)', 'Infaq::delete/$1');
$routes->post('/api/admin/infaq-generate', 'Infaq::generate');
$routes->get('/api/admin/infaq-generate-all/(:any)', 'Infaq::generateSemua/$1');