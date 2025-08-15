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
                                    <div class="d-flex align-items-center">
                                        <h4 class="card-title mb-0 flex-grow-1">Available Program Documents</h4>
                                        <div class="flex-shrink-0">
                                            <div class="d-flex gap-2">
                                                <button type="button" class="btn btn-soft-secondary btn-sm" id="btn-all">
                                                    All
                                                </button>
                                                <button type="button" class="btn btn-soft-info btn-sm" id="btn-upload">
                                                    Upload Required
                                                </button>
                                                <button type="button" class="btn btn-soft-primary btn-sm" id="btn-generate">
                                                    Can Generate
                                                </button>
                                                <button type="button" class="btn btn-soft-success btn-sm" id="btn-reference">
                                                    Reference
                                                </button>
                                            </div>
                                        </div>
                                    </div>
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
                                                        <th scope="col">Description</th>
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
                                                            // All documents returned by the API are already filtered for visibility and access
                                                            $hasVisibleDocs = true;
                                                    ?>
                                                                <tr class="document-row" data-type="<?= $document['is_upload'] == '1' ? 'upload-required' : ($document['is_generated'] == '1' ? 'can-generate' : 'reference') ?>">
                                                                    <td><?= $counter++ ?></td>
                                                                    <td>
                                                                        <h6 class="mb-1"><?= $document['name'] ?? 'Program Document' ?></h6>
                                                                    </td>
                                                                    <td>
                                                                        <p class="text-muted mb-0">
                                                                            <?php if (!empty($document['desc'])): ?>
                                                                                <?= substr($document['desc'], 0, 80) ?><?= strlen($document['desc']) > 80 ? '...' : '' ?>
                                                                            <?php else: ?>
                                                                                Important program document for participants
                                                                            <?php endif; ?>
                                                                        </p>
                                                                    </td>
                                                                    <td>
                                                                        <?php if ($document['is_upload'] == '1'): ?>
                                                                            <span class="badge bg-info">Upload Required</span>
                                                                        <?php elseif ($document['is_generated'] == '1'): ?>
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
                                                        endforeach;
                                                    endif;

                                                    if (!$hasVisibleDocs):
                                                        ?>
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

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Simple filter functionality to match existing app style
            const filterButtons = document.querySelectorAll('[id^="btn-"]');
            const documentRows = document.querySelectorAll('.document-row');
            
            filterButtons.forEach(button => {
                button.addEventListener('click', function() {
                    // Remove active class from all buttons
                    filterButtons.forEach(btn => {
                        btn.classList.remove('btn-primary');
                        btn.classList.add('btn-soft-secondary');
                    });
                    
                    // Add active class to clicked button
                    this.classList.remove('btn-soft-secondary');
                    this.classList.add('btn-primary');
                    
                    const filterType = this.id.replace('btn-', '');
                    
                    documentRows.forEach(row => {
                        if (filterType === 'all') {
                            row.style.display = '';
                        } else {
                            const rowType = row.getAttribute('data-type');
                            if (filterType === 'upload' && rowType === 'upload-required') {
                                row.style.display = '';
                            } else if (filterType === 'generate' && rowType === 'can-generate') {
                                row.style.display = '';
                            } else if (filterType === 'reference' && rowType === 'reference') {
                                row.style.display = '';
                            } else if (filterType !== 'all') {
                                row.style.display = 'none';
                            }
                        }
                    });
                });
            });
            
            // Set "All" as default active
            document.getElementById('btn-all').classList.remove('btn-soft-secondary');
            document.getElementById('btn-all').classList.add('btn-primary');
        });
    </script>
</body>

</html>