<?= $this->include('partials/main') ?>

<head>

    <?php echo view('partials/title-meta', array('title' => 'Certificates')); ?>

    <!-- Sweet Alert css-->
    <link href="/assets/libs/sweetalert2/sweetalert2.min.css" rel="stylesheet" type="text/css" />

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
                                    <p class="text-muted">View and download your earned certificates. These documents certify your successful completion of program milestones and achievements.</p>

                                    <?php if (isset($certificates_data) && isset($certificates_data['participant']) && $certificates_data['participant']): ?>
                                        <div class="alert alert-info border-0 mb-4">
                                            <div class="d-flex align-items-center">
                                                <div class="avatar-xs me-3">
                                                    <div class="avatar-title bg-info-subtle text-info rounded-circle">
                                                        <i class="ri-user-line"></i>
                                                    </div>
                                                </div>
                                                <div>
                                                    <h6 class="mb-1">Participant Information</h6>
                                                    <strong><?= esc($certificates_data['participant']['full_name'] ?? 'Unknown') ?></strong>
                                                    <?php if (isset($certificates_data['participant']['account_id'])): ?>
                                                        <span class="badge bg-primary ms-2"><?= esc($certificates_data['participant']['account_id']) ?></span>
                                                    <?php endif; ?>
                                                    <div>
                                                        <small class="text-muted">Participant ID: <?= esc($certificates_data['participant']['id']) ?></small>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endif; ?>

                                    <div class="live-preview">
                                        <div class="table-responsive">
                                            <table class="table table-hover align-middle mb-0">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th scope="col" style="width: 15%;">Certificate ID</th>
                                                        <th scope="col" style="width: 30%;">Award Details</th>
                                                        <th scope="col" style="width: 20%;">Assignment Info</th>
                                                        <th scope="col" style="width: 15%;">Status</th>
                                                        <th scope="col" style="width: 20%;">Actions</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php if (isset($certificates_data['certificates']) && !empty($certificates_data['certificates']) && is_array($certificates_data['certificates'])): ?>
                                                        <?php foreach($certificates_data['certificates'] as $certificate): ?>
                                                        <tr>
                                                            <td>
                                                                <div class="d-flex flex-column">
                                                                    <span class="badge bg-primary fs-12 mb-1">#<?= esc($certificate['id'] ?? 'N/A') ?></span>
                                                                    <small class="text-muted">Award ID: <?= esc($certificate['award_id'] ?? 'N/A') ?></small>
                                                                </div>
                                                            </td>
                                                            <td>
                                                                <div>
                                                                    <h6 class="mb-1"><?= esc($certificate['award_title'] ?? 'Achievement Award') ?></h6>
                                                                    <?php if (isset($certificate['award_type'])): ?>
                                                                        <span class="badge bg-<?= $certificate['award_type'] === 'winner' ? 'success' : 'info' ?> mb-1">
                                                                            <?= ucfirst(esc($certificate['award_type'])) ?>
                                                                        </span>
                                                                    <?php endif; ?>
                                                                    <?php if (!empty($certificate['notes'])): ?>
                                                                        <p class="text-muted mb-0 small"><?= esc($certificate['notes']) ?></p>
                                                                    <?php endif; ?>
                                                                </div>
                                                            </td>
                                                            <td>
                                                                <div>
                                                                    <?php if (isset($certificate['assigned_at'])): ?>
                                                                        <div class="mb-1">
                                                                            <strong class="d-block">Assigned:</strong>
                                                                            <span class="text-muted small"><?= date('M d, Y H:i', strtotime($certificate['assigned_at'])) ?></span>
                                                                        </div>
                                                                    <?php endif; ?>
                                                                </div>
                                                            </td>
                                                            <td>
                                                                <div class="d-flex flex-column gap-1">
                                                                    <?php if (isset($certificate['is_active']) && $certificate['is_active'] === '1'): ?>
                                                                        <span class="badge bg-success">Active</span>
                                                                    <?php else: ?>
                                                                        <span class="badge bg-secondary">Inactive</span>
                                                                    <?php endif; ?>
                                                                    <small class="text-muted">
                                                                        <?= isset($certificate['created_at']) ? date('M d, Y', strtotime($certificate['created_at'])) : 'N/A' ?>
                                                                    </small>
                                                                </div>
                                                            </td>
                                                            <td>
                                                                <div class="d-flex gap-2">
                                                                    <?php 
                                                                    // Handle both template_url and certificate_path for backward compatibility
                                                                    $certificateUrl = $certificate['template_url'] ?? $certificate['certificate_path'] ?? null;
                                                                    ?>
                                                                    <?php if (!empty($certificateUrl)): ?>
                                                                        <a href="<?= esc($certificateUrl) ?>" download 
                                                                           class="btn btn-sm btn-primary"
                                                                           data-bs-toggle="tooltip" 
                                                                           data-bs-placement="top" 
                                                                           title="Download Certificate">
                                                                            <i class="ri-download-2-line me-1"></i> Download
                                                                        </a>
                                                                        <a href="<?= esc($certificateUrl) ?>" target="_blank" 
                                                                           class="btn btn-sm btn-outline-primary" 
                                                                           data-bs-toggle="tooltip" 
                                                                           data-bs-placement="top" 
                                                                           title="Preview Certificate">
                                                                            <i class="ri-eye-line"></i>
                                                                        </a>
                                                                    <?php else: ?>
                                                                        <button class="btn btn-sm btn-warning" 
                                                                                onclick="generateCertificate(<?= esc($certificate['award_id']) ?>)"
                                                                                data-bs-toggle="tooltip" 
                                                                                data-bs-placement="top" 
                                                                                title="Generate Certificate">
                                                                            <i class="ri-file-add-line me-1"></i> Generate
                                                                        </button>
                                                                        <button class="btn btn-sm btn-outline-secondary" disabled
                                                                                data-bs-toggle="tooltip" 
                                                                                data-bs-placement="top" 
                                                                                title="Certificate not available yet">
                                                                            <i class="ri-download-2-line"></i>
                                                                        </button>
                                                                    <?php endif; ?>
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

                                        <?php if (isset($certificates_data['total_count']) && $certificates_data['total_count'] > 0): ?>
                                            <div class="mt-4 pt-3 border-top d-flex justify-content-between align-items-center">
                                                <div>
                                                    <small class="text-muted">
                                                        <i class="ri-award-line me-1"></i>
                                                        Total certificates: <strong class="text-primary"><?= $certificates_data['total_count'] ?></strong>
                                                    </small>
                                                </div>
                                                <div class="text-end">
                                                    <small class="text-muted">
                                                        <i class="ri-time-line me-1"></i>
                                                        Last updated: <?= date('M d, Y H:i') ?>
                                                    </small>
                                                </div>
                                            </div>
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
    <!-- END layout-wrapper -->

    <?= $this->include('partials/vendor-scripts') ?>

    <!-- Sweet alert init js-->
    <script src="/assets/js/pages/sweetalerts.init.js"></script>

    <!-- App js -->
    <script src="/assets/js/app.js"></script>

    <script>
        // Initialize tooltips for certificate action buttons
        document.addEventListener('DOMContentLoaded', function() {
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
        });

        // Function to generate certificate
        function generateCertificate(awardId) {
            // Show confirmation dialog following submission module pattern
            Swal.fire({
                title: 'Generate Certificate',
                html: `
                    <div class="text-start">
                        <p>You are about to generate a new certificate for this award.</p>
                        <div class="alert alert-info">
                            <i class="ri-information-line me-2"></i> <strong>Note:</strong> This will create a downloadable certificate document for your achievement.
                        </div>
                        <div class="form-check mt-3 p-3 border border-2 border-primary rounded bg-light">
                            <input class="form-check-input" type="checkbox" id="generate-confirm-checkbox" style="transform: scale(1.2); border: 2px solid #0d6efd;">
                            <label class="form-check-label fw-bold" for="generate-confirm-checkbox">
                                I confirm that I want to generate this certificate.
                            </label>
                        </div>
                    </div>
                `,
                showCancelButton: true,
                confirmButtonText: 'Yes, Generate Certificate',
                cancelButtonText: 'Cancel',
                confirmButtonColor: '#0d6efd',
                cancelButtonColor: '#6c757d',
                showCloseButton: true,
                preConfirm: () => {
                    const checkbox = document.getElementById('generate-confirm-checkbox');
                    if (!checkbox.checked) {
                        Swal.showValidationMessage('Please confirm that you want to generate the certificate');
                        return false;
                    }
                    return true;
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    // User confirmed, proceed with generation
                    proceedWithGeneration(awardId);
                }
            });
        }

        // Function to actually generate the certificate
        function proceedWithGeneration(awardId) {
            // Show loading state
            const button = event.target.closest('button');
            const originalText = button.innerHTML;
            button.innerHTML = '<i class="ri-loader-2-line me-1 spinner-border spinner-border-sm"></i> Generating...';
            button.disabled = true;

            // Show loading alert following submission module pattern
            Swal.fire({
                title: 'Generating Certificate',
                html: 'Please wait while we create your certificate...',
                allowOutsideClick: false,
                allowEscapeKey: false,
                showConfirmButton: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            // Get participant ID from session
            const participantId = <?= session()->get('current_participant_id') ?? 'null' ?>;
            
            if (!participantId) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    html: '<p>Unable to identify participant. Please refresh the page and try again.</p>',
                    confirmButtonText: 'OK'
                });
                button.innerHTML = originalText;
                button.disabled = false;
                return;
            }

            // Make API call to generate certificate
            fetch(`<?= base_url('api/certificates/generate') ?>`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({
                    participant_id: participantId,
                    award_id: awardId
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.status && data.data) {
                    // Handle successful certificate generation with base64 data
                    const certificateData = data.data;
                    
                    // Create blob from base64 data
                    const binaryString = atob(certificateData.file_data);
                    const bytes = new Uint8Array(binaryString.length);
                    for (let i = 0; i < binaryString.length; i++) {
                        bytes[i] = binaryString.charCodeAt(i);
                    }
                    const blob = new Blob([bytes], { type: certificateData.mime_type || 'application/pdf' });
                    
                    // Create download link
                    const url = window.URL.createObjectURL(blob);
                    const link = document.createElement('a');
                    link.href = url;
                    link.download = certificateData.file_name || 'certificate.pdf';
                    
                    // Trigger download
                    document.body.appendChild(link);
                    link.click();
                    document.body.removeChild(link);
                    window.URL.revokeObjectURL(url);
                    
                    // Show success message
                    Swal.fire({
                        icon: 'success',
                        title: 'Certificate Generated!',
                        html: `
                            <div class="text-center">
                                <p>${certificateData.message || 'Your certificate has been successfully generated and downloaded.'}</p>
                                <p class="mt-3">The certificate has been saved to your downloads folder.</p>
                                <p class="small text-muted">File: ${certificateData.file_name}</p>
                            </div>
                        `,
                        confirmButtonText: 'OK',
                        allowOutsideClick: false,
                        allowEscapeKey: false
                    }).then(() => {
                        // Reload the page to refresh the certificates list
                        window.location.reload();
                    });
                    
                    // Re-enable the button
                    button.innerHTML = originalText;
                    button.disabled = false;
                    
                } else {
                    // Re-enable the button
                    button.innerHTML = originalText;
                    button.disabled = false;
                    
                    // Show error message
                    Swal.fire({
                        icon: 'error',
                        title: 'Generation Failed',
                        html: `<p>${data.message || 'There was an error generating your certificate. Please try again.'}</p>`,
                        confirmButtonText: 'Try Again'
                    });
                }
            })
            .catch(error => {
                console.error('Error generating certificate:', error);
                
                // Re-enable the button
                button.innerHTML = originalText;
                button.disabled = false;
                
                // Show error message following submission module pattern
                Swal.fire({
                    icon: 'error',
                    title: 'Connection Error',
                    html: '<p>There was a problem connecting to the server. Please check your internet connection and try again.</p>',
                    confirmButtonText: 'Try Again'
                });
            });
        }
    </script>
</body>

</html>