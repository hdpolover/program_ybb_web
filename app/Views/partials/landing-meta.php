<?php
// Get the environment variables for the program
$programName = env('DEFAULT_SITE_NAME', 'Japan Youth Summit');
$programTagline = env('DEFAULT_TAGLINE', 'Connecting Japanese youth with the world');
$programLocation = env('DEFAULT_LOCATION', 'Tokyo, Japan');
$programOrganizer = env('DEFAULT_ORGANIZER', 'Youth Break the Boundaries Foundation');
$programThemeColor = env('DEFAULT_THEME_COLOR', '#ffffff');
$programBgColor = env('DEFAULT_BG_COLOR', '#ffffff');

// Get request uri for canonical URL
$currentUri = current_url(true);
$baseUrl = site_url();

// Set the title based on the page
$pageTitle = isset($title) ? $title : (isset($meta_title) ? $meta_title : '');
$fullTitle = $pageTitle ? $pageTitle . ' | ' . $programName : $programName;

// Set the description based on the page
$pageDescription = isset($meta_description) ? $meta_description : $programTagline;

// Set the image URL for social sharing
$shareImage = isset($meta_image) ? $meta_image : '/assets/favicon/android-chrome-512x512.png';
?>
<meta charset="utf-8">
<title><?= esc($fullTitle) ?></title>
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0">
<meta name="description" content="<?= esc($pageDescription) ?>">
<meta name="author" content="<?= esc($programOrganizer) ?>">

<!-- App favicon -->
<link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
<link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
<link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
<link rel="manifest" href="/assets/favicon/site.webmanifest">
<link rel="shortcut icon" href="/favicon.ico">
<meta name="msapplication-TileColor" content="<?= esc($programBgColor) ?>">
<meta name="msapplication-config" content="/browserconfig.xml">
<meta name="theme-color" content="<?= esc($programThemeColor) ?>">

<!-- SEO tags -->
<link rel="canonical" href="<?= esc($currentUri) ?>">
<meta name="robots" content="index, follow">
<meta name="googlebot" content="index, follow">

<!-- Open Graph / Facebook -->
<meta property="og:type" content="website">
<meta property="og:url" content="<?= esc($currentUri) ?>">
<meta property="og:site_name" content="<?= esc($programName) ?>">
<meta property="og:title" content="<?= esc($fullTitle) ?>">
<meta property="og:description" content="<?= esc($pageDescription) ?>">
<meta property="og:image" content="<?= esc(site_url($shareImage)) ?>">
<meta property="og:image:type" content="image/png">
<meta property="og:image:width" content="512">
<meta property="og:image:height" content="512">
<meta property="og:locale" content="en_US">

<!-- Twitter -->
<meta property="twitter:card" content="summary_large_image">
<meta property="twitter:url" content="<?= esc($currentUri) ?>">
<meta property="twitter:title" content="<?= esc($fullTitle) ?>">
<meta property="twitter:description" content="<?= esc($pageDescription) ?>">
<meta property="twitter:image" content="<?= esc(site_url($shareImage)) ?>">

<!-- Additional meta -->
<meta name="application-name" content="<?= esc($programName) ?>">
<meta name="mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-title" content="<?= esc($programName) ?>">
<meta name="apple-mobile-web-app-status-bar-style" content="default">
<meta name="format-detection" content="telephone=no">
<meta name="geo.placename" content="<?= esc($programLocation) ?>">

<!-- For better SEO with social sharing -->
<?php if (isset($meta_keywords)): ?>
<meta name="keywords" content="<?= esc($meta_keywords) ?>">
<?php endif; ?>

<!-- Minimal UI -->
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0">
<meta name="theme-color" content="<?= esc($programThemeColor) ?>">
