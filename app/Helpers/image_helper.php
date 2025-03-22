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
     * @param int $quality JPEG quality (0-100)
     * @param int $max_size Maximum size in KB to allow without compression (0 to always compress)
     * @return string URL to the cached image or original if processing fails
     */
    function compress_image($url, $width = null, $height = null, $quality = 90, $max_size = 250)
    {
        // Return original URL if empty
        if (empty($url)) {
            return $url;
        }
        
        // Check if the image is from an external URL or local path
        $is_external = (strpos($url, 'http://') === 0 || strpos($url, 'https://') === 0);
        
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
            // Get image content
            $imageData = @file_get_contents($url);
            if ($imageData === false) {
                log_message('error', 'Could not download image: ' . $url);
                return $url;
            }
            
            // Check the size of the image data
            $data_size = strlen($imageData) / 1024; // Size in KB
            
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
            log_message('error', 'Image compression failed: ' . $e->getMessage());
            return $url;
        }
    }
}
