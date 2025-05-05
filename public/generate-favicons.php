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

// Ensure the assets/images directory exists
$directory = __DIR__ . '/assets/images';
if (!is_dir($directory)) {
    mkdir($directory, 0755, true);
}

try {
    // Default site logo from BaseController
    $logoUrl = getenv('DEFAULT_LOGO_URL') ?: 'https://ybbfoundation.com/assets/images/logo.png';

    echo "Generating favicons from logo: {$logoUrl}\n";

    // Load the source logo image
    $image = $manager->read($logoUrl);

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
    echo "Created apple-touch-icon.png\n";

    // Copy to favicon.ico in assets/images as well
    copy(__DIR__ . '/favicon.ico', $directory . '/favicon.ico');
    echo "Copied favicon.ico to assets/images directory\n";

    echo "All favicons generated successfully!\n";
} catch (Exception $e) {
    echo "Error generating favicons: " . $e->getMessage();
}
