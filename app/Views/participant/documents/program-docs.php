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
                                    <p class="text-muted">Access and download important program materials. These documents contain essential information to ensure your successful program completion.</p>

                                    <div class="live-preview">
                                        <div class="table-responsive table-card">
                                            <table class="table table-striped table-nowrap align-middle mb-0">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th scope="col">#</th>
                                                        <th scope="col">Document Name</th>
                                                        <th scope="col">Type</th>
                                                        <th scope="col">Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php
                                                    $hasVisibleDocs = false;
                                                    $counter = 1;
                                                    if (isset($documents) && !empty($documents)):
                                                        foreach ($documents as $document):
                                                            // Only show documents with visibility = 1
                                                            if ($document['visibility'] == 1):
                                                                $hasVisibleDocs = true;
                                                    ?>
                                                                <tr>
                                                                    <td><?= $counter++ ?></td>
                                                                    <td><?= $document['name'] ?? 'Program Document' ?></td>
                                                                    <td>
                                                                        <?php if ($document['is_upload']): ?>
                                                                            <span class="badge bg-info">Upload Required</span>
                                                                        <?php elseif ($document['is_generated']): ?>
                                                                            <span class="badge bg-primary">Can Generate</span>
                                                                        <?php else: ?>
                                                                            <span class="badge bg-secondary">Reference</span>
                                                                        <?php endif; ?>
                                                                    </td>
                                                                    <td>
                                                                        <div class="btn-group">
                                                                            <?php if (!empty($document['file_url'])): ?>
                                                                                <a href="<?= $document['file_url'] ?>" class="btn btn-sm btn-primary" download>
                                                                                    <i class="ri-download-2-line align-middle"></i>
                                                                                </a>
                                                                            <?php elseif (!empty($document['drive_url'])): ?>
                                                                                <a href="<?= $document['drive_url'] ?>" class="btn btn-sm btn-info" target="_blank">
                                                                                    <i class="ri-external-link-line align-middle"></i>
                                                                                </a>
                                                                            <?php else: ?>
                                                                                <button class="btn btn-sm btn-secondary" disabled>
                                                                                    <i class="ri-close-circle-line align-middle"></i>
                                                                                </button>
                                                                            <?php endif; ?>
                                                                            <a href="<?= base_url('documents/program/details/' . ($document['id'] ?? '')) ?>" class="btn btn-sm btn-info" title="View Details">
                                                                                <i class="ri-eye-line align-middle"></i>
                                                                            </a>
                                                                        </div>
                                                                    </td>
                                                                </tr>
                                                        <?php
                                                            endif;
                                                        endforeach;
                                                    endif;

                                                    if (!$hasVisibleDocs):
                                                        ?>
                                                        <tr>
                                                            <td colspan="4" class="text-center">
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