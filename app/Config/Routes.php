<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */
$routes->get('/', 'Home::index');

$routes->group('api', ['namespace' => 'App\Controllers\Api'], function($routes) {

    $routes->group('transport', function($routes){
        $routes->get('transports', 'Transport::index');
        $routes->get('kota', 'Transport::kota');
        $routes->get('tarif', 'Transport::tarif');
        $routes->match(['get', 'post'], 'rekomendasi-wisata', 'Transport::rekomendasiWisata');
    });
});
