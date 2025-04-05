<?= $this->include('partials/main') ?>

<head>

    <?php echo view('partials/title-meta', array('title' => 'Program Documents')); ?>

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

                    <?php echo view('partials/page-title', array('pagetitle' => 'Documents', 'title' => 'Program Documents')); ?>

                    <div class="row">
                        <div class="col-lg-12">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title mb-0">Available Program Documents</h4>
                                </div>

                                <div class="card-body">
                                    <p class="text-muted">View and download program documents. These files are provided to help you successfully complete your program.</p>

                                    <div class="live-preview">
                                        <div class="table-responsive table-card">
                                            <table class="table table-striped table-nowrap align-middle mb-0">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th scope="col">Document Name</th>
                                                        <th scope="col">Category</th>
                                                        <th scope="col">Date Added</th>
                                                        <th scope="col">File Size</th>
                                                        <th scope="col">Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php if(isset($documents) && !empty($documents)): ?>
                                                        <?php foreach($documents as $document): ?>
                                                        <tr>
                                                            <td><?= $document->name ?? 'Program Document' ?></td>
                                                            <td><?= $document->category ?? 'General' ?></td>
                                                            <td><?= isset($document->created_at) ? date('M d, Y', strtotime($document->created_at)) : date('M d, Y') ?></td>
                                                            <td><?= $document->file_size ?? '1.2 MB' ?></td>
                                                            <td>
                                                                <a href="<?= $document->file_url ?? 'javascript:void(0);' ?>" class="btn btn-sm btn-primary">
                                                                    <i class="ri-download-2-line align-middle me-1"></i> Download
                                                                </a>
                                                            </td>
                                                        </tr>
                                                        <?php endforeach; ?>
                                                    <?php else: ?>
                                                        <tr>
                                                            <td colspan="5" class="text-center">
                                                                <div class="py-4">
                                                                    <div class="avatar-sm mx-auto mb-3">
                                                                        <div class="avatar-title bg-light text-secondary rounded-circle fs-24">
                                                                            <i class="ri-folder-open-line"></i>
                                                                        </div>
                                                                    </div>
                                                                    <h5>No documents available yet</h5>
                                                                    <p class="text-muted mb-0">Program documents will be available here when published.</p>
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