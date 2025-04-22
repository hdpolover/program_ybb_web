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
                                            <h2 class="text-primary">Welcome Back, Ambassador!</h2>
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
                                            <form action="<?= base_url('ambassadors/authorize') ?>" method="post" id="ambassador-login-form">
                                                <div class="mb-3">
                                                    <label for="email" class="form-label">Email</label>
                                                    <input type="text" class="form-control" id="email" name="email" placeholder="Enter email" required>
                                                    <div class="invalid-feedback">Please enter your email</div>
                                                </div>

                                                <div class="mb-3">
                                                    <label class="form-label" for="referral-input">Referral Code</label>
                                                    <div class="position-relative mb-3">
                                                        <input type="text" class="form-control" name="referral_code" placeholder="Enter referral code" id="referral-input" required>
                                                        <div class="invalid-feedback">Please enter your referral code</div>
                                                    </div>
                                                </div>

                                                <div id="form-error" class="alert alert-danger mb-3" style="display:none;">
                                                    Please fill in all required fields.
                                                </div>

                                                <div class="mt-4">
                                                    <button class="btn btn-success w-100" type="submit" id="submit-btn">Sign In</button>
                                                </div>



                                            </form>
                                        </div>

                                        <div class="mt-5 text-center">
                                            <p class="mb-0">Not an ambassador? <a href="<?= site_url('sign-in') ?>" class="fw-semibold text-primary text-decoration-underline"> Sign in as a participant here!</a> </p>
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

    <!-- Form validation script -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('ambassador-login-form');
            const errorMessage = document.getElementById('form-error');
            
            form.addEventListener('submit', function(event) {
                let isValid = true;
                const email = document.getElementById('email');
                const referralCode = document.getElementById('referral-input');
                
                // Reset validation state
                errorMessage.style.display = 'none';
                form.querySelectorAll('.form-control').forEach(input => {
                    input.classList.remove('is-invalid');
                });
                
                // Check if email is empty
                if (!email.value.trim()) {
                    email.classList.add('is-invalid');
                    isValid = false;
                }
                
                // Check if referral code is empty
                if (!referralCode.value.trim()) {
                    referralCode.classList.add('is-invalid');
                    isValid = false;
                }
                
                // If any validation fails, prevent form submission
                if (!isValid) {
                    event.preventDefault();
                    errorMessage.style.display = 'block';
                    // Scroll to error message
                    errorMessage.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
            });
            
            // Clear validation errors when user starts typing
            form.querySelectorAll('.form-control').forEach(input => {
                input.addEventListener('input', function() {
                    this.classList.remove('is-invalid');
                    if (document.querySelectorAll('.is-invalid').length === 0) {
                        errorMessage.style.display = 'none';
                    }
                });
            });
        });
    </script>
</body>

</html>