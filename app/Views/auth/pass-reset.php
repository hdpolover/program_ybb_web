<?= $this->include('partials/main') ?>

<head>

    <?php echo view('partials/title-meta', array('title'=>'Create New Password')); ?>

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
                            <div class="row justify-content-center g-0">
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
                                        <h5 class="text-primary">Create new password</h5>
                                        <p class="text-muted">Your new password must be different from previous used password.</p>

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
                                        
                                        <div class="p-2">
                                            <form autocomplete="off" action="<?= base_url('set-new-password') ?>" method="POST" id="password-form">

                                                <input type="hidden" name="token" value="<?= $token ?>">

                                                <div class="mb-3">
                                                    <label class="form-label" for="password-input">Password</label>
                                                    <div class="position-relative auth-pass-inputgroup">
                                                        <input type="password" class="form-control pe-5 password-input" name="password" onpaste="return false" placeholder="Enter password" id="password-input" aria-describedby="passwordInput" pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}" required>
                                                        <button class="btn btn-link position-absolute end-0 top-0 text-decoration-none text-muted password-addon" type="button" id="password-addon"><i class="ri-eye-fill align-middle"></i></button>
                                                    </div>
                                                </div>

                                                <div class="mb-3">
                                                    <label class="form-label" for="confirm-password-input">Confirm Password</label>
                                                    <div class="position-relative auth-pass-inputgroup mb-3">
                                                        <input type="password" class="form-control pe-5 password-input" name="confirm_password" onpaste="return false" placeholder="Confirm password" pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}" id="confirm-password-input" required>
                                                        <button class="btn btn-link position-absolute end-0 top-0 text-decoration-none text-muted password-addon" type="button" id="confirm-password-addon"><i class="ri-eye-fill align-middle"></i></button>
                                                    </div>
                                                    <div id="password-match-error" class="text-danger" style="display: none;">Passwords do not match!</div>
                                                </div>

                                                <div id="password-contain" class="p-3 bg-light mb-2 rounded">
                                                    <h5 class="fs-13">Password must contain:</h5>
                                                    <p id="pass-length" class="invalid fs-12 mb-2">Minimum <b>8 characters</b></p>
                                                    <p id="pass-lower" class="invalid fs-12 mb-2">At <b>lowercase</b> letter (a-z)</p>
                                                    <p id="pass-upper" class="invalid fs-12 mb-2">At least <b>uppercase</b> letter (A-Z)</p>
                                                    <p id="pass-number" class="invalid fs-12 mb-0">A least <b>number</b> (0-9)</p>
                                                </div>

                                                <div class="mt-4">
                                                    <button class="btn btn-success w-100" type="submit">Reset Password</button>
                                                </div>

                                            </form>
                                        </div>

                                        <div class="mt-5 text-center">
                                            <p class="mb-0">Wait, I remember my password... <a href="auth-signin-cover" class="fw-semibold text-primary text-decoration-underline"> Click here </a> </p>
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

    <!-- Custom script for password matching validation -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Get password fields and error message element
            const passwordInput = document.getElementById('password-input');
            const confirmPasswordInput = document.getElementById('confirm-password-input');
            const passwordMatchError = document.getElementById('password-match-error');
            const passwordForm = document.getElementById('password-form');
            const passwordAddon = document.getElementById('password-addon');
            const confirmPasswordAddon = document.getElementById('confirm-password-addon');

            // Initially hide the error message
            passwordMatchError.style.display = 'none';

            // Function to check if passwords match
            function checkPasswordsMatch() {
                if(confirmPasswordInput.value !== '' && passwordInput.value !== confirmPasswordInput.value) {
                    passwordMatchError.style.display = 'block';
                    return false;
                } else {
                    passwordMatchError.style.display = 'none';
                    return true;
                }
            }

            // Add event listeners
            confirmPasswordInput.addEventListener('input', checkPasswordsMatch);
            passwordInput.addEventListener('input', function() {
                if(confirmPasswordInput.value) {
                    checkPasswordsMatch();
                }
            });

            // Validate before form submission
            passwordForm.addEventListener('submit', function(event) {
                if(!checkPasswordsMatch()) {
                    event.preventDefault();
                }
            });

            // Handle password visibility toggle for main password
            passwordAddon.addEventListener('click', function() {
                if(passwordInput.type === 'password') {
                    passwordInput.type = 'text';
                } else {
                    passwordInput.type = 'password';
                }
            });

            // Handle password visibility toggle for confirm password
            confirmPasswordAddon.addEventListener('click', function() {
                if(confirmPasswordInput.type === 'password') {
                    confirmPasswordInput.type = 'text';
                } else {
                    confirmPasswordInput.type = 'password';
                }
            });

            // Password validation for strength requirements
            passwordInput.addEventListener('keyup', function() {
                // Validate lowercase letters
                const lowerCaseLetters = /[a-z]/g;
                const upperCaseLetters = /[A-Z]/g;
                const numbers = /[0-9]/g;
                const passLower = document.getElementById("pass-lower");
                const passUpper = document.getElementById("pass-upper");
                const passNumber = document.getElementById("pass-number");
                const passLength = document.getElementById("pass-length");
                
                // Lowercase validation
                if(passwordInput.value.match(lowerCaseLetters)) {
                    passLower.classList.remove("invalid");
                    passLower.classList.add("valid");
                } else {
                    passLower.classList.remove("valid");
                    passLower.classList.add("invalid");
                }
                
                // Uppercase validation
                if(passwordInput.value.match(upperCaseLetters)) {
                    passUpper.classList.remove("invalid");
                    passUpper.classList.add("valid");
                } else {
                    passUpper.classList.remove("valid");
                    passUpper.classList.add("invalid");
                }
                
                // Number validation
                if(passwordInput.value.match(numbers)) {
                    passNumber.classList.remove("invalid");
                    passNumber.classList.add("valid");
                } else {
                    passNumber.classList.remove("valid");
                    passNumber.classList.add("invalid");
                }
                
                // Length validation
                if(passwordInput.value.length >= 8) {
                    passLength.classList.remove("invalid");
                    passLength.classList.add("valid");
                } else {
                    passLength.classList.remove("valid");
                    passLength.classList.add("invalid");
                }
            });
        });
    </script>
</body>

</html>