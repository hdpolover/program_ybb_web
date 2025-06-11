<?php

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Load Composer's autoloader
require_once __DIR__ . '/../vendor/autoload.php';

use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

// Function to get settings from .env
function getEnvSettings() {
    $envFile = __DIR__ . '/../.env';
    if (!file_exists($envFile)) {
        return [];
    }

    $settings = [];
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos($line, '=') !== false && strpos($line, '#') !== 0) {
            list($key, $value) = explode('=', $line, 2);
            $key = trim($key);
            $value = trim($value, " \t\n\r\0\x0B\"'");
            $settings[$key] = $value;
        }
    }

    return $settings;
}

// Get settings from .env
$envSettings = getEnvSettings();

// Configuration with fallbacks
$config = [
    'logoPath' => __DIR__ . '/assets/logo/logo.png',
    'faviconDir' => __DIR__ . '/assets/favicon',
    'faviconRoot' => __DIR__, // For root level favicons
    'appName' => $envSettings['DEFAULT_SITE_NAME'] ?? 'Youth Break the Boundaries',
    'shortName' => $envSettings['DEFAULT_ORGANIZER'] ?? 'YBB',
    'themeColor' => $envSettings['DEFAULT_THEME_COLOR'] ?? '#ffffff',
    'backgroundColor' => $envSettings['DEFAULT_BG_COLOR'] ?? '#ffffff'
];

try {
    // Check if logo exists
    if (!file_exists($config['logoPath'])) {
        throw new Exception("Logo file not found at: {$config['logoPath']}. Please place your logo at public/assets/logo/logo.png");
    }

    // Create favicon directory if it doesn't exist
    if (!is_dir($config['faviconDir'])) {
        if (!@mkdir($config['faviconDir'], 0755, true)) {
            throw new Exception("Failed to create favicon directory: {$config['faviconDir']}");
        }
    }

    // Check if we need to regenerate
    $needsRegeneration = true;
    $cacheFile = $config['faviconDir'] . '/favicon.cache';
    if (file_exists($cacheFile)) {
        $cacheData = @json_decode(file_get_contents($cacheFile), true);
        if ($cacheData && 
            isset($cacheData['logo_mtime']) && 
            isset($cacheData['settings_hash']) &&
            $cacheData['logo_mtime'] === filemtime($config['logoPath']) &&
            $cacheData['settings_hash'] === md5(json_encode($config))) {
            $needsRegeneration = false;
        }
    }

    if ($needsRegeneration) {
        // Initialize image manager
        $manager = new ImageManager(new Driver());
        $sourceImage = $manager->read($config['logoPath']);

        // Generate main favicon.ico (32x32)
        $favicon32 = $sourceImage->resize(32, 32);
        $favicon32->save($config['faviconRoot'] . '/favicon.ico');
        $favicon32->save($config['faviconDir'] . '/favicon.ico');
        
        // Generate favicons for various sizes
        $sizes = [16, 32, 57, 60, 72, 76, 96, 114, 120, 144, 152, 180, 192, 512];
        foreach ($sizes as $size) {
            $resized = $sourceImage->resize($size, $size);
            $resized->save($config['faviconDir'] . "/favicon-{$size}x{$size}.png");
            
            // Save specific sizes at root for better compatibility
            if ($size == 16) {
                $resized->save($config['faviconRoot'] . '/favicon-16x16.png');
            } elseif ($size == 32) {
                $resized->save($config['faviconRoot'] . '/favicon-32x32.png');
            }
        }

        // Create special files with root copies
        $sourceImage->resize(180, 180)->save($config['faviconDir'] . '/apple-touch-icon.png');
        copy($config['faviconDir'] . '/apple-touch-icon.png', $config['faviconRoot'] . '/apple-touch-icon.png');
        
        $sourceImage->resize(192, 192)->save($config['faviconDir'] . '/android-chrome-192x192.png');
        $sourceImage->resize(512, 512)->save($config['faviconDir'] . '/android-chrome-512x512.png');

        // Generate web manifest
        $manifest = [
            'name' => $config['appName'],
            'short_name' => $config['shortName'],
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
            'theme_color' => $config['themeColor'],
            'background_color' => $config['backgroundColor'],
            'display' => 'standalone'
        ];

        if (!@file_put_contents($config['faviconDir'] . '/site.webmanifest', json_encode($manifest, JSON_PRETTY_PRINT))) {
            throw new Exception('Failed to write manifest file');
        }
        
        // Copy manifest to root
        copy($config['faviconDir'] . '/site.webmanifest', $config['faviconRoot'] . '/site.webmanifest');

        // Create browserconfig.xml for IE/Edge
        $browserconfig = <<<XML
<?xml version="1.0" encoding="utf-8"?>
<browserconfig>
    <msapplication>
        <tile>
            <square150x150logo src="/assets/favicon/mstile-150x150.png"/>
            <TileColor>{$config['backgroundColor']}</TileColor>
        </tile>
    </msapplication>
</browserconfig>
XML;
        file_put_contents($config['faviconDir'] . '/browserconfig.xml', $browserconfig);
        copy($config['faviconDir'] . '/browserconfig.xml', $config['faviconRoot'] . '/browserconfig.xml');

        // Update cache file with current logo modification time and settings hash
        $cacheData = [
            'logo_mtime' => filemtime($config['logoPath']),
            'settings_hash' => md5(json_encode($config))
        ];
        
        if (!@file_put_contents($cacheFile, json_encode($cacheData))) {
            throw new Exception('Failed to write cache file');
        }

        $message = "Favicons generated successfully!";
    } else {
        $message = "Favicons are up to date. No regeneration needed.";
    }
    $status = "success";
} catch (Exception $e) {
    $message = "Error: " . $e->getMessage();
    $status = "error";
}

?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Favicon Generator</title>
    <style>
        body {
            font-family: system-ui, -apple-system, sans-serif;
            max-width: 800px;
            margin: 2rem auto;
            padding: 0 1rem;
            line-height: 1.5;
        }
        .message {
            padding: 1rem;
            border-radius: 4px;
            margin: 1rem 0;
        }
        .success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .instructions {
            background-color: #f8f9fa;
            padding: 1rem;
            border-radius: 4px;
            margin-top: 1rem;
        }
        code {
            background: #e9ecef;
            padding: 0.2em 0.4em;
            border-radius: 3px;
            font-size: 85%;
        }
        pre {
            background: #f8f9fa;
            padding: 1rem;
            border-radius: 4px;
            overflow-x: auto;
        }
    </style>
</head>
<body>
    <h1>Favicon Generator</h1>
    <div class="message <?= $status ?>">
        <?= $message ?>
    </div>
    <?php if ($status === "success"): ?>
    <div class="instructions">
        <h2>How to update favicons:</h2>
        <ol>
            <li>Replace the logo file at <code>public/assets/logo/logo.png</code></li>
            <li>Update your <code>.env</code> file if needed to change:
                <ul>
                    <li><code>DEFAULT_SITE_NAME</code> - Full application name</li>
                    <li><code>DEFAULT_ORGANIZER</code> - Organization name (used as short name)</li>
                    <li><code>DEFAULT_THEME_COLOR</code> - Theme color (e.g., #ffffff)</li>
                    <li><code>DEFAULT_BG_COLOR</code> - Background color (e.g., #ffffff)</li>
                </ul>
            </li>
            <li>Run this script to generate new favicons</li>
        </ol>
        
        <h3>Current Settings</h3>
        <ul>
            <li>Site Name: <code><?= htmlspecialchars($config['appName']) ?></code></li>
            <li>Organization: <code><?= htmlspecialchars($config['shortName']) ?></code></li>
            <li>Theme Color: <code><?= htmlspecialchars($config['themeColor']) ?></code></li>
        </ul>

        <h3>Add these tags to your page header:</h3>
        <pre><code>&lt;link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png"&gt;
&lt;link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png"&gt;
&lt;link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png"&gt;
&lt;link rel="manifest" href="/site.webmanifest"&gt;
&lt;link rel="mask-icon" href="/assets/favicon/safari-pinned-tab.svg" color="<?= htmlspecialchars($config['themeColor']) ?>"&gt;
&lt;link rel="shortcut icon" href="/favicon.ico"&gt;
&lt;meta name="msapplication-TileColor" content="<?= htmlspecialchars($config['backgroundColor']) ?>"&gt;
&lt;meta name="msapplication-config" content="/browserconfig.xml"&gt;
&lt;meta name="theme-color" content="<?= htmlspecialchars($config['themeColor']) ?>"&gt;</code></pre>

        <h3>SEO Tip:</h3>
        <p>Make sure these meta tags are also in your header for better Google search appearance:</p>
        <pre><code>&lt;meta property="og:image" content="/assets/favicon/android-chrome-512x512.png"&gt;
&lt;meta property="og:image:type" content="image/png"&gt;
&lt;meta property="og:image:width" content="512"&gt;
&lt;meta property="og:image:height" content="512"&gt;
&lt;meta name="twitter:image" content="/assets/favicon/android-chrome-512x512.png"&gt;</code></pre>
    </div>
    <?php endif; ?>
</body>
</html>