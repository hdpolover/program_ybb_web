<meta charset="utf-8" />
<title><?= ($meta_title) ? $meta_title : 'Default Title' ?> | <?= $program_info['name']; ?></title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta content="<?= ($meta_description) ? $meta_description : $program_info['tagline'] ?>" name="description" />
<meta content="<?= ($tags) ? $tags : 'default, keywords' ?>" name="keywords" />
<meta content="<?= ($slug) ? $slug : 'default-slug' ?>" name="slug" />
<meta content="<?= $program_info['name']; ?>" name="author" />
<meta name="robots" content="index, follow" />
<meta name="csrf-token" content="<?= csrf_hash() ?>" />
<meta name="theme-color" content="#ffffff" />
<meta name="msapplication-TileColor" content="#ffffff" />
<meta name="msapplication-TileImage" content="<?=  $program_info['logo_url']; ?>" />
<meta name="mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="black" />
<meta name="apple-mobile-web-app-title" content="<?= $program_info['name']; ?>" />
<meta name="application-name" content="<?= $program_info['name']; ?>" />
<meta name="msapplication-TileColor" content="#ffffff" />
<meta name="msapplication-TileImage" content="<?= $program_info['logo_url']; ?>" />
<meta property="og:url" content="<?= current_url(); ?>" />
<meta property="og:type" content="website" />
<meta property="og:title" content="<?= ($meta_title) ? $meta_title : 'Default Title' ?> | <?= $program_info['name']; ?>" />
<meta property="og:description" content="<?= ($meta_description) ? $meta_description : 'Default Description' ?>" />
<meta property="og:image" content="<?= (!empty($img_url)) ? $img_url : $program_info['logo_url']; ?>" />
<meta property="og:site_name" content="<?= $program_info['name']; ?>" />
<link rel="canonical" href="<?= current_url(); ?>" />
<!-- App favicon -->
<link rel="shortcut icon" href="<?= $program_info['logo_url']; ?>">
<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "Event",
    "name": "<?= $program_info['name']; ?>",
    "description": "<?= $program_info['tagline']; ?>",
    "startDate": "<?= $program_info['start_date']; ?>",
    "endDate": "<?=  $program_info['end_date']; ?>",
    "location": {
        "@type": "Place",
        "name": "<?=  $program_info['location']; ?>",
        "address": "<?= $program_info['location']; ?>"
    },
    "image": "<?= $program_info['logo_url']; ?>",
    "organizer": {
        "@type": "Organization",
        "name": "Youth Break the Boundaries Foundation",
        "url": "<?= current_url(); ?>"
    }
}
</script>