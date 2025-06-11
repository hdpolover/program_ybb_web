<?php

namespace App\Services;

use Config\Favicon as FaviconConfig;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use CodeIgniter\Config\Factories;

class FaviconService
{
    protected $config;
    protected string $publicPath;
    protected string $cacheFile;
    protected ImageManager $imageManager;

    public function __construct()
    {
        // Try to load config from multiple methods to ensure it works
        try {
            $this->config = config('Favicon');
        } catch (\Throwable $e) {
            try {
                $this->config = new FaviconConfig();
            } catch (\Throwable $e) {
                // Set default values if config loading fails
                $this->config = new \stdClass();
                $this->config->logoPath = 'assets/logo/logo.png';
                $this->config->cacheDir = 'assets/favicon';
                $this->config->appName = 'Youth Break the Boundaries';
                $this->config->shortName = 'YBB';
                $this->config->themeColor = '#ffffff';
                $this->config->backgroundColor = '#ffffff';
            }
        }

        // Set public path
        $this->publicPath = defined('FCPATH') ? FCPATH : (__DIR__ . '/../../public/');
        $this->cacheFile = $this->publicPath . $this->config->cacheDir . '/favicon.cache';
        $this->imageManager = new ImageManager(new Driver());
    }

    /**
     * Check if favicons need to be regenerated
     */
    public function shouldRegenerate(): bool
    {
        $logoPath = $this->publicPath . $this->config->logoPath;
        
        // If cache file doesn't exist, regenerate
        if (!file_exists($this->cacheFile)) {
            return true;
        }

        // If logo file doesn't exist, don't regenerate
        if (!file_exists($logoPath)) {
            return false;
        }

        // Get the cached logo modification time
        $cachedMTime = @file_get_contents($this->cacheFile);
        if ($cachedMTime === false) {
            return true;
        }

        // If logo modification time is different, regenerate
        if (filemtime($logoPath) != $cachedMTime) {
            return true;
        }

        return false;
    }

    /**
     * Generate all favicon files
     */
    public function generate(): bool
    {
        try {
            $logoPath = $this->publicPath . $this->config->logoPath;

            // If logo doesn't exist, return false
            if (!file_exists($logoPath)) {
                log_message('error', 'Logo file not found at: ' . $logoPath);
                return false;
            }

            // Ensure favicon directory exists
            $directory = $this->publicPath . $this->config->cacheDir;
            if (!is_dir($directory)) {
                if (!@mkdir($directory, 0755, true)) {
                    log_message('error', 'Failed to create favicon directory: ' . $directory);
                    return false;
                }
            }

            // Load source image
            $sourceImage = $this->imageManager->read($logoPath);

            // Generate main favicon.ico (32x32)
            $sourceImage->resize(32, 32)->save($this->publicPath . 'favicon.ico');
            @copy($this->publicPath . 'favicon.ico', $directory . '/favicon.ico');

            // Generate favicons for various sizes
            $sizes = [16, 32, 57, 60, 72, 76, 96, 114, 120, 144, 152, 180, 192, 512];
            foreach ($sizes as $size) {
                $resized = $sourceImage->resize($size, $size);
                $resized->save($directory . "/favicon-{$size}x{$size}.png");
            }

            // Create special files
            $sourceImage->resize(180, 180)->save($directory . '/apple-touch-icon.png');
            $sourceImage->resize(192, 192)->save($directory . '/android-chrome-192x192.png');
            $sourceImage->resize(512, 512)->save($directory . '/android-chrome-512x512.png');

            // Generate web manifest
            $manifest = [
                'name' => $this->config->appName,
                'short_name' => $this->config->shortName,
                'icons' => [
                    [
                        'src' => '/assets/favicon/android-chrome-192x192.png',
                        'sizes' => '192x192',
                        'type' => 'image/png'
                    ],
                    [
                        'src' => '/assets/favicon/android-chrome-512x512.png',
                        'sizes' => '512x512',
                        'type' => 'image/png'
                    ]
                ],
                'theme_color' => $this->config->themeColor,
                'background_color' => $this->config->backgroundColor,
                'display' => 'standalone'
            ];

            if (!@file_put_contents($directory . '/site.webmanifest', json_encode($manifest, JSON_PRETTY_PRINT))) {
                log_message('error', 'Failed to write manifest file');
                return false;
            }

            // Update cache file with current logo modification time
            if (!@file_put_contents($this->cacheFile, filemtime($logoPath))) {
                log_message('error', 'Failed to write cache file');
                return false;
            }

            return true;
        } catch (\Throwable $e) {
            log_message('error', 'Failed to generate favicons: ' . $e->getMessage());
            return false;
        }
    }
}
