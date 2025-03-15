<?= $this->include('partials/main') ?>

<head>

    <?php echo view('partials/title-meta', array(
        'program_info' => $program_info,
        'img_url' => $announcement['img_url'],  
        'title' => $announcement['title'],
        'meta_title' => $announcement['meta_title'],
        'meta_description' => $announcement['meta_description'],
        'tags' => $announcement['tags'],
        'slug' => $announcement['slug']
    )); ?>

    <!--Swiper slider css-->
    <link href="/assets/libs/swiper/swiper-bundle.min.css" rel="stylesheet" type="text/css" />

    <?= $this->include('partials/head-css') ?>

</head>

<body data-bs-spy="scroll" data-bs-target="#navbar-example">

    <!-- Begin page -->
    <div class="layout-wrapper landing">
        <?= $this->include('ybb/common/navbar') ?>

        <!-- start blog -->
        <section class="section" id="blog">
            <div class="container">
            <div class="row mb-4">
                <div class="col-12">
                <div class="card">
                    <img src="<?= $announcement['img_url']; ?>" alt="" class="img-fluid rounded-top" style="object-fit: cover;" />
                    <div class="card-body">
                    <ul class="list-inline fs-14 text-muted">
                        <li class="list-inline-item">
                        <i class="ri-calendar-line align-bottom me-1"></i> <?= date('F j, Y', strtotime($announcement['created_at'])); ?>
                        </li>
                    </ul>
                    <a href="javascript:void(0);">
                        <h1 class="fw-bold"><?= $announcement['title']; ?></h1>
                    </a>
                    <p class="card-text text-muted fs-14"><?= $announcement['description']; ?></p>
                    </div>
                </div>
                </div>
            </div>
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