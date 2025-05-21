<?= $this->include('partials/main') ?>

<head>

    <!-- Title Meta -->
    <?= $this->include('partials/title-meta', [
        'meta_title' => "Program Insights - ",
    ]) ?>

    <!--Swiper slider css-->
    <link href="/assets/libs/swiper/swiper-bundle.min.css" rel="stylesheet" type="text/css" />

    <?= $this->include('partials/head-css') ?>

</head>

<body data-bs-spy="scroll" data-bs-target="#navbar-example">

    <!-- Begin page -->
    <div class="layout-wrapper landing">
        <?= $this->include('landing/common/navbar') ?>


        <!-- start hero section -->
        <section class="section bg-light">
            <div class="bg-overlay bg-overlay-pattern opacity-50"></div>
            <div class="container">

                <div class="row justify-content-center">
                    <div class="col-lg-8">
                        <div class="text-center pt-5 mt-5">
                            <h1 class="mb-3 ff-secondary fw-semibold text-capitalize lh-base">Gallery</h1>
                            <p class="text-muted fs-16">Stay updated with the latest news about our programs.</p>
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
                <div class="row g-3">
                    <?php
                    // Get photos from the array or use fallback images
                    $gallery_photos = $photos ?? [];
                    // If no photos available, use fallback demo images
                    if (empty($gallery_photos)) {
                        $gallery_photos = [
                            [
                                'img_url' => '/assets/images/small/img-1.jpg',
                                'title' => 'Demo Photo 1',
                                'description' => 'This is a demo photo for the gallery' 
                            ],
                            [
                                'img_url' => '/assets/images/small/img-2.jpg',
                                'title' => 'Demo Photo 2',
                                'description' => 'This is a demo photo for the gallery'
                            ],
                            [
                                'img_url' => '/assets/images/small/img-3.jpg',
                                'title' => 'Demo Photo 3',
                                'description' => 'This is a demo photo for the gallery'
                            ],
                            [
                                'img_url' => '/assets/images/small/img-4.jpg',
                                'title' => 'Demo Photo 4',
                                'description' => 'This is a demo photo for the gallery'
                            ],
                            [
                                'img_url' => '/assets/images/small/img-5.jpg',
                                'title' => 'Demo Photo 5',
                                'description' => 'This is a demo photo for the gallery'
                            ],
                            [
                                'img_url' => '/assets/images/small/img-6.jpg',
                                'title' => 'Demo Photo 6',
                                'description' => 'This is a demo photo for the gallery'
                            ]
                        ];
                    }
                    shuffle($gallery_photos);
                    $display_photos = array_slice($gallery_photos, 0, 6);

                    foreach ($display_photos as $index => $photo):
                        $size_class = ($index < 2) ? 'col-lg-6' : 'col-lg-4 col-md-6';
                        $title = $photo['title'] ?? ($program_info['name'] ?? 'Program') . ' Photo ' . ($index + 1);
                        $description = $photo['description'] ?? 'Program photo';
                        $img_url = $photo['img_url'] ?? '/assets/images/small/img-1.jpg';
                    ?>
                    <div class="<?= $size_class ?>">
                        <div class="gallery-box card border-0 overflow-hidden">
                            <div class="gallery-container">
                                <a href="javascript:void(0);" class="gallery-popup"
                                    data-title="<?= htmlspecialchars($title) ?>"
                                    data-description="<?= htmlspecialchars($description) ?>" data-src="<?= $img_url ?>">
                                    <img src="<?= function_exists('compress_image') ? compress_image($img_url, 600, 400, 80, true) : $img_url; ?>"
                                        alt="<?= htmlspecialchars($title) ?>" class="img-fluid gallery-img"
                                        style="width: 100%; height: 250px; object-fit: cover; transition: transform 0.3s ease;">
                                    <div class="gallery-overlay">
                                        <div class="overlay-content">
                                            <h5 class="text-white"><?= htmlspecialchars($title) ?></h5>
                                            <i class="ri-search-eye-line text-white fs-24 mt-2"></i>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <br><br>
                <div class="row mt-5">
                    <div class="col-lg-12">
                        <!-- Swiper -->
                        <div class="swiper mySwiper pb-4">
                            <div class="swiper-wrapper">
                                <div class="swiper-slide">
                                    <div class="card">
                                        <div class="card-body">
                                            <div class="row g-1 mb-3">
                                                <div class="col-6">
                                                    <img src="/assets/images/nft/img-06.jpg" alt=""
                                                        class="img-fluid rounded">
                                                    <img src="https://img.themesbrand.com/velzon/images/img-2.gif"
                                                        alt="" class="img-fluid rounded mt-1">
                                                </div>
                                                <!--end col-->
                                                <div class="col-6">
                                                    <img src="https://img.themesbrand.com/velzon/images/img-5.gif"
                                                        alt="" class="img-fluid rounded mb-1">
                                                    <img src="/assets/images/nft/img-03.jpg" alt=""
                                                        class="img-fluid rounded">
                                                </div>
                                                <!--end col-->
                                            </div>
                                            <!--end row-->
                                            <h5 class="mb-0 fs-16">
                                                <center>
                                                    Korea Youth Summit 2025
                                                    <a href="#" target="_blank"
                                                        class="btn btn-sm btn-outline-primary mt-3">
                                                        <i class="ri-external-link-line me-1"></i> Visit Website
                                                    </a>
                                                </center>

                                            </h5>
                                        </div>
                                    </div>
                                </div>
                                <div class="swiper-slide">
                                    <div class="card">
                                        <div class="card-body">
                                            <div class="row g-1 mb-3">
                                                <div class="col-6">
                                                    <img src="/assets/images/nft/img-05.jpg" alt=""
                                                        class="img-fluid rounded">
                                                    <img src="https://img.themesbrand.com/velzon/images/img-1.gif"
                                                        alt="" class="img-fluid rounded mt-1">
                                                </div>
                                                <!--end col-->
                                                <div class="col-6">
                                                    <img src="https://img.themesbrand.com/velzon/images/img-4.gif"
                                                        alt="" class="img-fluid rounded mb-1">
                                                    <img src="/assets/images/nft/img-04.jpg" alt=""
                                                        class="img-fluid rounded">
                                                </div>
                                                <!--end col-->
                                            </div>
                                            <!--end row-->

                                            <h5 class="mb-0 fs-16">
                                                <center>
                                                    World Youth Fest 2025
                                                    <a href="#" target="_blank"
                                                        class="btn btn-sm btn-outline-primary mt-3">
                                                        <i class="ri-external-link-line me-1"></i> Visit Website
                                                    </a>
                                                </center>
                                            </h5>
                                        </div>
                                    </div>
                                </div>
                                <div class="swiper-slide">
                                    <div class="card">
                                        <div class="card-body">
                                            <div class="row g-1 mb-3">
                                                <div class="col-6">
                                                    <img src="/assets/images/nft/img-02.jpg" alt=""
                                                        class="img-fluid rounded">
                                                    <img src="https://img.themesbrand.com/velzon/images/img-3.gif"
                                                        alt="" class="img-fluid rounded mt-1">
                                                </div>
                                                <!--end col-->
                                                <div class="col-6">
                                                    <img src="https://img.themesbrand.com/velzon/images/img-1.gif"
                                                        alt="" class="img-fluid rounded mb-1">
                                                    <img src="/assets/images/nft/img-01.jpg" alt=""
                                                        class="img-fluid rounded">
                                                </div>
                                                <!--end col-->
                                            </div>
                                            <!--end row-->
                                            <h5 class="mb-0 fs-16">
                                                <center>
                                                    Japan Youth Summit 2025
                                                    <a href="#" target="_blank"
                                                        class="btn btn-sm btn-outline-primary mt-3">
                                                        <i class="ri-external-link-line me-1"></i> Visit Website
                                                    </a>
                                                </center>
                                            </h5>
                                        </div>
                                    </div>
                                </div>

                            </div>
                            <div class="swiper-pagination swiper-pagination-dark"></div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- end canva embed section -->


        <!-- start Partners & Sponsors section -->
        <section class="section py-5 position-relative bg-light" id="partners-sponsors">
            <div class="bg-overlay bg-overlay-pattern opacity-25"></div>
            <div class="container">
                <div class="row">
                    <div class="col-12">
                        <div class="card bg-primary border-0 rounded-4 overflow-hidden shadow-lg">
                            <div class="card-body p-4 position-relative">
                                <div class="bg-overlay bg-overlay-pattern opacity-20"></div>
                                <div class="row align-items-center">
                                    <div class="col-lg-8">
                                        <h3 class="text-white fw-bold mb-3">Join Our Community of Partners</h3>
                                        <p class="text-white mb-lg-0">We collaborate with forward-thinking organizations
                                            and passionate individuals who share our vision. Discover the meaningful
                                            impact you can make as a partner or sponsor today.</p>
                                    </div>
                                    <div class="col-lg-4 text-lg-end">
                                        <a href="javascript:void(0);" class="btn btn-light shadow-lg fw-semibold">
                                            Contact Us <i class="ri-arrow-right-line align-bottom ms-1"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- end Partners & Sponsors section -->

        <?= $this->include('landing/common/footer') ?>

    </div>
    <!-- end layout wrapper -->

    <?= $this->include('partials/vendor-scripts') ?>

    <!--Swiper slider js-->
    <script src="/assets/libs/swiper/swiper-bundle.min.js"></script>

    <!-- landing init -->
    <script src="/assets/js/pages/landing.init.js"></script>
    <script src="/assets/js/pages/nft-landing.init.js"></script>

</body>

</html>