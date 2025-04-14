<?= $this->include('partials/main') ?>

<head>

    <?php echo view('partials/title-meta', array('title' => 'Submission Edit')); ?>

    <?= $this->include('partials/head-css') ?>

    <!-- Sweet Alert css-->
    <link href="/assets/libs/sweetalert2/sweetalert2.min.css" rel="stylesheet" type="text/css" />

    <!-- QuillJS CSS -->
    <link href="<?= base_url('assets/libs/quill/quill.core.css') ?>" rel="stylesheet" type="text/css" />
    <link href="<?= base_url('assets/libs/quill/quill.snow.css') ?>" rel="stylesheet" type="text/css" />
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
                    <?php echo view('partials/page-title', array('pagetitle' => 'Submission', 'title' => 'Edit Form')); ?>

                    <!--end card-->

                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title mb-0"><?= $currentProgram['name'] ?? 'Default' ?> Registration Form</h4>
                        </div><!-- end card header -->
                        <div class="card-body">
                            <form action="#" class="form-steps" autocomplete="off">

                                <div class="step-arrow-nav mb-4">
                                    <ul class="nav nav-pills custom-nav nav-justified" role="tablist">
                                        <li class="nav-item" role="presentation">
                                            <button class="nav-link active" id="steparrow-personal-tab" data-bs-toggle="pill" data-bs-target="#steparrow-personal" type="button" role="tab" aria-controls="steparrow-personal" aria-selected="true">Personal Details</button>
                                        </li>
                                        <li class="nav-item" role="presentation">
                                            <button class="nav-link" id="steparrow-professional-tab" data-bs-toggle="pill" data-bs-target="#steparrow-professional" type="button" role="tab" aria-controls="steparrow-professional" aria-selected="false">Professional Profile</button>
                                        </li>
                                        <li class="nav-item" role="presentation">
                                            <button class="nav-link" id="steparrow-entry-tab" data-bs-toggle="pill" data-bs-target="#steparrow-entry" type="button" role="tab" aria-controls="steparrow-entry" aria-selected="false">Entry Information</button>
                                        </li>
                                        <li class="nav-item" role="presentation">
                                            <button class="nav-link" id="steparrow-misc-tab" data-bs-toggle="pill" data-bs-target="#steparrow-misc" type="button" role="tab" aria-controls="steparrow-misc" aria-selected="false">Miscellaneous</button>
                                        </li>
                                        <li class="nav-item" role="presentation">
                                            <button class="nav-link" id="steparrow-preview-tab" data-bs-toggle="pill" data-bs-target="#steparrow-preview" type="button" role="tab" aria-controls="steparrow-preview" aria-selected="false">Preview</button>
                                        </li>
                                    </ul>
                                </div>

                                <div class="tab-content">
                                    <!-- Personal Details Tab -->
                                    <?= $this->include('participant/submission/edit-tab-contents/personal') ?>

                                    <!-- Professional Profile Tab -->
                                    <?= $this->include('participant/submission/edit-tab-contents/professional') ?>

                                    <!-- Entry Information Tab -->
                                    <?= $this->include('participant/submission/edit-tab-contents/entry') ?>

                                    <!-- Miscellaneous Tab -->
                                    <?= $this->include('participant/submission/edit-tab-contents/misc') ?>

                                    <!-- Preview Tab -->
                                    <?= $this->include('participant/submission/edit-tab-contents/preview') ?>
                                </div>
                                <!-- end tab content -->
                            </form>
                        </div>
                        <!-- end card body -->
                    </div>

                    <!--end row-->

                </div>
                <!-- container-fluid -->
            </div><!-- End Page-content -->

            <?= $this->include('partials/footer') ?>
        </div>
        <!-- end main content-->

    </div>
    <!-- END layout-wrapper -->

    <?= $this->include('partials/vendor-scripts') ?>

    <script>
        // Set base URL for the flag-input script to use
        var baseAssetsUrl = "<?= base_url('assets/json/') ?>";

        // Track form changes
        let formChanged = false;

        // Function to mark form as changed
        function markFormAsChanged() {
            formChanged = true;
        }

        // Add event listeners to all form inputs to track changes
        document.addEventListener('DOMContentLoaded', function() {
            const formInputs = document.querySelectorAll('input, select, textarea');
            formInputs.forEach(function(input) {
                input.addEventListener('change', markFormAsChanged);
                input.addEventListener('input', markFormAsChanged);
            });

            // Add event listeners to Quill editors if any
            const quillEditors = document.querySelectorAll('.quill-editor');
            quillEditors.forEach(function(editor) {
                if (editor.querySelector('.ql-editor')) {
                    editor.querySelector('.ql-editor').addEventListener('DOMSubtreeModified', markFormAsChanged);
                }
            });

            // Add event listener for the submit button to bypass the unsaved changes check
            const submitBtn = document.getElementById('submit-application-btn');
            if (submitBtn) {
                submitBtn.addEventListener('click', function() {
                    // Reset formChanged to false when submit button is clicked
                    // This prevents the beforeunload warning when form is properly submitted
                    formChanged = false;
                });
            }

            // Add event listeners for all save buttons to bypass the unsaved changes check
            const saveButtonIds = ['save-personal-btn', 'save-professional-btn', 'save-entry-btn', 'save-misc-btn'];
            saveButtonIds.forEach(function(btnId) {
                const saveBtn = document.getElementById(btnId);
                if (saveBtn) {
                    saveBtn.addEventListener('click', function() {
                        // Reset formChanged to false when any save button is clicked
                        // This prevents the beforeunload warning after user has saved the form
                        formChanged = false;
                    });
                }
            });

            // Show warning when user tries to leave the page
            window.addEventListener('beforeunload', function(e) {
                if (formChanged) {
                    // Standard message (browser will show its own dialog)
                    const message = 'You have unsaved changes. Are you sure you want to leave this page?';
                    e.returnValue = message;
                    return message;
                }
            });

            // For links within the application, use SweetAlert for better UX
            document.addEventListener('click', function(e) {
                // Check if the clicked element is a link or has a link parent
                const linkElement = e.target.closest('a');
                if (linkElement && formChanged && !linkElement.hasAttribute('data-no-confirm')) {
                    const href = linkElement.getAttribute('href');
                    // Only handle internal links and not anchors
                    if (href && href !== '#' && !href.startsWith('javascript:')) {
                        e.preventDefault();

                        Swal.fire({
                            title: 'Unsaved Changes',
                            text: 'You have unsaved changes. Are you sure you want to leave this page?',
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonText: 'Yes, leave page',
                            cancelButtonText: 'No, stay here',
                            confirmButtonColor: '#d33',
                            cancelButtonColor: '#3085d6',
                        }).then((result) => {
                            if (result.isConfirmed) {
                                window.location.href = href;
                            }
                        });
                    }
                }
            }); // Track the current active tab
            let currentActiveTab = document.querySelector('[data-bs-toggle="pill"].active');

            // Use Bootstrap's event system to intercept tab changes before they happen
            const tabEls = document.querySelectorAll('[data-bs-toggle="pill"]');
            tabEls.forEach(function(tabEl) {
                tabEl.addEventListener('show.bs.tab', function(event) {
                    // If form has changes and this is a tab switch (not initial load)
                    if (formChanged && currentActiveTab) {
                        // Prevent the default tab switching
                        event.preventDefault();

                        const clickedTab = event.target;

                        // Show confirmation dialog
                        Swal.fire({
                            title: 'Unsaved Changes',
                            text: 'You have unsaved changes in the current tab. Are you sure you want to switch tabs?',
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonText: 'Yes, switch tabs',
                            cancelButtonText: 'No, stay here',
                            confirmButtonColor: '#d33',
                            cancelButtonColor: '#3085d6',
                            allowOutsideClick: false
                        }).then((result) => {
                            if (result.isConfirmed) {
                                // Temporarily disable the change detection
                                const originalFormChanged = formChanged;
                                formChanged = false;

                                // Manually trigger tab switch using Bootstrap's API
                                const bsTab = new bootstrap.Tab(clickedTab);
                                bsTab.show();

                                // Update current active tab reference
                                currentActiveTab = clickedTab;

                                // Restore change detection state
                                setTimeout(() => {
                                    formChanged = originalFormChanged;
                                }, 100);
                            }
                        });
                    } else {
                        // Update current active tab reference
                        currentActiveTab = event.target;
                    }
                });
            });

            // Check if current program is active
            const programActive = <?= isset($currentProgram['is_active']) ? ($currentProgram['is_active'] ? 'true' : 'false') : 'true' ?>;
            const programName = "<?= isset($currentProgram['name']) ? htmlspecialchars($currentProgram['name'], ENT_QUOTES) : 'Selected program' ?>";

            if (!programActive) {
                // Disable all form elements
                const formElements = document.querySelectorAll('input, select, textarea, button.nexttab, button#submit-application-btn');
                formElements.forEach(function(element) {
                    element.disabled = true;
                });

                // Show alert message
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        title: 'Inactive Program',
                        html: `<p>The program <strong>${programName}</strong> is currently inactive.</p>
                           <p>You cannot access or edit the submission form for an inactive program.</p>
                           <p>Please select an active program from the dropdown menu above.</p>`,
                        icon: 'warning',
                        allowOutsideClick: false,
                        confirmButtonText: 'OK',
                        confirmButtonColor: '#3085d6',
                    }).then((result) => {
                        // Redirect to dashboard after dismissing
                        window.location.href = "<?= base_url('dashboard') ?>";
                    });
                } else {
                    // Fallback for browsers without SweetAlert
                    alert(`The program "${programName}" is currently inactive. You cannot access or edit the submission form for an inactive program. Please select an active program from the dropdown menu.`);
                    window.location.href = "<?= base_url('dashboard') ?>";
                }

                // Add a visual indicator at the top of the form
                const formCard = document.querySelector('.card-body');
                if (formCard) {
                    const inactiveAlert = document.createElement('div');
                    inactiveAlert.className = 'alert alert-warning mb-4';
                    inactiveAlert.innerHTML = `
                    <div class="d-flex align-items-center">
                        <i class="ri-alert-line me-3 fs-3"></i>
                        <div>
                            <h5 class="mb-1">Inactive Program</h5>
                            <p class="mb-0">This program is currently inactive. Form editing is disabled.</p>
                        </div>
                    </div>
                `;
                    formCard.prepend(inactiveAlert);
                }
            }
        });
    </script>

    <!-- QuillJS JavaScript -->
    <script src="<?= base_url('assets/libs/quill/quill.min.js') ?>"></script>

    <!-- input flag init -->
    <script src="/assets/js/custom/submission-flag-input.init.js"></script>

    <!-- Sweet Alerts js -->
    <script src="/assets/libs/sweetalert2/sweetalert2.min.js"></script>

    <!-- Sweet alert init js-->
    <script src="/assets/js/pages/sweetalerts.init.js"></script>

    <!-- App js -->
    <script src="/assets/js/app.js"></script>

</body>

</html>