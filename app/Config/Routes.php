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
$routes->set404Override('App\Controllers\ErrorController::error404');
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

// Maintenance route
$routes->get('maintenance', 'landing\Maintenance::index');

// AJAX Error Handler routes
$routes->get('ajax/timeout', 'AjaxHandler::timeout');
$routes->get('ajax/error/(:num)', 'AjaxHandler::error/$1');
$routes->post('ajax/timeout', 'AjaxHandler::timeout');
$routes->post('ajax/error/(:num)', 'AjaxHandler::error/$1');

$routes->get('/', 'landing\Home::index');
$routes->get('programs', 'landing\Programs::index');
$routes->get('programs/(:any)/details', 'landing\Programs::detail/$1');
$routes->get('insights', 'landing\Insights::index');
$routes->get('partners-sponsors', 'landing\PartnersSponsors::index');
$routes->get('announcements', 'landing\Announcements::index');
$routes->get('announcements/(:any)', 'landing\Announcements::detail/$1');

// Legacy routes - can be removed if not needed
// $routes->get('faqs', 'Faqs::index');
// $routes->get('about-us', 'AboutUs::index');
// $routes->get('sponsorships', 'Sponsorships::index');
// $routes->get('announcements', 'Announcements::index');
// $routes->get('announcements/(:segment)', 'Announcements::details/$1');

// Authentication routes
$routes->get('sign-in', 'Auth::index', ['filter' => 'noauth']);
$routes->get('sign-up', 'Auth::signUp', ['filter' => 'noauth']);
$routes->post('authorize', 'Auth::authorize', ['filter' => 'noauth']);
// register
$routes->post('register', 'Auth::register', ['filter' => 'noauth']);
$routes->get('sign-out', 'Auth::signOut');
$routes->get('forgot-password', 'Auth::forgotPassword', ['filter' => 'noauth']);
$routes->get('reset-password', 'Auth::resetPassword', ['filter' => 'noauth']);
// verify email
$routes->get('verify-email', 'Auth::verifyEmail', ['filter' => 'noauth']);
// set new password
$routes->post('set-new-password', 'Auth::setNewPassword', ['filter' => 'noauth']);
$routes->post('send-reset-link', 'Auth::sendResetLink', ['filter' => 'noauth']);
$routes->get('two-step-verification', 'Auth::twoStepVerification', ['filter' => 'noauth']);

// Protected routes for logged in users
$routes->group('', ['filter' => 'auth'], function ($routes) {
    $routes->get('dashboard', 'dashboard\Dashboard::index');

    // announcements
    $routes->get('dashboard-announcements', 'dashboard\Announcements::index');
    $routes->get('dashboard-announcements/(:num)', 'dashboard\Announcements::details/$1');
    $routes->get('dashboard-announcements/(:num)/details', 'dashboard\Announcements::details/$1');

    // settings
    $routes->get('settings', 'dashboard\Settings::index');

    // submission
    $routes->get('submission', 'dashboard\Submission::index');
    $routes->get('submission/edit', 'dashboard\Submission::edit');
    // New submission form handling endpoints
    $routes->post('submission/personal/(:num)/update', 'dashboard\Submission::updatePersonal/$1');
    $routes->post('submission/professional/(:num)/update', 'dashboard\Submission::updateProfessional/$1');
    $routes->post('submission/entry/(:num)/update', 'dashboard\Submission::updateEntry/$1');
    $routes->post('submission/miscs/(:num)/update', 'dashboard\Submission::updateMisc/$1');
    $routes->post('submission/validateAmbassadorCode', 'dashboard\Submission::validateAmbassadorCode');
    $routes->post('submission/submit', 'dashboard\Submission::submitForm');

    // payment
    $routes->get('payments', 'dashboard\Payments::index');
    $routes->get('payments/detail/(:num)', 'dashboard\Payments::detail/$1');
    $routes->post('payments/make', 'dashboard\Payments::makePayment');
    // documents
    $routes->get('documents/program', 'dashboard\Documents::index');
    $routes->get('documents/program/details/(:num)', 'dashboard\Documents::details/$1');
    $routes->get('documents/certificates', 'dashboard\Documents::certificates');
    // generate loa
    $routes->get('documents/generate-loa/(:num)/(:num)', 'dashboard\Documents::generateLoa/$1/$2');
    // ambassadors
    $routes->group('ambassadors', function ($routes) {
        $routes->get('sign-in', 'Auth::ambassadorSignIn', ['filter' => 'noauth']);
        $routes->get('referrals', 'dashboard\Ambassadors::referrals');
        $routes->get('resources', 'dashboard\Ambassadors::resources');
        $routes->post('generate-link', 'dashboard\Ambassadors::generateReferralLink');
    });
});

$routes->get('sitemap.xml', 'Sitemap::index');
$routes->get('sitemap', 'landing\Sitemap::sitemap'); // HTML sitemap for users

// Add this route to serve cached images
$routes->get('cached-images/(:any)', 'ImagesController::serve/$1');

// Topbar routes
$routes->get('topbar/getTopbarData', 'TopbarController::getTopbarData');
$routes->get('topbar/setProgram/(:num)', 'TopbarController::setProgram/$1');
$routes->post('topbar/setProgram/(:num)', 'TopbarController::setProgram/$1');
$routes->post('topbar/(:num)/create', 'TopbarController::registerForProgram/$1');

// Popup notification route for registration toasts
$routes->get('popup-notification/getRecentRegistrations', 'PopupNotification::getRecentRegistrations');
$routes->post('topbar/updateParticipantSession', 'TopbarController::updateParticipantSession');

// Public receipt download route (no authentication required)
$routes->get('payments/receipt/(:num)', 'dashboard\Payments::downloadReceipt/$1');

// API route for getting current user data
$routes->get('api/user/current', 'TopbarController::getCurrentUser');

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
