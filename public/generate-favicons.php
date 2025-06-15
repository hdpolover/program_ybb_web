
text/x-generic generate-favicons.php ( PHP script, ASCII text, with CRLF line terminators )
<?php
// Generate favicons from logo

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Require the Composer autoloader
require_once __DIR__ . '/../vendor/autoload.php';

// Use the Intervention Image package to handle image manipulation
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

// Create an instance of ImageManager
$manager = new ImageManager(new Driver());

// We need to first check that the Intervention Image library is available
if (!class_exists('Intervention\Image\ImageManager')) {
    die('Error: Intervention Image library is not installed. Please run: composer require intervention/image');
}

// Ensure the assets directory exists
$directory = __DIR__ . '/assets';
if (!is_dir($directory)) {
    mkdir($directory, 0755, true);
}

try {
    // Get current domain dynamically
    function getCurrentDomain() {
        if (php_sapi_name() === 'cli') {
            // If running from CLI, try to get from environment or use a default
            return getenv('SITE_DOMAIN') ?: 'localhost';
        } else {
            // If running in web context, get from HTTP_HOST
            $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
            $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
            return $protocol . '://' . $host;
        }
    }
    
    $currentDomain = getCurrentDomain();
    // Remove protocol for logo URL construction
    $domainOnly = preg_replace('~^https?://~', '', $currentDomain);    // Build logo URL using current domain
    $defaultLogoUrl = (strpos($currentDomain, 'https://') === 0 ? 'https://' : 'http://') . $domainOnly . '/assets/logo/logo.png';
    $logoUrl = getenv('DEFAULT_LOGO_URL') ?: $defaultLogoUrl;    echo "Current domain: {$currentDomain}\n";
    echo "Generating favicons from logo: {$logoUrl}\n";

    // Check if the logo URL is accessible and returns an image
    if (filter_var($logoUrl, FILTER_VALIDATE_URL)) {
        echo "Testing logo URL accessibility...\n";
        $headers = @get_headers($logoUrl, 1);
        if ($headers) {
            echo "HTTP Status: " . $headers[0] . "\n";
            $contentType = isset($headers['Content-Type']) ? $headers['Content-Type'] : 'unknown';
            echo "Content-Type: " . $contentType . "\n";
            
            if (strpos($headers[0], '200') === false) {
                echo "Logo URL returned non-200 status, trying local file fallback...\n";
                $logoUrl = null;
            } elseif (!str_contains(strtolower($contentType), 'image')) {
                echo "URL does not return an image, trying local file fallback...\n";
                $logoUrl = null;
            }
        } else {
            echo "Could not get headers for logo URL, trying local file fallback...\n";
            $logoUrl = null;
        }
    }

    // If URL failed, try local file paths
    if (!$logoUrl) {
        echo "Trying local file paths...\n";
        $localPaths = [
            __DIR__ . '/assets/logo/logo.png',
            __DIR__ . '/assets/images/logo-light.png',
            __DIR__ . '/assets/images/logo-dark.png',
            __DIR__ . '/assets/images/logo-sm.png',
            __DIR__ . '/assets/images/logo.png'
        ];
        
        foreach ($localPaths as $path) {
            if (file_exists($path)) {
                $logoUrl = $path;
                echo "Found local logo file: {$logoUrl}\n";
                break;
            } else {
                echo "File not found: {$path}\n";
            }
        }
        
        if (!$logoUrl) {
            echo "No local logo files found, using hardcoded fallback...\n";
            $logoUrl = 'https://ybbfoundation.com/assets/images/logo.png';
        }
    }

    echo "Final logo URL/path: {$logoUrl}\n";

    // Load the source logo image with better error handling
    try {
        $image = $manager->read($logoUrl);
        echo "Successfully loaded logo image.\n";
    } catch (Exception $e) {
        echo "Failed to load logo: " . $e->getMessage() . "\n";
        
        // Try one more fallback - a simple colored rectangle
        echo "Creating a simple colored favicon as fallback...\n";
        $image = $manager->create(180, 180, '#4a90e2'); // Create a blue square
        echo "Created fallback image.\n";
    }

    // Generate favicon.ico in root public directory
    $image->resize(32, 32)->save(__DIR__ . '/favicon.ico');
    echo "Created favicon.ico in public directory\n";

    // Generate favicon-16x16.png
    $image->resize(16, 16)->save($directory . '/favicon-16x16.png');
    echo "Created favicon-16x16.png\n";

    // Generate favicon-32x32.png
    $image->resize(32, 32)->save($directory . '/favicon-32x32.png');
    echo "Created favicon-32x32.png\n";

    // Generate apple-touch-icon.png (180x180)
    $image->resize(180, 180)->save($directory . '/apple-touch-icon.png');
    echo "Created apple-touch-icon.png\n";    // Copy to favicon.ico in assets as well
    copy(__DIR__ . '/favicon.ico', $directory . '/favicon.ico');
    echo "Copied favicon.ico to assets directory\n";

    echo "All favicons generated successfully!\n";
} catch (Exception $e) {
    echo "Error generating favicons: " . $e->getMessage();
}
