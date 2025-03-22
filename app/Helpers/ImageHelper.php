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
     * @return string URL to the cached image or original if processing fails
     */
    function compress_image($url, $width = null, $height = null, $quality = 80)
    {
        // Basic validation
        if (empty($url) || !extension_loaded('gd')) {
            return $url;
        }
        
        // Prepare cache directory
        $cacheDir = WRITEPATH . 'cache/images/';
        if (!is_dir($cacheDir) && !mkdir($cacheDir, 0755, true)) {
            return $url;
        }
        
        // Generate cache filename and path
        $filename = md5($url . $width . $height . $quality) . '.jpg';
        $cachePath = $cacheDir . $filename;
        $cacheUrl = base_url('writable/cache/images/' . $filename);
        
        // Return cached version if it exists
        if (file_exists($cachePath)) {
            return $cacheUrl;
        }
        
        try {
            // Get image content
            $imageData = @file_get_contents($url);
            if ($imageData === false) {
                return $url;
            }
            
            // Create image from string
            $srcImage = @imagecreatefromstring($imageData);
            if (!$srcImage) {
                return $url;
            }
            
            // Get original dimensions
            $srcWidth = imagesx($srcImage);
            $srcHeight = imagesy($srcImage);
            
            // Calculate new dimensions
            $dstWidth = $width ?: $srcWidth;
            $dstHeight = $height ?: $srcHeight;
            
            // Maintain aspect ratio if needed
            if ($width && !$height) {
                $dstHeight = floor($srcHeight * ($dstWidth / $srcWidth));
            } elseif (!$width && $height) {
                $dstWidth = floor($srcWidth * ($dstHeight / $srcHeight));
            }
            
            // Create destination image
            $dstImage = imagecreatetruecolor($dstWidth, $dstHeight);
            
            // Resize the image
            imagecopyresampled(
                $dstImage, $srcImage,
                0, 0, 0, 0,
                $dstWidth, $dstHeight, $srcWidth, $srcHeight
            );
            
            // Save as JPEG with specified quality
            imagejpeg($dstImage, $cachePath, $quality);
            
            // Free memory
            imagedestroy($srcImage);
            imagedestroy($dstImage);
            
            return $cacheUrl;
        } catch (\Throwable $e) {
            log_message('error', 'Image compression error: ' . $e->getMessage());
            return $url;
        }
    }
}
