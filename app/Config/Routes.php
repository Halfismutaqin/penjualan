<?php

namespace Config;

// Create a new instance of our RouteCollection class.
$routes = Services::routes();

/*
 * --------------------------------------------------------------------
 * Router Setup
 * --------------------------------------------------------------------
 */
$routes->setDefaultNamespace('App\Controllers');
// $routes->setDefaultController('Home');
$routes->setDefaultController('Homepage');
$routes->setDefaultMethod('index');
$routes->setTranslateURIDashes(false);
$routes->set404Override();
// The Auto Routing (Legacy) is very dangerous. It is easy to create vulnerable apps
// where controller filters or CSRF protection are bypassed.
// If you don't want to define all routes, please use the Auto Routing (Improved).
// Set `$autoRoutesImproved` to true in `app/Config/Feature.php` and set the following to true.
// $routes->setAutoRoute(false);

/*
 * --------------------------------------------------------------------
 * Route Definitions
 * --------------------------------------------------------------------
 */

// We get a performance increase by specifying the default
// route since we don't have to scan directories.
// $routes->get('/', 'Home::index');
$routes->get('/', 'homepage::index');
 
$routes->group('', ['filter' => 'auth'] ,function ($routes) {
	$routes->get('/cek', 'homepage::cek');
	$routes->get('/cart', 'cart::index');
	$routes->post('/cart/update', 'homepage::update_cart');
	$routes->get('/cart/delete/(:any)', 'homepage::delete/$1');
	$routes->add('/cart/add', 'homepage::add');
	$routes->get('/cart/clear', 'homepage::clear');
	$routes->get('/product', 'product::index');
	$routes->get('/product/read', 'product::read');
	$routes->post('/product/create', 'product::create');
	$routes->post('/product/edit', 'product::edit');
	$routes->post('/product/update', 'product::update');
	$routes->post('/product/delete', 'product::delete');
	$routes->post('/product/delete_batch', 'product::delete_batch');
});

// route auth with filter auth:page
$routes->group('', ['filter' => 'auth:page'] ,function ($routes) {
    $routes->get('/login', 'auth::index_login');
    $routes->post('/login', 'auth::login');
    $routes->get('/register', 'auth::index_register');
    $routes->post('/register', 'auth::register');
});
 
$routes->get('/logout', 'auth::logout');

/*
 * --------------------------------------------------------------------
 * Additional Routing
 * --------------------------------------------------------------------
 *
 * There will often be times that you need additional routing and you
 * need it to be able to override any defaults in this file. Environment
 * based routes is one such time. require() additional route files here
 * to make that happen.
 *
 * You will have access to the $routes object within that file without
 * needing to reload it.
 */
if (is_file(APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php')) {
    require APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php';
}
