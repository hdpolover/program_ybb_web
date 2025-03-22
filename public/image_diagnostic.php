<?php

echo "<h1>Image Processing Diagnostic Tool</h1>";

// Check GD extension
echo "<h2>1. PHP GD Library Check:</h2>";
if (extension_loaded('gd')) {
    echo "<p style='color:green'>✓ GD extension is installed</p>";
    echo "<p>GD Version: " . (function_exists('gd_info') ? gd_info()['GD Version'] : 'Unknown') . "</p>";
} else {
    echo "<p style='color:red'>✗ ERROR: GD extension is NOT installed. This is required for image processing.</p>";
    echo "<p>Enable the GD extension in php.ini and restart your web server.</p>";
}

// Check cache directory
echo "<h2>2. Cache Directory Check:</h2>";
$cacheDir = __DIR__ . '/../writable/cache/images/';
if (!is_dir($cacheDir)) {
    if (mkdir($cacheDir, 0755, true)) {
        echo "<p style='color:green'>✓ Cache directory created successfully at: " . $cacheDir . "</p>";
    } else {
        echo "<p style='color:red'>✗ ERROR: Failed to create cache directory at: " . $cacheDir . "</p>";
        echo "<p>Run this command manually: <code>mkdir -p " . $cacheDir . "</code></p>";
    }
} else {
    if (is_writable($cacheDir)) {
        echo "<p style='color:green'>✓ Cache directory exists and is writable</p>";
    } else {
        echo "<p style='color:red'>✗ ERROR: Cache directory exists but is NOT writable</p>";
        echo "<p>Run this command: <code>chmod 755 " . $cacheDir . "</code></p>";
    }
}

// Test image creation
echo "<h2>3. GD Image Creation Test:</h2>";
try {
    $img = imagecreatetruecolor(100, 100);
    $red = imagecolorallocate($img, 255, 0, 0);
    imagefill($img, 0, 0, $red);
    $testFile = $cacheDir . 'test.jpg';
    imagejpeg($img, $testFile, 90);
    imagedestroy($img);
    
    if (file_exists($testFile)) {
        echo "<p style='color:green'>✓ Successfully created and saved a test image</p>";
        echo "<p><img src='/writable/cache/images/test.jpg' alt='Test Image'></p>";
    } else {
        echo "<p style='color:red'>✗ ERROR: Failed to save the test image</p>";
    }
} catch (Exception $e) {
    echo "<p style='color:red'>✗ ERROR: " . $e->getMessage() . "</p>";
}

// Test remote image fetch
echo "<h2>4. Remote Image Fetch Test:</h2>";
$testUrl = 'https://picsum.photos/200';
$imageData = @file_get_contents($testUrl);
if ($imageData === false) {
    echo "<p style='color:red'>✗ ERROR: Failed to download test image from " . $testUrl . "</p>";
    echo "<p>Possible issues:</p>";
    echo "<ul>";
    echo "<li>allow_url_fopen is disabled in php.ini</li>";
    echo "<li>Network connectivity issues</li>";
    echo "</ul>";
    echo "<p>Current allow_url_fopen setting: " . (ini_get('allow_url_fopen') ? 'Enabled' : 'Disabled') . "</p>";
} else {
    $downloadTest = $cacheDir . 'download_test.jpg';
    if (file_put_contents($downloadTest, $imageData)) {
        echo "<p style='color:green'>✓ Successfully downloaded and saved test image</p>";
        echo "<p><img src='/writable/cache/images/download_test.jpg' alt='Downloaded Test'></p>";
    } else {
        echo "<p style='color:red'>✗ ERROR: Failed to save downloaded image</p>";
    }
}

// Check ImageHelper function
echo "<h2>5. ImageHelper Function Test:</h2>";
if (function_exists('compress_image')) {
    echo "<p style='color:green'>✓ compress_image() function exists</p>";
    
    // Try using the function
    try {
        $result = compress_image('https://picsum.photos/300', 150);
        echo "<p>Function returned: " . $result . "</p>";
        echo "<p><img src='" . $result . "' alt='Compressed'></p>";
    } catch (Exception $e) {
        echo "<p style='color:red'>✗ ERROR when running function: " . $e->getMessage() . "</p>";
    }
} else {
    echo "<p style='color:red'>✗ ERROR: compress_image() function does not exist</p>";
    echo "<p>Check that ImageHelper.php is properly loaded.</p>";
}

// PHP configuration
echo "<h2>6. PHP Configuration:</h2>";
echo "<ul>";
echo "<li>PHP Version: " . phpversion() . "</li>";
echo "<li>Memory Limit: " . ini_get('memory_limit') . "</li>";
echo "<li>Max Execution Time: " . ini_get('max_execution_time') . " seconds</li>";
echo "<li>allow_url_fopen: " . (ini_get('allow_url_fopen') ? 'Enabled' : 'Disabled') . "</li>";
echo "</ul>";
