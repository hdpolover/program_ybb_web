<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Require Composer's autoloader
require_once __DIR__ . '/../vendor/autoload.php';

use Intervention\Image\ImageManager;

try {
    // Check if a specific logo URL is provided in the request
    $logoUrl = $_GET['logo_url'] ?? null;
    
    echo "<h2>Dynamic Favicon Generator</h2>";
    
    // If no URL provided, try to get from BaseController logic (similar to how the website works)
    if (!$logoUrl) {
        // Make a simple HTTP request to get web settings (like BaseController does)
        function getCurrentUrl() {
            $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https://" : "http://";
            $host = $_SERVER['HTTP_HOST'];
            return $protocol . $host;
        }
        
        // Try to determine the current website domain
        $baseDomain = $_SERVER['HTTP_HOST'] ?? 'worldyouthfest.com';
        if ($baseDomain === 'localhost:8081') {
            $currentUrl = 'japanyouthsummit.com';
        } else {
            $currentUrl = $baseDomain;
        }
        
        // Remove protocol if present
        $currentUrl = preg_replace('~^https?://~', '', $currentUrl);
        
        echo "<p>Detected domain: <strong>{$currentUrl}</strong></p>";
        
        // Try to access the API to get web settings
        $apiBaseUrl = getenv('API_BASE_URL') ?: 'https://api.ybbfoundation.com/api';
        $url = $apiBaseUrl . '/web-settings?url=' . $currentUrl;
        
        echo "<p>Attempting to fetch web settings from: {$url}</p>";
        
        // Create a context with timeout
        $context = stream_context_create([
            'http' => [
                'timeout' => 5,
                'header' => "Accept: application/json\r\n"
            ]
        ]);
        
        // Try to get web settings
        $response = @file_get_contents($url, false, $context);
        
        if ($response) {
            $data = json_decode($response, true);
            $webSettings = $data['data'] ?? [];
            
            if (!empty($webSettings) && isset($webSettings['logo_url'])) {
                $logoUrl = $webSettings['logo_url'];
                echo "<p>Found logo URL in web settings: <strong>{$logoUrl}</strong></p>";
            } else {
                echo "<p>No logo URL found in web settings.</p>";
            }
        } else {
            echo "<p>Could not fetch web settings from API. Will use fallback logo.</p>";
        }
    }
    
    // Final fallback if still no logo URL
    if (!$logoUrl) {
        // Try commonly used logo files in the project
        $localLogos = [
            __DIR__ . '/assets/images/logo-light.png',
            __DIR__ . '/assets/images/logo-dark.png',
            __DIR__ . '/assets/images/logo-sm.png',
            __DIR__ . '/assets/images/logo.png'
        ];
        
        foreach ($localLogos as $localLogo) {
            if (file_exists($localLogo)) {
                $logoUrl = $localLogo;
                echo "<p>Using local logo file: <strong>{$logoUrl}</strong></p>";
                break;
            }
        }
        
        // If still no logo, use a remote URL as last resort
        if (!$logoUrl) {
            $logoUrl = 'https://worldyouthfest.com/assets/images/logo.png';
            echo "<p>Using remote fallback logo: <strong>{$logoUrl}</strong></p>";
        }
    }
    
    // Create an instance of the ImageManager
    $manager = new ImageManager('gd');
    
    // Ensure the assets/images directory exists
    $imgDir = __DIR__ . '/assets/images';
    if (!is_dir($imgDir)) {
        mkdir($imgDir, 0755, true);
    }
    
    // Load the source image
    echo "<p>Loading image from: <strong>{$logoUrl}</strong></p>";
    $img = $manager->read($logoUrl);
    
    // Generate favicons
    echo "<h3>Generating favicons...</h3>";
    
    // Generate favicon.ico in the root directory
    $img->resize(32, 32)->save(__DIR__ . '/favicon.ico');
    echo "<p>✅ Created favicon.ico in public directory</p>";
    
    // Generate PNG favicons
    $img->resize(16, 16)->save($imgDir . '/favicon-16x16.png');
    echo "<p>✅ Created favicon-16x16.png</p>";
    
    $img->resize(32, 32)->save($imgDir . '/favicon-32x32.png');
    echo "<p>✅ Created favicon-32x32.png</p>";
    
    $img->resize(180, 180)->save($imgDir . '/apple-touch-icon.png');
    echo "<p>✅ Created apple-touch-icon.png</p>";
    
    // Copy favicon.ico to assets/images
    copy(__DIR__ . '/favicon.ico', $imgDir . '/favicon.ico');
    echo "<p>✅ Copied favicon.ico to assets/images directory</p>";
    
    echo "<h3>All favicons created successfully!</h3>";
    echo "<p>Google search results should now show your custom favicon instead of the default CodeIgniter logo.</p>";
    
    // Show the favicon in the page
    echo "<div style='margin-top: 20px; padding: 15px; background: #f8f9fa; border-radius: 4px;'>";
    echo "<h4>Your favicon looks like this:</h4>";
    echo "<img src='" . base_url('favicon.ico') . "' style='border: 1px solid #ddd; padding: 5px;'> <strong>favicon.ico</strong><br>";
    echo "<img src='" . base_url('assets/images/favicon-16x16.png') . "' style='border: 1px solid #ddd; padding: 5px;'> <strong>favicon-16x16.png</strong><br>";
    echo "<img src='" . base_url('assets/images/favicon-32x32.png') . "' style='border: 1px solid #ddd; padding: 5px;'> <strong>favicon-32x32.png</strong><br>";
    echo "<img src='" . base_url('assets/images/apple-touch-icon.png') . "' style='border: 1px solid #ddd; padding: 5px;'> <strong>apple-touch-icon.png</strong><br>";
    echo "</div>";
    
    // Show the HTML code used in title-meta.php
    echo "<div style='margin-top: 20px; padding: 15px; background: #f8f9fa; border-radius: 4px;'>";
    echo "<h4>The following HTML has been added to your title-meta.php:</h4>";
    echo "<pre style='background: #fff; padding: 10px; border-radius: 4px; overflow-x: auto;'>";
    echo htmlspecialchars('<!-- App favicon -->
<?php 
// Use dynamic logo from web settings if available, otherwise use default favicon
$dynamicLogoUrl = $webSettings[\'logo_url\'] ?? $siteLogoUrl ?? null;
$defaultLogoUrl = base_url(\'assets/images/logo-light.png\');
$logoUrl = $dynamicLogoUrl ?: $defaultLogoUrl;

// Generate dynamic favicon link
echo \'<link rel="shortcut icon" href="\' . $logoUrl . \'">\';

// Add proper HTML for search engines to recognize the favicon
echo \'<link rel="icon" type="image/png" href="\' . $logoUrl . \'">\';
?>');
    echo "</pre>";
    echo "</div>";
    
} catch (Exception $e) {
    echo "<div style='color: red; padding: 15px; background: #f8d7da; border-radius: 4px;'>";
    echo "<strong>Error:</strong> " . $e->getMessage();
    echo "</div>";
}

// Helper function for use in this script
function base_url($uri = '')
{
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https://" : "http://";
    $host = $_SERVER['HTTP_HOST'];
    return $protocol . $host . '/' . ltrim($uri, '/');
}
?>
