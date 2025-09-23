<?php
/**
 * Production Diagnostic Tool - API-Based Architecture
 * Upload this file to your hosting public directory and access via browser
 */

// Enable error display for diagnostic purposes
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "<!DOCTYPE html>";
echo "<html><head><title>CodeIgniter Production Diagnostic</title>";
echo "<style>
body { font-family: Arial, sans-serif; margin: 20px; }
.success { color: green; padding: 5px; }
.error { color: red; padding: 5px; }
.warning { color: orange; padding: 5px; }
.section { margin: 20px 0; border: 1px solid #ddd; padding: 15px; }
pre { background: #f5f5f5; padding: 10px; overflow-x: auto; }
</style></head><body>";

echo "<h1>🔧 CodeIgniter Production Diagnostic Tool</h1>";
echo "<p><strong>Generated:</strong> " . date('Y-m-d H:i:s') . "</p>";

// Basic PHP Environment
echo "<div class='section'>";
echo "<h2>PHP Environment</h2>";
echo "PHP Version: " . phpversion() . "<br>";
echo "Server Software: " . ($_SERVER['SERVER_SOFTWARE'] ?? 'Unknown') . "<br>";
echo "Document Root: " . ($_SERVER['DOCUMENT_ROOT'] ?? 'Unknown') . "<br>";
echo "Script Path: " . __FILE__ . "<br>";
echo "</div>";

// Check critical PHP extensions
echo "<div class='section'>";
echo "<h2>Required PHP Extensions</h2>";
$extensions = ['curl', 'gd', 'json', 'mbstring', 'openssl'];
foreach ($extensions as $ext) {
    if (extension_loaded($ext)) {
        echo "<div class='success'>✅ $ext: Loaded</div>";
    } else {
        echo "<div class='error'>❌ $ext: Missing</div>";
    }
}
echo "</div>";

// File permissions check
echo "<div class='section'>";
echo "<h2>File Permissions</h2>";
$paths = [
    '../app' => 'App directory',
    '../writable' => 'Writable directory',
    '../writable/logs' => 'Logs directory',
    '../writable/cache' => 'Cache directory',
    '../system' => 'System directory'
];

foreach ($paths as $path => $description) {
    if (file_exists($path)) {
        $perms = substr(sprintf('%o', fileperms($path)), -4);
        if (is_readable($path)) {
            echo "<div class='success'>✅ $description: Readable (Permissions: $perms)</div>";
        } else {
            echo "<div class='error'>❌ $description: Not readable (Permissions: $perms)</div>";
        }
        
        if (strpos($path, 'writable') !== false && !is_writable($path)) {
            echo "<div class='error'>❌ $description: Not writable</div>";
        }
    } else {
        echo "<div class='error'>❌ $description: Not found at $path</div>";
    }
}
echo "</div>";

// Test API connectivity
echo "<div class='section'>";
echo "<h2>API Connectivity</h2>";
try {
    // Test localhost API (development)
    $localContext = stream_context_create([
        'http' => [
            'timeout' => 5,
            'method' => 'GET'
        ]
    ]);
    
    $localApiTest = @file_get_contents('http://localhost:8080/api', false, $localContext);
    if ($localApiTest !== false) {
        echo "<div class='success'>✅ Local API (localhost:8080): Accessible</div>";
    } else {
        echo "<div class='warning'>⚠️ Local API (localhost:8080): Not accessible (normal in production)</div>";
    }
    
    // Test production API
    $prodContext = stream_context_create([
        'http' => [
            'timeout' => 10,
            'method' => 'GET'
        ]
    ]);
    
    $prodApiTest = @file_get_contents('https://admin.ybbfoundation.com/api', false, $prodContext);
    if ($prodApiTest !== false) {
        echo "<div class='success'>✅ Production API (admin.ybbfoundation.com): Accessible</div>";
    } else {
        echo "<div class='error'>❌ Production API (admin.ybbfoundation.com): Not accessible</div>";
    }
    
} catch (Exception $e) {
    echo "<div class='error'>❌ API connectivity error: " . $e->getMessage() . "</div>";
}
echo "</div>";

// Test CodeIgniter bootstrap
echo "<div class='section'>";
echo "<h2>CodeIgniter Bootstrap Test</h2>";
try {
    // Try to load CodeIgniter minimally
    if (file_exists('../app/Config/App.php')) {
        echo "<div class='success'>✅ App config found</div>";
        
        // Check if we can load the autoloader
        if (file_exists('../vendor/autoload.php')) {
            echo "<div class='success'>✅ Composer autoloader found</div>";
        } else {
            echo "<div class='error'>❌ Composer autoloader missing</div>";
        }
        
        // Check system files
        if (file_exists('../system/CodeIgniter.php')) {
            echo "<div class='success'>✅ CodeIgniter system files found</div>";
        } else {
            echo "<div class='error'>❌ CodeIgniter system files missing</div>";
        }
        
    } else {
        echo "<div class='error'>❌ CodeIgniter app config not found</div>";
    }
    
} catch (Exception $e) {
    echo "<div class='error'>❌ Bootstrap error: " . $e->getMessage() . "</div>";
}
echo "</div>";

// Check for .htaccess
echo "<div class='section'>";
echo "<h2>Web Server Configuration</h2>";
if (file_exists('.htaccess')) {
    echo "<div class='success'>✅ .htaccess file found</div>";
    echo "<pre>" . htmlspecialchars(file_get_contents('.htaccess')) . "</pre>";
} else {
    echo "<div class='error'>❌ .htaccess file missing</div>";
}
echo "</div>";

// Check error logs
echo "<div class='section'>";
echo "<h2>Recent Error Logs</h2>";
$logPaths = [
    '../writable/logs',
    '/var/log/apache2/error.log',
    '/var/log/nginx/error.log'
];

$foundLogs = false;
foreach ($logPaths as $logPath) {
    if (is_dir($logPath)) {
        $files = glob($logPath . '/*.log');
        foreach ($files as $file) {
            if (is_readable($file)) {
                $foundLogs = true;
                echo "<h4>Log: " . basename($file) . "</h4>";
                $lines = file($file);
                $recentLines = array_slice($lines, -10);
                echo "<pre>" . htmlspecialchars(implode('', $recentLines)) . "</pre>";
            }
        }
    } elseif (is_readable($logPath)) {
        $foundLogs = true;
        echo "<h4>System Log: " . basename($logPath) . "</h4>";
        $lines = file($logPath);
        $recentLines = array_slice($lines, -10);
        echo "<pre>" . htmlspecialchars(implode('', $recentLines)) . "</pre>";
    }
}

if (!$foundLogs) {
    echo "<div class='warning'>⚠️ No accessible error logs found</div>";
}
echo "</div>";

// Memory and performance info
echo "<div class='section'>";
echo "<h2>System Resources</h2>";
echo "Memory Limit: " . ini_get('memory_limit') . "<br>";
echo "Max Execution Time: " . ini_get('max_execution_time') . "s<br>";
echo "Upload Max Filesize: " . ini_get('upload_max_filesize') . "<br>";
echo "Post Max Size: " . ini_get('post_max_size') . "<br>";
echo "Current Memory Usage: " . round(memory_get_usage(true) / 1024 / 1024, 2) . " MB<br>";
echo "</div>";

echo "<div class='section'>";
echo "<h2>🎯 Summary</h2>";
echo "<p><strong>Most Common 500 Error Causes:</strong></p>";
echo "<ol>";
echo "<li>File permissions on writable/ directory (fix: chmod -R 777 writable/)</li>";
echo "<li>Missing .htaccess file</li>";
echo "<li>PHP version incompatibility</li>";
echo "<li>Missing Composer dependencies</li>";
echo "<li>API endpoint accessibility issues</li>";
echo "</ol>";
echo "<p><strong>Next Steps:</strong> Fix any issues marked with ❌ above.</p>";
echo "</div>";

echo "</body></html>";
?>