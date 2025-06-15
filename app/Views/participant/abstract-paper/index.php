<?= $this->include('partials/main') ?>

<head>

    <?php echo view('partials/title-meta', array('title' => 'Abstract and Paper')); ?> <!-- Sweet Alert css-->
    <link href="/assets/libs/sweetalert2/sweetalert2.min.css" rel="stylesheet" type="text/css" />

    <!-- swiper css -->
    <link rel="stylesheet" href="/assets/libs/swiper/swiper-bundle.min.css"> <!-- Version Comparison CSS -->
    <link href="/assets/css/version-comparison.css" rel="stylesheet" type="text/css" />

    <!-- Enhanced layout CSS for abstract paper view -->
    <style>
        /* Ensure full-width utilization and equal height cards */
        .card.h-100 {
            height: 100% !important;
        }

        .row.mb-4 {
            margin-bottom: 2rem !important;
        }

        /* Remove any unwanted margins on cards */
        .card {
            margin-bottom: 0;
        }

        /* Ensure proper spacing between rows */
        .abstract-layout .row+.row {
            margin-top: 1.5rem;
        }

        /* Better card body padding for content */
        .card-body {
            padding: 1.5rem;
        }

        /* Responsive adjustments */
        @media (max-width: 991px) {
            .card.h-100 {
                margin-bottom: 1rem;
            }
        }

        /* Improve visual separation between sections */
        .main-content .page-content {
            padding-bottom: 2rem;
        }
    </style>

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

                    <?php echo view('partials/page-title', array('pagetitle' => 'Participant', 'title' => 'Abstract & Paper')); ?>

                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-body p-4">

                                    <!-- Subtheme Highlight Section -->
                                    <?php if (isset($subtheme_highlight) && !empty($subtheme_highlight)): ?>
                                        <div class="alert alert-success border-left-success mb-4" style="border-left: 4px solid #1f9d55;">
                                            <div class="d-flex align-items-center">
                                                <div class="flex-shrink-0">
                                                    <div class="avatar-sm">
                                                        <div class="avatar-title rounded-circle bg-success">
                                                            <i class="mdi mdi-check-circle-outline"></i>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="flex-grow-1 ms-3">
                                                    <h5 class="alert-heading mb-2">
                                                        <i class="mdi mdi-flag-checkered text-success"></i> Selected Subtheme
                                                    </h5>
                                                    <h6 class="mb-1 text-success fw-bold"><?= esc($subtheme_highlight['name']) ?></h6>
                                                    <?php if (!empty($subtheme_highlight['description'])): ?>
                                                        <p class="mb-0 text-muted small"><?= esc($subtheme_highlight['description']) ?></p>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endif; ?>

                                    <!-- Subtheme Warning Section -->
                                    <?php if (isset($subtheme_warning) && !empty($subtheme_warning)): ?>
                                        <div class="alert alert-<?= $subtheme_warning['type'] === 'warning' ? 'warning' : ($subtheme_warning['type'] === 'error' ? 'danger' : 'info') ?> border-left-<?= $subtheme_warning['type'] === 'warning' ? 'warning' : ($subtheme_warning['type'] === 'error' ? 'danger' : 'info') ?> mb-4"
                                            style="border-left: 4px solid <?= $subtheme_warning['type'] === 'warning' ? '#f1b44c' : ($subtheme_warning['type'] === 'error' ? '#f46a6a' : '#5156be') ?>;">
                                            <div class="d-flex align-items-center">
                                                <div class="flex-shrink-0">
                                                    <div class="avatar-sm">
                                                        <div class="avatar-title rounded-circle bg-<?= $subtheme_warning['type'] === 'warning' ? 'warning' : ($subtheme_warning['type'] === 'error' ? 'danger' : 'info') ?>">
                                                            <i class="mdi mdi-<?= $subtheme_warning['type'] === 'warning' ? 'alert-circle-outline' : ($subtheme_warning['type'] === 'error' ? 'close-circle-outline' : 'information-outline') ?>"></i>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="flex-grow-1 ms-3">
                                                    <h5 class="alert-heading mb-2"><?= esc($subtheme_warning['title']) ?></h5>
                                                    <p class="mb-0"><?= esc($subtheme_warning['message']) ?></p>
                                                    <?php if ($subtheme_warning['type'] === 'warning' && strpos($subtheme_warning['message'], 'subtheme') !== false): ?>
                                                        <div class="mt-2">
                                                            <a href="<?= base_url('dashboard/subtheme-selection') ?>" class="btn btn-warning btn-sm">
                                                                <i class="mdi mdi-arrow-right"></i> Select Subtheme
                                                            </a>
                                                        </div>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endif; ?>

                                    <!-- Main Content Area -->
                                    <div class="abstract-paper-content">
                                        <?php
                                        // Check if participant is eligible for abstract submission
                                        if (!isset($participant_data['eligible_for_abstract']) || $participant_data['eligible_for_abstract'] === false): ?>
                                            <?= $this->include('participant/abstract-paper/components/not-eligible') ?>
                                        <?php
                                        // Participant is eligible but hasn't submitted an abstract yet
                                        elseif ($participant_data['eligible_for_abstract'] === true && empty($participant_data['abstract'])): ?>
                                            <?= $this->include('participant/abstract-paper/components/empty-state') ?>
                                        <?php                        // Participant is eligible and has submitted an abstract
                                        else: ?>
                                            <?= $this->include('participant/abstract-paper/components/abstract-view') ?>
                                        <?php endif; ?>
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
    <!-- END layout-wrapper --> <?= $this->include('partials/vendor-scripts') ?>

    <!-- Sweet Alert js-->
    <script src="/assets/libs/sweetalert2/sweetalert2.min.js"></script>

    <!-- jQuery -->
    <script src="/assets/libs/jquery/jquery.min.js"></script>

    <!-- Abstract Paper View JS -->
    <script src="/assets/js/abstract-paper-view.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Enhanced success alert for abstract operations           
            <?php if (session()->has('abstract_success')): ?>
                <?php
                $successData = session('abstract_success');
                $isDraft = $successData['is_draft'] ?? false;
                $abstractTitle = $successData['title'] ?? 'Your Abstract';
                $status = $successData['status'] ?? 'unknown';
                $message = $successData['message'] ?? '';
                $versionNumber = $successData['version_number'] ?? null;
                $createdAt = $successData['created_at'] ?? null;
                $updatedAt = $successData['updated_at'] ?? null;
                ?>
                Swal.fire({
                    title: '<?= $isDraft ? "Draft Saved!" : "Abstract Submitted!" ?>',
                    html: `
                        <div class="text-start">
                            <h5 class="mb-3"><?= esc($abstractTitle) ?></h5>
                            <div class="row">
                                <div class="col-6">
                                    <p><strong>Status:</strong></p>
                                    <span class="badge bg-<?= $isDraft ? 'warning' : 'success' ?> fs-6 px-3 py-2"><?= ucfirst(esc($status)) ?></span>
                                </div>
                                <?php if ($versionNumber): ?>
                                <div class="col-6">
                                    <p><strong>Version:</strong></p>
                                    <span class="badge bg-info fs-6 px-3 py-2">v<?= esc($versionNumber) ?></span>
                                </div>
                                <?php endif; ?>
                            </div>
                            <?php if ($createdAt || $updatedAt): ?>
                            <div class="mt-3">
                                <?php if ($createdAt && $createdAt === $updatedAt): ?>
                                <p class="text-muted small mb-1"><i class="bx bx-time me-1"></i><strong>Created:</strong> <?= date('M j, Y \a\t g:i A', strtotime($createdAt)) ?></p>
                                <?php else: ?>
                                    <?php if ($createdAt): ?>
                                    <p class="text-muted small mb-1"><i class="bx bx-time me-1"></i><strong>Created:</strong> <?= date('M j, Y \a\t g:i A', strtotime($createdAt)) ?></p>
                                    <?php endif; ?>
                                    <?php if ($updatedAt): ?>
                                    <p class="text-muted small mb-1"><i class="bx bx-edit me-1"></i><strong>Last Updated:</strong> <?= date('M j, Y \a\t g:i A', strtotime($updatedAt)) ?></p>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </div>
                            <?php endif; ?>
                            <hr>
                            <p><?= esc($message) ?></p>
                            <?php if (!$isDraft): ?>
                            <p class="text-muted small">You will be notified once the review process is complete.</p>
                            <?php endif; ?>
                        </div>
                    `,
                    icon: 'success',
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#5156be',
                    allowOutsideClick: false
                });
            <?php elseif (session()->has('success')): ?>
                // Fallback for regular success messages
                Swal.fire({
                    title: '<?= session()->has('success_title') ? session('success_title') : 'Success!' ?>',
                    text: '<?= session('success') ?>',
                    icon: 'success',
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#5156be'
                });
            <?php endif; ?>

            // Display sweet alert for error messages
            <?php if (session()->has('error')): ?>
                Swal.fire({
                    title: '<?= session()->has('error_title') ? session('error_title') : 'Error!' ?>',
                    text: '<?= session('error') ?>',
                    icon: 'error',
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#5156be'
                });
            <?php endif; ?>
            // Handle create new abstract button
            const createAbstractBtn = document.getElementById('create-abstract-btn');
            if (createAbstractBtn) {
                createAbstractBtn.addEventListener('click', function() {
                    // Check if button is disabled (no subtheme selected)
                    if (createAbstractBtn.disabled) {
                        Swal.fire({
                            title: 'Subtheme Selection Required',
                            html: `
                                <div class="text-start">
                                    <p>Before creating an abstract, you need to select a research subtheme.</p>
                                    <p class="text-muted small">This helps us categorize your research and match it with appropriate reviewers.</p>
                                </div>
                            `,
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonText: 'Select Subtheme',
                            cancelButtonText: 'Cancel',
                            confirmButtonColor: '#f1b44c'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                window.location.href = '<?= base_url('dashboard/subtheme-selection') ?>';
                            }
                        });
                        return;
                    }

                    // Proceed to create abstract
                    window.location.href = '<?= base_url('abstract-paper/create') ?>';

                    // Show loading indicator
                    Swal.fire({
                        title: 'Loading...',
                        html: `
                            <div class="text-start">                                <p>Preparing the abstract creation form...</p>
                                <div class="progress mt-3">
                                    <div class="progress-bar progress-bar-striped progress-bar-animated bg-primary" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" style="width: 100%"></div>
                                </div>
                            </div>
                        `,
                        showConfirmButton: false,
                        allowOutsideClick: false,
                        allowEscapeKey: false
                    });
                });
            }
        });
    </script>

    <!-- App js -->
    <script src="/assets/js/app.js"></script>
</body>

</html>