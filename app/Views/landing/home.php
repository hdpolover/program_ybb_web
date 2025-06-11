<?php
// Extract the current active program (usually the latest one)
$program_info = !empty($programs) ? $programs[0] : [];
$program_testimonies = $testimonies ?? [];
$program_photos = $photos ?? [];
?>

<?= $this->include('partials/main') ?>

<head>    <?php 
    $siteName = env('DEFAULT_SITE_NAME', 'Japan Youth Summit');
    echo view('partials/landing-meta', array(
        'title' => 'Home',
        'meta_description' => 'Welcome to ' . $siteName . '. Connect with peers through cultural exchange programs and make a global impact. Join our community of young leaders and explore opportunities for international collaboration.',
        'meta_keywords' => strtolower($siteName) . ', youth exchange program, cultural exchange japan, ' . strtolower($siteName) . ' program, international youth program'
    )); ?>
    <?= $this->include('landing/home/home_head') ?>
    <?= $this->include('partials/head-css') ?>
</head>

<body data-bs-spy="scroll" data-bs-target="#navbar-example">
    <!-- Begin page -->
    <div class="layout-wrapper landing">
        <?= $this->include('landing/common/navbar') ?>

        <!-- Hero Section -->
        <?= $this->include('landing/home/hero', ['program_photos' => $program_photos]) ?>

        <!-- Program Category Section -->
        <?= $this->include('landing/home/program_category', ['category' => $category ?? []]) ?>

        <!-- Current Programs Section -->
        <?= $this->include('landing/home/upcoming_programs', ['programs' => $programs ?? [], 'category' => $category ?? []]) ?>

        <!-- Program Details Section -->
        <?= $this->include('landing/home/program_details', ['program_photos' => $program_photos, 'category' => $category ?? []]) ?>

        <!-- Video Section -->
        <?= $this->include('landing/home/video_section', ['program_info' => $program_info]) ?>

        <!-- Gallery Section -->
        <?= $this->include('landing/home/program-gallery', ['program_photos' => $program_photos]) ?>

        <!-- Testimonial Section -->
        <?= $this->include('landing/home/testimonials', ['program_testimonies' => $program_testimonies]) ?>

        <?= $this->include('landing/common/footer') ?>

    </div>
    <!-- End layout wrapper -->

    <?= $this->include('partials/vendor-scripts') ?>
    
    <!-- Home page specific scripts -->
    <?= $this->include('landing/home/home_scripts', ['program_info' => $program_info]) ?>
    
    <!-- Home page specific styles -->
    <?= $this->include('landing/home/home_styles') ?>
</body>

</html>