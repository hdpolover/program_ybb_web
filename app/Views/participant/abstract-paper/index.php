<?= $this->include('partials/main') ?>

<head>

     <?php echo view('partials/title-meta', array('title' => 'Abstract and Paper')); ?>    <!-- Sweet Alert css-->
    <link href="/assets/libs/sweetalert2/sweetalert2.min.css" rel="stylesheet" type="text/css" />

    <!-- swiper css -->
    <link rel="stylesheet" href="/assets/libs/swiper/swiper-bundle.min.css">

    <!-- Version Comparison CSS -->
    <link href="/assets/css/version-comparison.css" rel="stylesheet" type="text/css" />

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

                    <?php echo view('partials/page-title', array('pagetitle'=>'Participant', 'title'=>'Abstract & Paper')); ?>

                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-body">                                <?php
                                    // Check if participant is eligible for abstract submission
                                    if (!isset($participant_data['eligible_for_abstract']) || $participant_data['eligible_for_abstract'] === false): ?>
                                        <?= $this->include('participant/abstract-paper/components/not-eligible') ?>
                                    <?php 
                                    // Participant is eligible but hasn't submitted an abstract yet
                                    elseif ($participant_data['eligible_for_abstract'] === true && empty($participant_data['abstract'])): ?>
                                        <?= $this->include('participant/abstract-paper/components/empty-state') ?>
                                    <?php 
                                    // Participant is eligible and has submitted an abstract
                                    else: ?>
                                        <?= $this->include('participant/abstract-paper/components/abstract-view') ?>
                                    <?php endif; ?>
                                    
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
    <!-- END layout-wrapper -->    <?= $this->include('partials/vendor-scripts') ?>

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
                $abstractId = $successData['id'] ?? null;
                $abstractTitle = $successData['title'] ?? 'Your Abstract';
                $status = $successData['status'] ?? 'unknown';
                $message = $successData['message'] ?? '';
                $versionNumber = $successData['version_number'] ?? null;
                ?>
                  Swal.fire({
                    title: '<?= $isDraft ? "Draft Saved!" : "Abstract Submitted!" ?>',
                    html: `
                        <div class="text-start">
                            <h5 class="mb-3"><?= esc($abstractTitle) ?></h5>
                            <p><strong>ID:</strong> <?= esc($abstractId) ?></p>
                            <p><strong>Status:</strong> <span class="badge bg-<?= $isDraft ? 'warning' : 'success' ?>"><?= ucfirst(esc($status)) ?></span></p>
                            <?php if ($versionNumber): ?>
                            <p><strong>Version:</strong> <?= esc($versionNumber) ?></p>
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
                    window.location.href = '<?= base_url('abstract-paper/create') ?>';
                    
                    // Show loading indicator
                    Swal.fire({
                        title: 'Loading...',
                        html: `
                            <div class="text-start">
                                <p>Preparing the abstract creation form...</p>
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
        });    </script>

    <!-- Abstract Paper View JS -->
    <script src="/assets/js/abstract-paper-view.js"></script>

    <!-- App js -->
    <script src="/assets/js/app.js"></script>
</body>

</html>