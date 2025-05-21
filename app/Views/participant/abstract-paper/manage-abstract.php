<?= $this->include('partials/main') ?>

<head>

    <?php echo view('partials/title-meta', array('title'=>'Abstract Management')); ?>
    
    <!-- quill css -->
    <link href="/assets/libs/quill/quill.core.css" rel="stylesheet" type="text/css" />
    <link href="/assets/libs/quill/quill.bubble.css" rel="stylesheet" type="text/css" />
    <link href="/assets/libs/quill/quill.snow.css" rel="stylesheet" type="text/css" />
    
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

                    <?php 
                    $pageTitle = isset($abstract) ? 'Edit Abstract' : 'Create New Abstract';
                    echo view('partials/page-title', array('pagetitle'=>'Abstract Management', 'title'=>$pageTitle)); 
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

                    <div class="row">
                        <div class="col-lg-12">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title mb-0"><?= $pageTitle ?></h4>
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
                                                    <?php if(isset($topics) && is_array($topics)): ?>
                                                        <?php foreach($topics as $topic): ?>
                                                            <option value="<?= $topic['id'] ?>" <?= (isset($abstract) && $abstract['topic_id'] == $topic['id']) ? 'selected' : '' ?>>
                                                                <?= $topic['name'] ?>
                                                            </option>
                                                        <?php endforeach; ?>
                                                    <?php endif; ?>
                                                </select>
                                                <div class="invalid-feedback">Please select a topic.</div>
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
                                                <div class="hstack gap-2 justify-content-end">
                                                    <a href="<?= base_url('abstract-paper') ?>" class="btn btn-light">Cancel</a>
                                                    <button type="submit" class="btn btn-primary" id="submit-btn">
                                                        <?= isset($abstract) ? 'Update Abstract' : 'Submit Abstract' ?>
                                                    </button>
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

    <?= $this->include('partials/customizer') ?>

    <?= $this->include('partials/vendor-scripts') ?>

    <!-- Sweet Alert js-->
    <script src="/assets/libs/sweetalert2/sweetalert2.min.js"></script>

    <!-- quill js -->
    <script src="/assets/libs/quill/quill.min.js"></script>

    <!-- init js -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize Quill editor
            var quill = new Quill('#abstract-editor', {
                theme: 'snow',
                modules: {
                    toolbar: [
                        ['bold', 'italic', 'underline', 'strike'],
                        ['blockquote', 'code-block'],
                        [{ 'header': 1 }, { 'header': 2 }],
                        [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                        [{ 'script': 'sub'}, { 'script': 'super' }],
                        [{ 'indent': '-1'}, { 'indent': '+1' }],
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
            abstractForm.addEventListener('submit', function(e) {
                // Get editor content and set to hidden field
                const content = quill.root.innerHTML;
                document.getElementById('abstract-content').value = content;
                
                // Basic validation
                let isValid = true;
                
                if (!document.getElementById('topic').value) {
                    document.getElementById('topic').classList.add('is-invalid');
                    isValid = false;
                }
                
                if (!document.getElementById('title').value) {
                    document.getElementById('title').classList.add('is-invalid');
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
                
                if (!isValid) {
                    e.preventDefault();
                    Swal.fire({
                        title: 'Form Error!',
                        text: 'Please fill in all required fields correctly.',
                        icon: 'error',
                        confirmButtonText: 'OK',
                        confirmButtonColor: '#5156be'
                    });
                }
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
        });
    </script>

    <!-- App js -->
    <script src="/assets/js/app.js"></script>
</body>

</html>