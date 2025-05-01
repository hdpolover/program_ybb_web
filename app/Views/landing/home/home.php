<?php
// Extract the current active program (usually the latest one)
$program_info = !empty($programs) ? $programs[0] : [];
$program_testimonies = $testimonies ?? [];

?>

<?= $this->include('partials/main') ?>

<head>
    <?= $this->include('landing/home/home_head') ?>

    <!-- Title Meta -->
    <?= $this->include('partials/title-meta', ['meta_title' => "Home"]) ?>

    <?= $this->include('partials/head-css') ?>
</head>

<body data-bs-spy="scroll" data-bs-target="#navbar-example">
    <!-- Begin page -->
    <div class="layout-wrapper landing">
        <?= $this->include('landing/common/navbar') ?>

        <!-- Hero Section -->
        <?= $this->include('landing/home/hero') ?>

        <!-- Program Category Section -->
        <?= $this->include('landing/home/program_category') ?>

        <!-- Current Programs Section -->
        <?= $this->include('landing/home/upcoming_programs') ?>

        <!-- Program Details Section -->
        <?= $this->include('landing/home/program_details') ?>

        <!-- Video Section -->
        <?php if (!empty($category['main_video_url'] ?? null)): ?>
            <?= $this->include('landing/home/video_section') ?>
        <?php endif; ?>

        <!-- Gallery Section -->
        <?= $this->include('landing/home/program-gallery') ?>

        <!-- Testimonial Section -->
        <?= $this->include('landing/home/testimonials') ?>

        <?= $this->include('landing/common/footer') ?>

    </div>
    <!-- End layout wrapper -->

    <?= $this->include('partials/vendor-scripts') ?>
    
    <!-- Home page specific scripts -->
    <?= $this->include('landing/home/home_scripts') ?>
    
    <!-- Home page specific styles -->
    <?= $this->include('landing/home/home_styles') ?>
</body>

</html>