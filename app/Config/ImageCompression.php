<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

/**
 * Image Compression Configuration
 * 
 * This file contains all the configuration options for image compression
 * throughout the application, particularly for landing pages.
 */
class ImageCompression extends BaseConfig
{
    /**
     * Default compression settings
     */
    public array $defaults = [
        'quality' => 85,           // Default JPEG quality (0-100)
        'max_size' => 400,         // Default max file size in KB before compression
        'timeout' => 10,           // Download timeout in seconds
        'max_file_size' => 10240,  // Maximum file size to process (10MB)
        'max_dimensions' => 4000,   // Maximum width/height to process
    ];

    /**
     * Hero/Banner image settings
     * For large header images on landing pages
     */
    public array $hero = [
        'width' => 1920,
        'height' => 600,
        'quality' => 82,    // Slightly lower for large images
        'max_size' => 600,  // Allow larger file size for hero images
    ];

    /**
     * Gallery image settings
     * For photo galleries and content images
     */
    public array $gallery = [
        'width' => 600,
        'height' => 400,
        'quality' => 85,    // Good balance for galleries
        'max_size' => 400,
    ];

    /**
     * Thumbnail settings
     * For small thumbnails and avatars
     */
    public array $thumbnail = [
        'width' => 150,
        'height' => 150,
        'quality' => 88,    // Higher quality for small images
        'max_size' => 200,
    ];

    /**
     * Card image settings
     * For medium-sized content images in cards
     */
    public array $card = [
        'width' => 400,
        'height' => 250,
        'quality' => 85,    // Good balance for content cards
        'max_size' => 300,
    ];

    /**
     * Progressive JPEG settings
     * Whether to create progressive JPEGs (loads gradually)
     */
    public bool $progressive = true;

    /**
     * Cache settings
     */
    public array $cache = [
        'directory' => 'assets/cache/images/',
        'enabled' => true,
        'lifetime' => 2592000, // 30 days in seconds
    ];

    /**
     * WebP support (for future enhancement)
     */
    public array $webp = [
        'enabled' => false,   // Enable when ready to use WebP
        'quality' => 80,      // WebP quality
        'fallback' => true,   // Provide JPEG fallback
    ];
}