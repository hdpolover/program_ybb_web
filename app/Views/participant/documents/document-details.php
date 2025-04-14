<?= $this->include('partials/main') ?>

<head>
    <?php echo view('partials/title-meta', array('title' => 'Document Details')); ?>
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
                    <?php echo view('partials/page-title', array('pagetitle' => 'Documents', 'title' => 'Document Details')); ?>

                    <!-- Document Details Card -->
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="card">
                                <!-- Card Header with Back Button -->
                                <div class="card-header d-flex align-items-center">
                                    <div class="flex-grow-1">
                                        <h4 class="card-title mb-0">
                                            <?= $document['name'] ?? 'Document' ?>
                                        </h4>
                                    </div>
                                    <div class="flex-shrink-0">
                                        <a href="<?= base_url('documents/program') ?>" class="btn btn-primary btn-sm">
                                            <i class="ri-arrow-left-line align-middle me-1"></i> Back to Documents
                                        </a>
                                    </div>
                                </div>

                                <div class="card-body">
                                    <!-- Document Information -->
                                    <div class="row">
                                        <div class="col-lg-8">
                                            <!-- Document Details Section -->
                                            <div class="mb-4">

                                                <?php if (!empty($document['desc'])): ?>
                                                    <div class="mb-4">
                                                        <h6 class="text-muted fw-semibold mb-2">Description:</h6>
                                                        <div class="bg-light p-3 rounded">
                                                            <?= nl2br($document['desc'] ?? 'No description available') ?>
                                                        </div>
                                                    </div>
                                                <?php endif; ?>

                                                <!-- Document Metadata -->
                                                <div class="mt-4">
                                                    <div class="row">
                                                        <div class="col-md-6 mb-3">
                                                            <h6 class="text-muted fw-semibold mb-1">Document Type:</h6>
                                                            <p>
                                                                <?php if (isset($document['is_upload']) && $document['is_upload']): ?>
                                                                    <span class="badge bg-info">Upload Required</span>
                                                                <?php elseif (isset($document['is_generated']) && $document['is_generated']): ?>
                                                                    <span class="badge bg-primary">Can Generate</span>
                                                                <?php else: ?>
                                                                    <span class="badge bg-secondary">Reference Document</span>
                                                                <?php endif; ?>
                                                            </p>
                                                        </div>

                                                        <?php if (isset($document['created_at'])): ?>
                                                            <div class="col-md-6 mb-3">
                                                                <h6 class="text-muted fw-semibold mb-1">Available Since:</h6>
                                                                <p><?= date('F d, Y', strtotime($document['created_at'])) ?></p>
                                                            </div>
                                                        <?php endif; ?>

                                                        <?php if (isset($document['file_size'])): ?>
                                                            <div class="col-md-6 mb-3">
                                                                <h6 class="text-muted fw-semibold mb-1">File Size:</h6>
                                                                <p><?= $document['file_size'] ?> KB</p>
                                                            </div>
                                                        <?php endif; ?>

                                                        <?php if (isset($document['file_type'])): ?>
                                                            <div class="col-md-6 mb-3">
                                                                <h6 class="text-muted fw-semibold mb-1">Format:</h6>
                                                                <p><?= strtoupper($document['file_type']) ?></p>
                                                            </div>
                                                        <?php endif; ?>

                                                        <?php if (isset($document['required']) && $document['required']): ?>
                                                            <div class="col-md-6 mb-3">
                                                                <h6 class="text-muted fw-semibold mb-1">Requirement:</h6>
                                                                <p><span class="badge bg-danger">Required Document</span></p>
                                                            </div>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Document Instructions Section (if applicable) -->
                                            <?php if (!empty($document['instructions'])): ?>
                                                <div class="mb-4">
                                                    <h5 class="text-primary mb-3">Instructions</h5>
                                                    <div class="bg-light p-3 rounded">
                                                        <?= nl2br($document['instructions']) ?>
                                                    </div>
                                                </div>
                                            <?php endif; ?>
                                        </div>

                                        <!-- Sidebar/Action Card -->
                                        <div class="col-lg-4">
                                            <div class="card border shadow-none mb-3">
                                                <div class="card-header bg-soft-primary">
                                                    <h5 class="card-title mb-0">Document Actions</h5>
                                                </div>
                                                <div class="card-body">
                                                    <?php if (!empty($document['file_url'])): ?>
                                                        <div class="mb-3 text-center">
                                                            <div class="mb-3">
                                                                <i class="ri-file-download-line text-primary display-5"></i>
                                                            </div>
                                                            <h6 class="fs-15 mb-3">Download Document</h6>
                                                            <a href="<?= $document['file_url'] ?>" class="btn btn-primary w-100" download>
                                                                <i class="ri-download-2-line align-middle me-1"></i> Download
                                                            </a>
                                                        </div>
                                                    <?php elseif (!empty($document['drive_url'])): ?>
                                                        <div class="mb-3 text-center">
                                                            <div class="mb-3">
                                                                <i class="ri-external-link-line text-info display-5"></i>
                                                            </div>
                                                            <h6 class="fs-15 mb-3">External Document</h6>
                                                            <a href="<?= $document['drive_url'] ?>" class="btn btn-info w-100" target="_blank">
                                                                <i class="ri-external-link-line align-middle me-1"></i> Open Document
                                                            </a>
                                                        </div> <?php elseif (isset($document['is_upload']) && $document['is_upload']): ?>
                                                        <div class="mb-3 text-center">
                                                            <div class="mb-3">
                                                                <i class="ri-upload-cloud-2-line text-warning display-5"></i>
                                                            </div>
                                                            <h6 class="fs-15 mb-3">Upload Required</h6>
                                                            <p class="text-muted mb-2">You need to upload this document as part of your program requirements.</p>
                                                            <a href="#" class="btn btn-warning w-100">
                                                                <i class="ri-upload-2-line align-middle me-1"></i> Upload Document
                                                            </a>
                                                        </div>
                                                    <?php elseif (isset($document['type']) && $document['type'] === 'loa'): ?>
                                                        <div class="mb-3 text-center">
                                                            <div class="mb-3">
                                                                <i class="ri-file-text-line text-success display-5"></i>
                                                            </div>
                                                            <h6 class="fs-15 mb-3"><?= $document['name']?></h6>
                                                            <p class="text-muted mb-2">Click below to generate your personalized document. This document is important for your program participation.</p>
                                                            <button id="generate-loa-btn" class="btn btn-success w-100"
                                                                data-document-id="<?= $document['id'] ?? '' ?>"
                                                                data-participant-id="<?= session()->get('current_participant_id') ?>">
                                                                <i class="ri-file-paper-2-line align-middle me-1"></i> Generate Document
                                                            </button>
                                                        </div>
                                                    <?php else: ?>
                                                        <div class="text-center p-3">
                                                            <div class="mb-3">
                                                                <i class="ri-information-line text-secondary display-5"></i>
                                                            </div>
                                                            <p class="text-muted">No document file is available at this time. Please check back later.</p>
                                                        </div>
                                                    <?php endif; ?>

                                                    <?php if (isset($document['required']) && $document['required']): ?>
                                                        <div class="alert alert-warning mb-0 mt-3">
                                                            <div class="d-flex align-items-center">
                                                                <div class="flex-shrink-0 me-2">
                                                                    <i class="ri-error-warning-fill text-warning fs-18"></i>
                                                                </div>
                                                                <div class="flex-grow-1">
                                                                    <h6 class="mb-0">Required Document</h6>
                                                                    <small>This document is required for your program participation.</small>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    <?php endif; ?>
                                                </div>
                                            </div>

                                            <!-- Related Documents Card (optional) -->
                                            <?php if (!empty($document['related_documents'])): ?>
                                                <div class="card border shadow-none">
                                                    <div class="card-header bg-soft-info">
                                                        <h5 class="card-title mb-0">Related Documents</h5>
                                                    </div>
                                                    <div class="card-body">
                                                        <ul class="list-group list-group-flush">
                                                            <?php foreach ($document['related_documents'] as $relatedDoc): ?>
                                                                <li class="list-group-item px-0">
                                                                    <div class="d-flex align-items-start">
                                                                        <div class="flex-shrink-0 me-3">
                                                                            <i class="ri-file-text-line text-info fs-18"></i>
                                                                        </div>
                                                                        <div class="flex-grow-1">
                                                                            <a href="<?= base_url('documents/program/details/' . $relatedDoc['id']) ?>" class="text-reset">
                                                                                <?= $relatedDoc['name'] ?>
                                                                            </a>
                                                                        </div>
                                                                    </div>
                                                                </li>
                                                            <?php endforeach; ?>
                                                        </ul>
                                                    </div>
                                                </div>
                                            <?php endif; ?>
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

    <?= $this->include('partials/vendor-scripts') ?> <!-- App js -->
    <script src="/assets/js/app.js"></script>

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- LOA Generation Handler -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Find the generate LOA button
            const generateLoaBtn = document.getElementById('generate-loa-btn');

            if (generateLoaBtn) {
                generateLoaBtn.addEventListener('click', function() {
                    // Get data attributes
                    const documentId = this.getAttribute('data-document-id');
                    const participantId = this.getAttribute('data-participant-id');

                    // Show loading alert
                    Swal.fire({
                        title: 'Generating Document...',
                        html: 'Please wait while we prepare your document.',
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    }); // Make API request to generate LOA
                    const generateUrl = '/documents/generate-loa/' + documentId + '/' + participantId;
                    console.log('Generating LOA with URL:', generateUrl);

                    fetch(generateUrl)
                        .then(response => {
                            if (!response.ok) {
                                console.error('API error:', response.status, response.statusText);
                                throw new Error(`Failed to generate document: ${response.status}`);
                            }
                            // Check if response is JSON or PDF
                            const contentType = response.headers.get('content-type');
                            console.log('Response Content-Type:', contentType);

                            if (contentType && contentType.includes('application/json')) {
                                return response.json(); // Parse JSON response
                            } else {
                                return response.blob(); // Handle as binary data
                            }
                        })
                        .then(data => {
                            // Close the loading modal
                            Swal.close();

                            if (data.success) {
                                // get loa data
                                var loa = data.loa;
                                console.log('LOA Data:', loa);

                                var fileName = loa.file_name || 'Letter_of_Acceptance.pdf';
                                var mimeType = loa.mime_type || 'application/pdf';
                                var fileData = loa.file_data || null;

                                // Check if fileData is a Blob or base64 string
                                if (fileData) {
                                    // Create a Blob from the base64 string
                                    var byteCharacters = atob(fileData);
                                    var byteNumbers = new Array(byteCharacters.length);
                                    for (var i = 0; i < byteCharacters.length; i++) {
                                        byteNumbers[i] = byteCharacters.charCodeAt(i);
                                    }
                                    var byteArray = new Uint8Array(byteNumbers);
                                    var blob = new Blob([byteArray], {
                                        type: mimeType
                                    });

                                    // Create URL for the blob
                                    var blobUrl = URL.createObjectURL(blob);
                                    
                                    // show success alert
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Document Generated',
                                        text: 'Your Letter of Acceptance has been generated successfully.',
                                        showCancelButton: true,
                                        confirmButtonText: 'Download',
                                        cancelButtonText: 'View in New Tab'
                                    }).then((result) => {
                                        if (result.isConfirmed) {
                                            // Download the file
                                            var a = document.createElement('a');
                                            a.href = blobUrl;
                                            a.download = fileName;
                                            document.body.appendChild(a);
                                            a.click();
                                            document.body.removeChild(a);
                                        } else if (result.dismiss === Swal.DismissReason.cancel) {
                                            // Open in new tab
                                            window.open(blobUrl, '_blank');
                                        }
                                        // Clean up the URL object after a delay to ensure it's used
                                        setTimeout(() => {
                                            URL.revokeObjectURL(blobUrl);
                                        }, 1000);
                                    });
                                } else {
                                    // Handle the case where fileData is not available
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Generation Failed',
                                        text: 'The document could not be generated. Please try again later.'
                                    });
                                }
                            } else if (data.error) {
                                console.error('Error generating LOA:', data.error);
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Generation Failed',
                                    text: data.error
                                });

                            } else {
                                console.error('Unexpected response format:', data);
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Generation Failed',
                                    text: 'There was a problem generating your Letter of Acceptance. Please try again later.'
                                });
                            }
                        })
                        .catch(error => {
                            console.error('Error generating LOA:', error);
                            Swal.fire({
                                icon: 'error',
                                title: 'Generation Failed',
                                text: 'There was a problem generating your Letter of Acceptance. Please try again later.'
                            });
                        });
                });
            }
        });
    </script>
</body>

</html>