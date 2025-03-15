<?= $this->include('partials/main') ?>

<head>

    <?php echo view(
        'partials/title-meta',
        array(
            'program_info' => $program_info,
            'title' => $title,
            'meta_title' => $title,
            'meta_description' => $title,
            'tags' => $title,
            'slug' => $title
        )
    ); ?>

    <!--Swiper slider css-->
    <link href="/assets/libs/swiper/swiper-bundle.min.css" rel="stylesheet" type="text/css" />

    <?= $this->include('partials/head-css') ?>

</head>

<body data-bs-spy="scroll" data-bs-target="#navbar-example">

    <!-- Begin page -->
    <div class="layout-wrapper landing">
        <?= $this->include('ybb/common/navbar') ?>

        <!-- start hero section -->
        <section class="section bg-light">
            <div class="bg-overlay bg-overlay-pattern opacity-50"></div>
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-lg-10">
                        <div class="text-center mt-lg-5">
                            <h1 class="display-4 fw-bold mb-4 lh-base"><span class="text-success"><?= $title; ?></span></h1>
                            <p class="lead text-muted mb-4 lh-base">Discover detailed answers to common questions about our sponsorship program.</p>
                        </div>
                    </div>
                </div>
                <!-- end row -->
            </div>
            <!-- end container -->
        </section>
        <!-- end hero section -->

        <!-- start canva embed section -->
        <section class="section">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-lg-10">
                        <div class="embed-responsive embed-responsive-16by9" style="height: 100vh;">
                            <?= $program_info['sponsor_canva_url']; ?>
                        </div>
                    </div>
                    <!-- end row -->
                </div>
                <!-- end container -->
        </section>
        <!-- end canva embed section -->

        <?= $this->include('ybb/common/footer') ?>

    </div>
    <!-- end layout wrapper -->


    <?= $this->include('partials/vendor-scripts') ?>

    <!--Swiper slider js-->
    <script src="/assets/libs/swiper/swiper-bundle.min.js"></script>

    <!-- landing init -->
    <script src="/assets/js/pages/landing.init.js"></script>
</body>

</html>