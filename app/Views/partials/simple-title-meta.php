<meta charset="utf-8" />
<title><?= ($title) ? $title : 'Default Title' ?> | <?= $program_info['name']; ?></title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="<?= csrf_hash() ?>" />
<!-- App favicon -->
<link rel="shortcut icon" href="<?= $program_info['logo_url']; ?>">
<!-- Basic OpenGraph tags -->
<meta property="og:title" content="<?= ($title) ? $title : 'Default Title' ?> | <?= $program_info['name']; ?>" />
<meta property="og:image" content="<?= $program_info['logo_url']; ?>" />
<meta property="og:site_name" content="<?= $program_info['name']; ?>" />
