<?php
// Simple test script to verify basic PHP functionality and routing
require_once __DIR__ . '/../vendor/autoload.php';

// Test basic CodeIgniter initialization
try {
    echo "<h1>Paper Upload System Test</h1>";
    
    // Test 1: Check if CodeIgniter can initialize
    echo "<h3>Test 1: CodeIgniter Initialization</h3>";
    if (class_exists('CodeIgniter\CodeIgniter')) {
        echo "✅ CodeIgniter classes available<br>";
    } else {
        echo "❌ CodeIgniter classes not available<br>";
    }
    
    // Test 2: Check if controller file exists
    echo "<h3>Test 2: Controller File Check</h3>";
    $controllerPath = __DIR__ . '/../app/Controllers/dashboard/AbstractPaper.php';
    if (file_exists($controllerPath)) {
        echo "✅ AbstractPaper controller file exists<br>";
        
        // Check if the file can be parsed without syntax errors
        $code = file_get_contents($controllerPath);
        if (php_check_syntax($controllerPath)) {
            echo "✅ Controller syntax is valid<br>";
        } else {
            echo "❌ Controller has syntax errors<br>";
        }
    } else {
        echo "❌ AbstractPaper controller file not found<br>";
    }
    
    // Test 3: Check routes file
    echo "<h3>Test 3: Routes File Check</h3>";
    $routesPath = __DIR__ . '/../app/Config/Routes.php';
    if (file_exists($routesPath)) {
        echo "✅ Routes file exists<br>";
        
        if (php_check_syntax($routesPath)) {
            echo "✅ Routes syntax is valid<br>";
        } else {
            echo "❌ Routes has syntax errors<br>";
        }
    } else {
        echo "❌ Routes file not found<br>";
    }
    
    // Test 4: Check JavaScript file
    echo "<h3>Test 4: JavaScript File Check</h3>";
    $jsPath = __DIR__ . '/assets/js/paper-upload-handler.js';
    if (file_exists($jsPath)) {
        echo "✅ Paper upload handler JS file exists<br>";
    } else {
        echo "❌ Paper upload handler JS file not found<br>";
    }
    
    // Test 5: Check view files
    echo "<h3>Test 5: View Files Check</h3>";
    $viewFiles = [
        'abstract-view.php' => __DIR__ . '/../app/Views/participant/abstract-paper/components/abstract-view.php',
        'paper-upload-modals.php' => __DIR__ . '/../app/Views/participant/abstract-paper/components/paper-upload-modals.php',
        'abstract-paper-card.php' => __DIR__ . '/../app/Views/participant/abstract-paper/components/abstract-paper-card.php'
    ];
    
    foreach ($viewFiles as $name => $path) {
        if (file_exists($path)) {
            echo "✅ $name exists<br>";
        } else {
            echo "❌ $name not found<br>";
        }
    }
    
    echo "<h3>Test Complete</h3>";
    echo "<p>If all tests pass, the paper upload system should be working correctly.</p>";
    echo "<p><a href='test-paper-integration.html'>Test the paper upload interface</a></p>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>";
}

// Check if php_check_syntax function exists (it was removed in PHP 5.0.5)
if (!function_exists('php_check_syntax')) {
    function php_check_syntax($filename) {
        // Alternative syntax check using php -l
        $output = shell_exec("php -l " . escapeshellarg($filename) . " 2>&1");
        return strpos($output, 'No syntax errors detected') !== false;
    }
}
?>
