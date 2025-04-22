<?php
// Define default values from environment variables with fallbacks
$defaultSiteName = env('DEFAULT_SITE_NAME', 'Youth Break the Boundaries');
$defaultTagline = env('DEFAULT_TAGLINE', 'Connecting youth across borders');
$defaultLogoUrl = env('DEFAULT_LOGO_URL', 'https://ybbfoundation.com/assets/images/logo.png');
$defaultLocation = env('DEFAULT_LOCATION', 'Tokyo, Japan');
$defaultOrganizer = env('DEFAULT_ORGANIZER', 'Youth Break the Boundaries Foundation');

// Create variables for easier use
$siteName = $webSettings['name'] ?? $defaultSiteName;
$siteTagline = isset($meta_description) ? $meta_description : ($webSettings['tagline'] ?? $defaultTagline);
$siteLogoUrl = $webSettings['logo_url'] ?? $defaultLogoUrl;
$pageTitle = isset($meta_title) ? $meta_title : (isset($title) ? $title : 'YBB Program');
$fullTitle = $pageTitle . ' | ' . $siteName;
?>

<!-- Basic Meta Tags -->
<meta charset="utf-8" />
<title><?= $fullTitle ?></title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta content="<?= $siteTagline ?>" name="description" />
<meta content="<?= isset($tags) ? $tags : 'default, keywords' ?>" name="keywords" />
<meta content="<?= isset($slug) ? $slug : 'default-slug' ?>" name="slug" />
<meta content="<?= $siteName ?>" name="author" />
<meta name="robots" content="index, follow" />
<meta name="csrf-token" content="<?= csrf_hash() ?>" />
<meta name="theme-color" content="#ffffff" />
<meta name="msapplication-TileColor" content="#ffffff" />
<meta name="msapplication-TileImage" content="<?= $siteLogoUrl ?>" />

<!-- Open Graph Meta Tags (used by Facebook, Google, Twitter, and other platforms) -->
<meta property="og:title" content="<?= $fullTitle ?>" />
<meta property="og:description" content="<?= $siteTagline ?>" />
<meta property="og:image" content="<?= $siteLogoUrl ?>" />
<meta property="og:image:alt" content="<?= $siteName ?> logo" />
<meta property="og:type" content="website" />
<meta property="og:url" content="<?= current_url() ?>" />
<meta property="og:site_name" content="<?= $siteName ?>" />

<!-- Twitter Card Meta Tags -->
<meta name="twitter:card" content="summary_large_image" />
<meta name="twitter:title" content="<?= $fullTitle ?>" />
<meta name="twitter:description" content="<?= $siteTagline ?>" />
<meta name="twitter:image" content="<?= $siteLogoUrl ?>" />
<meta name="mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="black" />
<meta name="apple-mobile-web-app-title" content="<?= $webSettings['name'] ?? $defaultSiteName; ?>" />
<meta name="application-name" content="<?= $webSettings['name'] ?? $defaultSiteName; ?>" />
<meta name="msapplication-TileColor" content="#ffffff" />
<meta name="msapplication-TileImage" content="<?= $webSettings['logo_url'] ?? $defaultLogoUrl; ?>" />
<meta property="og:url" content="<?= current_url(); ?>" />
<meta property="og:type" content="website" />
<meta property="og:title" content="<?= isset($meta_title) ? $meta_title : 'YBB Program' ?> | <?= $webSettings['name'] ?? $defaultSiteName; ?>" />
<meta property="og:description" content="<?= isset($meta_description) ? $meta_description : 'A Program organized by Youth Break the Boundaries Foundation' ?>" />
<meta property="og:image" content="<?= isset($img_url) && !empty($img_url) ? $img_url : ($webSettings['logo_url'] ?? $defaultLogoUrl); ?>" />
<meta property="og:site_name" content="<?= $webSettings['name'] ?? $defaultSiteName; ?>" />
<link rel="canonical" href="<?= current_url(); ?>" />
<!-- App favicon -->
<link rel="shortcut icon" href="<?= $webSettings['logo_url'] ?? $defaultLogoUrl; ?>">
<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "Event",
    "name": "<?= $webSettings['name'] ?? $defaultSiteName; ?>",
    "description": "<?= $webSettings['tagline'] ?? $defaultTagline; ?>",
    "location": {
        "@type": "Place",
        "name": "<?= $webSettings['location'] ?? $defaultLocation; ?>",
        "address": "<?= $webSettings['location'] ?? $defaultLocation; ?>"
    },
    "image": "<?= $webSettings['logo_url'] ?? $defaultLogoUrl; ?>",
    "organizer": {
        "@type": "Organization",
        "name": "Youth Break the Boundaries Foundation",
        "url": "<?= current_url(); ?>"
    }
}
</script>