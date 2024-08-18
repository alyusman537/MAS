<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Anggota::index');

$routes->get('/api/render/bukti/(:any)', 'Render::bukti/$1');
$routes->get('/api/render/foto/(:any)', 'Render::image/$1');
$routes->get('/api/render/js/(:any)', 'Render::js/$1');

$routes->post('/api/user-login', 'Login::auth');
$routes->get('/api/user/refresh-token', 'Login::refreshToken');

$routes->get('/api/user/profile', 'Profile::index');
$routes->get('/api/user/profile/edit/(:any)', 'Profile::edit/$1');
$routes->put('/api/user/profile/update/(:any)', 'Profile::update/$1');
$routes->get('/api/user/profile/edit-password', 'Profile::editPassword');
$routes->put('/api/user/profile/update-password/(:any)', 'profile::updatePassword/$1');
$routes->post('/api/user/profile/foto', 'Profile::foto');

$routes->get('/api/user/infaq-umum/new', 'Umum::new');
$routes->post('/api/user/infaq-umum/add', 'Umum::add');
$routes->get('/api/user/infaq-umum/id/(:any)', 'Umum::byId/$1');
$routes->get('/api/user/infaq-umum/edit/(:any)', 'Umum::edit/$1');
$routes->put('/api/user/infaq-umum/update/(:any)', 'Umum::update/$1');
$routes->post('/api/user/infaq-umum-bukti/(:any)', 'Umum::unggahBukti/$1');

$routes->get('/api/user/home/daftar-infaq/(:any)', 'HomeUser::daftarInfaq/$1');
$routes->get('/api/user/home/infaq-umum', 'HomeUser::infaqUmum');

$routes->put('/api/user/pembayaran/(:any)', 'Pembayaran::bayar/$1');
$routes->post('/api/user/pembayaran-bukti/(:any)', 'Pembayaran::buktiBayar/$1');

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

$routes->get('/api/admin/daftar-bayar-infaq/(:any)', 'Penerimaan::daftarTunggu/$1');
$routes->get('/api/admin/daftar-bayar-umum/(:any)', 'Penerimaan::daftarUmum/$1');
$routes->get('/api/admin/terima-infaq/(:any)', 'Penerimaan::terimaInfaq/$1');
$routes->get('/api/admin/terima-umum/(:any)', 'Penerimaan::terimaUmum/$1');

$routes->get('/api/admin/mutasi', 'Mutasi::new');
$routes->post('/api/admin/mutasi', 'Mutasi::add');
$routes->get('/api/admin/mutasi-tanggal/(:any)/(:any)', 'Mutasi::list/$1/$2');
$routes->get('/api/admin/mutasi-detail/(:any)', 'Mutasi::detail/$1');
$routes->get('/api/admin/saldo-akhir', 'Mutasi::saldoAkhir');

///////////// VIEW USER ///////////////
$routes->get('/login', 'View::userLogin');
$routes->get('/profile', 'View::userProfile');
$routes->get('/infaq', 'View::userInfaq');
$routes->get('/infaq-umum', 'View::userInfaqUmum');
$routes->get('/laporan-kas', 'View::userKas');