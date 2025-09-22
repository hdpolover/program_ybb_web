<?php
/**
 * Simple debug test to check API configuration and data availability
 * Access via: http://localhost:8080/debug-config.php
 */

echo '<h1>Configuration Debug</h1>';

// Check environment
echo '<h3>Environment Information:</h3>';
echo '<p><strong>ENVIRONMENT:</strong> ' . (defined('ENVIRONMENT') ? ENVIRONMENT : 'Not defined') . '</p>';

// Check API URLs
echo '<h3>API Configuration:</h3>';
echo '<p><strong>DEV_BASE_API_URL:</strong> ' . (defined('DEV_BASE_API_URL') ? DEV_BASE_API_URL : 'Not defined') . '</p>';
echo '<p><strong>BASE_API_URL:</strong> ' . (defined('BASE_API_URL') ? BASE_API_URL : 'Not defined') . '</p>';

// Check if we can access CodeIgniter functions
echo '<h3>CodeIgniter Status:</h3>';
try {
    require_once __DIR__ . '/../app/Config/Paths.php';
    require_once SYSTEMPATH . 'bootstrap.php';
    
    echo '<p><strong>CodeIgniter:</strong> Loaded successfully</p>';
    
    // Check if we can get base URL
    if (function_exists('base_url')) {
        echo '<p><strong>Base URL:</strong> ' . base_url() . '</p>';
    } else {
        echo '<p><strong>Base URL:</strong> Function not available</p>';
    }
    
} catch (Exception $e) {
    echo '<p><strong>CodeIgniter Error:</strong> ' . $e->getMessage() . '</p>';
}

echo '<h3>Server Information:</h3>';
echo '<p><strong>PHP Version:</strong> ' . PHP_VERSION . '</p>';
echo '<p><strong>Server:</strong> ' . ($_SERVER['SERVER_SOFTWARE'] ?? 'Unknown') . '</p>';
echo '<p><strong>Document Root:</strong> ' . ($_SERVER['DOCUMENT_ROOT'] ?? 'Unknown') . '</p>';

echo '<hr>';
echo '<p><a href="/">← Back to Home</a></p>';
?>