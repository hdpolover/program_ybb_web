<?php
/**
 * Quick Error Visibility Tool
 * Place this in your public directory and access it to see what's wrong
 */

// Force error display
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Start output
echo "<h1>🚨 Error Detector</h1>";
echo "<p>Time: " . date('Y-m-d H:i:s') . "</p>";

// Test 1: Basic PHP test
echo "<h2>1. PHP Test</h2>";
echo "PHP Version: " . phpversion() . "<br>";

// Test 2: Try to include CodeIgniter bootstrap
echo "<h2>2. CodeIgniter Bootstrap Test</h2>";
try {
    if (file_exists('../app/Config/Paths.php')) {
        echo "✅ Paths.php found<br>";
        
        // Set up basic environment
        if (!defined('FCPATH')) {
            define('FCPATH', __DIR__ . DIRECTORY_SEPARATOR);
        }
        
        // Try to load the bootstrap
        require_once '../app/Config/Paths.php';
        echo "✅ Paths loaded<br>";
        
        $paths = new Config\Paths();
        echo "✅ Paths object created<br>";
        
        // Try to load constants
        if (file_exists('../app/Config/Constants.php')) {
            require_once '../app/Config/Constants.php';
            echo "✅ Constants loaded<br>";
        }
        
        // Try composer autoload
        if (file_exists('../vendor/autoload.php')) {
            require_once '../vendor/autoload.php';
            echo "✅ Composer autoload loaded<br>";
        } else {
            echo "❌ Composer autoload not found<br>";
        }
        
        // Try basic CodeIgniter boot
        if (file_exists('../system/Boot.php')) {
            echo "✅ Boot.php found<br>";
        } else {
            echo "❌ Boot.php not found<br>";
        }
        
        echo "✅ Basic bootstrap successful!<br>";
        
    } else {
        echo "❌ ../app/Config/Paths.php not found<br>";
    }
} catch (Exception $e) {
    echo "❌ Exception: " . $e->getMessage() . "<br>";
    echo "File: " . $e->getFile() . " Line: " . $e->getLine() . "<br>";
} catch (Error $e) {
    echo "❌ Fatal Error: " . $e->getMessage() . "<br>";
    echo "File: " . $e->getFile() . " Line: " . $e->getLine() . "<br>";
}

// Test 3: Environment check
echo "<h2>3. Environment Check</h2>";
if (file_exists('../.env')) {
    echo "✅ .env file exists<br>";
} else {
    echo "⚠️ .env file not found<br>";
}

// Test 4: Directory permissions
echo "<h2>4. Directory Permissions</h2>";
$dirs = ['../writable', '../writable/logs', '../writable/cache'];
foreach ($dirs as $dir) {
    if (is_dir($dir) && is_writable($dir)) {
        echo "✅ $dir is writable<br>";
    } else {
        echo "❌ $dir is not writable or doesn't exist<br>";
    }
}

// Test 5: Try to access the main application
echo "<h2>5. Main Application Test</h2>";
try {
    // Simulate accessing index.php
    echo "Attempting to access main application...<br>";
    
    // Check if we can at least load the front controller
    if (file_exists('index.php')) {
        echo "✅ index.php exists<br>";
    } else {
        echo "❌ index.php not found<br>";
    }
    
    // Check .htaccess
    if (file_exists('.htaccess')) {
        echo "✅ .htaccess exists<br>";
    } else {
        echo "❌ .htaccess not found<br>";
    }
    
} catch (Exception $e) {
    echo "❌ Application Error: " . $e->getMessage() . "<br>";
}

echo "<hr>";
echo "<p><strong>Next Steps:</strong></p>";
echo "<ul>";
echo "<li>If you see ❌ errors above, those need to be fixed first</li>";
echo "<li>Check file permissions for writable directories</li>";
echo "<li>Ensure all CodeIgniter files are uploaded correctly</li>";
echo "<li>Check your hosting PHP version compatibility</li>";
echo "<li>Look at the server error logs for more details</li>";
echo "</ul>";

echo "<p><a href='diagnostic.php'>Run Full Diagnostic</a></p>";
?>