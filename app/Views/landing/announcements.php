<?= $this->include('partials/main') ?>

<head>

    <?= $this->include('partials/title-meta', ['meta_title' => "Announcements"]) ?>

    <!--Swiper slider css-->
    <link href="/assets/libs/swiper/swiper-bundle.min.css" rel="stylesheet" type="text/css" />

    <?= $this->include('partials/head-css') ?>

</head>

<body data-bs-spy="scroll" data-bs-target="#navbar-example">

    <!-- Begin page -->
    <div class="layout-wrapper landing">
        <?= $this->include('landing/common/navbar') ?>

        <!-- start Announcements title section -->
        <section class="section position-relative pb-5 bg-light" id="announcements-title">
            <div class="bg-overlay bg-overlay-pattern opacity-50"></div>
            <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                <div class="text-center pt-5 mt-5">
                    <h1 class="mb-3 ff-secondary fw-semibold text-capitalize lh-base">Announcements</h1>
                    <p class="text-muted fs-16">Stay updated with the latest news about our programs.</p>
                </div>
                </div>
            </div>
            </div>
        </section>
        <!-- end Announcements title section -->

        <!-- start Announcements section -->
        <section class="section py-5 position-relative bg-light" id="announcements">

            <div class="container">

                <!-- Announcements content -->
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card shadow-lg border-0 rounded-4">
                            <div class="card-body p-4">
                                <div class="d-flex align-items-center mb-4">
                                    <div class="flex-shrink-0">
                                        <div class="avatar-sm">
                                            <div class="avatar-title bg-soft-success text-success rounded-circle fs-4">
                                                <i class="ri-volume-up-line"></i>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <h5 class="card-title mb-1">Recent Announcements</h5>
                                        <p class="text-muted mb-0">Stay updated with the latest news about our programs</p>
                                    </div>
                                </div>

                                <?php if (isset($news) && !empty($news)) : ?>
                                    <div class="row">
                                        <?php foreach ($news as $item) : ?>
                                            <div class="col-lg-6 mb-4">
                                                <div class="card border card-animate h-100">
                                                    <div class="card-body">
                                                        <div class="ribbon ribbon-info ribbon-shape trending-ribbon">
                                                            <span class="trending-ribbon-text">New</span> <i class="ri-flashlight-fill text-white align-bottom"></i>
                                                        </div>
                                                        <div class="d-flex align-items-center mb-3">
                                                            <div class="flex-shrink-0">
                                                                <div class="avatar-sm">
                                                                    <span class="avatar-title bg-soft-primary text-primary rounded-circle fs-18">
                                                                        <i class="ri-item-line"></i>
                                                                    </span>
                                                                </div>
                                                            </div>
                                                            <div class="flex-grow-1 ms-3">
                                                                <h5 class="fs-16 mb-1"><?= $item['title'] ?? 'Announcement Title' ?></h5>
                                                                <?php if (isset($item['published_date'])) : ?>
                                                                    <p class="text-muted mb-0">
                                                                        <i class="ri-calendar-event-line me-1 align-bottom"></i>
                                                                        <?= date('M d, Y', strtotime($item['published_date'])) ?>
                                                                    </p>
                                                                <?php endif; ?>
                                                            </div>
                                                        </div>
                                                        <p class="text-muted mb-3"><?= $item['short_description'] ?? substr($item['description'] ?? 'Announcement Description', 0, 150) . '...' ?></p>
                                                        <div>
                                                            <a href="javascript:void(0);" class="link-primary">Read More <i class="ri-arrow-right-line align-bottom ms-1"></i></a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                <?php else : ?>
                                    <div class="text-center p-4">
                                        <div class="avatar-lg mx-auto mb-4">
                                            <div class="avatar-title bg-light text-primary rounded-circle fs-24">
                                                <i class="ri-megaphone-line"></i>
                                            </div>
                                        </div>
                                        <h5>No announcements available</h5>
                                        <p class="text-muted">Check back later for announcements about our programs.</p>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <?= $this->include('landing/common/contact-widget') ?>

                
            </div>
        </section>
        <!-- end Announcements section -->

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