<?= $this->include('partials/main') ?>

<head>

    <?php echo view('partials/title-meta', array('title' => 'Submission')); ?>

    <!-- swiper css -->
    <link rel="stylesheet" href="/assets/libs/swiper/swiper-bundle.min.css">

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
                    <?php echo view('partials/page-title', array('pagetitle' => 'Submission', 'title' => 'Registration Form')); ?>
                    <div class="profile-foreground position-relative mx-n4 mt-n4">
                        <div class="profile-wid-bg">
                            <div class="profile-wid-img" style="background: linear-gradient(135deg, #1e3c72 50%, #2a5298 100%); height:auto;"></div>
                        </div>
                    </div>
                    <div class="pt-4 mb-4 mb-lg-3 pb-lg-4 profile-wrapper">
                        <div class="row g-4">
                            <div class="col-auto">
                                <div class="avatar-lg">
                                    <?php if (!empty($currentParticipant['picture_url'])): ?>
                                        <img src="<?= $currentParticipant['picture_url'] ?>" alt="user-img" class="img-thumbnail rounded-circle" />
                                    <?php else: ?>
                                        <div class="avatar-lg bg-light rounded-circle d-flex align-items-center justify-content-center">
                                            <i class="ri-user-3-fill text-primary fs-1"></i>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <!--end col-->
                            <div class="col">
                                <div class="p-2">
                                    <h3 class="text-white mb-1"><?= $currentParticipant['full_name'] ?></h3>
                                    <p class="text-white text-opacity-75"><?= !empty($currentParticipant['occupation']) ? $currentParticipant['occupation'] : '-' ?></p>
                                    <div class="hstack text-white-50 gap-1">
                                        <div class="me-2"><i class="ri-map-pin-user-line me-1 text-white text-opacity-75 fs-16 align-middle"></i><?= !empty($currentParticipant['nationality']) ? $currentParticipant['nationality'] : '-' ?></div>
                                        <div>
                                            <i class="ri-building-line me-1 text-white text-opacity-75 fs-16 align-middle"></i><?= !empty($currentParticipant['institution']) ? $currentParticipant['institution'] : '-' ?>
                                        </div>

                                    </div>
                                </div>
                            </div>
                            <!--end col-->
                            <!--end col-->
                            <div class="col-auto">
                                <div class="d-flex flex-column align-items-end">
                                    <div class="badge bg-light text-dark fs-13">
                                        <i class="ri-user-3-line me-1"></i> Account ID: <?= $currentParticipant['account_id'] ?>
                                    </div>
                                </div>
                            </div>

                        </div>
                        <!--end row-->
                    </div>

                    <div class="row">
                        <div class="col-lg-12">
                            <div>
                                <div class="d-flex profile-wrapper">
                                    <!-- Nav tabs -->
                                    <ul class="nav nav-pills animation-nav profile-nav gap-2 gap-lg-3 flex-grow-1" role="tablist">
                                        <li class="nav-item">
                                            <a class="nav-link fs-14 active" data-bs-toggle="tab" href="#personal-details" role="tab">
                                                <i class="ri-airplay-fill d-inline-block d-md-none"></i> <span class="d-none d-md-inline-block">Personal Details</span>
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link fs-14" data-bs-toggle="tab" href="#professional" role="tab">
                                                <i class="ri-list-unordered d-inline-block d-md-none"></i> <span class="d-none d-md-inline-block">Professional Profile</span>
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link fs-14" data-bs-toggle="tab" href="#entry" role="tab">
                                                <i class="ri-price-tag-line d-inline-block d-md-none"></i> <span class="d-none d-md-inline-block">Entry Information</span>
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link fs-14" data-bs-toggle="tab" href="#miscs" role="tab">
                                                <i class="ri-folder-4-line d-inline-block d-md-none"></i> <span class="d-none d-md-inline-block">Miscellaneous</span>
                                            </a>
                                        </li>
                                    </ul>
                                    <?php if (isset($currentProgram) && $currentProgram['is_active']): ?>
                                    <div class="flex-shrink-0">
                                        <a href="<?= base_url() ?>submission/edit" class="btn btn-success"><i class="ri-edit-box-line align-bottom"></i> Edit Submission</a>
                                    </div>
                                    <?php endif; ?>
                                </div>
                                <!-- Tab panes -->
                                <div class="tab-content pt-4 text-muted">
                                    <?= $this->include('participant/submission/tab-contents/personal') ?>
                                    <?= $this->include('participant/submission/tab-contents/professional') ?>
                                    <?= $this->include('participant/submission/tab-contents/entry') ?>
                                    <?= $this->include('participant/submission/tab-contents/miscs') ?>
                                </div>
                                <!--end tab-content-->
                            </div>
                        </div>
                        <!--end col-->
                    </div>
                    <!--end row-->

                </div><!-- container-fluid -->
            </div><!-- End Page-content -->

            <?= $this->include('partials/footer') ?>
        </div><!-- end main content-->

    </div>
    <!-- END layout-wrapper -->

    <?= $this->include('partials/vendor-scripts') ?>

    <!-- swiper js -->
    <script src="/assets/libs/swiper/swiper-bundle.min.js"></script>

    <!-- profile init js -->
    <script src="/assets/js/pages/profile.init.js"></script>

    <!-- App js -->
    <script src="/assets/js/app.js"></script>
</body>

</html>