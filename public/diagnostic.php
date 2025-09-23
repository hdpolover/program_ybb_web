<?php
/**
 * Production Debugging Tool
 * Upload this file to your web hosting and access it via browser
 * This will help identify what's causing the 500 error
 */

// Start output buffering to catch any errors
ob_start();

// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "<!DOCTYPE html>\n";
echo "<html><head><title>500 Error Diagnostic Tool</title>\n";
echo "<style>body{font-family:Arial,sans-serif;margin:20px;} .test{margin:10px 0;padding:10px;border:1px solid #ddd;} .pass{background:#d4edda;} .fail{background:#f8d7da;} .warning{background:#fff3cd;} h3{margin-top:30px;} pre{background:#f8f9fa;padding:10px;overflow:auto;}</style>\n";
echo "</head><body>\n";
echo "<h1>🔍 500 Error Diagnostic Tool</h1>\n";
echo "<p>Generated at: " . date('Y-m-d H:i:s') . "</p>\n";

$errors = [];
$warnings = [];
$passes = [];

// Test 1: Basic PHP Information
echo "<h3>1. PHP Environment</h3>\n";
try {
    $phpVersion = phpversion();
    echo "<div class='test pass'>✅ PHP Version: $phpVersion</div>\n";
    $passes[] = "PHP Version: $phpVersion";
} catch (Exception $e) {
    echo "<div class='test fail'>❌ PHP Version Check Failed: " . $e->getMessage() . "</div>\n";
    $errors[] = "PHP Version Check Failed: " . $e->getMessage();
}

// Test 2: Required Extensions
echo "<h3>2. Required PHP Extensions</h3>\n";
$required_extensions = ['json', 'mbstring', 'intl', 'curl', 'gd'];
foreach ($required_extensions as $ext) {
    if (extension_loaded($ext)) {
        echo "<div class='test pass'>✅ Extension '$ext' is loaded</div>\n";
        $passes[] = "Extension '$ext' is loaded";
    } else {
        echo "<div class='test fail'>❌ Extension '$ext' is NOT loaded</div>\n";
        $errors[] = "Extension '$ext' is NOT loaded";
    }
}

// Test 3: File/Directory Permissions
echo "<h3>3. File/Directory Permissions</h3>\n";
$paths_to_check = [
    'writable' => 'writable/',
    'writable/logs' => 'writable/logs/',
    'writable/cache' => 'writable/cache/',
    'writable/session' => 'writable/session/',
    'app' => 'app/',
    'system' => 'system/',
    'public' => 'public/',
    'vendor' => 'vendor/'
];

foreach ($paths_to_check as $name => $path) {
    if (file_exists($path)) {
        $perms = substr(sprintf('%o', fileperms($path)), -4);
        $writable = is_writable($path);
        
        if (strpos($name, 'writable') !== false && !$writable) {
            echo "<div class='test fail'>❌ $name ($path) - Permissions: $perms - NOT WRITABLE (Required)</div>\n";
            $errors[] = "$name is not writable";
        } elseif ($writable || strpos($name, 'writable') === false) {
            echo "<div class='test pass'>✅ $name ($path) - Permissions: $perms - " . ($writable ? 'Writable' : 'Read-only') . "</div>\n";
            $passes[] = "$name permissions OK";
        } else {
            echo "<div class='test warning'>⚠️ $name ($path) - Permissions: $perms - Check required</div>\n";
            $warnings[] = "$name permissions need checking";
        }
    } else {
        echo "<div class='test fail'>❌ $name ($path) - NOT FOUND</div>\n";
        $errors[] = "$name directory not found";
    }
}

// Test 4: CodeIgniter Files
echo "<h3>4. CodeIgniter Core Files</h3>\n";
$ci_files = [
    'app/Config/App.php',
    'app/Config/Database.php',
    'app/Config/Constants.php',
    'system/CodeIgniter.php',
    'system/Boot.php',
    'app/Controllers/BaseController.php',
    'public/index.php',
    'public/.htaccess'
];

foreach ($ci_files as $file) {
    if (file_exists($file)) {
        echo "<div class='test pass'>✅ $file exists</div>\n";
        $passes[] = "$file exists";
    } else {
        echo "<div class='test fail'>❌ $file NOT FOUND</div>\n";
        $errors[] = "$file not found";
    }
}

// Test 5: Environment Configuration
echo "<h3>5. Environment Configuration</h3>\n";
if (file_exists('.env')) {
    echo "<div class='test pass'>✅ .env file exists</div>\n";
    $passes[] = ".env file exists";
    
    // Try to read environment
    $env_content = file_get_contents('.env');
    if (strpos($env_content, 'CI_ENVIRONMENT') !== false) {
        echo "<div class='test pass'>✅ CI_ENVIRONMENT is configured</div>\n";
        $passes[] = "CI_ENVIRONMENT is configured";
    } else {
        echo "<div class='test warning'>⚠️ CI_ENVIRONMENT not found in .env</div>\n";
        $warnings[] = "CI_ENVIRONMENT not configured";
    }
} else {
    echo "<div class='test warning'>⚠️ .env file not found (using defaults)</div>\n";
    $warnings[] = ".env file not found";
}

// Check if we can detect the current environment
if (defined('ENVIRONMENT')) {
    echo "<div class='test pass'>✅ ENVIRONMENT constant: " . ENVIRONMENT . "</div>\n";
    $passes[] = "ENVIRONMENT: " . ENVIRONMENT;
} else {
    echo "<div class='test warning'>⚠️ ENVIRONMENT constant not defined</div>\n";
    $warnings[] = "ENVIRONMENT constant not defined";
}

// Test 6: Try to load CodeIgniter (carefully)
echo "<h3>6. CodeIgniter Bootstrap Test</h3>\n";
try {
    if (file_exists('app/Config/Paths.php')) {
        echo "<div class='test pass'>✅ Paths.php exists</div>\n";
        $passes[] = "Paths.php exists";
        
        // Try to include paths
        require_once 'app/Config/Paths.php';
        $paths = new Config\Paths();
        echo "<div class='test pass'>✅ Paths class loaded successfully</div>\n";
        $passes[] = "Paths class loaded";
        
    } else {
        echo "<div class='test fail'>❌ app/Config/Paths.php not found</div>\n";
        $errors[] = "Paths.php not found";
    }
} catch (Exception $e) {
    echo "<div class='test fail'>❌ Error loading Paths: " . $e->getMessage() . "</div>\n";
    $errors[] = "Error loading Paths: " . $e->getMessage();
} catch (Error $e) {
    echo "<div class='test fail'>❌ Fatal error loading Paths: " . $e->getMessage() . "</div>\n";
    $errors[] = "Fatal error loading Paths: " . $e->getMessage();
}

// Test 7: Database Configuration (if exists)
echo "<h3>7. Database Configuration</h3>\n";
try {
    if (file_exists('app/Config/Database.php')) {
        include_once 'app/Config/Database.php';
        echo "<div class='test pass'>✅ Database.php loaded successfully</div>\n";
        $passes[] = "Database.php loaded";
    } else {
        echo "<div class='test fail'>❌ app/Config/Database.php not found</div>\n";
        $errors[] = "Database.php not found";
    }
} catch (Exception $e) {
    echo "<div class='test fail'>❌ Error loading Database config: " . $e->getMessage() . "</div>\n";
    $errors[] = "Error loading Database config: " . $e->getMessage();
}

// Test 8: Composer Dependencies
echo "<h3>8. Composer Dependencies</h3>\n";
if (file_exists('vendor/autoload.php')) {
    echo "<div class='test pass'>✅ Composer autoload.php exists</div>\n";
    $passes[] = "Composer autoload exists";
    
    try {
        require_once 'vendor/autoload.php';
        echo "<div class='test pass'>✅ Composer autoload loaded successfully</div>\n";
        $passes[] = "Composer autoload loaded";
    } catch (Exception $e) {
        echo "<div class='test fail'>❌ Error loading Composer autoload: " . $e->getMessage() . "</div>\n";
        $errors[] = "Error loading Composer autoload: " . $e->getMessage();
    }
} else {
    echo "<div class='test fail'>❌ vendor/autoload.php not found</div>\n";
    $errors[] = "Composer autoload not found";
}

// Test 9: Memory and Execution Limits
echo "<h3>9. PHP Limits</h3>\n";
$memory_limit = ini_get('memory_limit');
$execution_time = ini_get('max_execution_time');
$upload_max = ini_get('upload_max_filesize');
$post_max = ini_get('post_max_size');

echo "<div class='test pass'>✅ Memory Limit: $memory_limit</div>\n";
echo "<div class='test pass'>✅ Max Execution Time: $execution_time seconds</div>\n";
echo "<div class='test pass'>✅ Upload Max Filesize: $upload_max</div>\n";
echo "<div class='test pass'>✅ Post Max Size: $post_max</div>\n";

// Test 10: Error Log Check
echo "<h3>10. Error Logs</h3>\n";
$log_files = [
    'writable/logs/log-' . date('Y-m-d') . '.log',
    'writable/logs/log-' . date('Y-m-d', strtotime('-1 day')) . '.log'
];

foreach ($log_files as $log_file) {
    if (file_exists($log_file)) {
        $log_size = filesize($log_file);
        echo "<div class='test pass'>✅ $log_file exists (Size: " . number_format($log_size) . " bytes)</div>\n";
        
        if ($log_size > 0) {
            $recent_errors = shell_exec("tail -20 '$log_file' 2>/dev/null") ?: 'Could not read recent entries';
            echo "<div class='test warning'>⚠️ Recent log entries:</div>\n";
            echo "<pre style='font-size:12px;max-height:200px;overflow:auto;'>" . htmlspecialchars($recent_errors) . "</pre>\n";
        }
    } else {
        echo "<div class='test warning'>⚠️ $log_file not found</div>\n";
    }
}

// Summary
echo "<h3>📊 Summary</h3>\n";
echo "<div class='test pass'>✅ Passed: " . count($passes) . " tests</div>\n";
echo "<div class='test warning'>⚠️ Warnings: " . count($warnings) . " issues</div>\n";
echo "<div class='test fail'>❌ Failed: " . count($errors) . " critical issues</div>\n";

if (!empty($errors)) {
    echo "<h3>🚨 Critical Issues (Fix These First)</h3>\n";
    foreach ($errors as $error) {
        echo "<div class='test fail'>❌ $error</div>\n";
    }
}

if (!empty($warnings)) {
    echo "<h3>⚠️ Warnings (Review These)</h3>\n";
    foreach ($warnings as $warning) {
        echo "<div class='test warning'>⚠️ $warning</div>\n";
    }
}

// Additional debugging info
echo "<h3>🔧 Additional Debug Information</h3>\n";
echo "<div class='test'>Current working directory: " . getcwd() . "</div>\n";
echo "<div class='test'>Document root: " . ($_SERVER['DOCUMENT_ROOT'] ?? 'Not set') . "</div>\n";
echo "<div class='test'>Script filename: " . ($_SERVER['SCRIPT_FILENAME'] ?? 'Not set') . "</div>\n";
echo "<div class='test'>HTTP Host: " . ($_SERVER['HTTP_HOST'] ?? 'Not set') . "</div>\n";
echo "<div class='test'>Request URI: " . ($_SERVER['REQUEST_URI'] ?? 'Not set') . "</div>\n";
echo "<div class='test'>User Agent: " . ($_SERVER['HTTP_USER_AGENT'] ?? 'Not set') . "</div>\n";

echo "</body></html>\n";

// Capture any output buffer content
$output = ob_get_clean();
echo $output;
?>