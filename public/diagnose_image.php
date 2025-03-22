<?php

echo "<h1>Image Compression Diagnostic Tool</h1>";

// Check GD extension
echo "<h2>GD Extension:</h2>";
if (extension_loaded('gd')) {
    echo "<p style='color:green'>✓ GD extension is installed</p>";
    $info = gd_info();
    echo "<p>GD Version: " . $info['GD Version'] . "</p>";
} else {
    echo "<p style='color:red'>✗ GD extension is NOT installed. This is required for image processing.</p>";
    echo "<p>Please enable the GD extension in your php.ini file and restart your web server.</p>";
}

// Check cache directory
echo "<h2>Cache Directory:</h2>";
$cacheDir = __DIR__ . '/../writable/cache/images/';
if (!is_dir($cacheDir)) {
    if (mkdir($cacheDir, 0755, true)) {
        echo "<p style='color:green'>✓ Cache directory created successfully</p>";
    } else {
        echo "<p style='color:red'>✗ Failed to create cache directory</p>";
    }
} else {
    if (is_writable($cacheDir)) {
        echo "<p style='color:green'>✓ Cache directory is writable</p>";
    } else {
        echo "<p style='color:red'>✗ Cache directory is NOT writable</p>";
    }
}

// Test image creation
echo "<h2>Image Creation Test:</h2>";
try {
    $img = imagecreatetruecolor(100, 100);
    $red = imagecolorallocate($img, 255, 0, 0);
    imagefill($img, 0, 0, $red);
    $testFile = $cacheDir . 'test.jpg';
    imagejpeg($img, $testFile, 90);
    imagedestroy($img);
    
    if (file_exists($testFile)) {
        echo "<p style='color:green'>✓ Successfully created test image</p>";
        echo "<p><img src='/writable/cache/images/test.jpg' alt='Test'></p>";
    } else {
        echo "<p style='color:red'>✗ Failed to save test image</p>";
    }
} catch (Exception $e) {
    echo "<p style='color:red'>✗ Error: " . $e->getMessage() . "</p>";
}

// Test image downloading
echo "<h2>Testing Image Download:</h2>";
$testUrl = 'https://picsum.photos/200';
$imageData = @file_get_contents($testUrl);
if ($imageData === false) {
    echo "<p style='color:red'>✗ Failed to download test image from $testUrl</p>";
    echo "<p>This could be due to:</p>";
    echo "<ul>";
    echo "<li>allow_url_fopen is disabled in php.ini</li>";
    echo "<li>Network connectivity issues</li>";
    echo "<li>The URL is no longer valid</li>";
    echo "</ul>";
} else {
    $downloadTest = $cacheDir . 'download_test.jpg';
    if (file_put_contents($downloadTest, $imageData)) {
        echo "<p style='color:green'>✓ Successfully downloaded and saved test image</p>";
        echo "<p><img src='/writable/cache/images/download_test.jpg' alt='Downloaded Test'></p>";
    } else {
        echo "<p style='color:red'>✗ Failed to save downloaded image</p>";
    }
}

// PHP configuration
echo "<h2>PHP Configuration:</h2>";
echo "<ul>";
echo "<li>PHP Version: " . phpversion() . "</li>";
echo "<li>Memory Limit: " . ini_get('memory_limit') . "</li>";
echo "<li>Max Execution Time: " . ini_get('max_execution_time') . " seconds</li>";
echo "<li>allow_url_fopen: " . (ini_get('allow_url_fopen') ? 'On' : 'Off') . "</li>";
echo "</ul>";

// Usage example
echo "<h2>Usage Example:</h2>";
echo "<pre>
// In your view file:
&lt;img src=\"&lt;?= compress_image(\$imageUrl, 800) ?>\" alt=\"Compressed image\">
</pre>";
?>
