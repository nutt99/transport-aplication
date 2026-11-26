<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */
$routes->get('/', 'Home::index');


// consider to delete this because not using it anymore 
$routes->group('wisata-proxy', static function ($routes) {
    $routes->get('destinasi', 'WisataProxy::destinasi');
    $routes->get('destinasi/(:segment)', 'WisataProxy::detail/$1');
    $routes->get('kategori', 'WisataProxy::kategori');
    $routes->get('kabupaten-kota', 'WisataProxy::kabupatenKota');
});

$routes->group('app-api', static function ($routes) {
    // Wisata JejakSumut
    $routes->get('wisata/destinasi', 'FrontendApi::destinasi');
    $routes->get('wisata/destinasi/(:segment)', 'FrontendApi::destinasiDetail/$1');
    $routes->get('wisata/kategori', 'FrontendApi::kategori');
    $routes->get('wisata/kabupaten-kota', 'FrontendApi::kabupatenKota');

    // Transportasi Internal
    $routes->get('transport/transports', 'FrontendApi::transports');
    $routes->get('transport/lokasi', 'FrontendApi::lokasi');
    $routes->get('transport/tarif', 'FrontendApi::tarif');
    $routes->get('transport/rekomendasi-wisata', 'FrontendApi::rekomendasiWisata');
});

$routes->group('api', [
    'namespace' => 'App\Controllers\Api',
    'filter'    => 'apikey',], 
    function($routes) {

    $routes->group('transport', function($routes){
        $routes->get('transports', 'Transport::index');
        $routes->get('lokasi', 'Transport::lokasi');
        $routes->get('tarif', 'Transport::tarif');
        $routes->match(['get', 'post'], 'rekomendasi-wisata', 'Transport::rekomendasiWisata');
    });

    $routes->resource('moda', ['controller' => 'Moda']);
    $routes->resource('lokasi', ['controller' => 'Lokasi']);
    $routes->resource('penyedia', ['controller' => 'Penyedia']);
    $routes->resource('jadwal', ['controller' => 'Jadwal']);
});

// ==================== PANEL ADMIN ====================
$routes->group('admin', static function ($routes) {
    $routes->get('/', static function () {
        return redirect()->to(base_url('admin/lokasi'));
    });

    $routes->get('login', 'Admin\AuthController::login');
    $routes->post('login', 'Admin\AuthController::loginProcess');
    $routes->get('logout', 'Admin\AuthController::logout');

    $routes->group('lokasi', ['filter' => 'adminauth'], static function ($routes) {
        $routes->get('/', 'Admin\LokasiController::index');
        $routes->post('simpan', 'Admin\LokasiController::simpan');
        $routes->post('update/(:num)', 'Admin\LokasiController::update/$1');
        $routes->post('hapus/(:num)', 'Admin\LokasiController::hapus/$1');
    });

    $routes->group('transport', ['filter' => 'adminauth'], static function ($routes) {
        $routes->get('/', 'Admin\TransportController::index');
        $routes->post('simpan', 'Admin\TransportController::simpan');
        $routes->post('update/(:num)', 'Admin\TransportController::update/$1');
        $routes->post('hapus/(:num)', 'Admin\TransportController::hapus/$1');
    });
});
