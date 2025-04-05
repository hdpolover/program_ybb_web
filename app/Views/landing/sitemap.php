<?= $this->include('partials/main') ?>

<head>

    <?php echo view('partials/title-meta', array('title'=>'Sitemap')); ?>

    <?= $this->include('partials/head-css') ?>

</head>

<body>

    <!-- Begin page -->
    <div id="layout-wrapper">

        <?= $this->include('partials/menu') ?>

        <!-- ============================================================== -->
        <!-- Start right Content here -->
        <!-- ============================================================== -->
        <div class="main-content">

            <div class="page-content">
                <div class="container-fluid">

                    <?php echo view('partials/page-title', array('pagetitle'=>'Pages', 'title'=>'Sitemap')); ?>

                    <div class="row">
                        <div class="col-lg-12">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title mb-0">Website Navigation</h4>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-4">
                                                <h5 class="fs-15 mb-3">Main Pages</h5>
                                                <ul class="list-unstyled mb-0">
                                                    <li class="mb-2"><a href="<?= base_url() ?>" class="text-muted"><i class="mdi mdi-chevron-right text-primary me-1"></i> Home</a></li>
                                                    <li class="mb-2"><a href="<?= base_url('about-us') ?>" class="text-muted"><i class="mdi mdi-chevron-right text-primary me-1"></i> About Us</a></li>
                                                    <li class="mb-2"><a href="<?= base_url('faqs') ?>" class="text-muted"><i class="mdi mdi-chevron-right text-primary me-1"></i> FAQs</a></li>
                                                    <li class="mb-2"><a href="<?= base_url('sponsorships') ?>" class="text-muted"><i class="mdi mdi-chevron-right text-primary me-1"></i> Sponsorships</a></li>
                                                </ul>
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="mb-4">
                                                <h5 class="fs-15 mb-3">User Access</h5>
                                                <ul class="list-unstyled mb-0">
                                                    <li class="mb-2"><a href="<?= base_url('sign-in') ?>" class="text-muted"><i class="mdi mdi-chevron-right text-primary me-1"></i> Sign In</a></li>
                                                    <li class="mb-2"><a href="<?= base_url('sign-up') ?>" class="text-muted"><i class="mdi mdi-chevron-right text-primary me-1"></i> Sign Up</a></li>
                                                    <li class="mb-2"><a href="<?= base_url('forgot-password') ?>" class="text-muted"><i class="mdi mdi-chevron-right text-primary me-1"></i> Forgot Password</a></li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>

                                    <?php 
                                    // Get announcements
                                    $announcementController = new \App\Controllers\Sitemap();
                                    $reflectionClass = new ReflectionClass($announcementController);
                                    $reflectionMethod = $reflectionClass->getMethod('makeGetRequest');
                                    $reflectionMethod->setAccessible(true);
                                    
                                    $reflectionMethodGetProgramInfo = $reflectionClass->getMethod('getProgramInfoDetail');
                                    $reflectionMethodGetProgramInfo->setAccessible(true);
                                    
                                    try {
                                        $programId = $reflectionMethodGetProgramInfo->invoke($announcementController, 'id');
                                        $announcements = $reflectionMethod->invoke($announcementController, '/program_announcements/list?program_id=' . $programId);
                                    } catch (\Exception $e) {
                                        $announcements = [];
                                    }
                                    ?>

                                    <?php if (!empty($announcements)): ?>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="mb-4">
                                                <h5 class="fs-15 mb-3">Announcements</h5>
                                                <div class="row">
                                                    <?php foreach (array_chunk($announcements, ceil(count($announcements) / 2)) as $announcementChunk): ?>
                                                    <div class="col-md-6">
                                                        <ul class="list-unstyled mb-0">
                                                            <?php foreach ($announcementChunk as $announcement): ?>
                                                            <li class="mb-2">
                                                                <a href="<?= base_url('announcements/' . $announcement['slug']) ?>" class="text-muted">
                                                                    <i class="mdi mdi-chevron-right text-primary me-1"></i> <?= $announcement['title'] ?>
                                                                </a>
                                                            </li>
                                                            <?php endforeach; ?>
                                                        </ul>
                                                    </div>
                                                    <?php endforeach; ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <?php endif; ?>

                                    <!-- Additional sections can be programmatically added here as your site grows -->

                                </div>
                                <!--end card-body-->
                            </div>
                            <!--end card-->
                        </div>
                        <!--end col-->
                    </div>
                    <!--end row-->

                    <div class="row">
                        <div class="col-lg-12">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title mb-0">Sitemap Overview</h4>
                                </div>
                                <div class="card-body">
                                    <div class="hori-sitemap">
                                        <ul class="list-unstyled mb-0">
                                            <li class="p-0 parent-title"><a href="javascript: void(0);" class="fw-semibold fs-14">Site Structure</a></li>
                                            <li>
                                                <ul class="list-unstyled second-list row g-0 pt-0">
                                                    <li class="col-sm-3">
                                                        <a href="javascript: void(0);" class="fw-semibold sub-title">Main Pages</a>
                                                        <ul class="list-unstyled row g-0 second-list">
                                                            <li class="col-sm-6">
                                                                <a href="<?= base_url() ?>">Home</a>
                                                            </li>
                                                            <li class="col-sm-6">
                                                                <a href="<?= base_url('about-us') ?>">About Us</a>
                                                            </li>
                                                            <li class="col-sm-6">
                                                                <a href="<?= base_url('faqs') ?>">FAQs</a>
                                                            </li>
                                                            <li class="col-sm-6">
                                                                <a href="<?= base_url('sponsorships') ?>">Sponsorships</a>
                                                            </li>
                                                        </ul>
                                                    </li>
                                                    <li class="col-sm-3">
                                                        <a href="javascript: void(0);" class="fw-semibold sub-title">User Access</a>
                                                        <ul class="list-unstyled second-list pt-0">
                                                            <li>
                                                                <div>
                                                                    <a href="<?= base_url('sign-in') ?>">Sign In</a>
                                                                    <a href="<?= base_url('sign-up') ?>">Sign Up</a>
                                                                    <a href="<?= base_url('forgot-password') ?>">Forgot Password</a>
                                                                </div>
                                                            </li>
                                                        </ul>
                                                    </li>
                                                    <li class="col-sm-3">
                                                        <a href="<?= base_url('announcements') ?>" class="fw-semibold sub-title">Announcements</a>
                                                    </li>
                                                    <li class="col-sm-3">
                                                        <a href="javascript: void(0);" class="fw-semibold">Legal</a>
                                                        <ul class="list-unstyled second-list pt-0">
                                                            <li>
                                                                <div>
                                                                    <a href="<?= base_url('terms-of-service') ?>">Terms of Service</a>
                                                                    <a href="<?= base_url('privacy-policy') ?>">Privacy Policy</a>
                                                                </div>
                                                            </li>
                                                        </ul>
                                                    </li>
                                                </ul>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                                <!--end card-body-->
                            </div>
                            <!--end card-->
                        </div>
                        <!--end col-->
                    </div>
                    <!--end row-->
                </div>
                <!-- container-fluid -->
            </div>
            <!-- End Page-content -->

            <?= $this->include('landing/common/footer') ?>
        </div>
        <!-- end main content-->

    </div>
    <!-- END layout-wrapper -->

    <?= $this->include('partials/vendor-scripts') ?>

    <!-- App js -->
    <script src="/assets/js/app.js"></script>
</body>

</html>