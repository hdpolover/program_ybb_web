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
$routes->setDefaultController('Home');
$routes->setDefaultMethod('index');
$routes->setTranslateURIDashes(false);
$routes->set404Override();
$routes->setAutoRoute(false);
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
// $routes->get('/login', 'Auth::index', ['filter' => 'noauth']);
// $routes->post('/login', 'Auth::login');

// Filter on route group for logged in user
// $routes->group('', ['filter'=>'auth'], function ($routes){
//     $routes->get('/logout', 'Auth::logout');
//     $routes->get('/', 'Home::index');
//     $routes->get('/(:any)', 'Home::root/$1');
// });

$routes->get('/', 'Home::index');
$routes->get('faqs', 'Faqs::index');
$routes->get('about-us', 'AboutUs::index');
$routes->get('sponsorships', 'Sponsorships::index');
$routes->get('announcements', 'Announcements::index');
$routes->get('announcements/(:segment)', 'Announcements::details/$1');

// Payment routes
$routes->group('', function($routes) {
    $routes->get('payments', 'Payment::index');
    $routes->get('payments/detail/(:num)', 'Payment::detail/$1');
    $routes->post('payments/make', 'Payment::makePayment');
    $routes->get('payments/receipt/(:num)', 'Payment::downloadReceipt/$1');
});

$routes->get('dashboard', 'Dashboard::index');

// auth
$routes->get('sign-in', 'Auth::index');
$routes->post('authorize', 'Auth::authorize');
$routes->get('sign-out', 'Auth::signOut');
$routes->get('sign-up', 'Auth::signUp');
$routes->post('register', 'Auth::register');
$routes->get('forgot-password', 'Auth::forgotPassword');
$routes->post('reset-password', 'Auth::resetPassword');
// two step verification
$routes->get('two-step-verification', 'Auth::twoStepVerification');



$routes->get('sitemap.xml', 'Sitemap::index');

// submission
$routes->get('submission', 'Submission::index', ['filter' => 'noauth']);
$routes->get('submission/edit', 'Submission::edit', ['filter' => 'noauth']);

// Add this route to serve cached images
$routes->get('cached-images/(:any)', 'ImagesController::serve/$1');

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
