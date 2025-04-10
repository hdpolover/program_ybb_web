<meta charset="utf-8" />
<title><?= isset($meta_title) ? $meta_title : (isset($title) ? $title : 'Default Title') ?> | <?= $webSettings['name']; ?></title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta content="<?= isset($meta_description) ? $meta_description : $webSettings['tagline'] ?>" name="description" />
<meta content="<?= isset($tags) ? $tags : 'default, keywords' ?>" name="keywords" />
<meta content="<?= isset($slug) ? $slug : 'default-slug' ?>" name="slug" />
<meta content="<?= $webSettings['name']; ?>" name="author" />
<meta name="robots" content="index, follow" />
<meta name="csrf-token" content="<?= csrf_hash() ?>" />
<meta name="theme-color" content="#ffffff" />
<meta name="msapplication-TileColor" content="#ffffff" />
<meta name="msapplication-TileImage" content="<?=  $webSettings['logo_url']; ?>" />
<meta name="mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="black" />
<meta name="apple-mobile-web-app-title" content="<?= $webSettings['name']; ?>" />
<meta name="application-name" content="<?= $webSettings['name']; ?>" />
<meta name="msapplication-TileColor" content="#ffffff" />
<meta name="msapplication-TileImage" content="<?= $webSettings['logo_url']; ?>" />
<meta property="og:url" content="<?= current_url(); ?>" />
<meta property="og:type" content="website" />
<meta property="og:title" content="<?= isset($meta_title) ? $meta_title : 'Default Title' ?> | <?= $webSettings['name']; ?>" />
<meta property="og:description" content="<?= isset($meta_description) ? $meta_description : 'Default Description' ?>" />
<meta property="og:image" content="<?= isset($img_url) && !empty($img_url) ? $img_url : $webSettings['logo_url']; ?>" />
<meta property="og:site_name" content="<?= $webSettings['name']; ?>" />
<link rel="canonical" href="<?= current_url(); ?>" />
<!-- App favicon -->
<link rel="shortcut icon" href="<?= $webSettings['logo_url']; ?>">
<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "Event",
    "name": "<?= $webSettings['name']; ?>",
    "description": "<?= $webSettings['tagline']; ?>",
    "location": {
        "@type": "Place",
        "name": "<?=  $webSettings['location']; ?>",
        "address": "<?= $webSettings['location']; ?>"
    },
    "image": "<?= $webSettings['logo_url']; ?>",
    "organizer": {
        "@type": "Organization",
        "name": "Youth Break the Boundaries Foundation",
        "url": "<?= current_url(); ?>"
    }
}
</script>