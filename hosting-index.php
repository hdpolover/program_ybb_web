<?php
// hosting-index.php - Use this as your main index.php file on hosting server

// Show errors temporarily for debugging (REMOVE THIS IN PRODUCTION)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Path to the front controller (this file)
define('FCPATH', __DIR__ . DIRECTORY_SEPARATOR);

// Ensure the current directory is pointing to the front controller's directory
if (getcwd() . DIRECTORY_SEPARATOR !== FCPATH) {
    chdir(FCPATH);
}

/*
 *---------------------------------------------------------------
 * BOOTSTRAP THE APPLICATION
 *---------------------------------------------------------------
 * This process sets up the path constants, loads and registers
 * our autoloader, along with Composer's, loads our constants
 * and fires up an environment-specific bootstrapping.
 */

// Location of the Paths config file.
// This is the line that might need to be changed, depending on your folder structure.
$pathsConfig = FCPATH . 'app/Config/Paths.php';

if (!file_exists($pathsConfig)) {
    exit('Paths config file not found. Check file structure.');
}

require $pathsConfig;
// ^^^ Change this line if you move your application folder

$paths = new Config\Paths();

// Validate critical paths exist
$systemPath = rtrim($paths->systemDirectory, '\\/ ') . DIRECTORY_SEPARATOR . 'bootstrap.php';
if (!file_exists($systemPath)) {
    exit('System bootstrap file not found at: ' . $systemPath);
}

$writablePath = rtrim($paths->writableDirectory, '\\/ ');
if (!is_dir($writablePath)) {
    exit('Writable directory not found: ' . $writablePath);
}

if (!is_writable($writablePath)) {
    exit('Writable directory is not writable: ' . $writablePath);
}

// Location of the framework bootstrap file.
require $systemPath;

// Load environment settings from .env files into $_SERVER and $_ENV
require_once SYSTEMPATH . 'Config/DotEnv.php';

try {
    (new CodeIgniter\Config\DotEnv(ROOTPATH))->load();
} catch (Exception $e) {
    exit('Environment file loading failed: ' . $e->getMessage());
}

/*
 * ---------------------------------------------------------------
 * LAUNCH THE APPLICATION
 * ---------------------------------------------------------------
 * Now that everything is setup, it's time to actually fire
 * up the engines and make this app do its thang.
 */

try {
    $app = Config\Services::codeigniter();
    $app->initialize();
    $context = is_cli() ? 'php-cli' : 'web';
    $app->setContext($context);

    $response = $app->run();
    $response->send();
} catch (Exception $e) {
    echo '<h1>Application Error</h1>';
    echo '<p>Error: ' . $e->getMessage() . '</p>';
    echo '<p>File: ' . $e->getFile() . '</p>';
    echo '<p>Line: ' . $e->getLine() . '</p>';
    
    // Log the error
    if (function_exists('error_log')) {
        error_log('CodeIgniter Error: ' . $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine());
    }
}