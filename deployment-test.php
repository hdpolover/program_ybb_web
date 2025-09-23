<?php
// deployment-test.php - Upload this file to test your hosting environment

header('Content-Type: text/html; charset=utf-8');
echo '<!DOCTYPE html><html><head><title>Hosting Environment Test</title></head><body>';
echo '<h1>🔍 Hosting Environment Test</h1>';

// Test 1: PHP Version
echo '<h2>✅ PHP Version Test</h2>';
$phpVersion = phpversion();
echo '<p>PHP Version: <strong>' . $phpVersion . '</strong></p>';
if (version_compare($phpVersion, '8.1.0', '>=')) {
    echo '<p style="color: green;">✅ PHP version is compatible with CodeIgniter 4</p>';
} else {
    echo '<p style="color: red;">❌ PHP version is too old. Need PHP 8.1 or higher</p>';
}

// Test 2: Required Extensions
echo '<h2>📦 Required PHP Extensions</h2>';
$required_extensions = [
    'json' => 'JSON support',
    'mbstring' => 'Multibyte String',
    'mysqli' => 'MySQL Improved',
    'curl' => 'cURL',
    'zip' => 'ZIP Archive',
    'openssl' => 'OpenSSL',
    'fileinfo' => 'File Information',
    'gd' => 'GD Graphics Library'
];

foreach ($required_extensions as $ext => $description) {
    if (extension_loaded($ext)) {
        echo '<p style="color: green;">✅ ' . $description . ' (' . $ext . ')</p>';
    } else {
        echo '<p style="color: red;">❌ ' . $description . ' (' . $ext . ') - MISSING</p>';
    }
}

// Test 3: File Permissions
echo '<h2>📁 File/Directory Tests</h2>';

// Check if critical directories exist
$directories = [
    'app' => 'Application directory',
    'system' => 'System directory', 
    'writable' => 'Writable directory',
    'vendor' => 'Vendor directory (Composer)'
];

foreach ($directories as $dir => $description) {
    if (is_dir($dir)) {
        echo '<p style="color: green;">✅ ' . $description . ' exists</p>';
    } else {
        echo '<p style="color: red;">❌ ' . $description . ' missing</p>';
    }
}

// Test writable permissions
if (is_dir('writable')) {
    $testFile = 'writable/test_write.txt';
    $canWrite = @file_put_contents($testFile, 'test');
    if ($canWrite !== false) {
        echo '<p style="color: green;">✅ Writable directory is writable</p>';
        @unlink($testFile);
    } else {
        echo '<p style="color: red;">❌ Writable directory is not writable - check permissions</p>';
    }
}

// Test 4: Environment File
echo '<h2>🔧 Configuration Tests</h2>';
if (file_exists('.env')) {
    echo '<p style="color: green;">✅ .env file exists</p>';
    
    // Try to load environment
    $envContent = file_get_contents('.env');
    if (strpos($envContent, 'CI_ENVIRONMENT') !== false) {
        echo '<p style="color: green;">✅ .env file contains CI_ENVIRONMENT</p>';
    } else {
        echo '<p style="color: orange;">⚠️ .env file missing CI_ENVIRONMENT setting</p>';
    }
    
    if (strpos($envContent, 'database.') !== false) {
        echo '<p style="color: green;">✅ .env file contains database configuration</p>';
    } else {
        echo '<p style="color: orange;">⚠️ .env file missing database configuration</p>';
    }
} else {
    echo '<p style="color: red;">❌ .env file missing</p>';
}

// Test 5: Database Connection (if configured)
echo '<h2>🗃️ Database Connection Test</h2>';
if (file_exists('.env')) {
    $envContent = file_get_contents('.env');
    
    // Extract database info from .env
    preg_match('/database\.default\.hostname\s*=\s*(.+)/', $envContent, $host_match);
    preg_match('/database\.default\.database\s*=\s*(.+)/', $envContent, $db_match);
    preg_match('/database\.default\.username\s*=\s*(.+)/', $envContent, $user_match);
    preg_match('/database\.default\.password\s*=\s*(.+)/', $envContent, $pass_match);
    
    if (!empty($host_match[1]) && !empty($db_match[1]) && !empty($user_match[1])) {
        $host = trim($host_match[1]);
        $database = trim($db_match[1]);
        $username = trim($user_match[1]);
        $password = isset($pass_match[1]) ? trim($pass_match[1]) : '';
        
        try {
            $pdo = new PDO("mysql:host=$host;dbname=$database", $username, $password);
            echo '<p style="color: green;">✅ Database connection successful</p>';
        } catch (PDOException $e) {
            echo '<p style="color: red;">❌ Database connection failed: ' . $e->getMessage() . '</p>';
        }
    } else {
        echo '<p style="color: orange;">⚠️ Database configuration incomplete in .env</p>';
    }
} else {
    echo '<p style="color: orange;">⚠️ Cannot test database - .env file missing</p>';
}

// Test 6: Composer Autoloader
echo '<h2>📚 Composer Test</h2>';
if (file_exists('vendor/autoload.php')) {
    echo '<p style="color: green;">✅ Composer autoloader exists</p>';
    try {
        require_once 'vendor/autoload.php';
        echo '<p style="color: green;">✅ Composer autoloader loads successfully</p>';
    } catch (Exception $e) {
        echo '<p style="color: red;">❌ Composer autoloader error: ' . $e->getMessage() . '</p>';
    }
} else {
    echo '<p style="color: red;">❌ Composer autoloader missing - run "composer install"</p>';
}

// Test 7: CodeIgniter Bootstrap
echo '<h2>🚀 CodeIgniter Bootstrap Test</h2>';
if (file_exists('app/Config/Paths.php') && file_exists('vendor/autoload.php')) {
    try {
        require_once 'vendor/autoload.php';
        require_once 'app/Config/Paths.php';
        $paths = new Config\Paths();
        
        if (file_exists($paths->systemDirectory . '/bootstrap.php')) {
            echo '<p style="color: green;">✅ CodeIgniter system bootstrap found</p>';
        } else {
            echo '<p style="color: red;">❌ CodeIgniter system bootstrap missing</p>';
        }
        
        if (is_dir($paths->writableDirectory)) {
            echo '<p style="color: green;">✅ Writable path configured correctly</p>';
        } else {
            echo '<p style="color: red;">❌ Writable path configuration issue</p>';
        }
        
    } catch (Exception $e) {
        echo '<p style="color: red;">❌ CodeIgniter bootstrap error: ' . $e->getMessage() . '</p>';
    }
} else {
    echo '<p style="color: red;">❌ Missing required files for CodeIgniter bootstrap</p>';
}

// Test 8: .htaccess
echo '<h2>🔗 URL Rewriting Test</h2>';
if (file_exists('.htaccess')) {
    echo '<p style="color: green;">✅ .htaccess file exists</p>';
    
    $htaccess = file_get_contents('.htaccess');
    if (strpos($htaccess, 'RewriteEngine On') !== false) {
        echo '<p style="color: green;">✅ RewriteEngine is enabled in .htaccess</p>';
    } else {
        echo '<p style="color: orange;">⚠️ RewriteEngine not found in .htaccess</p>';
    }
} else {
    echo '<p style="color: red;">❌ .htaccess file missing</p>';
}

// Summary
echo '<h2>📋 Summary</h2>';
echo '<p>This test helps identify common hosting issues. If you see red ❌ items, fix those first.</p>';
echo '<p><strong>Next steps:</strong></p>';
echo '<ol>';
echo '<li>Fix any red ❌ issues above</li>';
echo '<li>Upload your CodeIgniter application files</li>';
echo '<li>Configure your .env file with correct database credentials</li>';
echo '<li>Set proper file permissions (writable/ directory needs 755 or 777)</li>';
echo '<li>Test your application</li>';
echo '</ol>';

echo '<hr>';
echo '<p><em>Delete this file after testing for security!</em></p>';
echo '</body></html>';
?>