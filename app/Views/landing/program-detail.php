<?= $this->include('partials/main') ?>

<head>    <?php 
    $siteName = env('DEFAULT_SITE_NAME', 'Japan Youth Summit');
    // Get program info from the view data
    $programTitle = isset($program['title']) ? $program['title'] : 'Program Details';
    $programDesc = isset($program['description']) ? 
        substr(strip_tags($program['description']), 0, 160) :
        "Discover the details of this " . $siteName . " program. Learn about the unique opportunities, requirements, and benefits of participating.";
    echo view('partials/landing-meta', array(
        'title' => $programTitle,
        'meta_description' => $programDesc,
        'meta_keywords' => strtolower($siteName) . ' program details, ' . strtolower($siteName) . ' opportunity, cultural exchange program, youth development japan'
    )); ?>

    <!--Swiper slider css-->
    <link href="/assets/libs/swiper/swiper-bundle.min.css" rel="stylesheet" type="text/css" />
    
    <!-- AOS Animation Library -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    
    <?= $this->include('partials/head-css') ?>
</head>

<body data-bs-spy="scroll" data-bs-target="#navbar-example">
    <!-- Begin page -->
    <div class="layout-wrapper landing">
        <?= $this->include('landing/common/navbar') ?>

        <!-- Hero Section -->
        <?= $this->include('landing/program-detail/hero') ?>

        <!-- Main Program Detail Section -->
        <section class="section py-5 position-relative bg-light" id="program-detail">
            <div class="container">
                <!-- Registration Guidelines CTA -->
                <?= $this->include('landing/program-detail/guideline-full-card') ?>

                <div class="row mt-4">
                    <!-- Left Column: Program Content -->
                    <div class="col-lg-8">
                        <!-- Program Overview -->
                        <?= $this->include('landing/program-detail/overview') ?>

                        <!-- Program Speakers -->
                        <?php if (isset($speakers) && !empty($speakers)): ?>
                            <?= $this->include('landing/program-detail/components/program-speakers') ?>
                        <?php endif; ?>

                        <!-- Program Rundowns -->
                        <?php if (isset($rundowns) && !empty($rundowns)): ?>
                            <?= $this->include('landing/program-detail/components/program-rundowns') ?>
                        <?php endif; ?>

                        <!-- Program Schedules -->
                        <?php if (isset($schedules) && !empty($schedules)): ?>
                            <?= $this->include('landing/program-detail/components/program-schedules') ?>
                        <?php endif; ?>

                        <!-- Program Faqs -->
                        <?php if (isset($faqs) && !empty($faqs)): ?>
                            <?= $this->include('landing/program-detail/faqs') ?>
                        <?php endif; ?>

                        <!-- Program Testimonials -->
                        <?php if (isset($testimonials) && !empty($testimonials)): ?>
                            <?= $this->include('landing/program-detail/testimonials') ?>
                        <?php endif; ?>
                    </div>

                    <!-- Right Column: Sidebar -->
                    <div class="col-lg-4">
                        <?= $this->include('landing/program-detail/sidebar') ?>
                    </div>
                </div>
            </div>
        </section>

        <!-- Photo Gallery Section -->

        <!-- Footer -->
        <?= $this->include('landing/common/footer') ?>
    </div>
    <!-- end layout wrapper -->

    <?= $this->include('partials/vendor-scripts') ?>
    <!--Swiper slider js-->
    <script src="/assets/libs/swiper/swiper-bundle.min.js"></script>
    
    <!-- AOS Animation Library -->
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        AOS.init({
            duration: 800,
            easing: 'ease-in-out',
            once: true,
            offset: 100
        });
    </script>
    
    <!-- landing init -->
    <script src="/assets/js/pages/landing.init.js"></script>
</body>

</html>