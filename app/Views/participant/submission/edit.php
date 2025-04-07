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
    </script>

    <!-- QuillJS JavaScript -->
    <script src="<?= base_url('assets/libs/quill/quill.min.js') ?>"></script>

    <!-- Initialize QuillJS editors -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Professional Experiences editor
            var experiencesQuill = new Quill('#professional-experiences-editor', {
                theme: 'snow',
                modules: {
                    toolbar: [
                        ['bold', 'italic', 'underline', 'strike'],
                        [{
                            'header': [1, 2, 3, 4, 5, 6, false]
                        }],
                        [{
                            'color': []
                        }, {
                            'background': []
                        }],
                        [{
                            'list': 'ordered'
                        }, {
                            'list': 'bullet'
                        }],
                        [{
                            'align': []
                        }],
                        ['clean']
                    ]
                },
                placeholder: 'Describe your relevant professional experiences...'
            });

            // Set existing content if available
            if (typeof <?= json_encode($currentParticipant['experiences'] ?? '') ?> === 'string' && <?= json_encode($currentParticipant['experiences'] ?? '') ?>.length > 0) {
                experiencesQuill.root.innerHTML = <?= json_encode($currentParticipant['experiences'] ?? '') ?>;
                document.getElementById('professional-experiences').value = experiencesQuill.root.innerHTML;
            }

            // Achievements editor
            var achievementsQuill = new Quill('#professional-achievements-editor', {
                theme: 'snow',
                modules: {
                    toolbar: [
                        ['bold', 'italic', 'underline', 'strike'],
                        [{
                            'header': [1, 2, 3, 4, 5, 6, false]
                        }],
                        [{
                            'color': []
                        }, {
                            'background': []
                        }],
                        [{
                            'list': 'ordered'
                        }, {
                            'list': 'bullet'
                        }],
                        [{
                            'align': []
                        }],
                        ['clean']
                    ]
                },
                placeholder: 'List your key achievements and recognitions...'
            });

            // Set existing content if available
            if (typeof <?= json_encode($currentParticipant['achievements'] ?? '') ?> === 'string' && <?= json_encode($currentParticipant['achievements'] ?? '') ?>.length > 0) {
                achievementsQuill.root.innerHTML = <?= json_encode($currentParticipant['achievements'] ?? '') ?>;
                document.getElementById('professional-achievements').value = achievementsQuill.root.innerHTML;
            }

            // Store content in hidden inputs when form is submitted
            document.querySelector('form').addEventListener('submit', function() {
                document.getElementById('professional-experiences').value = experiencesQuill.root.innerHTML;
                document.getElementById('professional-achievements').value = achievementsQuill.root.innerHTML;
            });

            // Also update the hidden inputs when moving to next steps
            document.querySelectorAll('.nexttab').forEach(function(button) {
                button.addEventListener('click', function() {
                    document.getElementById('professional-experiences').value = experiencesQuill.root.innerHTML;
                    document.getElementById('professional-achievements').value = achievementsQuill.root.innerHTML;
                });
            });
        });
    </script>

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