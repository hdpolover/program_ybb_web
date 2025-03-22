# Installing and Troubleshooting the Image Library

This document provides instructions for installing and troubleshooting the Intervention/Image library.

## Installation

1. Run the following command in your project root:

```bash
composer require intervention/image
```

2. Make sure the GD extension is installed for PHP:

For Ubuntu/Debian:
```bash
sudo apt-get install php-gd
sudo service apache2 restart  # or nginx, depending on your server
```

For Windows/XAMPP:
- Open php.ini
- Uncomment the line: extension=gd
- Restart Apache

3. Create the cache directory:

```bash
mkdir -p writable/cache/images
chmod 755 writable/cache/images
```

## Troubleshooting

If you're having issues with the image library, run the diagnostic tool:

1. Open your browser and navigate to: 
   http://your-site.com/image_diagnostic.php

2. The diagnostic tool will:
   - Check if Intervention/Image is installed
   - Verify PHP extensions (GD/Imagick)
   - Test directory permissions
   - Attempt to process a sample image
   - Display PHP configuration

3. Common issues:
   - Missing GD extension
   - Directory permission problems
   - Memory limits too low
   - Intervention/Image not installed correctly

## Usage

Once installed, you can use the image compression helper like this:

```php
<img src="<?= compress_image($imageUrl, 800, null, 80) ?>" alt="Compressed image">
```

Parameters:
- `$imageUrl`: Original image URL
- `$width`: Width to resize to (null for auto)
- `$height`: Height to resize to (null for auto)
- `$quality`: JPEG quality (0-100)
