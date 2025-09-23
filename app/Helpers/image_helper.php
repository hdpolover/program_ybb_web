<?php

/**
 * Simple Image Helper for compressing and resizing images
 */

if (!function_exists('compress_image')) {
    /**
     * Compress and cache an image from a URL
     * 
     * @param string $url Original image URL
     * @param int $width Width to resize to (null for auto)
     * @param int $height Height to resize to (null for auto)
     * @param int $quality JPEG quality (0-100) - 85 is good balance for moderate compression
     * @param int $max_size Maximum size in KB to allow without compression (0 to always compress)
     * @return string URL to the cached image or original if processing fails
     */
    function compress_image($url, $width = null, $height = null, $quality = 85, $max_size = 400)
    {
        // Quick exit if image processing is disabled
        if (defined('DISABLE_IMAGE_PROCESSING') && DISABLE_IMAGE_PROCESSING === true) {
            return $url;
        }
        
        // Safe mode: only process trusted/local images
        if (defined('SAFE_IMAGE_PROCESSING_ONLY') && SAFE_IMAGE_PROCESSING_ONLY === true) {
            $trusted_domains = [
                'localhost',
                '127.0.0.1',
                $_SERVER['HTTP_HOST'] ?? '',
            ];
            
            $is_trusted = false;
            foreach ($trusted_domains as $domain) {
                if (strpos($url, $domain) !== false) {
                    $is_trusted = true;
                    break;
                }
            }
            
            // Also check if it's a relative URL (local)
            if (!$is_trusted && !preg_match('#^https?://#', $url)) {
                $is_trusted = true; // Relative URLs are considered safe
            }
            
            if (!$is_trusted) {
                log_message('debug', 'Safe mode: Bypassing compression for external URL: ' . substr($url, 0, 100));
                return $url;
            }
        }
        
        // Emergency bypass for problematic URLs (temporary)
        if (strpos($url, 'storage.ybbfoundation.com') !== false || 
            strpos($url, 'admin.ybbfoundation.com') !== false ||
            strlen($url) > 500) { // Very long URLs are often problematic
            log_message('debug', 'Bypassing compression for potentially problematic URL: ' . substr($url, 0, 100) . '...');
            return $url;
        }
        
        // Return original URL if empty
        if (empty($url)) {
            return $url;
        }
        
        // Quick URL validation to prevent timeouts
        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            log_message('warning', 'Invalid URL format: ' . $url);
            return $url;
        }
        
        // Check if the image is from an external URL or local path
        $is_external = (strpos($url, 'http://') === 0 || strpos($url, 'https://') === 0);
        
        // For external URLs, do a quick connection test first
        if ($is_external) {
            $parsed_url = parse_url($url);
            if (!$parsed_url || empty($parsed_url['host'])) {
                log_message('warning', 'Invalid URL host: ' . $url);
                return $url;
            }
            
            // Quick DNS and connectivity check with very short timeout
            $host = $parsed_url['host'];
            $port = $parsed_url['port'] ?? ($parsed_url['scheme'] === 'https' ? 443 : 80);
            
            // Test if we can connect quickly (2 second timeout)
            $connection = @fsockopen($host, $port, $errno, $errstr, 2);
            if (!$connection) {
                log_message('warning', 'Cannot connect to host: ' . $host . ' (' . $errstr . ')');
                return $url;
            }
            fclose($connection);
        }
        
        // For local images, check file size first
        if (!$is_external && file_exists($url)) {
            $file_size = filesize($url) / 1024; // Convert to KB
            
            // If image size is below the threshold and no resizing is needed, return original
            if ($max_size > 0 && $file_size <= $max_size && !$width && !$height) {
                return $url;
            }
        }
        
        // Set up cache directory
        $cacheDir = WRITEPATH . 'cache/images/';
        if (!is_dir($cacheDir)) {
            if (!mkdir($cacheDir, 0755, true)) {
                log_message('error', 'Failed to create image cache directory');
                return $url;
            }
        }
        
        // Generate cache filename
        $filename = md5($url . $width . $height . $quality) . '.jpg';
        $cachePath = $cacheDir . $filename;
        
        // Use the new route to serve cached images
        $cacheUrl = site_url('cached-images/' . $filename);
        
        // Return cached version if it exists
        if (file_exists($cachePath)) {
            return $cacheUrl;
        }
        
        // Process the image using GD directly instead of Intervention/Image
        try {
            // Set very aggressive timeout for image downloads (5 seconds max)
            $context = stream_context_create([
                'http' => [
                    'timeout' => 5, // Reduced to 5 seconds
                    'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
                    'follow_location' => true,
                    'max_redirects' => 2, // Reduced redirects
                    'method' => 'GET'
                ]
            ]);
            
            // Try to get headers first to check content length
            $headers = @get_headers($url, true, $context);
            if ($headers && isset($headers['Content-Length'])) {
                $content_length = is_array($headers['Content-Length']) 
                    ? end($headers['Content-Length']) 
                    : $headers['Content-Length'];
                
                // Skip if file is too large (over 5MB)
                if ($content_length > 5242880) {
                    log_message('warning', 'Image too large before download (' . number_format($content_length/1024/1024, 2) . 'MB): ' . $url);
                    return $url;
                }
            }
            
            // Get image content with aggressive timeout
            $imageData = @file_get_contents($url, false, $context);
            if ($imageData === false) {
                log_message('error', 'Could not download image (timeout or error): ' . $url);
                return $url;
            }
            
            // Check if the downloaded data is actually an image
            $imageInfo = @getimagesizefromstring($imageData);
            if ($imageInfo === false) {
                log_message('error', 'Downloaded data is not a valid image: ' . $url);
                return $url;
            }
            
            // Check the size of the image data (limit to 5MB instead of 10MB)
            $data_size = strlen($imageData) / 1024; // Size in KB
            if ($data_size > 5120) { // 5MB limit
                log_message('error', 'Image too large (' . number_format($data_size, 2) . 'KB): ' . $url);
                return $url;
            }
            
            // Check image dimensions (limit to 2000x2000 instead of 4000x4000)
            if ($imageInfo[0] > 2000 || $imageInfo[1] > 2000) {
                log_message('error', 'Image dimensions too large (' . $imageInfo[0] . 'x' . $imageInfo[1] . '): ' . $url);
                return $url;
            }
            
            // If no resizing is needed and the image is already small enough, return original
            if ($max_size > 0 && $data_size <= $max_size && !$width && !$height) {
                return $url;
            }
            
            // Create image from string
            $srcImage = imagecreatefromstring($imageData);
            if (!$srcImage) {
                log_message('error', 'Could not create image from string');
                return $url;
            }
            
            // Get original dimensions
            $srcWidth = imagesx($srcImage);
            $srcHeight = imagesy($srcImage);
            
            // Estimate memory requirement and set appropriate limit
            $estimated_memory = $srcWidth * $srcHeight * 4; // 4 bytes per pixel for RGBA
            $required_memory = $estimated_memory * 3; // Source + destination + overhead
            
            // Skip processing very large images that would require too much memory
            if ($required_memory > 512 * 1024 * 1024) { // Skip if needs more than 512MB
                log_message('warning', 'Image too large for processing (needs ' . number_format($required_memory/1024/1024, 2) . 'MB): ' . $url);
                imagedestroy($srcImage);
                return $url;
            }
            
            // If we need more memory, try to increase the limit temporarily
            if ($required_memory > 256 * 1024 * 1024) { // If we need more than 256MB
                ini_set('memory_limit', '512M');
            }
            
            // Set a very short execution time limit for image processing
            set_time_limit(15); // 15 seconds total for this operation
            
            // Calculate new dimensions
            $dstWidth = $width ?: $srcWidth;
            $dstHeight = $height ?: $srcHeight;
            
            // Maintain aspect ratio if only one dimension is provided
            if ($width && !$height) {
                $dstHeight = floor($srcHeight * ($dstWidth / $srcWidth));
            } elseif (!$width && $height) {
                $dstWidth = floor($srcWidth * ($dstHeight / $srcHeight));
            }
            
            // Create destination image
            $dstImage = imagecreatetruecolor($dstWidth, $dstHeight);
            
            // Preserve transparency for PNG images
            imagealphablending($dstImage, false);
            imagesavealpha($dstImage, true);
            
            // Resize the image with higher quality
            // Use IMG_BICUBIC for better quality resizing
            imagecopyresampled(
                $dstImage, $srcImage,
                0, 0, 0, 0,
                $dstWidth, $dstHeight, $srcWidth, $srcHeight
            );
            
            // Apply a slight unsharp mask to improve perceived sharpness
            if (function_exists('imageconvolution')) {
                // Unsharp mask parameters
                $amount = 50; // Strength of the sharpening effect (0-100)
                $radius = 0.5; // Radius of the sharpening effect (0.5-1.0 recommended)
                $threshold = 3; // Threshold for the sharpening effect (0-255)
                
                // Convert to usable values
                $amount = min(500, max(1, $amount)) * 0.016;
                $radius = min(50, max(1, $radius * 2)) * 0.5;
                $threshold = min(255, max(0, $threshold));
                
                // Apply unsharp mask
                $matrix = [
                    [-1, -1, -1],
                    [-1, 16, -1],
                    [-1, -1, -1],
                ];
                $divisor = array_sum(array_map('array_sum', $matrix));
                $offset = 0;
                
                imageconvolution($dstImage, $matrix, $divisor, $offset);
            }
            
            // Save as JPEG with higher quality
            imagejpeg($dstImage, $cachePath, $quality);
            
            // Free memory
            imagedestroy($srcImage);
            imagedestroy($dstImage);
            
            return $cacheUrl;
            
        } catch (\Exception $e) {
            // Cleanup any created images on error
            if (isset($srcImage) && ($srcImage instanceof \GdImage || is_resource($srcImage))) {
                imagedestroy($srcImage);
            }
            if (isset($dstImage) && ($dstImage instanceof \GdImage || is_resource($dstImage))) {
                imagedestroy($dstImage);
            }
            
            log_message('error', 'Image compression failed: ' . $e->getMessage() . ' for URL: ' . $url);
            return $url;
        } catch (\Error $e) {
            // Handle fatal errors (like memory exhaustion)
            if (isset($srcImage) && ($srcImage instanceof \GdImage || is_resource($srcImage))) {
                imagedestroy($srcImage);
            }
            if (isset($dstImage) && ($dstImage instanceof \GdImage || is_resource($dstImage))) {
                imagedestroy($dstImage);
            }
            
            log_message('error', 'Image compression fatal error: ' . $e->getMessage() . ' for URL: ' . $url);
            return $url;
        }
    }
}

if (!function_exists('compress_hero_image')) {
    /**
     * Compress hero/banner images with moderate quality
     * Suitable for large header images on landing pages
     */
    function compress_hero_image($url, $width = null, $height = null)
    {
        $config = config('ImageCompression');
        $settings = $config->hero;
        
        return compress_image(
            $url, 
            $width ?? $settings['width'], 
            $height ?? $settings['height'], 
            $settings['quality'], 
            $settings['max_size']
        );
    }
}

if (!function_exists('compress_gallery_image')) {
    /**
     * Compress gallery images with good balance of quality and size
     * Suitable for photo galleries and content images
     */
    function compress_gallery_image($url, $width = null, $height = null)
    {
        $config = config('ImageCompression');
        $settings = $config->gallery;
        
        return compress_image(
            $url, 
            $width ?? $settings['width'], 
            $height ?? $settings['height'], 
            $settings['quality'], 
            $settings['max_size']
        );
    }
}

if (!function_exists('compress_thumbnail')) {
    /**
     * Compress small thumbnails and avatars
     * Higher quality since they're small anyway
     */
    function compress_thumbnail($url, $width = null, $height = null)
    {
        $config = config('ImageCompression');
        $settings = $config->thumbnail;
        
        return compress_image(
            $url, 
            $width ?? $settings['width'], 
            $height ?? $settings['height'], 
            $settings['quality'], 
            $settings['max_size']
        );
    }
}

if (!function_exists('compress_card_image')) {
    /**
     * Compress card images for content sections
     * Good balance for medium-sized content images
     */
    function compress_card_image($url, $width = null, $height = null)
    {
        $config = config('ImageCompression');
        $settings = $config->card;
        
        return compress_image(
            $url, 
            $width ?? $settings['width'], 
            $height ?? $settings['height'], 
            $settings['quality'], 
            $settings['max_size']
        );
    }
}
