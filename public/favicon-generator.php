<?php

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Load Composer's autoloader
require_once __DIR__ . '/../vendor/autoload.php';

// Import the Intervention Image package
use Intervention\Image\ImageManager;

// Only process POST requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check if a file was uploaded
    if (isset($_FILES['logo']) && $_FILES['logo']['error'] === UPLOAD_ERR_OK) {

        try {
            // Create an instance of the ImageManager
            $manager = new ImageManager('gd');

            // Load the uploaded image
            $uploadedImage = $manager->read($_FILES['logo']['tmp_name']);

            // Generate different sized favicon files

            // Main favicon in root
            $uploadedImage->resize(32, 32)->save(__DIR__ . '/favicon.ico');

            // Ensure assets/images directory exists
            $directory = __DIR__ . '/assets/images';
            if (!is_dir($directory)) {
                mkdir($directory, 0755, true);
            }

            // Create favicon-16x16.png
            $uploadedImage->resize(16, 16)->save($directory . '/favicon-16x16.png');

            // Create favicon-32x32.png
            $uploadedImage->resize(32, 32)->save($directory . '/favicon-32x32.png');

            // Create apple-touch-icon.png
            $uploadedImage->resize(180, 180)->save($directory . '/apple-touch-icon.png');

            // Copy favicon.ico to assets/images
            copy(__DIR__ . '/favicon.ico', $directory . '/favicon.ico');

            $message = "Favicon files generated successfully!";
            $status = "success";
        } catch (Exception $e) {
            $message = "Error: " . $e->getMessage();
            $status = "error";
        }
    } else {
        $message = "Error uploading file. Please try again.";
        $status = "error";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Favicon Generator</title>
    <link rel="stylesheet" href="assets/css/favicon-generator.css">
</head>

<body>
    <h1>Favicon Generator</h1>

    <form action="" method="post" enctype="multipart/form-data">
        <p>Upload your logo to generate favicon files:</p>
        <input type="file" name="logo" accept="image/*" required>
        <input type="submit" value="Generate Favicons">

        <?php if (isset($message)): ?>
            <div class="message <?= $status ?>">
                <?= $message ?>
            </div>
        <?php endif; ?>
    </form>
</body>

</html>