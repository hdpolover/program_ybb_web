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

        <!-- Hero Section with Program Banner and Quick Info -->
        <?= $this->include('landing/program-detail/hero') ?>

        <!-- start Program Detail section -->
        <section class="section py-5 position-relative bg-light" id="program-detail">
            <div class="container">
                <!-- Registration Guidelines CTA Section -->
                <?= $this->include('landing/program-detail/registration-cta') ?>

                <div class="row mt-4">
                    <!-- Program Content Column -->
                    <div class="col-lg-8">
                        <!-- Program Overview -->
                        <?= $this->include('landing/program-detail/overview') ?>

                        <!-- Program Timeline -->
                        <?php if (isset($schedules) && !empty($schedules)) : ?>
                            <?= $this->include('landing/program-detail/timeline') ?>
                        <?php endif; ?>

                        <!-- Program Testimonials -->
                        <?php if (isset($testimonials) && !empty($testimonials)) : ?>
                            <?= $this->include('landing/program-detail/testimonials') ?>
                        <?php endif; ?>
                    </div>

                    <!-- Sidebar Column -->
                    <div class="col-lg-4">
                        <?= $this->include('landing/program-detail/sidebar') ?>
                    </div>
                </div>
            </div>
        </section>
        <!-- end Program Detail section -->

        <!-- Photo Gallery Section -->
        <?= $this->include('landing/program-detail/gallery') ?>

        <?= $this->include('landing/common/footer') ?>
    </div>
    <!-- end layout wrapper -->

    <?= $this->include('partials/vendor-scripts') ?>

    <!--Swiper slider js-->
    <script src="/assets/libs/swiper/swiper-bundle.min.js"></script>

    <!-- Initialize Lightbox for Gallery -->
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // Initialize image popups if Magnific Popup is available
            if (typeof $.fn.magnificPopup !== 'undefined') {
                $('.image-popup').magnificPopup({
                    type: 'image',
                    closeOnContentClick: true,
                    mainClass: 'mfp-img-mobile',
                    image: {
                        verticalFit: true
                    }
                });
            }
        });
    </script>

    <!-- landing init -->
    <script src="/assets/js/pages/landing.init.js"></script>
</body>

</html>