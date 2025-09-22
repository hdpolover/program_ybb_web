<?php
/**
 * Server Diagnostic Script
 * Upload this file to your public folder and access it via browser
 * URL: https://istanbulyouthsummit.com/debug.php
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Server Diagnostic Report</h1>";
echo "<p>Generated on: " . date('Y-m-d H:i:s') . "</p>";

// 1. PHP Version Check
echo "<h2>1. PHP Information</h2>";
echo "<p><strong>PHP Version:</strong> " . phpversion() . "</p>";
echo "<p><strong>Required:</strong> 7.4 or higher</p>";
echo "<p><strong>Status:</strong> " . (version_compare(phpversion(), '7.4.0', '>=') ? '✅ OK' : '❌ UPGRADE NEEDED') . "</p>";

// 2. Required Extensions
echo "<h2>2. Required PHP Extensions</h2>";
$requiredExtensions = [
    'mysqli', 'mysqlnd', 'curl', 'json', 'mbstring', 'xml', 
    'openssl', 'tokenizer', 'zip', 'fileinfo', 'gd'
];

foreach ($requiredExtensions as $ext) {
    $loaded = extension_loaded($ext);
    echo "<p><strong>{$ext}:</strong> " . ($loaded ? '✅ Loaded' : '❌ Missing') . "</p>";
}

// 3. File Permissions
echo "<h2>3. File Permissions</h2>";
$pathsToCheck = [
    '../writable' => '../writable',
    '../writable/cache' => '../writable/cache',
    '../writable/logs' => '../writable/logs',
    '../writable/session' => '../writable/session',
    '../writable/uploads' => '../writable/uploads'
];

foreach ($pathsToCheck as $path => $display) {
    if (file_exists($path)) {
        $perms = substr(sprintf('%o', fileperms($path)), -4);
        $writable = is_writable($path);
        echo "<p><strong>{$display}:</strong> {$perms} " . ($writable ? '✅ Writable' : '❌ Not Writable') . "</p>";
    } else {
        echo "<p><strong>{$display}:</strong> ❌ Directory Missing</p>";
    }
}

// 4. Environment Check
echo "<h2>4. Environment Configuration</h2>";
echo "<p><strong>ENVIRONMENT constant:</strong> " . (defined('ENVIRONMENT') ? ENVIRONMENT : 'Not defined') . "</p>";

// Check for .env file
$envExists = file_exists('../.env');
echo "<p><strong>.env file:</strong> " . ($envExists ? '✅ Exists' : '❌ Missing') . "</p>";

// 5. Database Connection Test
echo "<h2>5. Database Connection</h2>";
try {
    // Try to include CodeIgniter bootstrap
    if (file_exists('../app/Config/Database.php')) {
        echo "<p><strong>Database config:</strong> ✅ Found</p>";
        
        // Basic database connection test
        $hostname = $_ENV['database.default.hostname'] ?? 'localhost';
        $username = $_ENV['database.default.username'] ?? '';
        $password = $_ENV['database.default.password'] ?? '';
        $database = $_ENV['database.default.database'] ?? '';
        
        if ($username && $database) {
            try {
                $connection = new mysqli($hostname, $username, $password, $database);
                if ($connection->connect_error) {
                    echo "<p><strong>Database connection:</strong> ❌ Failed - " . $connection->connect_error . "</p>";
                } else {
                    echo "<p><strong>Database connection:</strong> ✅ Successful</p>";
                    $connection->close();
                }
            } catch (Exception $e) {
                echo "<p><strong>Database connection:</strong> ❌ Error - " . $e->getMessage() . "</p>";
            }
        } else {
            echo "<p><strong>Database connection:</strong> ⚠️ No credentials configured</p>";
        }
    } else {
        echo "<p><strong>Database config:</strong> ❌ Missing</p>";
    }
} catch (Exception $e) {
    echo "<p><strong>Database test:</strong> ❌ Error - " . $e->getMessage() . "</p>";
}

// 6. CodeIgniter Files
echo "<h2>6. CodeIgniter Installation</h2>";
$ciFiles = [
    '../system/CodeIgniter.php' => 'CodeIgniter Core',
    '../app/Config/App.php' => 'App Config',
    '../app/Config/Routes.php' => 'Routes Config',
    '../vendor/autoload.php' => 'Composer Autoloader'
];

foreach ($ciFiles as $file => $name) {
    $exists = file_exists($file);
    echo "<p><strong>{$name}:</strong> " . ($exists ? '✅ Found' : '❌ Missing') . "</p>";
}

// 7. Server Information
echo "<h2>7. Server Information</h2>";
echo "<p><strong>Server Software:</strong> " . ($_SERVER['SERVER_SOFTWARE'] ?? 'Unknown') . "</p>";
echo "<p><strong>Document Root:</strong> " . ($_SERVER['DOCUMENT_ROOT'] ?? 'Unknown') . "</p>";
echo "<p><strong>Script Filename:</strong> " . ($_SERVER['SCRIPT_FILENAME'] ?? 'Unknown') . "</p>";

// 8. Error Log Check
echo "<h2>8. Recent Error Logs</h2>";
$errorLogPaths = [
    '../writable/logs/',
    '/var/log/apache2/',
    '/var/log/nginx/',
    ini_get('error_log')
];

$foundErrors = false;
foreach ($errorLogPaths as $logPath) {
    if ($logPath && file_exists($logPath)) {
        if (is_dir($logPath)) {
            $files = glob($logPath . '*.log');
            foreach ($files as $file) {
                if (is_readable($file)) {
                    $content = file_get_contents($file);
                    $recentLines = array_slice(explode("\n", $content), -10);
                    if (!empty(array_filter($recentLines))) {
                        echo "<h3>Recent entries from: {$file}</h3>";
                        echo "<pre>" . htmlspecialchars(implode("\n", $recentLines)) . "</pre>";
                        $foundErrors = true;
                    }
                }
            }
        } elseif (is_readable($logPath)) {
            $content = file_get_contents($logPath);
            $recentLines = array_slice(explode("\n", $content), -10);
            if (!empty(array_filter($recentLines))) {
                echo "<h3>Recent entries from: {$logPath}</h3>";
                echo "<pre>" . htmlspecialchars(implode("\n", $recentLines)) . "</pre>";
                $foundErrors = true;
            }
        }
    }
}

if (!$foundErrors) {
    echo "<p>No accessible error logs found or logs are empty.</p>";
}

// 9. Quick Fix Suggestions
echo "<h2>9. Quick Fix Suggestions</h2>";
echo "<ol>";
echo "<li><strong>File Permissions:</strong> Set writable directories to 755 or 775<br>";
echo "<code>chmod -R 755 writable/</code></li>";
echo "<li><strong>Missing .env:</strong> Create .env file with database credentials</li>";
echo "<li><strong>Composer Dependencies:</strong> Run <code>composer install --no-dev</code></li>";
echo "<li><strong>Clear Cache:</strong> Delete all files in writable/cache/ (except index.html)</li>";
echo "<li><strong>Check Error Logs:</strong> Look in cPanel Error Logs or /var/log/</li>";
echo "</ol>";

echo "<hr>";
echo "<p><em>After fixing issues, delete this debug.php file for security.</em></p>";
?>