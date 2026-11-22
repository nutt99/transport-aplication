<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */
$routes->get('/', 'Home::index');

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
