<?= $this->include('partials/main') ?>

<head>

    <?php echo view('partials/title-meta', array('title' => 'Abstract Management')); ?>

    <!-- quill css -->
    <link href="/assets/libs/quill/quill.core.css" rel="stylesheet" type="text/css" />
    <link href="/assets/libs/quill/quill.bubble.css" rel="stylesheet" type="text/css" />
    <link href="/assets/libs/quill/quill.snow.css" rel="stylesheet" type="text/css" />

    <!-- Sweet Alert css-->
    <link href="/assets/libs/sweetalert2/sweetalert2.min.css" rel="stylesheet" type="text/css" />
    <style>
        .bg-light-subtle {
            background-color: rgba(var(--bs-light-rgb), 0.5) !important;
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

                    <?php
                    $pageTitle = isset($abstract) ? 'Edit Abstract' : 'Create New Abstract';
                    echo view('partials/page-title', array('pagetitle' => 'Abstract Management', 'title' => $pageTitle));
                    ?>

                    <!-- Display validation errors and flash messages -->
                    <?php if (session()->has('errors')): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <ul class="mb-0">
                                <?php foreach (session('errors') as $error): ?>
                                    <li><?= esc($error) ?></li>
                                <?php endforeach; ?>
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>

                    <?php if (isset($abstract) && isset($abstract['is_draft']) && $abstract['is_draft']): ?> <div class="alert alert-info alert-dismissible fade show" role="alert">
                            <div class="d-flex">
                                <div class="flex-shrink-0 me-2">
                                    <i class="bx bx-edit-alt fs-5"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <h5 class="alert-heading">Editing Draft Abstract</h5>
                                    <p class="mb-0">This abstract is currently saved as a draft. You can continue editing and save as a draft again, or complete all required fields and submit when ready.</p>
                                    <hr>
                                    <p class="mb-0 small">
                                        <i class="bx bx-info-circle me-1"></i> Only the title is required to save a draft. All fields marked with <span class="text-danger">*</span> are required for final submission.
                                    </p>
                                </div>
                            </div>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>

                    <div class="row">
                        <div class="col-lg-12">
                            <div class="card">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h4 class="card-title mb-0"><?= $pageTitle ?></h4>
                                    <div class="text-muted small">
                                        <i class="bx bx-info-circle me-1"></i> Fields marked with <span class="text-danger">*</span> are required for final submission.
                                    </div>
                                </div>
                                <div class="card-body">
                                    <form id="abstractForm" method="post" action="<?= isset($abstract) ? base_url('abstract-paper/update/' . $abstract['id']) : base_url('abstract-paper/save') ?>">
                                        <?= csrf_field() ?>
                                        <input type="hidden" name="abstract_id" value="<?= isset($abstract) ? $abstract['id'] : '' ?>">
                                        <input type="hidden" name="program_id" value="<?= session()->get('current_program_id') ?>">
                                        <input type="hidden" name="primary_participant_id" value="<?= session()->get('current_participant_id') ?>">
                                        <div class="row mb-3">
                                            <div class="col-lg-12">
                                                <label for="abstract_topic_id" class="form-label">Topic <span class="text-danger">*</span></label>
                                                <select class="form-select" id="abstract_topic_id" name="abstract_topic_id" required>
                                                    <option value="">Select Topic</option>
                                                    <?php if (isset($topics) && is_array($topics)): ?>
                                                        <?php foreach ($topics as $topic): ?>
                                                            <option value="<?= $topic['id'] ?>"
                                                                data-description="<?= htmlspecialchars($topic['description'] ?? '') ?>"
                                                                <?= (isset($abstract) && $abstract['topic_id'] == $topic['id']) ? 'selected' : '' ?>>
                                                                <?= $topic['name'] ?>
                                                            </option>
                                                        <?php endforeach; ?>
                                                    <?php endif; ?>
                                                </select>
                                                <div class="invalid-feedback">Please select a topic.</div>
                                                <div id="topic-description" class="form-text text-muted mt-2"></div>
                                            </div>
                                        </div>
                                        <div class="row mb-3">
                                            <div class="col-lg-12">
                                                <label for="title" class="form-label">Title <span class="text-danger">*</span></label>
                                                <input type="text" class="form-control" id="title" name="title"
                                                    value="<?= isset($abstract) ? $abstract['title'] : '' ?>" required>
                                                <div class="invalid-feedback">Please enter the abstract title.</div>
                                            </div>
                                        </div>

                                        <div class="row mb-3">
                                            <div class="col-lg-12">
                                                <label for="keywords" class="form-label">Keywords <span class="text-danger">*</span></label>
                                                <input type="text" class="form-control" id="keywords" name="keywords"
                                                    value="<?= isset($abstract) ? $abstract['keywords'] : '' ?>"
                                                    placeholder="Enter keywords separated by commas" required>
                                                <div class="invalid-feedback">Please enter keywords.</div>
                                                <div class="form-text text-muted">Enter keywords separated by commas (e.g., research, medicine, science)</div>
                                            </div>
                                        </div>
                                        <div class="row mb-3">
                                            <div class="col-lg-12">
                                                <label class="form-label">Abstract Content <span class="text-danger">*</span></label>
                                                <div id="abstract-editor" style="height: 300px;">
                                                    <?= isset($abstract) ? $abstract['content'] : '' ?>
                                                </div>
                                                <input type="hidden" name="content" id="abstract-content">
                                                <div class="invalid-feedback" id="content-feedback">Please enter abstract content.</div>
                                            </div>
                                        </div>

                                        <div class="row mt-4">
                                            <div class="col-lg-12">
                                                <div class="d-flex flex-column">
                                                    <div class="text-muted mb-3 ms-auto">
                                                        <small><i class="bx bx-info-circle me-1"></i> You can save your work as a draft and complete it later.</small>
                                                    </div>
                                                    <div class="hstack gap-2 justify-content-end">
                                                        <button type="button" class="btn btn-secondary" id="save-draft-btn" data-bs-toggle="tooltip" data-bs-placement="top" title="Save your work without submitting">
                                                            <i class="bx bx-save me-1"></i> Save Draft
                                                        </button>
                                                        <button type="submit" class="btn btn-primary" id="submit-btn">
                                                            <i class="bx bx-check-circle me-1"></i> <?= isset($abstract) ? 'Update Abstract' : 'Submit Abstract' ?>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                                <!-- end card body -->
                            </div>
                            <!-- end card -->
                        </div>
                        <!-- end col -->
                    </div>
                    <!-- end row -->

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

    <!-- Sweet Alert js-->
    <script src="/assets/libs/sweetalert2/sweetalert2.min.js"></script>

    <!-- quill js -->
    <script src="/assets/libs/quill/quill.min.js"></script>

    <!-- init js -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize tooltips
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });

            // Initialize Quill editor
            var quill = new Quill('#abstract-editor', {
                theme: 'snow',
                modules: {
                    toolbar: [
                        ['bold', 'italic', 'underline', 'strike'],
                        ['blockquote', 'code-block'],
                        [{
                            'header': 1
                        }, {
                            'header': 2
                        }],
                        [{
                            'list': 'ordered'
                        }, {
                            'list': 'bullet'
                        }],
                        [{
                            'script': 'sub'
                        }, {
                            'script': 'super'
                        }],
                        [{
                            'indent': '-1'
                        }, {
                            'indent': '+1'
                        }],
                        ['clean']
                    ]
                },
                placeholder: 'Write your abstract content here...',
            }); 
            
            // Show SweetAlert messages if there are flash messages
            <?php if (session()->has('success')): ?>
                <?php
                $abstractData = session()->getFlashdata('abstract_data');
                $hasAbstractData = !empty($abstractData) && is_array($abstractData);
                ?>
                Swal.fire({
                    title: '<?= session()->has('success_title') ? session('success_title') : 'Success!' ?>',
                    html: `
                        <div class="text-start">
                            <p><?= session('success') ?></p>
                            <?php if ($hasAbstractData): ?>
                            <hr>
                            <div class="card bg-light mb-0 mt-3">
                                <div class="card-body p-3">
                                    <h6 class="card-title">Abstract Details</h6>
                                    <ul class="list-unstyled mb-0">
                                        <li><strong>ID:</strong> <?= $abstractData['id'] ?? 'N/A' ?></li>
                                        <li><strong>Title:</strong> <?= $abstractData['title'] ?? 'Your Abstract' ?></li>
                                        <li><strong>Status:</strong> <span class="badge bg-<?= ($abstractData['status'] ?? '') === 'draft' ? 'warning' : 'info' ?>"><?= ucfirst($abstractData['status'] ?? 'Pending') ?></span></li>
                                    </ul>
                                </div>
                            </div>
                            <?php endif; ?>
                        </div>
                    `,
                    icon: 'success',
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#5156be'
                });
            <?php endif; ?> <?php if (session()->has('error')): ?>
                Swal.fire({
                    title: '<?= session()->has('error_title') ? session('error_title') : 'Error!' ?>',
                    html: `
                        <div class="text-start">
                            <p><?= session('error') ?></p>
                            <div class="alert alert-warning mt-3 mb-0">
                                <i class="bx bx-info-circle me-1"></i>
                                <small>If this problem persists, please contact support with reference to the time of this error.</small>
                            </div>
                        </div>
                    `,
                    icon: 'error',
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#5156be'
                });
            <?php endif; ?>

            // Form submission handling
            const abstractForm = document.getElementById('abstractForm');
            const submitBtn = document.getElementById('submit-btn');
            const saveDraftBtn = document.getElementById('save-draft-btn'); // Helper function to validate form
            function validateForm(isFullValidation = true) {
                // Get editor content and set to hidden field
                const content = quill.root.innerHTML;
                document.getElementById('abstract-content').value = content;

                // Basic validation
                let isValid = true;

                // For draft, we only require title
                if (isFullValidation) {
                    if (!document.getElementById('abstract_topic_id').value) {
                        document.getElementById('abstract_topic_id').classList.add('is-invalid');
                        isValid = false;
                    }

                    if (!document.getElementById('keywords').value) {
                        document.getElementById('keywords').classList.add('is-invalid');
                        isValid = false;
                    }

                    if (quill.getText().trim().length === 0) {
                        document.getElementById('abstract-editor').classList.add('is-invalid');
                        document.getElementById('content-feedback').style.display = 'block';
                        isValid = false;
                    }
                }

                // Title is required for both draft and submission
                if (!document.getElementById('title').value) {
                    document.getElementById('title').classList.add('is-invalid');
                    isValid = false;
                }

                return isValid;
            } 
            
            // Prevent the form from submitting directly when the submit button is clicked
            submitBtn.addEventListener('click', function(e) {
                e.preventDefault();

                // Validate with full validation
                if (!validateForm(true)) {
                    Swal.fire({
                        title: 'Form Error!',
                        text: 'Please fill in all required fields correctly.',
                        icon: 'error',
                        confirmButtonText: 'OK',
                        confirmButtonColor: '#5156be'
                    });
                    return;
                }

                // Set status to submitted
                if (!document.getElementById('status')) {
                    const statusInput = document.createElement('input');
                    statusInput.type = 'hidden';
                    statusInput.id = 'status';
                    statusInput.name = 'status';
                    statusInput.value = 'submitted';
                    abstractForm.appendChild(statusInput);
                } else {
                    document.getElementById('status').value = 'submitted';
                }

                // Show confirmation dialog
                Swal.fire({
                    title: 'Submit Abstract',
                    html: `
                        <div class="text-start">
                            <p>Are you sure you want to submit this abstract? This will finalize your submission.</p>
                            <p class="mb-0"><strong>Note:</strong></p>
                            <ul class="text-start mb-0">
                                <li>All required fields must be completed.</li>
                                <li>After submission, your abstract will be sent for review.</li>
                                <li>You can still view your submission and track its status.</li>
                            </ul>
                        </div>
                    `,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, submit it!',
                    cancelButtonText: 'No, review again',
                    confirmButtonColor: '#5156be',
                    cancelButtonColor: '#fd625e'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Disable buttons to prevent multiple submissions
                        submitBtn.disabled = true;
                        saveDraftBtn.disabled = true;

                        // Show loading spinner on the submit button
                        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> Submitting...';

                        // Show submission in progress dialog
                        Swal.fire({
                            title: 'Submitting Abstract',
                            html: `
                                <div class="text-start">
                                    <p><i class="bx bx-paper-plane me-1"></i> Submitting your abstract for review...</p>
                                    <p><small>This may take a few moments. Please don't close this window.</small></p>
                                    <div class="progress mt-3">
                                        <div class="progress-bar progress-bar-striped progress-bar-animated bg-success" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" style="width: 100%"></div>
                                    </div>
                                </div>
                            `,
                            icon: 'info',
                            showConfirmButton: false,
                            allowOutsideClick: false,
                            allowEscapeKey: false,
                            didOpen: () => {
                                // Submit the form after showing the dialog
                                setTimeout(() => {
                                    abstractForm.submit();

                                    // Set a timeout to show a message if the server takes too long
                                    setTimeout(() => {
                                        // Check if the alert is still open
                                        if (Swal.isVisible()) {
                                            Swal.update({
                                                title: 'Still Processing',
                                                html: `
                                                    <div class="text-start">
                                                        <p><i class="bx bx-time me-1"></i> The server is taking longer than expected to respond.</p>
                                                        <p>Your request is still being processed. You can:</p>
                                                        <ul>
                                                            <li>Continue waiting</li>
                                                            <li>Check your abstract list in a few minutes to see if it was submitted</li>
                                                            <li>Try again if you don't see your abstract in the list</li>
                                                        </ul>
                                                    </div>
                                                `,
                                                icon: 'warning'
                                            });
                                        }
                                    }, 20000); // Show timeout message after 20 seconds
                                }, 500);
                            }
                        });
                    }
                });
            }); 
            
            // Save Draft functionality
            saveDraftBtn.addEventListener('click', function(e) {
                e.preventDefault();

                // Only validate title for draft
                if (!validateForm(false)) {
                    Swal.fire({
                        title: 'Form Error!',
                        text: 'Please provide at least a title for your draft.',
                        icon: 'error',
                        confirmButtonText: 'OK',
                        confirmButtonColor: '#5156be'
                    });
                    return;
                }

                // Disable buttons to prevent multiple submissions
                saveDraftBtn.disabled = true;
                submitBtn.disabled = true;

                // Show loading spinner on the save draft button
                saveDraftBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> Saving...';

                // Add a hidden field to indicate this is a draft
                if (!document.getElementById('status')) {
                    const statusInput = document.createElement('input');
                    statusInput.type = 'hidden';
                    statusInput.id = 'status';
                    statusInput.name = 'status';
                    statusInput.value = 'draft';
                    abstractForm.appendChild(statusInput);
                } else {
                    document.getElementById('status').value = 'draft';
                }

                // Show a detailed saving message with timeout
                let saveAlert = Swal.fire({
                    title: 'Saving Draft',
                    html: `
                        <div class="text-start">
                            <p><i class="bx bx-save me-1"></i> Saving your draft abstract...</p>
                            <p><small>This may take a few moments. Please don't close this window.</small></p>
                            <div class="progress mt-3">
                                <div class="progress-bar progress-bar-striped progress-bar-animated bg-primary" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" style="width: 100%"></div>
                            </div>
                        </div>
                    `,
                    icon: 'info',
                    showConfirmButton: false,
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    didOpen: () => {
                        // Submit the form after showing the dialog
                        setTimeout(() => {
                            abstractForm.submit();

                            // Set a timeout to show a message if the server takes too long
                            setTimeout(() => {
                                // Check if the alert is still open
                                if (Swal.isVisible()) {
                                    Swal.update({
                                        title: 'Still Processing',
                                        html: `
                                            <div class="text-start">
                                                <p><i class="bx bx-time me-1"></i> The server is taking longer than expected to respond.</p>
                                                <p>Your request is still being processed. You can:</p>
                                                <ul>
                                                    <li>Continue waiting</li>
                                                    <li>Check your abstract list in a few minutes to see if it was saved</li>
                                                    <li>Try again if you don't see your abstract in the list</li>
                                                </ul>
                                            </div>
                                        `,
                                        icon: 'warning'
                                    });
                                }
                            }, 20000); // Show timeout message after 20 seconds
                        }, 500);
                    }
                });
            });

            // Clear validation error on input
            const inputs = document.querySelectorAll('.form-control, .form-select');
            inputs.forEach(input => {
                input.addEventListener('input', function() {
                    this.classList.remove('is-invalid');
                });
            });

            // Clear editor validation on input
            quill.on('text-change', function() {
                document.getElementById('abstract-editor').classList.remove('is-invalid');
                document.getElementById('content-feedback').style.display = 'none';
            }); // Handle topic selection to show description
            const topicSelect = document.getElementById('abstract_topic_id');
            const topicDescription = document.getElementById('topic-description');

            // Function to show/hide topic description
            function updateTopicDescription() {
                const selectedOption = topicSelect.options[topicSelect.selectedIndex];
                const description = selectedOption.getAttribute('data-description');

                // Simply update the text content directly
                topicDescription.textContent = description || '';
            }

            // Update description on page load if a topic is already selected
            if (topicSelect.value) {
                updateTopicDescription();
            }

            // Add event listener for topic selection change
            topicSelect.addEventListener('change', updateTopicDescription);
        });
    </script>

    <!-- App js -->
    <script src="/assets/js/app.js"></script>
</body>

</html>