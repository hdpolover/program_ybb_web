<?= $this->include('partials/main') ?>

<head>
    <?= $this->include('partials/head-css') ?>
    <style>
        /* Custom styles for maintenance page */
        .maintenance-icon {
            font-size: 80px;
            color: #fff;
            margin-bottom: 20px;
        }
        
        /* Rotation animation for the gear icon */
        @keyframes spin {
            from {
                transform: rotate(0deg);
            }
            to {
                transform: rotate(360deg);
            }
        }
        
        .rotating-icon {
            display: inline-block;
            animation: spin 5s linear infinite;
        }
        
        /* Secondary rotation for nested icon if needed */
        @keyframes spin-reverse {
            from {
                transform: rotate(0deg);
            }
            to {
                transform: rotate(-360deg);
            }
        }
        
        .rotating-icon-reverse {
            display: inline-block;
            animation: spin-reverse 7s linear infinite;
        }
    </style>
</head>

<body>
    <div class="auth-page-wrapper pt-5">
        <!-- auth page bg -->
        <div class="auth-one-bg-position auth-one-bg" id="auth-particles" style="height: 80vh; background-image: url('<?= $webSettings['img_url'] ?>'); background-size: cover;">
            <div class="bg-overlay"></div>

            <div class="shape">
            <svg xmlns="http://www.w3.org/2000/svg" version="1.1" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 1440 120">
            <path d="M 0,36 C 144,53.6 432,123.2 720,124 C 1008,124.8 1296,56.8 1440,40L1440 140L0 140z"></path>
            </svg>
            </div>
        </div>

        <!-- auth page content -->
        <div class="auth-page-content">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="text-center mt-sm-5 pt-4">
                            <!-- Logo -->
                            <div class="mb-4">
                                <img src="<?= $webSettings['logo_url'] ?>" alt="Site Logo" height="50">
                            </div>
                            
                            <div class="mb-5 text-white-50">
                                <h1 class="display-5 coming-soon-text">Site is Under Maintenance</h1>
                                
                                <div class="maintenance-icon mt-5">
                                    <i class="mdi mdi-cog-outline text-white rotating-icon"></i>
                                    <i class="mdi mdi-cog text-white rotating-icon-reverse" style="font-size: 50px; margin-left: -30px; vertical-align: top;"></i>
                                </div>
                                
                                <div class="alert alert-info mt-4">
                                    <p>We're currently performing scheduled maintenance to improve your experience.</p>
                                    <p>Thank you for your patience while we make these improvements.</p>
                                    <p>We apologize for any inconvenience this may cause.</p>
                                </div>
                                
                            </div>

                            <p class="mb-0 text-muted">&copy;
                                <script>document.write(new Date().getFullYear())</script> 
                                <?= $webSettings['name'] ?>. All Rights Reserved
                            </p>
                        </div>
                    </div>
                </div>
                <!-- end row -->
            </div>
            <!-- end container -->
        </div>
        <!-- end auth page content -->
    </div>
    <!-- end auth-page-wrapper -->

    <?= $this->include('partials/vendor-scripts') ?>

    <!-- particles js -->
    <script src="/assets/libs/particles.js/particles.js"></script>
    <!-- particles app js -->
    <script src="/assets/js/pages/particles.app.js"></script>
</body>
</html>