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

// Sitemap route
$routes->get('sitemap.xml', 'SitemapController::index');

// AJAX Error Handler routes
$routes->get('ajax/timeout', 'AjaxHandler::timeout');
$routes->get('ajax/error/(:num)', 'AjaxHandler::error/$1');
$routes->post('ajax/timeout', 'AjaxHandler::timeout');
$routes->post('ajax/error/(:num)', 'AjaxHandler::error/$1');

$routes->get('/', 'landing\Home::index');
$routes->get('programs', 'landing\Programs::index');
$routes->get('programs/(:any)/details', 'landing\Programs::detail/$1');
$routes->get('programs/(:any)', 'landing\Programs::detail/$1');
$routes->get('insights', 'landing\Insights::index');
$routes->get('gallery', 'landing\Gallery::index');
$routes->get('partners-sponsors', 'landing\PartnersSponsors::index');
$routes->get('announcements', 'landing\Announcements::index');
$routes->get('announcements/(:any)', 'landing\Announcements::detail/$1');
$routes->get('contact', 'landing\Contact::index');
$routes->post('contact', 'landing\Contact::submit');

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
// Authentication API endpoint (no /api prefix)
// Authentication API routes (no /api prefix but following API patterns)
$routes->post('auth/sign-in', 'Auth::authApiSignIn', ['filter' => 'noauth']);
$routes->get('auth/profile', 'Auth::authProfile', ['filter' => 'noauth']);
$routes->post('auth/refresh', 'Auth::authRefresh', ['filter' => 'noauth']);
// register
$routes->post('register', 'Auth::register', ['filter' => 'noauth']);
$routes->get('sign-out', 'Auth::signOut');
$routes->get('forgot-password', 'Auth::forgotPassword', ['filter' => 'noauth']);
$routes->get('reset-password', 'Auth::resetPassword', ['filter' => 'noauth']);
// verify email
$routes->get('verify-email', 'Auth::verifyEmail', ['filter' => 'noauth']);
// resend verification email
$routes->post('resend-verification', 'Auth::resendVerification', ['filter' => 'noauth']);
// set new password
$routes->post('set-new-password', 'Auth::setNewPassword', ['filter' => 'noauth']);
$routes->post('send-reset-link', 'Auth::sendResetLink', ['filter' => 'noauth']);
$routes->get('test-forgot-password-api', 'Auth::testForgotPasswordAPI', ['filter' => 'noauth']);
$routes->get('two-step-verification', 'Auth::twoStepVerification', ['filter' => 'noauth']);

// Ambassador authentication routes (must be outside protected group)
$routes->get('ambassadors/sign-in', 'Auth::ambassadorSignIn', ['filter' => 'noauth']);
$routes->post('ambassadors/authorize', 'Auth::authorizeAmbassador', ['filter' => 'noauth']);

// Protected routes for logged in users
$routes->group('', ['filter' => 'auth'], function ($routes) {    // Abstract API endpoints
    $routes->post('api/abstracts/(:num)/save-version', 'AjaxHandler::saveAbstractVersion/$1');
    $routes->get('api/abstracts/versions/compare', 'AjaxHandler::compareAbstractVersions');
    $routes->get('api/test/abstract-version-creation', 'AjaxHandler::testAbstractVersionCreation'); // Debug endpoint

    $routes->get('dashboard', 'dashboard\Dashboard::index');
    $routes->post('dashboard/switch-category', 'dashboard\Dashboard::switchCategory');

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
    $routes->post('submission/submit', 'dashboard\Submission::submitForm');    // abstract paper
    $routes->get('abstract-paper', 'dashboard\AbstractPaper::index');
    $routes->get('abstract-paper/create', 'dashboard\AbstractPaper::create');
    $routes->get('abstract-paper/view/(:num)', 'dashboard\AbstractPaper::view/$1'); // Detail view
    $routes->get('abstract-paper/edit/(:num)', 'dashboard\AbstractPaper::edit/$1');
    $routes->get('abstract-paper/edit/(:num)/(:num)', 'dashboard\AbstractPaper::edit/$1/$2'); // Direct version access    
    $routes->get('abstract-paper/edit/(:num)/version/(:num)', 'dashboard\AbstractPaper::edit/$1/$2'); // With 'version' in URL
    $routes->get('abstract-paper/compare/(:num)/(:num)', 'dashboard\AbstractPaper::compareVersions/$1/$2'); // Version comparison
    $routes->post('abstract-paper/save', 'dashboard\AbstractPaper::save');
    $routes->post('abstract-paper/update/(:num)', 'dashboard\AbstractPaper::update/$1');
    $routes->post('abstract-paper/add-author', 'dashboard\AbstractPaper::addAuthor');
    $routes->post('abstract-paper/update-author', 'dashboard\AbstractPaper::updateAuthor');
    $routes->post('abstract-paper/delete-author', 'dashboard\AbstractPaper::deleteAuthor');    $routes->post('abstract-paper/validate-author/(:num)', 'dashboard\AbstractPaper::validateAuthor/$1'); // Validate author email
    $routes->post('abstract-paper/search-participant', 'dashboard\AbstractPaper::searchParticipant'); // Search for registered participants
    $routes->get('abstract-paper/search-participant', 'dashboard\AbstractPaper::searchParticipant'); // Search for registered participants (GET)
    
    // Paper upload routes
    $routes->post('abstract-paper/upload-paper/(:num)', 'dashboard\AbstractPaper::uploadPaper/$1'); // Upload paper
    $routes->post('abstract-paper/update-paper/(:num)', 'dashboard\AbstractPaper::updatePaper/$1'); // Update paper
    $routes->post('abstract-paper/replace-paper/(:num)', 'dashboard\AbstractPaper::replacePaper/$1'); // Replace paper
    $routes->delete('abstract-paper/delete-paper/(:num)', 'dashboard\AbstractPaper::deletePaper/$1'); // Delete paper
    $routes->post('abstract-paper/delete-paper/(:num)', 'dashboard\AbstractPaper::deletePaper/$1'); // Delete paper (POST with _method)
    $routes->get('abstract-paper/download-paper/(:num)', 'dashboard\AbstractPaper::downloadPaper/$1'); // Download paper

    // Upload Agreement Letter
    $routes->post('agreement_letter/upload', 'dashboard\Documents::addDocument');

    // payment
    $routes->get('payments', 'dashboard\Payments::index');
    $routes->get('payments/detail/(:num)', 'dashboard\Payments::detail/$1');
    $routes->post('payments/make', 'dashboard\Payments::makePayment');
    $routes->post('payments/cancel/(:num)', 'dashboard\Payments::cancelPayment/$1');
    $routes->get('payments/test-api', 'dashboard\Payments::testPaymentAPI'); // Temporary debug endpoint
    $routes->get('payments/debug-detail/(:num)/(:num)', 'dashboard\Payments::debugProgramPaymentDetail/$1/$2'); // Debug program payment detail
    $routes->get('payments/debug-detail/(:num)', 'dashboard\Payments::debugProgramPaymentDetail/$1'); // Debug with default participant
    $routes->get('payments/debug-detail', 'dashboard\Payments::debugProgramPaymentDetail'); // Debug with defaults
    $routes->get('payments/debug-modal/(:num)', 'dashboard\Payments::debugModalData/$1'); // Debug modal data
    $routes->get('payments/debug-modal', 'dashboard\Payments::debugModalData'); // Debug modal data with defaults
    $routes->get('payments/debug-currency/(:num)', 'dashboard\Payments::debugCurrencyConversion/$1'); // Debug currency conversion
    $routes->get('payments/debug-currency', 'dashboard\Payments::debugCurrencyConversion'); // Debug currency with defaults
    $routes->get('debug/session', 'DebugController::sessionData'); // Debug session endpoint
    
    // Payment API endpoints (no /api prefix)
    $routes->get('payments/participant/(:num)', 'dashboard\Payments::getParticipantPayments/$1');
    $routes->get('payments/program-payment/(:num)/participant/(:num)', 'dashboard\Payments::getPaymentsByProgramPayment/$1/$2');
    $routes->get('payments/get/(:num)', 'dashboard\Payments::getPaymentDetails/$1');
    // documents
    $routes->get('documents/program', 'dashboard\Documents::index');
    $routes->get('documents/program/details/(:num)', 'dashboard\Documents::details/$1');
    $routes->get('documents/certificates', 'dashboard\Documents::certificates');
    $routes->post('api/certificates/generate', 'dashboard\Documents::generateCertificate');
    // generate loa
    $routes->get('documents/generate-loa/(:num)/(:num)', 'dashboard\Documents::generateLoa/$1/$2');

    // ambassadors
    $routes->group('ambassadors', function ($routes) {
        $routes->get('referrals', 'dashboard\Ambassadors::referrals');
        $routes->get('resources', 'dashboard\Ambassadors::resources');
        $routes->post('generate-link', 'dashboard\Ambassadors::generateReferralLink');

        // dashboard ambassador routes
        $routes->get('dashboard', 'ambassador\Dashboard::index');
        $routes->get('referred-participants', 'ambassador\ReferredParticipants::index');
        $routes->get('profile', 'ambassador\Profile::index');
        $routes->get('payments', 'ambassador\Payments::index');
        $routes->get('performance', 'ambassador\Performance::index');
        
        // dashboard API endpoints (no /api prefix)
        $routes->get('dashboard/overview', 'ambassador\Dashboard::overview');
        $routes->get('dashboard/participants', 'ambassador\ReferredParticipants::participants');
        $routes->get('dashboard/participant-payment/(:num)', 'ambassador\ReferredParticipants::participantPayment/$1');
        $routes->get('dashboard/payments', 'ambassador\Payments::payments');
        $routes->get('dashboard/performance', 'ambassador\Performance::performance');
        
        // payment analytics data endpoint  
        $routes->get('payments/data', 'ambassador\Payments::getData');
    });
});

// $routes->get('sitemap.xml', 'Sitemap::index'); // Removed duplicate route
$routes->get('sitemap', 'landing\Sitemap::sitemap'); // HTML sitemap for users

// Add this route to serve cached images
$routes->get('cached-images/(:any)', 'ImagesController::serve/$1');

// Topbar routes
$routes->get('topbar/getTopbarData', 'TopbarController::getTopbarData');
$routes->get('topbar/setProgram/(:num)', 'TopbarController::setProgram/$1');
$routes->post('topbar/setProgram/(:num)', 'TopbarController::setProgram/$1');
$routes->post('topbar/(:num)/create', 'TopbarController::registerForProgram/$1');
$routes->get('server-time', 'TopbarController::getServerTime');
$routes->get('debug-timezone', 'TopbarController::debugTimezone');

// Popup notification route for registration toasts
$routes->get('popup-notification/getRecentRegistrations', 'PopupNotification::getRecentRegistrations');
$routes->post('topbar/updateParticipantSession', 'TopbarController::updateParticipantSession');

// Public receipt download route (no authentication required)
$routes->get('payments/receipt/(:num)', 'dashboard\Payments::downloadReceipt/$1');

// Debug routes for receipt development (remove in production)
$routes->get('payments/debugProgramAPI/(:num)', 'dashboard\Payments::debugProgramAPI/$1');
$routes->get('payments/debugProgramAPI', 'dashboard\Payments::debugProgramAPI');
$routes->get('payments/testReceiptHTML/(:num)', 'dashboard\Payments::testReceiptHTML/$1');

// Production Debug Routes (secured with key and IP)
$routes->get('prod-debug', 'ProductionDebugController::index');
$routes->get('prod-debug/logs', 'ProductionDebugController::logs');
$routes->get('prod-debug/clear-cache', 'ProductionDebugController::clearCache');
$routes->get('prod-debug/test-db', 'ProductionDebugController::testDb');
$routes->get('prod-debug/php-info', 'ProductionDebugController::phpInfo');

// API route for getting current user data
$routes->get('api/user/current', 'TopbarController::getCurrentUser');

// Ambassador Admin API endpoints
$routes->group('api/ambassadors', function($routes) {
    $routes->get('/', 'Api\AmbassadorsApiController::index');
    $routes->get('(:num)', 'Api\AmbassadorsApiController::show/$1');
    $routes->get('(:num)/referrals', 'Api\AmbassadorsApiController::referrals/$1');
    $routes->get('(:num)/generate-link', 'Api\AmbassadorsApiController::generateLink/$1');
    $routes->get('check-query', 'Api\AmbassadorsApiController::checkQuery');
});

// Cache management routes (admin only in production)
$routes->get('cache/clear', 'CacheController::clearAll');
$routes->get('cache/clear/(:any)', 'CacheController::clearPattern/$1');
$routes->get('cache/stats', 'CacheController::stats');

// Chat Widget API routes (public endpoints)
$routes->group('api/chat', function($routes) {
    $routes->post('/', 'ChatController::sendMessage');
    $routes->post('send', 'ChatController::sendMessage');
    $routes->get('history', 'ChatController::getChatHistory');
    $routes->get('status', 'ChatController::getStatus');
    $routes->post('typing', 'ChatController::typing');
});

// Countries API routes (public endpoints)
$routes->group('api/countries', function($routes) {
    $routes->get('/', 'Api\CountriesApiController::index');
    $routes->get('search', 'Api\CountriesApiController::search');
    $routes->get('codes', 'Api\CountriesApiController::codes');
    $routes->get('by-name/(:segment)', 'Api\CountriesApiController::byName/$1');
    $routes->get('(:num)', 'Api\CountriesApiController::show/$1');
});

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
