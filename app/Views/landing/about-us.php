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
                            <p class="lead text-muted mb-4 lh-base">Discover more about our foundation and programs</p>
                        </div>
                    </div>
                </div>
                <!-- end row -->
            </div>
            <!-- end container -->
        </section>
        <!-- end hero section -->

        <!-- start features -->
        <section class="section">
            <div class="bg-overlay bg-overlay-pattern opacity-50"></div>
            <div class="container">
                <div class="row align-items-center gy-4">
                    <div class="col-lg-12 order-2 order-lg-1">
                        <div>
                            <h1 class="text-uppercase text-success">About Foundation</h1>
                            <?= $about_us['about_ybb']; ?>
                        </div>
                    </div>
                    <!-- end col -->
                </div>
                <!-- end row -->
            </div>
            <!-- end container -->
        </section>
        <!-- end features -->

        <!-- start embed youtube video section -->
        <section class="section bg-light">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-lg-10">
                        <div class="text-center mt-lg-5">
                            <h2 class="fw-bold mb-4">Lean More About Us</h2>
                            <div class="embed-responsive embed-responsive-16by9" style="height: 70vh; ">
                                <iframe class="embed-responsive-item" src="<?= $about_us['ybb_video_url']; ?>" allowfullscreen style="height: 100%; width: 100%;"></iframe>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- end row -->
            </div>
            <!-- end container -->
        </section>
        <!-- end embed youtube video section -->

        <section class="section">
            <div class="container">
                <div class="row align-items-center mt-5 pt-lg-5 gy-4">
                    <div class="col-lg-4">
                        <div>
                            <?php
                            $random_photo = $program_photos[array_rand($program_photos)]['img_url'];
                            ?>
                            <img src="<?= $random_photo; ?>" alt="Random Program Photo" class="img-fluid" style="width: 100%; height: auto;">
                        </div>
                    </div>
                    <div class="col-lg-8">
                        <div class="ps-lg-5">
                            <h1 class="text-uppercase text-success">About Program</h1>
                            <?= $about_us['about_program']; ?>
                        </div>
                    </div>
                    <!-- end col -->
                </div>
                <!-- end row -->
            </div>
        </section>

        <div class="container-fluid">
            <div class="row align-items-center">
            <div class="col-lg-6 p-0">
                <div>
                <?php
                $random_photo_left = $program_photos[array_rand($program_photos)]['img_url'];
                ?>
                <img src="<?= $random_photo_left; ?>" alt="Random Program Photo Left" class="img-fluid" style="width: 100%; height: 100vh; object-fit: cover;">
                </div>
            </div>
            <div class="col-lg-6 p-0">
                <div>
                <?php
                $random_photo_right = $program_photos[array_rand($program_photos)]['img_url'];
                ?>
                <img src="<?= $random_photo_right; ?>" alt="Random Program Photo Right" class="img-fluid" style="width: 100%; height: 100vh; object-fit: cover;">
                </div>
            </div>
            </div>
            <!-- end row -->
        </div>
        <!-- end container-fluid -->

        <section class="section bg-light">
            <div class="container">

            </div>
        </section>


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