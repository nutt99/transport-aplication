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
