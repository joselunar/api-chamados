<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */

$routes->group('', ['filter' => 'guest'], static function (RouteCollection $routes): void {
    $routes->get('login', 'Auth::login');
    $routes->post('login', 'Auth::attempt');
});

$routes->get('logout', 'Auth::logout', ['filter' => 'auth']);

$routes->group('', ['filter' => 'auth'], static function (RouteCollection $routes): void {
    $routes->get('/', 'Tickets::index');
    $routes->post('chamados', 'Tickets::create');
    $routes->get('chamados/(:num)', 'Tickets::show/$1');
    $routes->post('chamados/(:num)', 'Tickets::update/$1');
    $routes->post('chamados/(:num)/comentarios', 'Tickets::comment/$1');
});

$routes->post('api/login', 'Api\Auth::login');

$routes->group('api', ['filter' => 'apiAuth'], static function (RouteCollection $routes): void {
    $routes->get('chamados', 'Api\Tickets::index');
    $routes->post('chamados', 'Api\Tickets::create');
    $routes->get('chamados/(:num)', 'Api\Tickets::show/$1');
    $routes->put('chamados/(:num)', 'Api\Tickets::update/$1');
    $routes->patch('chamados/(:num)', 'Api\Tickets::update/$1');
    $routes->post('chamados/(:num)/comentarios', 'Api\Tickets::comment/$1');
    $routes->get('chamados/(:num)/historico', 'Api\Tickets::history/$1');
});
