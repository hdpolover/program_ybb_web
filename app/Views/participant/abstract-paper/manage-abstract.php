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
                                        <i class="bx bx-info-circle me-1"></i> Topic and title are required to save a draft. All fields marked with <span class="text-danger">*</span> are required for final submission.
                                    </p>
                                </div>
                            </div>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>

                    <?php if (isset($abstract) && isset($abstract['current_version'])): ?>
                        <div class="alert alert-info alert-dismissible fade show" role="alert">
                            <div class="d-flex">
                                <div class="flex-shrink-0 me-2">
                                    <i class="bx bx-revision fs-5"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <h5 class="alert-heading">Editing Abstract Version</h5>
                                    <p class="mb-0">You are editing version <?= $abstract['current_version']['version_number'] ?> of your abstract, created on <?= date('F j, Y', strtotime($abstract['current_version']['created_at'])) ?>.</p>
                                    <?php if (isset($abstract['current_version']['updated_at']) && $abstract['current_version']['created_at'] !== $abstract['current_version']['updated_at']): ?>
                                        <p class="mb-0 small">Last updated on <?= date('F j, Y', strtotime($abstract['current_version']['updated_at'])) ?></p>
                                    <?php endif; ?>
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

                                <?php if (isset($abstract) && isset($abstractVersions) && count($abstractVersions) > 1): ?>
                                    <div class="card-header bg-light-subtle border-top border-bottom">
                                        <div class="d-flex align-items-center">
                                            <h5 class="mb-0 me-3 fs-6">Version History</h5>
                                            <div class="btn-group btn-group-sm" role="group">
                                                <?php foreach ($abstractVersions as $version): ?>
                                                    <a href="<?= base_url('abstract-paper/edit/' . $abstract['id'] . '/' . $version['version_number']) ?>"
                                                        class="btn <?= ($abstract['current_version']['version_number'] == $version['version_number']) ? 'btn-primary' : 'btn-outline-secondary' ?>">
                                                        v<?= $version['version_number'] ?>
                                                        <?php if (isset($version['is_active']) && $version['is_active'] == '1'): ?>
                                                            <i class="bx bx-check-circle ms-1" data-bs-toggle="tooltip" title="Active Version"></i>
                                                        <?php endif; ?>
                                                    </a>
                                                <?php endforeach; ?>
                                            </div>
                                            <div class="ms-auto">
                                                <small class="text-muted">
                                                    <i class="bx bx-info-circle"></i> Click on a version number to view or edit that version
                                                </small>
                                            </div>
                                        </div>
                                    </div>
                                <?php endif; ?>                                <div class="card-body">
                                    <form id="abstractForm" method="POST" action="">
                                        <?= csrf_field() ?>
                                        <input type="hidden" name="abstract_id" value="<?= isset($abstract) ? $abstract['id'] : '' ?>">
                                        <?php if (isset($abstract) && isset($abstract['current_version'])): ?>
                                            <input type="hidden" name="version_id" value="<?= $abstract['current_version']['id'] ?>">
                                            <input type="hidden" name="version_number" value="<?= $abstract['current_version']['version_number'] ?>">
                                        <?php endif; ?>
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
                                                                <?= (isset($abstract) && isset($abstract['abstract_topic_id']) && $abstract['abstract_topic_id'] == $topic['id']) ? 'selected' : '' ?>>
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
                                                    value="<?= isset($abstract) && isset($abstract['current_version']) ? $abstract['current_version']['title'] : (isset($abstract) && isset($abstract['versions']) && !empty($abstract['versions']) ? $abstract['versions'][0]['title'] : '') ?>"
                                                    placeholder="Enter a concise and descriptive title for your abstract" required>
                                                <div class="invalid-feedback">Please enter the abstract title.</div>
                                                <div class="form-text text-muted">A good title should clearly represent the content and focus of your research.</div>
                                            </div>
                                        </div>                                        <div class="row mb-3">
                                            <div class="col-lg-12">
                                                <label for="keywords" class="form-label">Keywords</label>
                                                <input type="text" class="form-control" id="keywords" name="keywords"
                                                    value="<?= isset($abstract) && isset($abstract['current_version']) ? $abstract['current_version']['keywords'] : (isset($abstract) && isset($abstract['versions']) && !empty($abstract['versions']) ? $abstract['versions'][0]['keywords'] : '') ?>"
                                                    placeholder="Enter keywords separated by commas">
                                                <div class="invalid-feedback">Please enter keywords.</div>
                                                <div class="form-text text-muted">Enter keywords separated by commas (e.g., research, medicine, science)</div>
                                            </div>
                                        </div>


                                        <div class="row mb-3">
                                            <div class="col-lg-12">
                                                <label class="form-label">Abstract Content <span class="text-danger">*</span></label>
                                                <div id="abstract-editor" style="height: 300px;">
                                                    <?= isset($abstract) && isset($abstract['current_version']) ? $abstract['current_version']['content'] : (isset($abstract) && isset($abstract['versions']) && !empty($abstract['versions']) ? $abstract['versions'][0]['content'] : '') ?>
                                                </div>
                                                <input type="hidden" name="content" id="abstract-content">
                                                <div class="invalid-feedback" id="content-feedback">Please enter abstract content.</div>
                                            </div>
                                        </div>

                                        <div class="row mb-3">
                                            <div class="col-lg-12">
                                                <label for="refs" class="form-label">References</label>
                                                <textarea class="form-control" id="refs" name="refs" rows="6"
                                                    placeholder="Enter references in the required format (e.g., APA, IEEE, etc.)"><?= isset($abstract) && isset($abstract['current_version']) ? $abstract['current_version']['refs'] : (isset($abstract) && isset($abstract['versions']) && !empty($abstract['versions']) ? $abstract['versions'][0]['refs'] : '') ?></textarea>
                                                <div class="invalid-feedback">Please enter references.</div>
                                                <div class="form-text text-muted">Include all references cited in your abstract following the conference's citation format</div>
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
                                                        </button>                                        <button type="submit" class="btn btn-primary" id="submit-btn">
                                            <i class="bx bx-check-circle me-1"></i> Submit Abstract
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

    <!-- jQuery -->
    <script src="/assets/libs/jquery/jquery.min.js"></script>

    <!-- quill js -->
    <script src="/assets/libs/quill/quill.min.js"></script>

    <!-- Sweet Alerts js -->
    <script src="/assets/libs/sweetalert2/sweetalert2.min.js"></script>

    <!-- Abstract Version Manager -->
    <script src="/assets/js/abstract-version-manager.js"></script>

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
            <?php endif; ?> // Form submission handling
            const abstractForm = document.getElementById('abstractForm');
            const submitBtn = document.getElementById('submit-btn');
            const saveDraftBtn = document.getElementById('save-draft-btn'); // Helper function to validate form
            function validateForm(isFullValidation = true) {
                // Get editor content and set to hidden field
                const content = quill.root.innerHTML;
                document.getElementById('abstract-content').value = content;

                // Basic validation
                let isValid = true;

                // Topic is required for both draft and full submission
                if (!document.getElementById('abstract_topic_id').value) {
                    document.getElementById('abstract_topic_id').classList.add('is-invalid');
                    isValid = false;
                }

                // Title is required for both draft and submission
                if (!document.getElementById('title').value) {
                    document.getElementById('title').classList.add('is-invalid');
                    isValid = false;
                }                // For full submission, we require additional fields
                if (isFullValidation) {
                    // Keywords are permit_empty according to API, so not required for submission
                    
                    if (quill.getText().trim().length === 0) {
                        document.getElementById('abstract-editor').classList.add('is-invalid');
                        document.getElementById('content-feedback').style.display = 'block';
                        isValid = false;
                    }
                }

                return isValid;
            }

            // Function to extract content from Quill editor
            function getQuillContent() {
                return quill.root.innerHTML;
            }            // Setup form handlers - use traditional form submission for all abstracts
            console.log('Setting up form handlers');
            setupNewAbstractHandlers(
                '#abstractForm',
                '#save-draft-btn',
                '#submit-btn',
                getQuillContent,
                validateForm
            );// Clear validation error on input
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