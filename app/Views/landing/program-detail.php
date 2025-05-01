<?= $this->include('partials/main') ?>

<head>
    <!-- Title Meta -->
    <?= $this->include('partials/title-meta', ['meta_title' => "Program Details"]) ?>

    <!--Swiper slider css-->
    <link href="/assets/libs/swiper/swiper-bundle.min.css" rel="stylesheet" type="text/css" />
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

                        <!-- Program Rundowns -->
                        <?php if (isset($rundowns) && !empty($rundowns)): ?>
                            <?= $this->include('landing/program-detail/components/program-rundowns') ?>
                        <?php endif; ?>

                        <!-- Program Timeline -->
                        <?php if (isset($schedules) && !empty($schedules)): ?>
                            <?= $this->include('landing/program-detail/timeline') ?>
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
    <!-- landing init -->
    <script src="/assets/js/pages/landing.init.js"></script>
</body>

</html>