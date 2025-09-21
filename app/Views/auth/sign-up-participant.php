<?= $this->include('partials/main') ?>

<head> <?php echo view('partials/landing-meta', array(
            'title' => 'Sign Up as Participant',
            'meta_description' => 'Join Japan Youth Summit as a participant. Register for our cultural exchange program and start your journey.',
            'meta_keywords' => 'japan youth summit registration, participant signup, youth program registration, jys application'
        )); ?>

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
                        <div class="card overflow-hidden m-0">
                            <div class="row justify-content-center g-0">
                                <div class="col-lg-6">
                                    <div class="p-lg-5 p-4 auth-one-bg h-100" style="background-image: url('<?= $webSettings['img_url'] ?? '/assets/images/bg-auth.jpg' ?>');">
                                        <div class="bg-overlay"></div>
                                        <div class="position-relative h-100 d-flex flex-column justify-content-center align-items-center">
                                            <div class="mb-4 text-center">
                                                <a href="/" class="d-block">
                                                    <img src="<?= $webSettings['logo_url'] ?? '/assets/images/logo-light.png' ?>" alt="Logo" height="80" class="mx-auto">
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-lg-6">
                                    <div class="p-lg-5 p-4">
                                        <div>
                                            <h5 class="text-primary">Participate in <?= $program['name'] ?? 'Our Programs' ?></h5>
                                            <p class="text-muted">Create your account to get started.</p>
                                            <?php if (!empty($registrationType)): ?>
                                                <div class="alert alert-info alert-dismissible fade show" role="alert">
                                                    <i class="ri-information-line me-1"></i>
                                                    <strong>Registration Type:</strong> 
                                                    <?php if ($registrationType === 'fully_funded'): ?>
                                                        <span class="text-success">Fully Funded</span> - Reimbursement program with essays and interviews
                                                    <?php else: ?>
                                                        <span class="text-primary">Self Funded</span> - Guaranteed participation with standard payment
                                                    <?php endif; ?>
                                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                                </div>
                                            <?php endif; ?>
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
                                            <form action="<?= base_url('register') ?>" method="post" class="needs-validation" novalidate>
                                                <!-- Hidden fields for ambassador referral -->
                                                <input type="hidden" name="ambassador_id" value="<?= $ambassadorId ?? '' ?>">
                                                <input type="hidden" name="q" value="<?= $ambassadorQuery ?? '' ?>">
                                                <!-- Hidden field for registration type (category) -->
                                                <input type="hidden" name="registration_type" value="<?= $registrationType ?? '' ?>">

                                                <div class="mb-3">
                                                    <label for="fullname" class="form-label">Full Name <span class="text-danger">*</span></label>
                                                    <input type="text" class="form-control" id="fullname" name="fullname" placeholder="Enter your full name" required>
                                                    <div class="invalid-feedback">
                                                        Please enter your full name
                                                    </div>
                                                </div>

                                                <div class="mb-3">
                                                    <label for="useremail" class="form-label">Email <span class="text-danger">*</span></label>
                                                    <input type="email" class="form-control" id="useremail" name="email" placeholder="Enter email address" required>
                                                    <div class="invalid-feedback">
                                                        Please enter a valid email
                                                    </div>
                                                </div>

                                                <div class="mb-3">
                                                    <label class="form-label" for="password-input">Password <span class="text-danger">*</span></label>
                                                    <div class="position-relative auth-pass-inputgroup">
                                                        <input type="password" class="form-control pe-5 password-input" name="password" onpaste="return false" placeholder="Enter password" id="password-input" aria-describedby="passwordInput" pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}" required>
                                                        <button class="btn btn-link position-absolute end-0 top-0 text-decoration-none text-muted password-addon" type="button" id="password-addon"><i class="ri-eye-fill align-middle"></i></button>
                                                        <div class="invalid-feedback">
                                                            Please enter password
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="mb-3">
                                                    <label class="form-label" for="confirm-password-input">Confirm Password <span class="text-danger">*</span></label>
                                                    <div class="position-relative auth-pass-inputgroup">
                                                        <input type="password" class="form-control pe-5 password-input" name="confirm_password" onpaste="return false" placeholder="Confirm password" id="confirm-password-input" pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}" required>
                                                        <button class="btn btn-link position-absolute end-0 top-0 text-decoration-none text-muted password-addon" type="button" id="confirm-password-addon"><i class="ri-eye-fill align-middle"></i></button>
                                                        <div class="invalid-feedback">
                                                            Please confirm your password
                                                        </div>
                                                    </div>
                                                </div>

                                                <div id="password-contain" class="p-3 bg-light mb-2 rounded">
                                                    <h5 class="fs-13">Password must contain:</h5>
                                                    <p id="pass-length" class="invalid fs-12 mb-2">Minimum <b>8 characters</b></p>
                                                    <p id="pass-lower" class="invalid fs-12 mb-2">At <b>lowercase</b> letter (a-z)</p>
                                                    <p id="pass-upper" class="invalid fs-12 mb-2">At least <b>uppercase</b> letter (A-Z)</p>
                                                    <p id="pass-number" class="invalid fs-12 mb-0">A least <b>number</b> (0-9)</p>
                                                </div>

                                                <div class="mt-4">
                                                    <button class="btn btn-success w-100" type="submit">Sign Up</button>
                                                </div>

                                            </form>
                                        </div>

                                        <div class="mt-5 text-center">
                                            <p class="mb-0">Already have an account? <a href="<?= site_url('sign-in') ?>" class="fw-semibold text-primary text-decoration-underline"> Sign In here</a> </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
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
                                </script>
                                <?= $webSettings['name'] ?? 'Your Company Name' ?>. All Rights Reserved.
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

    <!-- validation init -->
    <script src="/assets/js/pages/form-validation.init.js"></script>
    <!-- password create init -->
    <script src="/assets/js/pages/passowrd-create.init.js"></script>

    <!-- Add SweetAlert2 library for better user notifications -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Custom script for password confirmation validation -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const passwordInput = document.getElementById('password-input');
            const confirmPasswordInput = document.getElementById('confirm-password-input');
            const confirmPasswordFeedback = confirmPasswordInput.nextElementSibling.nextElementSibling;

            // Function to validate passwords match
            function validatePasswordMatch() {
                if (confirmPasswordInput.value === '') {
                    confirmPasswordFeedback.textContent = 'Please confirm your password';
                    confirmPasswordInput.setCustomValidity('');
                    return;
                }

                if (passwordInput.value !== confirmPasswordInput.value) {
                    confirmPasswordFeedback.textContent = 'Passwords do not match';
                    confirmPasswordInput.classList.add('is-invalid');
                    confirmPasswordInput.setCustomValidity('Passwords do not match');
                } else {
                    confirmPasswordInput.classList.remove('is-invalid');
                    if (confirmPasswordInput.value !== '') {
                        confirmPasswordInput.classList.add('is-valid');
                    }
                    confirmPasswordInput.setCustomValidity('');
                }
            }

            // Add event listeners to both password fields
            passwordInput.addEventListener('input', validatePasswordMatch);
            confirmPasswordInput.addEventListener('input', validatePasswordMatch);
        });
    </script>

    <!-- SweetAlert loading animation -->
    <script>
        document.querySelector('form.needs-validation').addEventListener('submit', function(event) {
            // Form validation will handle preventing the submission if invalid
            // We only want to show the loading animation if the form is valid
            if (this.checkValidity()) {
                Swal.fire({
                    title: 'Creating your account...',
                    html: '<div class="my-3">Please wait while we process your registration</div>',
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
            }
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