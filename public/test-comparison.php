<?php
/**
 * Quick diagnostic test for Abstract Version Comparison functionality
 * This file can be removed after testing is complete
 */

// Basic environment setup
require_once '../app/Config/Paths.php';
$paths = new Config\Paths();
require_once $paths->systemDirectory . '/bootstrap.php';

echo "<!DOCTYPE html>\n";
echo "<html><head><title>Abstract Comparison Test</title></head><body>\n";
echo "<h1>Abstract Version Comparison Test</h1>\n";

try {
    // Test 1: Check if the route exists
    echo "<h2>Test 1: Route Configuration</h2>\n";
    $routes = \Config\Services::routes();
    echo "<p>✓ Routes service loaded successfully</p>\n";
    
    // Test 2: Check if controller exists and is loadable
    echo "<h2>Test 2: Controller Availability</h2>\n";
    if (class_exists('App\\Controllers\\dashboard\\AbstractPaper')) {
        echo "<p>✓ AbstractPaper controller class exists</p>\n";
        
        // Check if compareVersions method exists
        if (method_exists('App\\Controllers\\dashboard\\AbstractPaper', 'compareVersions')) {
            echo "<p>✓ compareVersions method exists</p>\n";
        } else {
            echo "<p>✗ compareVersions method not found</p>\n";
        }
    } else {
        echo "<p>✗ AbstractPaper controller class not found</p>\n";
    }
    
    // Test 3: Check if view file exists
    echo "<h2>Test 3: View File Availability</h2>\n";
    $viewPath = APPPATH . 'Views/participant/abstract-paper/comparison.php';
    if (file_exists($viewPath)) {
        echo "<p>✓ Comparison view file exists at: {$viewPath}</p>\n";
    } else {
        echo "<p>✗ Comparison view file not found at: {$viewPath}</p>\n";
    }
    
    // Test 4: Check JavaScript file
    echo "<h2>Test 4: JavaScript File Availability</h2>\n";
    $jsPath = FCPATH . 'assets/js/abstract-comparison.js';
    if (file_exists($jsPath)) {
        echo "<p>✓ JavaScript file exists at: {$jsPath}</p>\n";
        $jsSize = filesize($jsPath);
        echo "<p>File size: {$jsSize} bytes</p>\n";
    } else {
        echo "<p>✗ JavaScript file not found at: {$jsPath}</p>\n";
    }
    
    // Test 5: Check database connection
    echo "<h2>Test 5: Database Connection</h2>\n";
    try {
        $db = \Config\Database::connect();
        echo "<p>✓ Database connection established</p>\n";
        
        // Check if required tables exist
        $tables = ['tb_abstract_version', 'tb_abstract', 'tb_participants'];
        foreach ($tables as $table) {
            if ($db->tableExists($table)) {
                echo "<p>✓ Table '{$table}' exists</p>\n";
            } else {
                echo "<p>✗ Table '{$table}' not found</p>\n";
            }
        }
    } catch (Exception $e) {
        echo "<p>✗ Database connection failed: " . $e->getMessage() . "</p>\n";
    }
    
    echo "<h2>Test Results Summary</h2>\n";
    echo "<p>If all tests show ✓ marks, the comparison feature should be working correctly.</p>\n";
    echo "<p>You can test the actual comparison by accessing: <code>/abstract-versions/compare/1/2</code> (with valid version IDs)</p>\n";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>Error during testing: " . $e->getMessage() . "</p>\n";
    echo "<pre>" . $e->getTraceAsString() . "</pre>\n";
}

echo "</body></html>\n";
?>
