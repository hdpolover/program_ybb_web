<?= $this->include('partials/main') ?>

<head>

    <?php echo view('partials/title-meta', array('title' => 'Abstract Management')); ?>

    <!-- quill css -->
    <link href="/assets/libs/quill/quill.core.css" rel="stylesheet" type="text/css" />
    <link href="/assets/libs/quill/quill.bubble.css" rel="stylesheet" type="text/css" />
    <link href="/assets/libs/quill/quill.snow.css" rel="stylesheet" type="text/css" />

    <!-- Sweet Alert css-->
    <link href="/assets/libs/sweetalert2/sweetalert2.min.css" rel="stylesheet" type="text/css" />    <style>        .bg-light-subtle {
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
                                        <div class="row mb-3">
                                            <div class="col-lg-12">
                                                <label for="topic" class="form-label">Topic <span class="text-danger">*</span></label>
                                                <select class="form-select" id="topic" name="topic" required>
                                                    <option value="">Select Topic</option>
                                                    <?php if (isset($topics) && is_array($topics)): ?>
                                                        <?php foreach ($topics as $topic): ?>
                                                            <option value="<?= $topic['id'] ?>"
                                                                data-description="<?= htmlspecialchars($topic['description'] ?? '') ?>"
                                                                <?= (isset($abstract) && $abstract['topic_id'] == $topic['id']) ? 'selected' : '' ?>>
                                                                <?= $topic['name'] ?>
                                                            </option>
                                                        <?php endforeach; ?>
                                                    <?php endif; ?>                                                </select>
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
                                                        <a href="<?= base_url('abstract-paper') ?>" class="btn btn-light">Cancel</a>
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
                Swal.fire({
                    title: 'Success!',
                    text: '<?= session('success') ?>',
                    icon: 'success',
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#5156be'
                });
            <?php endif; ?>

            <?php if (session()->has('error')): ?>
                Swal.fire({
                    title: 'Error!',
                    text: '<?= session('error') ?>',
                    icon: 'error',
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#5156be'
                });
            <?php endif; ?> 
            
            // Form submission handling
            const abstractForm = document.getElementById('abstractForm');
            const submitBtn = document.getElementById('submit-btn');
            const saveDraftBtn = document.getElementById('save-draft-btn');

            // Helper function to validate form
            function validateForm(isFullValidation = true) {
                // Get editor content and set to hidden field
                const content = quill.root.innerHTML;
                document.getElementById('abstract-content').value = content;

                // Basic validation
                let isValid = true;

                // For draft, we only require title
                if (isFullValidation) {
                    if (!document.getElementById('topic').value) {
                        document.getElementById('topic').classList.add('is-invalid');
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

                // Remove draft flag if it exists
                if (document.getElementById('is_draft')) {
                    document.getElementById('is_draft').value = '0';
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
                        // If confirmed, submit the form
                        abstractForm.submit();
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

                // Add a hidden field to indicate this is a draft
                if (!document.getElementById('is_draft')) {
                    const draftInput = document.createElement('input');
                    draftInput.type = 'hidden';
                    draftInput.id = 'is_draft';
                    draftInput.name = 'is_draft';
                    draftInput.value = '1';
                    abstractForm.appendChild(draftInput);
                } else {
                    document.getElementById('is_draft').value = '1';
                }
                // Show a brief saving message
                Swal.fire({
                    title: 'Saving Draft',
                    html: `
                        <div class="text-start">
                            <p>Saving your draft abstract...</p>
                            <p><small>You can return to complete and submit it later.</small></p>
                        </div>
                    `,
                    icon: 'info',
                    showConfirmButton: false,
                    timer: 1500,
                    timerProgressBar: true
                }).then(() => {
                    // Submit the form
                    abstractForm.submit();
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
            });            
            // Handle topic selection to show description
            const topicSelect = document.getElementById('topic');
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