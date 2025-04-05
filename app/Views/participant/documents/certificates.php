<?= $this->include('partials/main') ?>

<head>

    <?php echo view('partials/title-meta', array('title' => 'Certificates')); ?>

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

                    <?php echo view('partials/page-title', array('pagetitle' => 'Documents', 'title' => 'Certificates')); ?>

                    <div class="row">
                        <div class="col-lg-12">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title mb-0">Your Achievement Certificates</h4>
                                </div>

                                <div class="card-body">
                                    <p class="text-muted">View and download your earned certificates. These documents certify your successful completion of program milestones.</p>

                                    <div class="live-preview">
                                        <div class="table-responsive table-card">
                                            <table class="table table-striped table-nowrap align-middle mb-0">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th scope="col">Certificate Name</th>
                                                        <th scope="col">Awarded For</th>
                                                        <th scope="col">Issue Date</th>
                                                        <th scope="col">Expiry</th>
                                                        <th scope="col">Actions</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php if(isset($certificates) && !empty($certificates)): ?>
                                                        <?php foreach($certificates as $certificate): ?>
                                                        <tr>
                                                            <td><?= $certificate->name ?? 'Program Certificate' ?></td>
                                                            <td><?= $certificate->awarded_for ?? 'Course Completion' ?></td>
                                                            <td><?= isset($certificate->issue_date) ? date('M d, Y', strtotime($certificate->issue_date)) : date('M d, Y') ?></td>
                                                            <td><?= isset($certificate->expiry_date) ? date('M d, Y', strtotime($certificate->expiry_date)) : 'No expiry' ?></td>
                                                            <td>
                                                                <div class="hstack gap-2">
                                                                    <a href="<?= $certificate->view_url ?? 'javascript:void(0);' ?>" class="btn btn-sm btn-soft-primary">
                                                                        <i class="ri-eye-line align-bottom"></i> View
                                                                    </a>
                                                                    <a href="<?= $certificate->download_url ?? 'javascript:void(0);' ?>" class="btn btn-sm btn-primary">
                                                                        <i class="ri-download-2-line align-bottom"></i> Download
                                                                    </a>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                        <?php endforeach; ?>
                                                    <?php else: ?>
                                                        <tr>
                                                            <td colspan="5" class="text-center">
                                                                <div class="py-4">
                                                                    <div class="avatar-sm mx-auto mb-3">
                                                                        <div class="avatar-title bg-light text-secondary rounded-circle fs-24">
                                                                            <i class="ri-award-line"></i>
                                                                        </div>
                                                                    </div>
                                                                    <h5>No certificates available yet</h5>
                                                                    <p class="text-muted mb-0">Your certificates will appear here as you complete program achievements.</p>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    <?php endif; ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
                <!-- container-fluid -->
            </div>
            <!-- End Page-content -->

            <?= $this->include('partials/footer') ?>
        </div>
        <!-- end main content-->

    </div>
    <!-- END layout-wrapper -->

    <?= $this->include('partials/vendor-scripts') ?>

    <!-- App js -->
    <script src="/assets/js/app.js"></script>
</body>

</html>