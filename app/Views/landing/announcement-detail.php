<?= $this->include('partials/main') ?>

<head>
    <?php echo view('partials/title-meta', array(
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

        <?= $this->include('landing/common/navbar') ?>

        <!-- start Announcement title section -->
        <section class="section position-relative pb-5" id="announcement-title" style="background-color: #f8f9fa;">
            <div class="bg-overlay bg-overlay-pattern opacity-50"></div>
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-lg-8">
                        <div class="text-center pt-5 mt-5">
                            <h1 class="mb-3 ff-secondary fw-semibold text-capitalize lh-base">Announcement</h1>
                            <p class="text-muted fs-16">Stay updated with our latest news, events, and important information.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- end title section -->

        <!-- start announcement content -->
        <section class="section py-5" id="announcement-detail">
            <div class="container">                <div class="row">
                    <div class="col-lg-12">
                        <div class="card border-0 shadow-sm overflow-hidden">
                            <!-- Full width banner image at top -->
                            <div class="card-img-top position-relative bg-light" style="height: 350px; overflow: hidden;">
                                <img src="<?= $announcement['img_url']; ?>" alt="<?= $announcement['title']; ?>" 
                                    class="img-fluid mx-auto d-block" 
                                    style="object-fit: contain; max-height: 100%; max-width: 100%;" />
                            </div>
                            
                            <div class="row g-0">
                                <!-- Content below the image -->
                                <div class="col-12">
                                    <div class="card-body p-4 p-lg-5">
                                        <div class="badge bg-primary-subtle text-primary mb-3">Announcement</div>
                                        
                                        <div class="d-flex align-items-center mb-3">
                                            <div class="flex-shrink-0">
                                                <i class="ri-calendar-line fs-16 text-muted"></i>
                                            </div>
                                            <div class="flex-grow-1 ms-2">
                                                <span class="text-muted fs-14"><?= date('F j, Y', strtotime($announcement['created_at'])); ?></span>
                                            </div>
                                        </div>
                                        
                                        <h2 class="fw-bold mb-4"><?= $announcement['title']; ?></h2>
                                        
                                        <div class="announcement-content fs-15 text-muted">
                                            <?= $announcement['content']; ?>
                                        </div>
                                        
                                        <?php if(!empty($announcement['tags'])): ?>
                                        <div class="mt-4 pt-2">
                                            <div class="d-flex flex-wrap gap-2">
                                                <?php 
                                                $tags = explode(',', $announcement['tags']);
                                                foreach($tags as $tag): 
                                                    $tag = trim($tag);
                                                    if(!empty($tag)):
                                                ?>
                                                <span class="badge bg-light text-muted fs-13"><?= $tag; ?></span>
                                                <?php 
                                                    endif;
                                                endforeach; 
                                                ?>
                                            </div>                                        </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- end container -->
        </section>
        <!-- end announcement content -->

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