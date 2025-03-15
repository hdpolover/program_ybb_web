<?= $this->include('partials/main') ?>

<head>

    <?php echo view('partials/title-meta', array(
        'program_info' => $program_info,
        'title' => $title,
        'meta_title' => $title,
        'meta_description' => $title,
        'tags' => $title,
        'slug' => $title
    )); ?>

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
                            <p class="lead text-muted lh-base">Read the latest announcements for <?= $program_info['name']; ?> </p>
                        </div>
                    </div>
                </div>
                <!-- end row -->
            </div>
            <!-- end container -->
        </section>
        <!-- end hero section -->

        <!-- start blog -->
        <section class="section" id="blog">
            <div class="container">
                <?php
                usort($announcements, function ($a, $b) {
                    return strtotime($b['created_at']) - strtotime($a['created_at']);
                });
                foreach ($announcements as $announcement):
                    $formatted_date = date('F j, Y', strtotime($announcement['created_at']));
                ?>
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="row g-0">
                                    <div class="col-md-4">
                                        <img src="<?= $announcement['img_url']; ?>" alt="" class="img-fluid rounded-start h-100" style="object-fit: cover;" />
                                    </div>
                                    <div class="col-md-8">
                                        <div class="card-body">
                                            <ul class="list-inline fs-14 text-muted">
                                                <li class="list-inline-item">
                                                    <i class="ri-calendar-line align-bottom me-1"></i> <?= $formatted_date; ?>
                                                </li>
                                            </ul>
                                            <a href="javascript:void(0);">
                                                <h1 class=" fw-bold"><?= $announcement['title']; ?></h1>
                                            </a>
                                            <p class="card-text text-muted fs-14"><?= substr($announcement['description'], 0, 250); ?>...</p>
                                            <div class="text-end">
                                                <a href="<?= base_url('announcements/' . $announcement['slug']); ?>" class="link-success">Read More <i class="ri-arrow-right-line align-bottom ms-1"></i></a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            <!-- end container -->
        </section>
        <!-- end blog -->

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