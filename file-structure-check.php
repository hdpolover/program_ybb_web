<?php
// file-structure-check.php - Upload this to check if your files are in the right place

header('Content-Type: text/html; charset=utf-8');
echo '<!DOCTYPE html><html><head><title>File Structure Check</title></head><body>';
echo '<h1>📁 File Structure Verification</h1>';

echo '<p><strong>Current location:</strong> ' . __DIR__ . '</p>';
echo '<p><strong>Should be:</strong> /public_html/istanbulyouthsummit.com/</p>';

$required_structure = [
    'app' => 'Application directory',
    'app/Config' => 'Config directory',
    'app/Config/App.php' => 'App configuration',
    'app/Config/Database.php' => 'Database configuration',
    'app/Config/Paths.php' => 'Paths configuration',
    'app/Controllers' => 'Controllers directory',
    'app/Controllers/BaseController.php' => 'Base controller',
    'system' => 'System directory',
    'system/CodeIgniter.php' => 'CodeIgniter core',
    'system/bootstrap.php' => 'Bootstrap file',
    'vendor' => 'Composer vendor directory',
    'vendor/autoload.php' => 'Composer autoloader',
    'writable' => 'Writable directory',
    'writable/logs' => 'Logs directory',
    'writable/cache' => 'Cache directory', 
    'writable/session' => 'Session directory',
    '.env' => 'Environment file',
    'public' => 'Public directory (optional)',
    'public/index.php' => 'Main index file',
    'public/.htaccess' => 'URL rewrite rules'
];

$missing_files = [];
$found_files = [];

foreach ($required_structure as $path => $description) {
    $full_path = __DIR__ . '/' . $path;
    
    if (file_exists($full_path) || is_dir($full_path)) {
        echo '<p style="color: green;">✅ ' . $description . ' (' . $path . ')</p>';
        $found_files[] = $path;
    } else {
        echo '<p style="color: red;">❌ ' . $description . ' (' . $path . ') - MISSING</p>';
        $missing_files[] = $path;
    }
}

// Check if index.php is in root (alternative structure)
if (!in_array('public/index.php', $found_files)) {
    if (file_exists(__DIR__ . '/index.php')) {
        echo '<p style="color: green;">✅ Root index.php found (shared hosting structure)</p>';
    } else {
        echo '<p style="color: red;">❌ No index.php found in public/ or root!</p>';
    }
}

// Check writable permissions
if (is_dir(__DIR__ . '/writable')) {
    $test_file = __DIR__ . '/writable/test_permissions.txt';
    if (@file_put_contents($test_file, 'test') !== false) {
        echo '<p style="color: green;">✅ Writable directory has correct permissions</p>';
        @unlink($test_file);
    } else {
        echo '<p style="color: red;">❌ Writable directory permissions incorrect</p>';
        echo '<p style="color: orange;">⚠️ Run: chmod -R 777 writable/</p>';
    }
}

echo '<hr>';
echo '<h2>📋 Summary</h2>';
echo '<p><strong>Found:</strong> ' . count($found_files) . ' items</p>';
echo '<p><strong>Missing:</strong> ' . count($missing_files) . ' items</p>';

if (count($missing_files) > 0) {
    echo '<h3 style="color: red;">❌ Missing Files/Directories:</h3>';
    echo '<ul>';
    foreach ($missing_files as $missing) {
        echo '<li>' . $missing . '</li>';
    }
    echo '</ul>';
    
    echo '<h3>🔧 Next Steps:</h3>';
    echo '<ol>';
    echo '<li>Upload the missing files/directories from your local project</li>';
    echo '<li>Set proper permissions: chmod -R 777 writable/</li>';
    echo '<li>Create .env file with production settings</li>';
    echo '<li>Run composer install (or upload vendor/ directory)</li>';
    echo '<li>Test your site</li>';
    echo '</ol>';
} else {
    echo '<h3 style="color: green;">✅ All Required Files Found!</h3>';
    echo '<p>Your file structure looks correct. If you\'re still getting errors:</p>';
    echo '<ol>';
    echo '<li>Check .env file has correct database settings</li>';
    echo '<li>Verify writable/ directory permissions (777)</li>';
    echo '<li>Check hosting PHP version (needs 8.1+)</li>';
    echo '<li>Review error logs in cPanel</li>';
    echo '</ol>';
}

echo '<hr>';
echo '<p><em>Delete this file after checking for security!</em></p>';
echo '</body></html>';
?>