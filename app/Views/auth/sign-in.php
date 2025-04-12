<?= $this->include('partials/main') ?>

<head>

    <?php echo view('partials/title-meta', array('title' => 'Sign In')); ?>

    <?= $this->include('partials/head-css') ?>

</head>

<body>

    <!-- auth-page wrapper -->
    <div class="auth-page-wrapper auth-bg-cover py-5 d-flex justify-content-center align-items-center min-vh-100">
        <div class="bg-overlay"></div>
        <!-- auth-page content -->
        <div class="auth-page-content overflow-hidden pt-lg-5">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card overflow-hidden">
                            <div class="row g-0">
                                <div class="col-lg-6">
                                    <div class="p-lg-5 p-4 auth-one-bg h-100" style="background-image: url('<?= $webSettings['img_url'] ?>');">
                                        <div class="bg-overlay"></div>
                                        <div class="position-relative h-100 d-flex flex-column justify-content-center align-items-center">
                                            <div class="mb-4 text-center">
                                                <a href="/" class="d-block">
                                                    <img src="<?= $webSettings['logo_url'] ?>" alt="" height="50">
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- end col -->

                                <div class="col-lg-6">
                                    <div class="p-lg-5 p-4">
                                        <div>
                                            <h2 class="text-primary">Welcome Back!</h2>
                                            <p class="text-muted">Sign in to continue.</p>
                                        </div>

                                        <?php if (session()->has('error')): ?>
                                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                                <?= session('error') ?>
                                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                            </div>
                                        <?php endif; ?>

                                        <?php if (session()->has('success')): ?>
                                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                                <?= session('success') ?>
                                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                            </div>
                                        <?php endif; ?>

                                        <div class="mt-4">
                                            <form action="<?= base_url('authorize') ?>" method="post" id="login-form">
                                                <!-- Hidden field for user type (2=participant) -->
                                                <input type="hidden" name="type" value="2">

                                                <div class="mb-3">
                                                    <label for="email" class="form-label">Email</label>
                                                    <input type="text" class="form-control" id="email" name="email" placeholder="Enter email">
                                                </div>

                                                <div class="mb-3">
                                                    <div class="float-end">
                                                        <a href="<?= site_url('forgot-password') ?>" class="text-muted">Forgot password?</a>
                                                    </div>
                                                    <label class="form-label" for="password-input">Password</label>
                                                    <div class="position-relative auth-pass-inputgroup mb-3">
                                                        <input type="password" class="form-control pe-5 password-input" name="password" placeholder="Enter password" id="password-input">
                                                        <button class="btn btn-link position-absolute end-0 top-0 text-decoration-none text-muted password-addon" type="button" id="password-addon"><i class="ri-eye-fill align-middle"></i></button>
                                                    </div>
                                                </div>

                                                <div class="mt-4">
                                                    <button class="btn btn-success w-100" type="submit" id="login-button">Sign In</button>
                                                </div>
                                            </form>
                                        </div>

                                        <div class="mt-5 text-center">
                                            <p class="mb-0">Don't have an account? <a href="<?= site_url('sign-up') ?>" class="fw-semibold text-primary text-decoration-underline"> Sign up now!</a> </p>
                                            <p class="mt-3 mb-0">Part of our program ambassadors? <a href="<?= site_url('ambassadors/sign-in') ?>" class="fw-semibold text-primary text-decoration-underline">Sign in here!</a></p>
                                        </div>
                                    </div>
                                </div>
                                <!-- end col -->
                            </div>
                            <!-- end row -->
                        </div>
                        <!-- end card -->
                    </div>
                    <!-- end col -->

                </div>
                <!-- end row -->
            </div>
            <!-- end container -->
        </div>
        <!-- end auth page content -->

        <!-- footer -->
        <footer class="footer">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="text-center">
                            <p class="mb-0">&copy;
                                <script>
                                    document.write(new Date().getFullYear())
                                </script> <?= $webSettings['name'] ?>. All rights reserved.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </footer>
        <!-- end Footer -->
    </div>
    <!-- end auth-page-wrapper -->

    <?= $this->include('partials/vendor-scripts') ?>

    <!-- password-addon init -->
    <script src="/assets/js/pages/password-addon.init.js"></script>

     <!-- Add SweetAlert2 library for better user notifications -->
     <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>    <!-- SweetAlert loading animation -->
    <script>
        document.getElementById('login-form').addEventListener('submit', function() {
            Swal.fire({
                title: 'Signing you in...',
                html: '<div class="my-3">Please wait while we authenticate your credentials</div>',
                icon: 'info',
                showConfirmButton: false,
                allowOutsideClick: false,
                allowEscapeKey: false,
                customClass: {
                    popup: 'animated-popup',
                    title: 'swal-title',
                    icon: 'swal-icon'
                },
                didOpen: () => {
                    Swal.showLoading();
                }
            });
        });
    </script>
    
    <!-- Custom styles for SweetAlert loading -->
    <style>
        .animated-popup {
            border-radius: 15px !important;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1) !important;
            border: 1px solid rgba(0, 128, 0, 0.1) !important;
        }
        
        .swal-title {
            font-weight: 600 !important;
            color: #2f55d4 !important;
        }
        
        .swal-icon {
            border-color: #2f55d4 !important;
        }
        
        .swal2-loader {
            border-color: #2f55d4 transparent #2f55d4 transparent !important;
        }
    </style>
</body>

</html>