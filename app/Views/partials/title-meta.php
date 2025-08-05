<?php
// Check and generate favicons if needed
$faviconService = new \App\Services\FaviconService();
if ($faviconService->shouldRegenerate()) {
    $faviconService->generate();
}

// Define default values from environment variables with fallbacks
$defaultSiteName = env('DEFAULT_SITE_NAME', 'Youth Break the Boundaries');
$defaultTagline = env('DEFAULT_TAGLINE', 'Connecting youth across borders');
$defaultLogoUrl = env('DEFAULT_LOGO_URL', 'https://ybbfoundation.com/assets/images/logo.png');
$defaultLocation = env('DEFAULT_LOCATION', 'Tokyo, Japan');
$defaultOrganizer = env('DEFAULT_ORGANIZER', 'Youth Break the Boundaries Foundation');
$defaultThemeColor = env('DEFAULT_THEME_COLOR', '#ffffff');
$defaultBgColor = env('DEFAULT_BG_COLOR', '#ffffff');

// Create variables for easier use
$siteName = $webSettings['name'] ?? $defaultSiteName;
$siteTagline = isset($meta_description) ? $meta_description : ($webSettings['tagline'] ?? $defaultTagline);
$siteLogoUrl = $webSettings['logo_url'] ?? $defaultLogoUrl;
$pageTitle = isset($meta_title) ? $meta_title : (isset($title) ? $title : 'YBB Program');
$fullTitle = $pageTitle . ' | ' . $siteName;
?>
<meta charset="utf-8">
<title><?= esc($fullTitle) ?></title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta content="<?= esc($siteTagline) ?>" name="description">
<meta content="<?= esc($defaultOrganizer) ?>" name="author">

<!-- App favicons and icons -->
<link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
<link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
<link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
<link rel="manifest" href="/assets/favicon/site.webmanifest">
<link rel="shortcut icon" href="/favicon.ico">
<meta name="msapplication-TileColor" content="<?= esc($defaultBgColor) ?>">
<meta name="msapplication-config" content="/browserconfig.xml">
<meta name="theme-color" content="<?= esc($defaultThemeColor) ?>">

<!-- SEO and sharing meta tags -->
<link rel="canonical" href="<?= current_url() ?>">
<meta property="og:type" content="website">
<meta property="og:url" content="<?= current_url() ?>">
<meta property="og:site_name" content="<?= esc($siteName) ?>">
<meta property="og:title" content="<?= esc($fullTitle) ?>">
<meta property="og:description" content="<?= esc($siteTagline) ?>">
<meta property="og:image" content="/android-chrome-512x512.png">
<meta property="og:image:type" content="image/png">
<meta property="og:image:width" content="512">
<meta property="og:image:height" content="512">
<meta property="og:locale" content="en_US">

<!-- Twitter Card -->
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="<?= esc($fullTitle) ?>">
<meta name="twitter:description" content="<?= esc($siteTagline) ?>">
<meta name="twitter:image" content="/android-chrome-512x512.png">

<!-- Additional meta -->
<meta name="application-name" content="<?= esc($siteName) ?>">
<meta name="mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-title" content="<?= esc($siteName) ?>">
<meta name="apple-mobile-web-app-status-bar-style" content="default">
<link rel="mask-icon" href="/assets/favicon/safari-pinned-tab.svg" color="<?= esc($defaultThemeColor) ?>">